<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed themes first
        $this->call(ThemeSeeder::class);

        // Roles & permissions should be seeded before demo data
        if (class_exists(\Database\Seeders\RolesAndPermissionsSeeder::class)) {
            $this->call(\Database\Seeders\RolesAndPermissionsSeeder::class);
        }

        if (class_exists(\Database\Seeders\UserSeeder::class)) {
            $this->call(\Database\Seeders\UserSeeder::class);
        }

        $this->call(InitialDemoSeeder::class);

        // Seed navigation items for all tenants
        $this->call(NavigationSeeder::class);

        if (class_exists(\Database\Seeders\CmsSeeder::class)) {
            $this->call(\Database\Seeders\CmsSeeder::class);
        }
        if (class_exists(\Database\Seeders\UserSeeder::class)) {
            $this->call(\Database\Seeders\UserSeeder::class);
        }
    }
}
