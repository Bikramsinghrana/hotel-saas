<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Theme;
use App\Models\Navigation;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $tenant = null;
        $theme = null;
        $rooms = collect();
        $navigations = collect();
        $useDummy = false;

        // Resolve tenant from session or hostname
        if (class_exists(Tenant::class)) {
            try {
                $host = $request->getHost();
                $tenant = Tenant::where('domain', $host)->first();
                if ($tenant) {
                    session(['tenant_id' => $tenant->id]);
                } elseif (session()->has('tenant_id')) {
                    $tenant = Tenant::find(session('tenant_id'));
                }
            } catch (\Throwable $e) {
                $tenant = null;
            }
        }

        // Load theme if available
        if ($tenant && class_exists(Theme::class)) {
            try {
                $theme = $tenant->theme_id ? Theme::find($tenant->theme_id) : null;
            } catch (\Throwable $e) {
                $theme = null;
            }
        }

        // Load navigations
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
            $roomService = new \App\Services\RoomService();
            $rooms = $roomService->searchAvailableRooms($request->all(), $tenant ? $tenant->id : null);
        }

        // Fallback to dummy data if DB is empty
        if ($rooms->isEmpty()) {
            $useDummy = true;
            $dummyData = [
                (object)['id' => 1, 'room_slug' => 'deluxe-suite', 'room_type' => 'Deluxe Suite', 'price_per_day' => 199.00, 'total_rooms' => 5, 'max_adults' => 2, 'max_children' => 1, 'status' => 'active', 'gallery' => ['image_path' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800&h=600&fit=crop'], 'facilities' => ['Ocean View', 'Free WiFi', 'Mini Bar']],
                (object)['id' => 2, 'room_slug' => 'presidential-suite', 'room_type' => 'Presidential Suite', 'price_per_day' => 499.00, 'total_rooms' => 2, 'max_adults' => 4, 'max_children' => 2, 'status' => 'active', 'gallery' => ['image_path' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800&h=600&fit=crop'], 'facilities' => ['Private Pool', 'Butler Service', 'Lounge Access']],
                (object)['id' => 3, 'room_slug' => 'standard-room', 'room_type' => 'Standard Room', 'price_per_day' => 99.00, 'total_rooms' => 20, 'max_adults' => 2, 'max_children' => 0, 'status' => 'active', 'gallery' => ['image_path' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=800&h=600&fit=crop'], 'facilities' => ['City View', 'Free WiFi', 'TV']],
                (object)['id' => 4, 'room_slug' => 'family-suite', 'room_type' => 'Family Suite', 'price_per_day' => 249.00, 'total_rooms' => 10, 'max_adults' => 4, 'max_children' => 3, 'status' => 'active', 'gallery' => ['image_path' => 'https://images.unsplash.com/photo-1591088398332-8a7791972843?w=800&h=600&fit=crop'], 'facilities' => ['Connecting Rooms', 'Kitchenette', 'Game Console']],
            ];
            
            // Simple dummy filtering
            if ($request->filled('room_type_id')) {
                $id = (int)$request->room_type_id;
                $dummyData = array_filter($dummyData, fn($r) => $r->id === $id);
            }
            if ($request->filled('max_price')) {
                $dummyData = array_filter($dummyData, fn($r) => $r->price_per_day <= $request->max_price);
            }
            
            // Re-index array after filter
            $rooms = collect(array_values($dummyData));
        }

        return view('rooms.index', compact('rooms', 'tenant', 'theme', 'navigations', 'useDummy'));
    }

    public function checkout(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        
        $checkIn = $request->get('check_in', now()->format('Y-m-d'));
        $checkOut = $request->get('check_out', now()->addDay()->format('Y-m-d'));
        $totalRooms = $request->get('total_rooms', 1);
        $adults = $request->get('adults', 1);
        $children = $request->get('children', 0);

        $checkInDate = \Carbon\Carbon::parse($checkIn);
        $checkOutDate = \Carbon\Carbon::parse($checkOut);
        $nights = max(1, $checkInDate->diffInDays($checkOutDate));

        $totalPrice = $nights * $room->price_per_day * $totalRooms;

        return view('rooms.checkout', compact(
            'room', 'checkIn', 'checkOut', 'nights', 'totalRooms', 'adults', 'children', 'totalPrice'
        ));
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

        $room = Room::findOrFail($id);
        
        $checkInDate = \Carbon\Carbon::parse($request->check_in);
        $checkOutDate = \Carbon\Carbon::parse($request->check_out);
        $nights = max(1, $checkInDate->diffInDays($checkOutDate));
        $totalRooms = $request->get('total_rooms', 1);
        $totalPrice = $nights * $room->price_per_day * $totalRooms;

        $order = \App\Models\RoomOrder::create([
            'tenant_id' => $room->tenant_id,
            'hotel_id' => $room->hotel_id,
            'room_id' => $room->id,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'status' => 'pending',
            'start_date' => $request->check_in,
            'end_date' => $request->check_out,
            'total_person' => $request->get('adults', 1) + $request->get('children', 0),
            'total_nights' => $nights,
            'customer_name' => $request->customer_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'total_amount' => $totalPrice,
            'payment_status' => 'pending',
        ]);

        // Update room_availabilities booked count
        $roomService = new \App\Services\RoomService();
        $roomService->updateRoomBookedCount($room->id, $request->check_in, $request->check_out, $totalRooms);

        return redirect()->route('rooms.index')->with('success', 'Booking created successfully! Order #: ' . $order->order_number);
    }
}
