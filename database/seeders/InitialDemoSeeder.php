<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Theme;
use App\Models\SubTheme;
use App\Models\Tenant;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Media;
use Illuminate\Support\Facades\Log;

class InitialDemoSeeder extends Seeder
{
    public function run()
    {
        // Themes and Subthemes
        $hotelTheme = Theme::firstOrCreate(['key' => 'hotel'], ['name' => 'Hotel & Hospitality', 'description' => 'Hotel theme']);
        $lux = SubTheme::firstOrCreate(['theme_id' => $hotelTheme->id, 'key' => 'luxury'], ['name' => 'Luxury Resort', 'type' => 'single_hotel']);
        $budget = SubTheme::firstOrCreate(['theme_id' => $hotelTheme->id, 'key' => 'budget'], ['name' => 'Budget Stay', 'type' => 'multi_hotel']);

        // Default Tenant
        $tenant = Tenant::firstOrCreate(
            ['domain' => config('app.domain') ?? 'localhost'],
            ['name' => 'Demo Hotel Resort', 'theme_id' => $hotelTheme->id, 'sub_theme_id' => $lux->id]
        );
        Log::info('InitialDemoSeeder: Created demo tenant with ID ' . $tenant->id);

        $roomType = RoomType::first();

        // Sample hotel
        $hotel = Hotel::create([
            'tenant_id' => $tenant->id,
            'name' => 'Demo Hotel Grand',
            'description' => 'A sample demo luxury hotel for listing',
            'rating' => 5,
            'status' => 'active',
            'address' => ['line1' => '123 Demo St', 'city' => 'Demo City'],
            'base_price' => 120.00,
            'discount' => 10.00,
            'tax' => 12.5,
            'facilities' => ['wifi', 'pool', 'parking'],
        ]);

        // Rooms
        $room1 = Room::create([
            'tenant_id' => $tenant->id,
            'hotel_id' => $hotel->id,
            'room_type_id' => $roomType?->id,
            'post_title' => 'Standard Deluxe Room',
            'room_slug' => 'standard-deluxe-room',
            'total_rooms' => 10,
            'price_per_day' => 120,
            'base_price' => 120,
            'status' => 'active',
        ]);

        $room2 = Room::create([
            'tenant_id' => $tenant->id,
            'hotel_id' => $hotel->id,
            'room_type_id' => $roomType?->id,
            'post_title' => 'Executive Suite',
            'room_slug' => 'executive-suite',
            'total_rooms' => 5,
            'price_per_day' => 180,
            'base_price' => 180,
            'status' => 'active',
        ]);

        // Media entries
        Media::create([
            'tenant_id' => $tenant->id,
            'hotel_id' => $hotel->id,
            'mediable_type' => Hotel::class,
            'mediable_id' => $hotel->id,
            'type' => 'thumbnail',
            'disk' => 'public',
            'path' => 'samples/hotel1-thumb.jpg'
        ]);

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
            'facilities' => ['wifi', 'breakfast'],
        ]);

        $room3 = Room::create([
            'tenant_id' => $tenant->id,
            'hotel_id' => $hotel2->id,
            'room_type_id' => $roomType?->id,
            'post_title' => 'Budget Single Room',
            'room_slug' => 'budget-single-room',
            'total_rooms' => 20,
            'price_per_day' => 50,
            'base_price' => 50,
            'status' => 'active',
        ]);
    }
}
