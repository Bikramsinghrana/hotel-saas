<?php

namespace Database\Seeders;

use App\Models\Navigation;
use App\Models\Tenant;
use App\Models\Theme;
use App\Models\SubTheme;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Enums\ModuleStatusEnum;
use Illuminate\Support\Facades\Log;

class NavigationSeeder extends Seeder
{
    public function run(): void
    {
       
        $hotelTheme = Theme::firstOrCreate(['key' => 'hotel'], ['name' => 'Hotel', 'description' => 'Hotel theme']);
        $luxury = SubTheme::firstOrCreate(['theme_id' => $hotelTheme->id, 'key' => 'luxury'], ['name' => 'Luxury', 'type' => 'single_hotel']);
        $budget = SubTheme::firstOrCreate(['theme_id' => $hotelTheme->id, 'key' => 'budget'], ['name' => 'Budget', 'type' => 'multi_hotel']);

        $tenants = Tenant::all();
        Log::info('NavigationSeeder: Found ' . $tenants->count() . ' tenants to seeder navigation');

        if ($tenants->isEmpty()) {
            //If needs insert default theme_id' => $hotelTheme->id, 'sub_theme_id' => $luxury->id
            $tenants = [Tenant::create(['name' => 'Default Hotel', 'domain' => config('app.domain'),'sub_theme_id' => $luxury->id])];
        }

        $defaultNavigations = [
            [
                'title' => 'Home',
                'slug' => 'home',
                'url' => '/',
                'description' => 'Main entry point of the website.',
                'status' => ModuleStatusEnum::PUBLISHED,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'url' => '/about',
                'description' => 'Information about our hotel and history.',
                'status' => ModuleStatusEnum::PUBLISHED,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Rooms & Suites',
                'slug' => 'rooms',
                'url' => '/rooms',
                'description' => 'Browse our luxurious room options.',
                'status' => ModuleStatusEnum::PUBLISHED,
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Contact',
                'slug' => 'contact',
                'url' => '/contact',
                'description' => 'Get in touch with us.',
                'status' => ModuleStatusEnum::PUBLISHED,
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($tenants as $tenant) {
            foreach ($defaultNavigations as $nav) {
                Navigation::updateOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'slug' => $nav['slug'],
                    ],
                    [
                        'title' => $nav['title'],
                        'url' => $nav['url'],
                        'description' => $nav['description'],
                        'status' => $nav['status'],
                        'order' => $nav['order'],
                        'is_active' => $nav['is_active'],
                    ]
                );
            }
        }
    }
}
