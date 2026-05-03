<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Enums\RoleEnum;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage users',
            'manage hotels',
            'manage bookings',
            'manage cms',
            'manage roles',
            'manage merchandise',
            'manage sales',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Dynamically create roles from Enum
        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value]);
        }

        // Assign basic default permissions
        $admin = Role::where('name', RoleEnum::ADMIN->value)->first();
        if ($admin) $admin->syncPermissions($permissions);

        $manager = Role::where('name', RoleEnum::MANAGER->value)->first();
        if ($manager) $manager->syncPermissions(['manage hotels','manage bookings']);

        $merchant = Role::where('name', RoleEnum::MERCHANT->value)->first();
        if ($merchant) $merchant->syncPermissions(['manage merchandise', 'manage bookings']);
        
        $seller = Role::where('name', RoleEnum::SELLER->value)->first();
        if ($seller) $seller->syncPermissions(['manage sales', 'manage bookings']);
        
        $customer = Role::where('name', RoleEnum::CUSTOMER->value)->first();
        if ($customer) $customer->syncPermissions([]);
    }
}
