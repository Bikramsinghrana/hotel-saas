<?php

namespace App\Services;

use App\Models\Coupon;
use Carbon\Carbon;

class CouponService
{
    /**
     * Validate a coupon code or instance with date, days-of-week, time-slot, min-spend, and limit verification.
     * 
     * @param string|Coupon $couponOrCode
     * @param int|null $hotelId
     * @param int|null $tenantId
     * @param string|Carbon|null $date (check-in or booking date)
     * @param string|Carbon|null $time (booking time)
     * @param float $amount (order or room total for min_spend validation)
     * @return array
     */
    public function validate($couponOrCode, $hotelId = null, $tenantId = null, $date = null, $time = null, $amount = 0)
    {
        if ($couponOrCode instanceof Coupon) {
            $coupon = $couponOrCode;
        } else {
            $code = trim((string)$couponOrCode);
            if (empty($code) || $code === '__NONE__' || strtolower($code) === 'none') {
                return ['success' => false, 'message' => 'Please enter a valid coupon code.'];
            }

            $query = Coupon::where('code', $code)
                ->where('status', true);

            if ($tenantId) {
                $query->where(function($q) use ($tenantId) {
                    $q->where('tenant_id', $tenantId)->orWhereNull('tenant_id');
                });
            }

            if ($hotelId) {
                $query->where(function($q) use ($hotelId) {
                    $q->where('hotel_id', $hotelId)->orWhereNull('hotel_id');
                });
            }

            $coupon = $query->first();
        }

        if (!$coupon || !$coupon->status) {
            return ['success' => false, 'message' => 'Invalid or inactive coupon code.'];
        }

        $dateObj = $date ? Carbon::parse($date) : now();

        // 1. Date range check
        if ($coupon->start_date && $coupon->start_date->startOfDay()->gt($dateObj->startOfDay())) {
            return ['success' => false, 'message' => "This coupon is valid from " . $coupon->start_date->format('d M Y') . "."];
        }
        if ($coupon->expire_date && $coupon->expire_date->endOfDay()->lt($dateObj->startOfDay())) {
            return ['success' => false, 'message' => "This coupon expired on " . $coupon->expire_date->format('d M Y') . "."];
        }

        // 2. Day-wise validation
        if (!empty($coupon->applicable_days) && is_array($coupon->applicable_days) && count($coupon->applicable_days) > 0 && count($coupon->applicable_days) < 7) {
            $currentDay = strtolower($dateObj->format('l'));
            $allowedDays = array_map('strtolower', $coupon->applicable_days);
            if (!in_array($currentDay, $allowedDays)) {
                $daysFormatted = implode(', ', array_map('ucfirst', $coupon->applicable_days));
                return [
                    'success' => false, 
                    'message' => "This coupon is only valid on selected days: {$daysFormatted} (Current/Check-in day: " . ucfirst($currentDay) . ")."
                ];
            }
        }

        // 3. Time slot validation
        if (!empty($coupon->start_time) && !empty($coupon->end_time)) {
            $timeStr = $time ? Carbon::parse($time)->format('H:i:s') : now()->format('H:i:s');
            $startTimeStr = Carbon::parse($coupon->start_time)->format('H:i:s');
            $endTimeStr = Carbon::parse($coupon->end_time)->format('H:i:s');

            if ($timeStr < $startTimeStr || $timeStr > $endTimeStr) {
                $timeWindow = Carbon::parse($coupon->start_time)->format('h:i A') . ' to ' . Carbon::parse($coupon->end_time)->format('h:i A');
                return [
                    'success' => false, 
                    'message' => "This coupon is only valid between {$timeWindow}."
                ];
            }
        }

        // 4. Minimum spend validation
        if (!empty($coupon->min_spend) && (float)$coupon->min_spend > 0 && (float)$amount > 0) {
            if ((float)$amount < (float)$coupon->min_spend) {
                $minSpendFormatted = \App\Helpers\CurrencyHelper::format($coupon->min_spend);
                return [
                    'success' => false,
                    'message' => "This coupon requires a minimum spend of {$minSpendFormatted}."
                ];
            }
        }

        // 5. Usage limit validation
        if (!is_null($coupon->usage_limit) && $coupon->usage_limit > 0 && $coupon->used_count >= $coupon->usage_limit) {
            return ['success' => false, 'message' => 'This coupon has reached its maximum usage limit.'];
        }

        return ['success' => true, 'coupon' => $coupon];
    }
}
