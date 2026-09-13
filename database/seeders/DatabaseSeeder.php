<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Themes, Sub-Themes & Feature Catalog
        $this->call(ThemeSeeder::class);

        // 2. Roles & Permissions (Spatie matrix)
        if (class_exists(\Database\Seeders\RolesAndPermissionsSeeder::class)) {
            $this->call(\Database\Seeders\RolesAndPermissionsSeeder::class);
        }

        // 3. Seed Tenants and Users (Super Admin, 2 Merchant Admins, Managers, Staff, Customers)
        if (class_exists(\Database\Seeders\UserSeeder::class)) {
            $this->call(\Database\Seeders\UserSeeder::class);
        }

        // 4. Navigation items for tenants
        $this->call(NavigationSeeder::class);

        // 5. Subscription Plans & Tailored Merchant Subscriptions
        $this->call(PlanAndSubscriptionSeeder::class);

        // 6. CMS Pages & Blocks
        if (class_exists(\Database\Seeders\CmsSeeder::class)) {
            $this->call(\Database\Seeders\CmsSeeder::class);
        }

        // 7. Room Types & Terms
        if (class_exists(\Database\Seeders\RoomTypeSeeder::class)) {
            $this->call(RoomTypeSeeder::class);
        }

        // 8. Demo Hotels, Rooms & Media
        if (class_exists(\Database\Seeders\InitialDemoSeeder::class)) {
            $this->call(InitialDemoSeeder::class);
        }
    }
}
