<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Theme;
use App\Models\SubTheme;
use App\Models\Feature;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\Subscription;

class PlanAndSubscriptionSeeder extends Seeder
{
    public function run()
    {
        $hotelTheme = Theme::where('key', 'hotel')->first();
        $restoTheme = Theme::where('key', 'restaurant')->first();

        // 1. Hotel Starter Plan
        $hotelStarter = Plan::updateOrCreate(
            ['slug' => 'hotel-starter'],
            [
                'name' => 'Hotel Starter',
                'theme_id' => $hotelTheme?->id,
                'price' => 29.00,
                'billing_period' => 'monthly',
                'trial_days' => 14,
                'status' => 'active',
                'is_featured' => false,
                'description' => 'Ideal for budget motels and small guest houses.'
            ]
        );

        $budgetSubTheme = SubTheme::where('key', 'budget')->first();
        if ($budgetSubTheme) {
            $hotelStarter->subThemes()->sync([$budgetSubTheme->id]);
        }

        $starterFeatures = Feature::whereIn('key', [
            'room_management',
            'booking_engine',
            'room_inventory',
        ])->pluck('id');
        $hotelStarter->features()->sync($starterFeatures);


        // 2. Hotel Pro / Enterprise Plan
        $hotelPro = Plan::updateOrCreate(
            ['slug' => 'hotel-pro'],
            [
                'name' => 'Hotel Pro & Luxury',
                'theme_id' => $hotelTheme?->id,
                'price' => 89.00,
                'billing_period' => 'monthly',
                'trial_days' => 14,
                'status' => 'active',
                'is_featured' => true,
                'description' => 'Complete suite for luxury resorts, boutique hotels, and chains.'
            ]
        );

        $hotelSubThemes = SubTheme::where('theme_id', $hotelTheme?->id)->pluck('id');
        $hotelPro->subThemes()->sync($hotelSubThemes);

        $hotelProFeatures = Feature::where('theme_id', $hotelTheme?->id)
            ->orWhereNull('theme_id')
            ->pluck('id');
        $hotelPro->features()->sync($hotelProFeatures);


        // 3. Restaurant Standard Plan
        $restoStandard = Plan::updateOrCreate(
            ['slug' => 'resto-standard'],
            [
                'name' => 'Restaurant Standard',
                'theme_id' => $restoTheme?->id,
                'price' => 25.00,
                'billing_period' => 'monthly',
                'trial_days' => 14,
                'status' => 'active',
                'is_featured' => false,
                'description' => 'Great for cafes, bakeries, and fast-food takeaways.'
            ]
        );

        $restoStandardSubThemes = SubTheme::whereIn('key', ['fast_food', 'cafe_bistro'])->pluck('id');
        $restoStandard->subThemes()->sync($restoStandardSubThemes);

        $restoStandardFeatures = Feature::whereIn('key', ['menu_catalog', 'qr_ordering'])->pluck('id');
        $restoStandard->features()->sync($restoStandardFeatures);


        // 4. Restaurant Pro Plan
        $restoPro = Plan::updateOrCreate(
            ['slug' => 'resto-pro'],
            [
                'name' => 'Restaurant Premium Suite',
                'theme_id' => $restoTheme?->id,
                'price' => 79.00,
                'billing_period' => 'monthly',
                'trial_days' => 14,
                'status' => 'active',
                'is_featured' => true,
                'description' => 'Complete digital operations with Table Booking, KDS, and POS.'
            ]
        );

        $restoSubThemes = SubTheme::where('theme_id', $restoTheme?->id)->pluck('id');
        $restoPro->subThemes()->sync($restoSubThemes);

        $restoProFeatures = Feature::where('theme_id', $restoTheme?->id)
            ->orWhereNull('theme_id')
            ->pluck('id');
        $restoPro->features()->sync($restoProFeatures);


        // 5. Cross-Vertical All-in-One Plan
        $allInOne = Plan::updateOrCreate(
            ['slug' => 'all-in-one-growth'],
            [
                'name' => 'Enterprise All-Access',
                'theme_id' => null, // Cross-vertical
                'price' => 149.00,
                'billing_period' => 'monthly',
                'trial_days' => 30,
                'status' => 'active',
                'is_featured' => false,
                'description' => 'Full access to all current and future verticals, themes, and pro tools.'
            ]
        );

        $allSubThemes = SubTheme::pluck('id');
        $allInOne->subThemes()->sync($allSubThemes);

        $allFeatures = Feature::pluck('id');
        $allInOne->features()->sync($allFeatures);


        // 6. Assign tailored active subscriptions to tenants based on their vertical
        $tenants = Tenant::with('theme')->get();
        foreach ($tenants as $tenant) {
            $planId = $hotelPro->id;
            if ($tenant->theme && $tenant->theme->key === 'restaurant') {
                $planId = $restoPro->id;
            }

            Subscription::updateOrCreate(
                ['tenant_id' => $tenant->id],
                [
                    'plan_id' => $planId,
                    'status' => 'active',
                    'starts_at' => now(),
                    'ends_at' => now()->addYear(),
                    'trial_ends_at' => null,
                ]
            );
        }
    }
}
