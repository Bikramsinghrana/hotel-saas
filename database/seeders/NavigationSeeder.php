<?php

namespace Database\Seeders;

use App\Models\Navigation;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NavigationSeeder extends Seeder
{
    public function run(): void
    {
        // Get all tenants (for multi-tenant support)
        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            // If no tenants exist, create a default one
            $tenants = [Tenant::create(['name' => 'Default Hotel', 'domain' => 'localhost'])];
        }

        // Default navigation items for all merchants
        $defaultNavigations = [
            [
                'title' => 'Home',
                'content' => 'Welcome to our hotel',
                'url' => '/',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'About',
                'content' => 'Learn more about us',
                'url' => '/about',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Rooms',
                'content' => 'Browse our rooms',
                'url' => '/rooms',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        // Insert default navigations for each tenant
        foreach ($tenants as $tenant) {
            foreach ($defaultNavigations as $nav) {
                Navigation::firstOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'title' => $nav['title'],
                    ],
                    [
                        'content' => $nav['content'],
                        'url' => $nav['url'],
                        'order' => $nav['order'],
                        'is_active' => $nav['is_active'],
                    ]
                );
            }
        }
    }
}
