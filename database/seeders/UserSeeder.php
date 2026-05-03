<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $password = Hash::make('password'); // Default password for all seeded users

        foreach (RoleEnum::cases() as $role) {
            $user = User::firstOrCreate(
                ['email' => $role->value . '@gmail.com'],
                [
                    'name' => ucfirst($role->value) . ' User',
                    'password' => $password,
                    'email_verified_at' => now(),
                ]
            );

            // Assign the role if not already assigned
            if (!$user->hasRole($role->value)) {
                $user->assignRole($role->value);
            }
        }
    }
}
