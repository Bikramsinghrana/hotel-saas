<?php

namespace App\Services;
use App\Models\Room;
use App\Models\Term;
use App\Models\Coupon;

class PriceCalculationService
{
    /**
     * Calculate total price for a booking.
     * 
     * @param Room $room
     * @param int $quantity
     * @param int $nights
     * @param array $extraServiceIds
     * @param string|null $couponCode
     * @return array
     */
    public function calculate(Room $room, $quantity, $nights, $extraServiceIds = [], $couponCode = null)
    {
        $basePrice = $room->price_per_day;
        
        // Apply room-level discount if available (stored as percentage in rooms.discount)
        $discountedPrice = $basePrice;
        if ($room->discount > 0) {
            $discountedPrice = $basePrice - ($basePrice * ($room->discount / 100));
        }

        $roomTotal = $discountedPrice * $quantity * $nights;

        // Calculate extra services
        $extraTotal = 0;
        if (!empty($extraServiceIds)) {
            $services = Term::whereIn('id', $extraServiceIds)->get();
            foreach ($services as $service) {
                // Assuming service price is per booking or per day? Usually per stay.
                $extraTotal += $service->price;
            }
        }

        $subTotal = $roomTotal + $extraTotal;

        // Apply coupon
        $couponDiscount = 0;
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->where('status', true)->first();
            if ($coupon && $coupon->isActive()) {
                if ($coupon->discount_type === 'percentage') {
                    $couponDiscount = $subTotal * ($coupon->discount_value / 100);
                } else {
                    $couponDiscount = $coupon->discount_value;
                }
            }
        }

        $totalPayable = max(0, $subTotal - $couponDiscount);

        return [
            'base_price' => $basePrice,
            'discounted_price' => $discountedPrice,
            'room_total' => $roomTotal,
            'extra_total' => $extraTotal,
            'sub_total' => $subTotal,
            'coupon_discount' => $couponDiscount,
            'total_payable' => $totalPayable,
            'nights' => $nights,
            'quantity' => $quantity
        ];
    }
}
