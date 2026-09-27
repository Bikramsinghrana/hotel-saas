<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Term;
use App\Models\Coupon;

class PriceCalculationService
{
    /**
     * Calculate total price, discounts, taxes (GST), and grand total for a booking.
     * 
     * @param Room $room
     * @param int $quantity
     * @param int $nights
     * @param array $extraServiceIds
     * @param string|null $couponCode
     * @param string|null $checkIn
     * @param string|null $bookingTime
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
                $extraTotal += $price * $quantity;
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
        $netSubtotal = max(0, $subTotal - $couponDiscount);

        // -------------------------------------------------------------
        // GST & Tax Calculation
        // -------------------------------------------------------------
        $taxEnabled = function_exists('option') ? (bool)option('tax_enabled', 1, $room->tenant_id, $room->hotel_id) : true;
        $configuredGstRate = function_exists('option') ? (float)option('gst_rate', 18, $room->tenant_id, $room->hotel_id) : 18;
        $gstRate = $configuredGstRate > 0 ? $configuredGstRate : 18;

        $cgstRate = function_exists('option') ? (float)option('cgst_rate', $gstRate / 2, $room->tenant_id, $room->hotel_id) : ($gstRate / 2);
        $sgstRate = function_exists('option') ? (float)option('sgst_rate', $gstRate / 2, $room->tenant_id, $room->hotel_id) : ($gstRate / 2);
        $igstRate = function_exists('option') ? (float)option('igst_rate', $gstRate, $room->tenant_id, $room->hotel_id) : $gstRate;
        $taxCalcType = function_exists('option') ? (string)option('tax_calculation_type', 'exclusive', $room->tenant_id, $room->hotel_id) : 'exclusive';
        $gstinNumber = function_exists('option') ? (string)option('gstin_number', '', $room->tenant_id, $room->hotel_id) : '';

        $taxAmount = 0;
        $cgstAmount = 0;
        $sgstAmount = 0;
        $igstAmount = 0;
        $totalPayable = $netSubtotal;

        if ($taxEnabled && $gstRate > 0 && $netSubtotal > 0) {
            if ($taxCalcType === 'inclusive') {
                $taxAmount = round($netSubtotal - ($netSubtotal / (1 + ($gstRate / 100))), 2);
                $cgstAmount = round($taxAmount / 2, 2);
                $sgstAmount = round($taxAmount / 2, 2);
                $igstAmount = $taxAmount;
                $totalPayable = $netSubtotal;
            } else {
                // Exclusive (Default & standard)
                $taxAmount = round($netSubtotal * ($gstRate / 100), 2);
                $cgstAmount = round($netSubtotal * ($cgstRate / 100), 2);
                $sgstAmount = round($netSubtotal * ($sgstRate / 100), 2);
                $igstAmount = round($netSubtotal * ($igstRate / 100), 2);
                $totalPayable = round($netSubtotal + $taxAmount, 2);
            }
        }

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
            'net_subtotal' => $netSubtotal,
            'coupon_code' => $couponModel ? $couponModel->code : null,
            'coupon' => $couponModel,
            'coupon_discount' => $couponDiscount,
            'coupon_error' => $couponError,
            'total_discount' => $totalDiscount,

            // GST Details
            'tax_enabled' => $taxEnabled,
            'tax_rate' => $gstRate,
            'gst_rate' => $gstRate,
            'cgst_rate' => $cgstRate,
            'sgst_rate' => $sgstRate,
            'igst_rate' => $igstRate,
            'tax_amount' => $taxAmount,
            'cgst_amount' => $cgstAmount,
            'sgst_amount' => $sgstAmount,
            'igst_amount' => $igstAmount,
            'tax_calculation_type' => $taxCalcType,
            'gstin_number' => $gstinNumber,

            'total_payable' => $totalPayable,
            'nights' => $nights,
            'quantity' => $quantity,
        ];
    }

    /**
     * Get tax configuration for a tenant/hotel.
     *
     * @param int|null $tenantId
     * @param int|null $hotelId
     * @return array
     */
    public function getTaxConfig($tenantId = null, $hotelId = null)
    {
        $taxEnabled = function_exists('option') ? (bool)option('tax_enabled', 1, $tenantId, $hotelId) : true;
        $configuredGstRate = function_exists('option') ? (float)option('gst_rate', 18, $tenantId, $hotelId) : 18;
        $gstRate = $configuredGstRate > 0 ? $configuredGstRate : 18;

        $cgstRate = function_exists('option') ? (float)option('cgst_rate', $gstRate / 2, $tenantId, $hotelId) : ($gstRate / 2);
        $sgstRate = function_exists('option') ? (float)option('sgst_rate', $gstRate / 2, $tenantId, $hotelId) : ($gstRate / 2);
        $igstRate = function_exists('option') ? (float)option('igst_rate', $gstRate, $tenantId, $hotelId) : $gstRate;
        $taxCalcType = function_exists('option') ? (string)option('tax_calculation_type', 'exclusive', $tenantId, $hotelId) : 'exclusive';
        $gstinNumber = function_exists('option') ? (string)option('gstin_number', '', $tenantId, $hotelId) : '';

        return [
            'tax_enabled' => $taxEnabled,
            'gst_rate' => $gstRate,
            'cgst_rate' => $cgstRate,
            'sgst_rate' => $sgstRate,
            'igst_rate' => $igstRate,
            'tax_calculation_type' => $taxCalcType,
            'gstin_number' => $gstinNumber,
        ];
    }
}
