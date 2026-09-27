<?php

namespace App\Services;

use App\Models\Option;
use Illuminate\Support\Facades\Cache;

class OptionService
{
    /**
     * Cache TTL in seconds (24 hours by default, invalidated on write)
     */
    protected const CACHE_TTL = 86400;

    /**
     * Get an option value with hierarchical fallback:
     * 1. Hotel specific override (if hotel_id provided)
     * 2. Tenant specific setting (if tenant_id provided or session active)
     * 3. Global platform default (tenant_id = null, hotel_id = null)
     * 4. Passed $default value
     *
     * @param string $key
     * @param mixed $default
     * @param int|null $tenantId
     * @param int|null $hotelId
     * @return mixed
     */
    public function get(string $key, $default = null, $tenantId = null, $hotelId = null)
    {
        $tenantId = $tenantId ?? (function_exists('tenant') && tenant() ? tenant()->id : session('tenant_id'));

        $cacheKey = $this->getCacheKey($tenantId, $hotelId);
        $options = $this->loadAutoloadOptions($tenantId, $hotelId, $cacheKey);

        if (isset($options[$key])) {
            return $options[$key];
        }

        // If not in autoload cache, check database directly
        $query = Option::active()->where('key', $key);
        
        // 1. Check hotel specific override
        if ($hotelId) {
            $hotelOption = (clone $query)->where('hotel_id', $hotelId)->first();
            if ($hotelOption) {
                return $hotelOption->typed_value;
            }
        }

        // 2. Check tenant specific setting
        if ($tenantId) {
            $tenantOption = (clone $query)->where('tenant_id', $tenantId)->whereNull('hotel_id')->first();
            if ($tenantOption) {
                return $tenantOption->typed_value;
            }
        }

        // 3. Check global system default
        $globalOption = (clone $query)->whereNull('tenant_id')->whereNull('hotel_id')->first();
        if ($globalOption) {
            return $globalOption->typed_value;
        }

        return $default;
    }

    /**
     * Set or update an option
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @param string|null $type
     * @param int|null $tenantId
     * @param int|null $hotelId
     * @param array $extraAttributes
     * @return Option
     */
    public function set(string $key, $value, string $group = 'general', $type = null, $tenantId = null, $hotelId = null, array $extraAttributes = [])
    {
        $tenantId = $tenantId ?? (function_exists('tenant') && tenant() ? tenant()->id : session('tenant_id'));

        if (!$type) {
            $type = $this->inferType($value);
        }

        $preparedValue = Option::prepareValue($value, $type);

        $option = Option::updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'hotel_id' => $hotelId,
                'key' => $key,
            ],
            array_merge([
                'group' => $group,
                'value' => $preparedValue,
                'type' => $type,
                'status' => true,
            ], $extraAttributes)
        );

        $this->clearCache($tenantId, $hotelId);

        return $option;
    }

    /**
     * Get all options belonging to a specific group (e.g. 'tax_gst', 'pagination', 'booking')
     *
     * @param string $group
     * @param int|null $tenantId
     * @param int|null $hotelId
     * @return array
     */
    public function getGroup(string $group, $tenantId = null, $hotelId = null): array
    {
        $tenantId = $tenantId ?? (function_exists('tenant') && tenant() ? tenant()->id : session('tenant_id'));

        // Load global defaults for this group first
        $globals = Option::active()
            ->whereNull('tenant_id')
            ->whereNull('hotel_id')
            ->where('group', $group)
            ->get()
            ->keyBy('key');

        $result = [];
        foreach ($globals as $opt) {
            $result[$opt->key] = $opt->typed_value;
        }

        // Overlay tenant specific settings
        if ($tenantId) {
            $tenantOptions = Option::active()
                ->where('tenant_id', $tenantId)
                ->whereNull('hotel_id')
                ->where('group', $group)
                ->get();

            foreach ($tenantOptions as $opt) {
                $result[$opt->key] = $opt->typed_value;
            }
        }

        // Overlay hotel specific overrides
        if ($hotelId) {
            $hotelOptions = Option::active()
                ->where('hotel_id', $hotelId)
                ->where('group', $group)
                ->get();

            foreach ($hotelOptions as $opt) {
                $result[$opt->key] = $opt->typed_value;
            }
        }

        return $result;
    }

    /**
     * Get all public options (e.g. for frontend scripts, calculators, currency displays)
     */
    public function getPublicOptions($tenantId = null, $hotelId = null): array
    {
        $tenantId = $tenantId ?? (function_exists('tenant') && tenant() ? tenant()->id : session('tenant_id'));

        $publicOptions = Option::active()
            ->where('is_public', true)
            ->where(function($q) use ($tenantId, $hotelId) {
                $q->whereNull('tenant_id')->whereNull('hotel_id');
                if ($tenantId) {
                    $q->orWhere(fn($sq) => $sq->where('tenant_id', $tenantId)->whereNull('hotel_id'));
                }
                if ($hotelId) {
                    $q->orWhere(fn($sq) => $sq->where('hotel_id', $hotelId));
                }
            })
            ->get();

        $result = [];
        foreach ($publicOptions as $opt) {
            $result[$opt->key] = $opt->typed_value;
        }

        return $result;
    }

    /**
     * Invalidate cached options
     */
    public function clearCache($tenantId = null, $hotelId = null): void
    {
        $cacheKey = $this->getCacheKey($tenantId, $hotelId);
        Cache::forget($cacheKey);
        Cache::forget('options_autoload_global');
    }

    /**
     * Load and cache all active autoload options
     */
    protected function loadAutoloadOptions($tenantId, $hotelId, string $cacheKey): array
    {
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($tenantId, $hotelId) {
            // 1. Fetch global autoload options
            $globalOptions = Option::active()
                ->autoload()
                ->whereNull('tenant_id')
                ->whereNull('hotel_id')
                ->get();

            $merged = [];
            foreach ($globalOptions as $opt) {
                $merged[$opt->key] = $opt->typed_value;
            }

            // 2. Fetch tenant autoload options
            if ($tenantId) {
                $tenantOptions = Option::active()
                    ->autoload()
                    ->where('tenant_id', $tenantId)
                    ->whereNull('hotel_id')
                    ->get();

                foreach ($tenantOptions as $opt) {
                    $merged[$opt->key] = $opt->typed_value;
                }
            }

            // 3. Fetch hotel autoload options
            if ($hotelId) {
                $hotelOptions = Option::active()
                    ->autoload()
                    ->where('hotel_id', $hotelId)
                    ->get();

                foreach ($hotelOptions as $opt) {
                    $merged[$opt->key] = $opt->typed_value;
                }
            }

            return $merged;
        });
    }

    protected function getCacheKey($tenantId, $hotelId): string
    {
        return 'options_autoload_' . ($tenantId ?? 'global') . '_' . ($hotelId ?? 'all');
    }

    protected function inferType($value): string
    {
        if (is_bool($value)) return 'boolean';
        if (is_int($value)) return 'number';
        if (is_float($value)) return 'float';
        if (is_array($value)) return 'json';
        return 'string';
    }
}
