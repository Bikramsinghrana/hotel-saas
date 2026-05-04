<?php

namespace Database\Seeders;

use App\Models\Navigation;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Enums\ModuleStatusEnum;

class NavigationSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $tenants = [Tenant::create(['name' => 'Default Hotel', 'domain' => 'localhost'])];
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
