<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Theme;
use App\Models\Navigation;

use App\Models\Term;
use App\Models\Coupon;
use App\Services\RoomService;
use App\Services\CouponService;
use App\Services\PriceCalculationService;
use App\Services\BookingService;
use App\Helpers\CurrencyHelper;
use App\Services\PaymentServiceInterface;
use App\Repositories\PaymentRepositoryInterface;
use Illuminate\Support\Facades\Log;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $tenant = null;
        $theme = null;
        $rooms = collect();
        $navigations = collect();
        $useDummy = false;
        $extraServices = collect();

        // Resolve tenant using helper
        $tenant = function_exists('tenant') ? tenant() : null;

        // Load navigations... (keep existing logic)
        if (class_exists(Navigation::class)) {
            try {
                $query = Navigation::active()->ordered();
                if ($tenant) {
                    $query->where('tenant_id', $tenant->id);
                } else {
                    $firstTenant = Tenant::first();
                    if ($firstTenant) {
                        $query->where('tenant_id', $firstTenant->id);
                    }
                }
                $navigations = $query->get();
            } catch (\Throwable $e) {
                $navigations = collect();
            }
        }

        // Filter Rooms using RoomService
        if (class_exists(Room::class)) {
            $roomService = new RoomService();
            $rooms = $roomService->searchAvailableRooms($request->all(), $tenant ? $tenant->id : null);
            
            // Fetch Extra Services (Terms)
            $extraServices = Term::where('type', \App\Enums\TermTypeEnum::EXTRA_SERVICE)
                ->where(function($q) use ($tenant) {
                    if ($tenant) $q->where('tenant_id', $tenant->id);
                })->get();
        }

        // Fallback dummy data if needed... (keep existing)
        if ($rooms->isEmpty()) {
            $useDummy = true;
            // (Dummy data logic remains same)
            $rooms = collect(); // For now let's assume real data or empty
        }

        // Fetch active offers/coupons
        $offers = collect();
        if (class_exists(Coupon::class)) {
            $now = now()->startOfDay();
            $offers = Coupon::where('status', true)
                ->where(function($q) use ($now) {
                    $q->where('start_date', '<=', $now)->orWhereNull('start_date');
                })
                ->where(function($q) use ($now) {
                    $q->where('expire_date', '>=', $now)->orWhereNull('expire_date');
                })
                ->where(function($q) use ($tenant) {
                    if ($tenant) {
                        $q->where('tenant_id', $tenant->id);
                    }
                })
                ->latest()
                ->get();
        }

        return view('rooms.index', compact('rooms', 'tenant', 'navigations', 'useDummy', 'extraServices', 'offers'));
    }

    /**
     * AJAX: Validate coupon code
     */
    public function validateCoupon(Request $request)
    {
        $service = new CouponService();
        $result = $service->validate(
            $request->code, 
            $request->hotel_id, 
            session('tenant_id')
        );

        return response()->json($result);
    }

    /**
     * Initial checkout step - store selection in session
     */
    public function checkoutInit(Request $request)
    {
        $data = json_decode($request->booking_data, true);
        if (!$data) return back()->with('error', 'Invalid booking data.');

        session(['pending_booking' => $data]);

        // For now we assume the first room in selection is the main one for the checkout page
        // (Simplified for single-room type selection as per UI usually)
        $roomId = $data['rooms'][0]['id'] ?? null;
        if (!$roomId) return back()->with('error', 'No room selected.');

        return redirect()->route('rooms.checkout', ['id' => $roomId]);
    }

    public function checkout(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        $pending = session('pending_booking', []);
        
        $checkIn = $request->get('check_in', now()->format('Y-m-d'));
        $checkOut = $request->get('check_out', now()->addDay()->format('Y-m-d'));
        
        $checkInDate = \Carbon\Carbon::parse($checkIn);
        $checkOutDate = \Carbon\Carbon::parse($checkOut);
        $nights = max(1, $checkInDate->diffInDays($checkOutDate));

        // Use PriceCalculationService for backend validation of totals
        $priceService = new PriceCalculationService();
        $calc = $priceService->calculate(
            $room,
            $pending['rooms'][0]['quantity'] ?? 1,
            $nights,
            collect($pending['rooms'][0]['extraServices'] ?? [])->pluck('id')->toArray(),
            $pending['coupon'] ?? null
        );

        return view('rooms.checkout', compact('room', 'calc', 'checkIn', 'checkOut', 'nights', 'pending'));
    }

    public function book(Request $request, $id)
    {
        $request->validate([
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'customer_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $pending = session('pending_booking', []);
        $bookingService = new BookingService(new PriceCalculationService(), new CouponService());
        
        try {
            $data = array_merge($request->all(), [
                'room_id' => $id,
                'quantity' => $pending['rooms'][0]['quantity'] ?? 1,
                'nights' => \Carbon\Carbon::parse($request->check_in)->diffInDays(\Carbon\Carbon::parse($request->check_out)),
                'extra_services' => collect($pending['rooms'][0]['extraServices'] ?? [])->pluck('id')->toArray(),
                'coupon_code' => $pending['coupon'] ?? null
            ]);

            $order = $bookingService->createBooking($data);

            // Update room availability
            $roomService = new RoomService();
            $roomService->updateRoomBookedCount($id, $request->check_in, $request->check_out, $data['quantity']);

            session()->forget('pending_booking');

            return redirect()->route('rooms.booking.complete', $order->id)->with('success', 'Booking created successfully! Order #: ' . $order->order_number);
        } catch (\Exception $e) {
            return back()->with('error', 'Booking failed: ' . $e->getMessage());
        }
    }

    public function bookingComplete(Request $request, $orderId)
    {
        $order = \App\Models\RoomOrder::findOrFail($orderId);
        return view('rooms.booking_complete', compact('order'));
    }

    public function bookAjax(Request $request, $id, PaymentServiceInterface $paymentService, PaymentRepositoryInterface $paymentRepo)
    {   
        // dd($request->all());
        Log::info('BookAjax request', ['request' => $request->all()]);
        $request->validate([
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'customer_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|in:online,cash',
        ]);

        $pending = session('pending_booking', []);
        $bookingService = new BookingService(new PriceCalculationService(), new CouponService());
        // dd(config('services.stripe'));
        // If online payment requested, ensure Stripe is configured BEFORE creating the booking
        if ($request->get('payment_method') === 'online') {
            if (!config('services.stripe.key') || !config('services.stripe.secret')) {
                return response()->json(['status' => 'error', 'message' => 'Payment gateway not configured. Please contact support.'], 500);
            }
        }

        try {
            $data = array_merge($request->all(), [
                'room_id' => $id,
                'quantity' => $pending['rooms'][0]['quantity'] ?? 1,
                'nights' => \Carbon\Carbon::parse($request->check_in)->diffInDays(\Carbon\Carbon::parse($request->check_out)),
                'extra_services' => collect($pending['rooms'][0]['extraServices'] ?? [])->pluck('id')->toArray(),
                'coupon_code' => $pending['coupon'] ?? null
            ]);

            $order = $bookingService->createBooking($data);
            // Log::info('Booking created', ['order' => $order]);
            // Update room availability
            $roomService = new RoomService();
            $roomService->updateRoomBookedCount($id, $request->check_in, $request->check_out, $data['quantity']);

            session()->forget('pending_booking');

            $paymentMethod = $request->get('payment_method');

            if ($paymentMethod === 'online') {
                // Create a PaymentIntent and return client_secret so frontend can collect card
                $intent = $paymentService->createPaymentIntent($order);
                return response()->json(['status' => 'intent', 'client_secret' => $intent['client_secret'] ?? null, 'order_id' => $order->id]);
            }

            // Cash payment: do not create a Payment record, only mark order as cash/pending
            $order->update(['payment_method' => 'cash', 'payment_status' => 'pending']);

            return response()->json(['status' => 'success', 'redirect' => route('rooms.booking.complete', $order->id)]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
