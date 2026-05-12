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
            
            $calc = $this->priceService->calculate(
                $room, 
                $data['quantity'], 
                $data['nights'], 
                $data['extra_services'] ?? [], 
                $data['coupon_code'] ?? null
            );

            // Create the order
            $order = RoomOrder::create([
                'tenant_id' => $room->tenant_id,
                'hotel_id' => $room->hotel_id,
                'room_id' => $room->id,
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'status' => 'pending',
                'start_date' => $data['check_in'],
                'end_date' => $data['check_out'],
                'total_person' => ($data['adults'] ?? 1) + ($data['children'] ?? 0),
                'total_nights' => $data['nights'],
                'customer_name' => $data['customer_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'total_amount' => $calc['total_payable'],
                'payment_status' => 'pending',
                'extra_info' => json_encode([
                    'room_qty' => $data['quantity'],
                    'base_price' => $calc['base_price'],
                    'discounted_price' => $calc['discounted_price'],
                    'coupon_discount' => $calc['coupon_discount'],
                    'extra_services' => $data['extra_services'] ?? []
                ]),
            ]);

            return $order;
        });
    }
}
