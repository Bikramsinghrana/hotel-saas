<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Theme;
use App\Models\SubTheme;
use App\Models\Tenant;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Media;
use App\Models\User;
use App\Models\UserDetail;

class InitialDemoSeeder extends Seeder
{
    public function run()
    {
        // Themes and Subthemes
        $hotelTheme = Theme::firstOrCreate(['key' => 'hotel'], ['name' => 'Hotel', 'description' => 'Hotel theme']);

        $lux = SubTheme::firstOrCreate(['theme_id' => $hotelTheme->id, 'key' => 'luxury'], ['name' => 'Luxury', 'type' => 'single_hotel']);
        $budget = SubTheme::firstOrCreate(['theme_id' => $hotelTheme->id, 'key' => 'budget'], ['name' => 'Budget', 'type' => 'multi_hotel']);

        // Tenant demo
        $tenant = Tenant::firstOrCreate(['domain' => 'demo.local'], ['name' => 'Demo Tenant', 'theme_id' => $hotelTheme->id, 'sub_theme_id' => $lux->id]);

        // Create demo users for tenant and assign roles
        $adminUser = User::factory()->create(["name" => "Demo Owner", "email" => "owner@demo.local", 'tenant_id' => $tenant->id]);
        $managerUser = User::factory()->create(["name" => "Demo Manager", "email" => "manager@demo.local", 'tenant_id' => $tenant->id]);
        $customerUser = User::factory()->create(["name" => "Demo Customer", "email" => "customer@demo.local", 'tenant_id' => $tenant->id]);

        // assign roles if spatie roles exist
        if (method_exists($adminUser, 'assignRole')) {
            $adminUser->assignRole('admin');
            $managerUser->assignRole('manager');
            $customerUser->assignRole('customer');
        }

        UserDetail::create(['user_id' => $adminUser->id, 'bio' => 'Demo tenant owner', 'avatar_path' => 'samples/owner.jpg']);

        // Sample hotel
        $hotel = Hotel::create([
            'tenant_id' => $tenant->id,
            'name' => 'Demo Hotel Grand',
            'description' => 'A sample demo hotel for listing',
            'rating' => 4,
            'status' => 'active',
            'address' => ['line1' => '123 Demo St', 'city' => 'Demo City'],
            'base_price' => 120.00,
            'discount' => 10.00,
            'tax' => 12.5,
            'facilities' => ['wifi','pool','parking'],
        ]);

        // Rooms
        Room::create(['hotel_id' => $hotel->id, 'room_type' => 'Standard', 'total_rooms' => 10, 'price_per_day' => 120]);
        Room::create(['hotel_id' => $hotel->id, 'room_type' => 'Deluxe', 'total_rooms' => 5, 'price_per_day' => 180]);

        // Media entries (paths are placeholders; add real files in storage/public/samples)
        Media::create(['mediable_type' => Hotel::class, 'mediable_id' => $hotel->id, 'type' => 'thumbnail', 'disk' => 'public', 'path' => 'samples/hotel1-thumb.jpg']);
        Media::create(['mediable_type' => Hotel::class, 'mediable_id' => $hotel->id, 'type' => 'gallery', 'disk' => 'public', 'path' => 'samples/hotel1-1.jpg']);

        // Another sample hotel for multi_hotel listing
        $hotel2 = Hotel::create([
            'tenant_id' => $tenant->id,
            'name' => 'Budget Stay Demo',
            'description' => 'Budget-friendly demo hotel',
            'rating' => 3,
            'status' => 'active',
            'address' => ['line1' => '45 Cheap Rd', 'city' => 'Demo City'],
            'base_price' => 50.00,
            'discount' => 5.00,
            'tax' => 8.0,
            'facilities' => ['wifi','breakfast'],
        ]);

        Room::create(['hotel_id' => $hotel2->id, 'room_type' => 'Standard', 'total_rooms' => 20, 'price_per_day' => 50]);
        Media::create(['mediable_type' => Hotel::class, 'mediable_id' => $hotel2->id, 'type' => 'thumbnail', 'disk' => 'public', 'path' => 'samples/hotel2-thumb.jpg']);
    }
}
