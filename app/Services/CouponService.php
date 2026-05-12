<?php

namespace App\Services;

use App\Models\Coupon;
use Carbon\Carbon;

class CouponService
{
    /**
     * Validate a coupon code.
     * 
     * @param string $code
     * @param int|null $hotelId
     * @param int|null $tenantId
     * @return array
     */
    public function validate($code, $hotelId = null, $tenantId = null)
    {
        $query = Coupon::where('code', $code)
            ->where('status', true);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if ($hotelId) {
            $query->where(function($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId)->orWhereNull('hotel_id');
            });
        }

        $coupon = $query->first();

        if (!$coupon) {
            return ['success' => false, 'message' => 'Invalid coupon code.'];
        }

        if (!$coupon->isActive()) {
            return ['success' => false, 'message' => 'This coupon has expired or is not yet active.'];
        }

        return ['success' => true, 'coupon' => $coupon];
    }
}
