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

        // Reset any leftover pending booking or coupon on visiting room index search page
        session()->forget(['pending_booking', 'applied_coupon']);

        return view('rooms.index', compact('rooms', 'tenant', 'navigations', 'useDummy', 'extraServices', 'offers'));
    }

    /**
     * AJAX: Validate coupon code & calculate discounts
     */
    public function validateCoupon(Request $request)
    {
        $code = trim((string)$request->get('code'));
        $roomId = $request->get('room_id');
        $room = $roomId ? Room::find($roomId) : null;

        $hotelId = $request->get('hotel_id', $room?->hotel_id);
        $tenantId = $room?->tenant_id ?? session('tenant_id') ?? $request->get('tenant_id');

        $checkIn = $request->get('check_in', now()->format('Y-m-d'));
        $bookingTime = $request->get('time', now()->format('H:i'));

        // If removing coupon (code is empty or '__NONE__')
        if (empty($code) || $code === '__NONE__' || strtolower($code) === 'none') {
            session()->forget('applied_coupon');
            $pending = session('pending_booking', []);
            if (isset($pending['coupon'])) {
                unset($pending['coupon']);
                session(['pending_booking' => $pending]);
            }

            if ($room) {
                $checkOut = $request->get('check_out', now()->addDay()->format('Y-m-d'));
                $nights = max(1, \Carbon\Carbon::parse($checkIn)->diffInDays(\Carbon\Carbon::parse($checkOut)));
                $quantity = max(1, (int)$request->get('quantity', 1));
                $extraServices = (array)$request->get('extra_services', []);

                $priceService = new PriceCalculationService();
                $calc = $priceService->calculate($room, $quantity, $nights, $extraServices, null, $checkIn, $bookingTime);

                return response()->json([
                    'success' => true,
                    'removed' => true,
                    'message' => 'Coupon removed successfully.',
                    'coupon' => null,
                    'calc' => $calc,
                    'formatted' => [
                        'base_price' => CurrencyHelper::format($calc['base_price']),
                        'room_discount_amount' => CurrencyHelper::format($calc['room_discount_amount']),
                        'coupon_discount' => CurrencyHelper::format(0),
                        'total_discount' => CurrencyHelper::format($calc['total_discount']),
                        'room_total' => CurrencyHelper::format($calc['room_total']),
                        'extra_total' => CurrencyHelper::format($calc['extra_total']),
                        'sub_total' => CurrencyHelper::format($calc['sub_total']),
                        'tax_amount' => CurrencyHelper::format($calc['tax_amount']),
                        'cgst_amount' => CurrencyHelper::format($calc['cgst_amount']),
                        'sgst_amount' => CurrencyHelper::format($calc['sgst_amount']),
                        'total_payable' => CurrencyHelper::format($calc['total_payable']),
                    ]
                ]);
            }

            return response()->json(['success' => true, 'removed' => true, 'message' => 'Coupon removed.']);
        }

        $result = validate_coupon($code, $hotelId, $tenantId, $checkIn, $bookingTime);

        if (!$result['success']) {
            return response()->json($result, 422);
        }

        $coupon = $result['coupon'];
        session(['applied_coupon' => $coupon->code]);
        $pending = session('pending_booking', []);
        $pending['coupon'] = $coupon->code;
        session(['pending_booking' => $pending]);

        // If room is present, recalculate complete stay totals for instant frontend feedback
        if ($room) {
            $checkOut = $request->get('check_out', now()->addDay()->format('Y-m-d'));
            $nights = max(1, \Carbon\Carbon::parse($checkIn)->diffInDays(\Carbon\Carbon::parse($checkOut)));
            $quantity = max(1, (int)$request->get('quantity', 1));
            $extraServices = (array)$request->get('extra_services', []);

            $priceService = new PriceCalculationService();
            $calc = $priceService->calculate($room, $quantity, $nights, $extraServices, $coupon->code, $checkIn, $bookingTime);

            return response()->json([
                'success' => true,
                'message' => 'Coupon "' . $coupon->code . '" applied! Discount: ' . ($coupon->discount_type === 'percentage' ? $coupon->discount_value . '%' : CurrencyHelper::format($coupon->discount_value)),
                'coupon' => $coupon,
                'calc' => $calc,
                'formatted' => [
                    'base_price' => CurrencyHelper::format($calc['base_price']),
                    'room_discount_amount' => CurrencyHelper::format($calc['room_discount_amount']),
                    'coupon_discount' => CurrencyHelper::format($calc['coupon_discount']),
                    'total_discount' => CurrencyHelper::format($calc['total_discount']),
                    'room_total' => CurrencyHelper::format($calc['room_total']),
                    'extra_total' => CurrencyHelper::format($calc['extra_total']),
                    'sub_total' => CurrencyHelper::format($calc['sub_total']),
                    'tax_amount' => CurrencyHelper::format($calc['tax_amount']),
                    'cgst_amount' => CurrencyHelper::format($calc['cgst_amount']),
                    'sgst_amount' => CurrencyHelper::format($calc['sgst_amount']),
                    'total_payable' => CurrencyHelper::format($calc['total_payable']),
                ]
            ]);
        }

        return response()->json($result);
    }

    /**
     * Initial checkout step - store selection in session and forward query parameters
     */
    public function checkoutInit(Request $request)
    {
        $data = json_decode($request->booking_data, true);
        if (!$data) return back()->with('error', 'Invalid booking data.');

        session(['pending_booking' => $data]);
        if (!empty($data['coupon'])) {
            session(['applied_coupon' => $data['coupon']]);
        }

        $roomId = $data['rooms'][0]['id'] ?? null;
        if (!$roomId) return back()->with('error', 'No room selected.');

        $params = ['id' => $roomId];
        if (!empty($data['check_in'])) $params['check_in'] = $data['check_in'];
        if (!empty($data['check_out'])) $params['check_out'] = $data['check_out'];
        if (!empty($data['rooms'][0]['quantity'])) $params['quantity'] = $data['rooms'][0]['quantity'];
        if (!empty($data['coupon'])) $params['coupon'] = $data['coupon'];

        return redirect()->route('rooms.checkout', $params);
    }

    public function checkout(Request $request, $id)
    {
        $room = Room::with(['hotel.tenant', 'roomType', 'media'])->findOrFail($id);
        $tenant = $room->hotel?->tenant ?? \App\Models\Tenant::find(session('tenant_id')) ?? \App\Models\Tenant::first();
        $navigations = \App\Models\Navigation::active()->ordered()->where(function($q) use ($tenant) {
            if ($tenant) {
                $q->where('tenant_id', $tenant->id);
            }
        })->get();

        $pending = session('pending_booking', []);
        
        // Find matching room in pending booking if present
        $selectedRoomData = null;
        if (!empty($pending['rooms'])) {
            foreach ($pending['rooms'] as $r) {
                if (($r['id'] ?? null) == $id) {
                    $selectedRoomData = $r;
                    break;
                }
            }
            if (!$selectedRoomData && !empty($pending['rooms'][0])) {
                $selectedRoomData = $pending['rooms'][0];
            }
        }

        $checkIn = $request->get('check_in', $pending['check_in'] ?? now()->format('Y-m-d'));
        $checkOut = $request->get('check_out', $pending['check_out'] ?? now()->addDay()->format('Y-m-d'));
        $quantity = max(1, (int)$request->get('quantity', $selectedRoomData['quantity'] ?? 1));
        
        $checkInDate = \Carbon\Carbon::parse($checkIn);
        $checkOutDate = \Carbon\Carbon::parse($checkOut);
        $nights = max(1, $checkInDate->diffInDays($checkOutDate));

        // Determine coupon code: prioritize query parameter, then applied_coupon session, then pending session, then room default
        if ($request->has('coupon')) {
            $rawCoupon = $request->get('coupon');
            $couponCode = (empty($rawCoupon) || $rawCoupon === 'none' || $rawCoupon === '__NONE__') ? null : $rawCoupon;
            if ($couponCode) {
                session(['applied_coupon' => $couponCode]);
                $pending['coupon'] = $couponCode;
                session(['pending_booking' => $pending]);
            } else {
                session()->forget('applied_coupon');
                if (isset($pending['coupon'])) {
                    unset($pending['coupon']);
                    session(['pending_booking' => $pending]);
                }
            }
        } else {
            $couponCode = session('applied_coupon', $pending['coupon'] ?? ($room->coupon ?: null));
        }

        // Resolve extra services
        $extraServices = [];
        if ($request->has('extra_services')) {
            $extraServices = (array)$request->get('extra_services');
        } elseif (!empty($selectedRoomData['extraServices'])) {
            $extraServices = collect($selectedRoomData['extraServices'])->pluck('id')->toArray();
        }

        // Use PriceCalculationService for backend validation of totals
        $priceService = new PriceCalculationService();
        $calc = $priceService->calculate(
            $room,
            $quantity,
            $nights,
            $extraServices,
            $couponCode,
            $checkIn,
            now()->format('H:i')
        );

        // Effective coupon code after validity check
        $effectiveCoupon = $calc['coupon_code'] ?? null;
        if ($effectiveCoupon) {
            session(['applied_coupon' => $effectiveCoupon]);
        }

        return view('rooms.checkout', compact('room', 'tenant', 'navigations', 'calc', 'checkIn', 'checkOut', 'nights', 'quantity', 'pending', 'couponCode', 'effectiveCoupon', 'extraServices'));
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
            $couponCode = $request->get('coupon_code', $request->get('coupon', $pending['coupon'] ?? null));
            $quantity = max(1, (int)$request->get('quantity', $pending['rooms'][0]['quantity'] ?? 1));
            $nights = max(1, \Carbon\Carbon::parse($request->check_in)->diffInDays(\Carbon\Carbon::parse($request->check_out)));

            $data = array_merge($request->all(), [
                'room_id' => $id,
                'quantity' => $quantity,
                'nights' => $nights,
                'extra_services' => $request->get('extra_services', collect($pending['rooms'][0]['extraServices'] ?? [])->pluck('id')->toArray()),
                'coupon_code' => $couponCode,
                'notes' => $request->get('notes')
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
        $order = \App\Models\RoomOrder::with(['room.hotel', 'room.roomType'])->findOrFail($orderId);
        $tenant = $order->hotel?->tenant ?? \App\Models\Tenant::find(session('tenant_id')) ?? \App\Models\Tenant::first();
        $navigations = \App\Models\Navigation::active()->ordered()->where(function($q) use ($tenant) {
            if ($tenant) {
                $q->where('tenant_id', $tenant->id);
            }
        })->get();
        return view('rooms.booking_complete', compact('order', 'tenant', 'navigations'));
    }

    public function bookAjax(Request $request, $id, PaymentServiceInterface $paymentService, PaymentRepositoryInterface $paymentRepo)
    {   
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
        
        if ($request->get('payment_method') === 'online') {
            if (!config('services.stripe.key') || !config('services.stripe.secret')) {
                return response()->json(['status' => 'error', 'message' => 'Payment gateway not configured. Please contact support.'], 500);
            }
        }

        try {
            $couponCode = $request->get('coupon_code', $request->get('coupon', $pending['coupon'] ?? null));
            $quantity = max(1, (int)$request->get('quantity', $pending['rooms'][0]['quantity'] ?? 1));
            $nights = max(1, \Carbon\Carbon::parse($request->check_in)->diffInDays(\Carbon\Carbon::parse($request->check_out)));

            $data = array_merge($request->all(), [
                'room_id' => $id,
                'quantity' => $quantity,
                'nights' => $nights,
                'extra_services' => $request->get('extra_services', collect($pending['rooms'][0]['extraServices'] ?? [])->pluck('id')->toArray()),
                'coupon_code' => $couponCode,
                'notes' => $request->get('notes')
            ]);

            $order = $bookingService->createBooking($data);
            
            // Update room availability
            $roomService = new RoomService();
            $roomService->updateRoomBookedCount($id, $request->check_in, $request->check_out, $data['quantity']);

            session()->forget('pending_booking');

            $paymentMethod = $request->get('payment_method');

            if ($paymentMethod === 'online') {
                $intent = $paymentService->createPaymentIntent($order);
                return response()->json(['status' => 'intent', 'client_secret' => $intent['client_secret'] ?? null, 'order_id' => $order->id]);
            }

            // Cash payment
            $order->update(['payment_method' => 'cash', 'payment_status' => 'pending']);

            return response()->json(['status' => 'success', 'redirect' => route('rooms.booking.complete', $order->id)]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
