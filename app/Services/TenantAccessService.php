<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\Theme;
use App\Models\SubTheme;
use App\Models\Feature;
use Illuminate\Support\Collection;

class TenantAccessService
{
    /**
     * Check if a tenant has access to a specific feature key.
     * Order of precedence:
     * 1. Super Admin Custom Tenant Override (true/false)
     * 2. Active Subscription Plan features
     */
    public function canAccessFeature(?Tenant $tenant, string $featureKey): bool
    {
        if (!$tenant) {
            return false;
        }

        // 1. Check Super Admin Override
        $override = $tenant->featureOverrides()
            ->whereHas('feature', fn($q) => $q->where('key', $featureKey))
            ->first();

        if ($override !== null) {
            return (bool) $override->is_enabled;
        }

        // 2. Check Active Subscription Plan
        $activeSubscription = $tenant->activeSubscription;
        if (!$activeSubscription || !$activeSubscription->isActive()) {
            return false;
        }

        $plan = $activeSubscription->plan;
        if (!$plan) {
            return false;
        }

        return $plan->features()->where('key', $featureKey)->where('status', 'active')->exists();
    }

    /**
     * Get feature limit or quota value if defined in plan or override.
     */
    public function getFeatureLimit(?Tenant $tenant, string $featureKey): ?string
    {
        if (!$tenant) {
            return null;
        }

        // 1. Check Super Admin Override custom limit
        $override = $tenant->featureOverrides()
            ->whereHas('feature', fn($q) => $q->where('key', $featureKey))
            ->first();

        if ($override && !empty($override->custom_limit)) {
            return $override->custom_limit;
        }

        // 2. Check Active Subscription Plan pivot limit
        $activeSubscription = $tenant->activeSubscription;
        if ($activeSubscription && $activeSubscription->isActive() && $activeSubscription->plan) {
            $feature = $activeSubscription->plan->features()
                ->where('key', $featureKey)
                ->first();

            return $feature?->pivot?->limit_value;
        }

        return null;
    }

    /**
     * Check if a tenant can use a given sub-theme.
     */
    public function canAccessSubTheme(?Tenant $tenant, SubTheme|int $subTheme): bool
    {
        if (!$tenant) {
            return false;
        }

        $subThemeId = $subTheme instanceof SubTheme ? $subTheme->id : $subTheme;

        // 1. Check Super Admin Override
        $override = $tenant->subThemeAccesses()
            ->where('sub_theme_id', $subThemeId)
            ->first();

        if ($override !== null) {
            return (bool) $override->is_allowed;
        }

        // 2. Check Active Subscription Plan
        $activeSubscription = $tenant->activeSubscription;
        if (!$activeSubscription || !$activeSubscription->isActive() || !$activeSubscription->plan) {
            return false;
        }

        return $activeSubscription->plan->subThemes()->where('sub_themes.id', $subThemeId)->exists();
    }

    /**
     * Check if a tenant can use a given main theme (vertical).
     */
    public function canAccessTheme(?Tenant $tenant, Theme|int $theme): bool
    {
        if (!$tenant) {
            return false;
        }

        $themeId = $theme instanceof Theme ? $theme->id : $theme;

        // Check if any sub-theme of this theme is accessible
        $accessibleSubThemes = $this->getAccessibleSubThemes($tenant, $themeId);
        return $accessibleSubThemes->isNotEmpty();
    }

    /**
     * Get all accessible sub-themes for a tenant.
     */
    public function getAccessibleSubThemes(?Tenant $tenant, ?int $themeId = null): Collection
    {
        if (!$tenant) {
            return collect();
        }

        $activeSubscription = $tenant->activeSubscription;
        $planSubThemeIds = ($activeSubscription && $activeSubscription->isActive() && $activeSubscription->plan)
            ? $activeSubscription->plan->subThemes()->pluck('sub_themes.id')->toArray()
            : [];

        $overrides = $tenant->subThemeAccesses()->get()->keyBy('sub_theme_id');

        $query = SubTheme::query()->where('status', 'active');
        if ($themeId) {
            $query->where('theme_id', $themeId);
        }

        return $query->get()->filter(function ($st) use ($planSubThemeIds, $overrides) {
            if ($overrides->has($st->id)) {
                return (bool) $overrides->get($st->id)->is_allowed;
            }
            return in_array($st->id, $planSubThemeIds);
        })->values();
    }

    /**
     * Get all accessible main themes for a tenant.
     */
    public function getAccessibleThemes(?Tenant $tenant): Collection
    {
        if (!$tenant) {
            return collect();
        }

        $accessibleSubThemes = $this->getAccessibleSubThemes($tenant);
        $themeIds = $accessibleSubThemes->pluck('theme_id')->unique();

        return Theme::whereIn('id', $themeIds)->where('status', 'active')->get();
    }

    /**
     * Get all active feature keys enabled for a tenant.
     */
    public function getEnabledFeatures(?Tenant $tenant): Collection
    {
        if (!$tenant) {
            return collect();
        }

        $activeSubscription = $tenant->activeSubscription;
        $planFeatureKeys = ($activeSubscription && $activeSubscription->isActive() && $activeSubscription->plan)
            ? $activeSubscription->plan->features()->where('status', 'active')->pluck('key')->toArray()
            : [];

        $overrides = $tenant->featureOverrides()->with('feature')->get();

        $features = collect($planFeatureKeys);

        foreach ($overrides as $override) {
            if ($override->feature) {
                if ($override->is_enabled) {
                    $features->push($override->feature->key);
                } else {
                    $features = $features->reject(fn($k) => $k === $override->feature->key);
                }
            }
        }

        return $features->unique()->values();
    }
}
