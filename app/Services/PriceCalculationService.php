<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Term;
use App\Models\Coupon;

class PriceCalculationService
{
    /**
     * Calculate total price and discounts for a booking.
     * 
     * @param Room $room
     * @param int $quantity
     * @param int $nights
     * @param array $extraServiceIds
     * @param string|null $couponCode
     * @return array
     */
    public function calculate(Room $room, $quantity = 1, $nights = 1, $extraServiceIds = [], $couponCode = null, $checkIn = null, $bookingTime = null)
    {
        $quantity = max(1, (int)$quantity);
        $nights = max(1, (int)$nights);
        $basePrice = (float)($room->price_per_day ?: $room->base_price ?: 0);
        
        // Room-level / Festival offer discount (stored as percentage in rooms.discount)
        $discountPercent = (float)($room->discount ?? 0);
        $roomDiscountAmountPerNight = 0;
        if ($discountPercent > 0) {
            $roomDiscountAmountPerNight = $basePrice * ($discountPercent / 100);
        }
        $discountedPrice = max(0, $basePrice - $roomDiscountAmountPerNight);

        $roomOriginalTotal = $basePrice * $quantity * $nights;
        $roomDiscountTotal = $roomDiscountAmountPerNight * $quantity * $nights;
        $roomTotal = $discountedPrice * $quantity * $nights;

        // Calculate extra services
        $extraTotal = 0;
        $extraServicesList = [];
        if (!empty($extraServiceIds)) {
            $services = Term::whereIn('id', (array)$extraServiceIds)->get();
            foreach ($services as $service) {
                $price = (float)$service->price;
                $extraTotal += $price;
                $extraServicesList[] = [
                    'id' => $service->id,
                    'name' => $service->name ?? $service->title ?? 'Extra Service',
                    'price' => $price,
                ];
            }
        }

        $subTotal = $roomTotal + $extraTotal;

        // Apply coupon with day, date, time window, and min spend checking via validate_coupon helper
        $couponDiscount = 0;
        $couponModel = null;
        $couponError = null;

        $sanitizedCode = trim((string)$couponCode);
        if (!empty($sanitizedCode) && $sanitizedCode !== '__NONE__' && strtolower($sanitizedCode) !== 'none') {
            $validation = validate_coupon($sanitizedCode, $room->hotel_id, $room->tenant_id, $checkIn, $bookingTime, $subTotal);
            if ($validation['success'] && !empty($validation['coupon'])) {
                $couponModel = $validation['coupon'];
                if ($couponModel->discount_type === 'percentage') {
                    $couponDiscount = $subTotal * ($couponModel->discount_value / 100);
                } else {
                    $couponDiscount = min($subTotal, (float)$couponModel->discount_value);
                }
            } else {
                $couponError = $validation['message'] ?? 'Coupon is not valid.';
            }
        }

        $totalDiscount = $roomDiscountTotal + $couponDiscount;
        $totalPayable = max(0, $subTotal - $couponDiscount);

        return [
            'base_price' => $basePrice,
            'discount_percent' => $discountPercent,
            'room_discount_amount' => $roomDiscountTotal,
            'discounted_price' => $discountedPrice,
            'room_original_total' => $roomOriginalTotal,
            'room_total' => $roomTotal,
            'extra_total' => $extraTotal,
            'extra_services_list' => $extraServicesList,
            'sub_total' => $subTotal,
            'coupon_code' => $couponModel ? $couponModel->code : null,
            'coupon' => $couponModel,
            'coupon_discount' => $couponDiscount,
            'coupon_error' => $couponError,
            'total_discount' => $totalDiscount,
            'total_payable' => $totalPayable,
            'nights' => $nights,
            'quantity' => $quantity,
        ];
    }
}

