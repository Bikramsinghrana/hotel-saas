<?php

namespace App\Helpers;

use App\Models\RoomType;
use Illuminate\Support\Facades\Cache;

class RoomHelper
{
    /**
     * Get all available room types for dropdowns.
     * Caches the result to optimize performance.
     *
     * @param int|null $tenantId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRoomTypes($tenantId = null)
    {
        // Use the tenant ID in the cache key if applicable
        $cacheKey = 'room_types_' . ($tenantId ?? 'all');

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($tenantId) {
            $query = RoomType::query();
            
            if ($tenantId) {
                $query->where('tenant_id', $tenantId);
            }

            return $query->orderBy('room_type', 'asc')->get();
        });
    }
}
