<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Theme;
use App\Models\SubTheme;
use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $password = Hash::make('password');

        // Resolve Themes & SubThemes
        $hotelTheme = Theme::where('key', 'hotel')->first();
        $hotelSubTheme = SubTheme::where('theme_id', $hotelTheme?->id)->where('key', 'luxury')->first();

        $restoTheme = Theme::where('key', 'restaurant')->first();
        $restoSubTheme = SubTheme::where('theme_id', $restoTheme?->id)->where('key', 'fine_dining')->first();

        // 1. Merchant 1: Hotel Tenant (Default Domain / Localhost)
        $hotelTenant = Tenant::firstOrCreate(
            ['domain' => config('app.domain') ?? 'localhost'],
            [
                'name' => 'Grand Palace Hotel & Resort',
                'theme_id' => $hotelTheme?->id,
                'sub_theme_id' => $hotelSubTheme?->id,
                'theme_config' => [
                    'primary_color' => '#1e3a8a',
                    'secondary_color' => '#d97706',
                    'font' => 'Outfit'
                ]
            ]
        );

        // 2. Merchant 2: Restaurant Tenant
        $restoTenant = Tenant::firstOrCreate(
            ['domain' => 'resto.localhost'],
            [
                'name' => 'Urban Bistro & Fine Dining',
                'theme_id' => $restoTheme?->id,
                'sub_theme_id' => $restoSubTheme?->id,
                'theme_config' => [
                    'primary_color' => '#18181b',
                    'secondary_color' => '#eab308',
                    'font' => 'Playfair Display'
                ]
            ]
        );

        // -------------------------------------------------------------
        // Seed Users
        // -------------------------------------------------------------

        // A. Super Admin (Global Platform Scope - No Tenant)
        $superAdminUsers = [
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@gmail.com',
                'role' => RoleEnum::SUPER_ADMIN->value,
                'tenant_id' => null,
            ],
            [
                'name' => 'Super Admin (Alias)',
                'email' => 'superadmin@gmail.com',
                'role' => RoleEnum::SUPER_ADMIN->value,
                'tenant_id' => null,
            ],
        ];

        foreach ($superAdminUsers as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $password,
                    'email_verified_at' => now(),
                    'tenant_id' => $userData['tenant_id'],
                ]
            );
            $user->syncRoles([$userData['role']]);
        }

        // B. Merchant 1 Users (Hotel Tenant)
        $hotelUsers = [
            [
                'name' => 'Hotel Admin (Merchant 1)',
                'email' => 'hoteladmin@gmail.com',
                'role' => RoleEnum::ADMIN->value,
                'tenant_id' => $hotelTenant->id,
            ],
            [
                'name' => 'Hotel Merchant',
                'email' => 'merchant@gmail.com',
                'role' => RoleEnum::MERCHANT->value,
                'tenant_id' => $hotelTenant->id,
            ],
            [
                'name' => 'Hotel General Manager',
                'email' => 'hotelmanager@gmail.com',
                'role' => RoleEnum::MANAGER->value,
                'tenant_id' => $hotelTenant->id,
            ],
            [
                'name' => 'Hotel Frontdesk Staff',
                'email' => 'hotelstaff@gmail.com',
                'role' => RoleEnum::STAFF->value,
                'tenant_id' => $hotelTenant->id,
            ],
            [
                'name' => 'Hotel Seller',
                'email' => 'seller@gmail.com',
                'role' => RoleEnum::SELLER->value,
                'tenant_id' => $hotelTenant->id,
            ],
        ];

        foreach ($hotelUsers as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $password,
                    'email_verified_at' => now(),
                    'tenant_id' => $userData['tenant_id'],
                ]
            );
            $user->syncRoles([$userData['role']]);
        }

        // C. Merchant 2 Users (Restaurant Tenant)
        $restoUsers = [
            [
                'name' => 'Restaurant Admin (Merchant 2)',
                'email' => 'restoadmin@gmail.com',
                'role' => RoleEnum::ADMIN->value,
                'tenant_id' => $restoTenant->id,
            ],
            [
                'name' => 'Restaurant Merchant',
                'email' => 'restomerchant@gmail.com',
                'role' => RoleEnum::MERCHANT->value,
                'tenant_id' => $restoTenant->id,
            ],
            [
                'name' => 'Restaurant Head Manager',
                'email' => 'restomanager@gmail.com',
                'role' => RoleEnum::MANAGER->value,
                'tenant_id' => $restoTenant->id,
            ],
            [
                'name' => 'Restaurant Floor Staff',
                'email' => 'restostaff@gmail.com',
                'role' => RoleEnum::STAFF->value,
                'tenant_id' => $restoTenant->id,
            ],
        ];

        foreach ($restoUsers as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $password,
                    'email_verified_at' => now(),
                    'tenant_id' => $userData['tenant_id'],
                ]
            );
            $user->syncRoles([$userData['role']]);
        }

        // D. General Customers (Guests - No tenant binding)
        $customers = [
            [
                'name' => 'Customer User',
                'email' => 'customer@gmail.com',
                'role' => RoleEnum::CUSTOMER->value,
                'tenant_id' => null,
            ],
            [
                'name' => 'Guest User',
                'email' => 'guest@gmail.com',
                'role' => RoleEnum::CUSTOMER->value,
                'tenant_id' => null,
            ],
            [
                'name' => 'Standard Test User',
                'email' => 'test@example.com',
                'role' => RoleEnum::CUSTOMER->value,
                'tenant_id' => null,
            ],
        ];

        foreach ($customers as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $password,
                    'email_verified_at' => now(),
                    'tenant_id' => $userData['tenant_id'],
                ]
            );
            $user->syncRoles([$userData['role']]);
        }
    }
}
