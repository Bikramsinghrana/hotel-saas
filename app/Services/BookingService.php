<?php

namespace App\Services;

use App\Models\Room;
use App\Models\RoomOrder;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;
use Exception;

class BookingService
{
    protected $priceService;
    protected $couponService;

    public function __construct(PriceCalculationService $priceService, CouponService $couponService)
    {
        $this->priceService = $priceService;
        $this->couponService = $couponService;
    }

    /**
     * Finalize and save a booking.
     * 
     * @param array $data
     * @return RoomOrder
     */
    public function createBooking(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Recalculate everything on backend to prevent manipulation
            $room = Room::findOrFail($data['room_id']);
            $quantity = max(1, (int)($data['quantity'] ?? 1));
            $nights = max(1, (int)($data['nights'] ?? 1));
            $couponCode = !empty($data['coupon_code']) ? trim($data['coupon_code']) : (!empty($data['coupon']) ? trim($data['coupon']) : null);
            
            $calc = $this->priceService->calculate(
                $room, 
                $quantity, 
                $nights, 
                $data['extra_services'] ?? [], 
                $couponCode
            );

            // Create the order
            $order = RoomOrder::create([
                'tenant_id' => $room->tenant_id,
                'hotel_id' => $room->hotel_id,
                'room_id' => $room->id,
                'user_id' => auth()->check() ? auth()->id() : ($data['user_id'] ?? null),
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'status' => 'pending',
                'start_date' => $data['check_in'],
                'end_date' => $data['check_out'],
                'total_person' => ($data['adults'] ?? 1) + ($data['children'] ?? 0),
                'total_nights' => $nights,
                'customer_name' => $data['customer_name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'notes' => $data['notes'] ?? null,
                'special_request' => $data['notes'] ?? null,
                'sub_total' => $calc['room_original_total'] + $calc['extra_total'],
                'discount_amount' => $calc['total_discount'],
                'tax_amount' => 0,
                'total_amount' => $calc['total_payable'],
                'payment_status' => 'pending',
                'payment_method' => $data['payment_method'] ?? 'online',
                'payment_response' => json_encode([
                    'room_qty' => $quantity,
                    'base_price' => $calc['base_price'],
                    'discount_percent' => $calc['discount_percent'],
                    'room_discount_amount' => $calc['room_discount_amount'],
                    'discounted_price' => $calc['discounted_price'],
                    'room_original_total' => $calc['room_original_total'],
                    'room_total' => $calc['room_total'],
                    'coupon_code' => $calc['coupon_code'],
                    'coupon_discount' => $calc['coupon_discount'],
                    'total_discount' => $calc['total_discount'],
                    'extra_services' => $calc['extra_services_list'] ?? [],
                    'is_guest' => !auth()->check(),
                ]),
            ]);

            return $order;
        });
    }
}


