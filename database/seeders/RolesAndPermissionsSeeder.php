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
        // Clear cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define all granular permissions across modules
        $permissions = [
            // Platform Super Admin
            'manage platform',
            'manage all tenants',
            'manage themes',
            'manage features',
            'manage plans',
            'manage subscriptions',
            'override tenant access',

            // Tenant Settings & Team
            'manage tenant settings',
            'manage staff',
            'manage roles',
            'view reports',

            // Hotel Vertical Operations
            'manage hotels',
            'manage rooms',
            'manage room availability',
            'manage room bookings',
            'manage bookings',
            'checkin checkout guests',
            'manage guest profiles',

            // Restaurant Vertical Operations
            'manage restaurant',
            'manage tables',
            'manage table reservations',
            'manage digital menu',
            'manage menus',
            'manage kitchen orders',
            'manage kitchen',
            'manage pos billing',
            'manage orders',

            // CMS, Content & Marketing
            'manage cms',
            'manage navigations',
            'manage blogs',
            'manage coupons',
            'manage media',
            'manage sales',
            'manage users',

            // Billing & Payments
            'manage payments',
            'view invoices',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // 2. Ensure all roles exist from Enum
        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value]);
        }

        // 3. Assign Permissions by Role
        
        // A. Super Admin (Full Platform Access)
        $superAdmin = Role::where('name', RoleEnum::SUPER_ADMIN->value)->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions(Permission::all());
        }

        // B. Admin / Merchant (Full Tenant-Level Access)
        $tenantAdminPermissions = [
            'manage tenant settings',
            'manage staff',
            'manage roles',
            'manage users',
            'view reports',
            'manage hotels',
            'manage rooms',
            'manage room availability',
            'manage room bookings',
            'manage bookings',
            'checkin checkout guests',
            'manage guest profiles',
            'manage restaurant',
            'manage tables',
            'manage table reservations',
            'manage digital menu',
            'manage menus',
            'manage kitchen orders',
            'manage kitchen',
            'manage pos billing',
            'manage orders',
            'manage cms',
            'manage navigations',
            'manage blogs',
            'manage coupons',
            'manage media',
            'manage sales',
            'manage payments',
            'view invoices',
        ];

        $admin = Role::where('name', RoleEnum::ADMIN->value)->first();
        if ($admin) {
            $admin->syncPermissions($tenantAdminPermissions);
        }

        $merchant = Role::where('name', RoleEnum::MERCHANT->value)->first();
        if ($merchant) {
            $merchant->syncPermissions($tenantAdminPermissions);
        }

        // C. Manager (Operational Supervision)
        $managerPermissions = [
            'manage hotels',
            'manage rooms',
            'manage room availability',
            'manage room bookings',
            'manage bookings',
            'checkin checkout guests',
            'manage guest profiles',
            'manage restaurant',
            'manage tables',
            'manage table reservations',
            'manage digital menu',
            'manage menus',
            'manage kitchen orders',
            'manage kitchen',
            'manage pos billing',
            'manage orders',
            'manage coupons',
            'manage media',
            'manage sales',
            'view reports',
            'view invoices',
        ];

        $manager = Role::where('name', RoleEnum::MANAGER->value)->first();
        if ($manager) {
            $manager->syncPermissions($managerPermissions);
        }

        // D. Staff / Receptionist / Waiter (Daily Front Desk & Floor Operations)
        $staffPermissions = [
            'manage room bookings',
            'checkin checkout guests',
            'manage guest profiles',
            'manage table reservations',
            'manage kitchen orders',
            'manage pos billing',
            'view invoices',
        ];

        $staff = Role::where('name', RoleEnum::STAFF->value)->first();
        if ($staff) {
            $staff->syncPermissions($staffPermissions);
        }

        $seller = Role::where('name', RoleEnum::SELLER->value)->first();
        if ($seller) {
            $seller->syncPermissions($staffPermissions);
        }

        // E. Customer (Guest Facing - No Admin backend perms)
        $customer = Role::where('name', RoleEnum::CUSTOMER->value)->first();
        if ($customer) {
            $customer->syncPermissions([]);
        }
    }
}
