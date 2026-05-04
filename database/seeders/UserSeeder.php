<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $password = Hash::make('password');

        // Resolve the first tenant for linking admin/staff accounts
        $defaultTenant = Tenant::first();

        foreach (RoleEnum::cases() as $role) {
            // Customers don't need a tenant_id at the seeder level
            $tenantId = ($role->value === RoleEnum::CUSTOMER->value)
                ? null
                : ($defaultTenant?->id ?? null);

            $user = User::firstOrCreate(
                ['email' => $role->value . '@gmail.com'],
                [
                    'name'               => ucfirst($role->value) . ' User',
                    'password'           => $password,
                    'email_verified_at'  => now(),
                    'tenant_id'          => $tenantId,
                ]
            );

            // If the user already existed but has no tenant_id, backfill it now
            if (!$user->tenant_id && $tenantId) {
                $user->tenant_id = $tenantId;
                $user->save();
            }

            // Assign the role if not already assigned
            if (!$user->hasRole($role->value)) {
                $user->assignRole($role->value);
            }
        }
    }
}
