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

        // Users are seeded elsewhere; skip creating demo users here.

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
        $room1 = Room::create(['hotel_id' => $hotel->id, 'room_type' => 'Standard', 'total_rooms' => 10, 'price_per_day' => 120]);
        $room2 = Room::create(['hotel_id' => $hotel->id, 'room_type' => 'Deluxe', 'total_rooms' => 5, 'price_per_day' => 180]);

        // Media entries (paths are placeholders; add real files in storage/public/samples)
        Media::create([
            'tenant_id' => $tenant->id,
            'hotel_id' => $hotel->id,
            'mediable_type' => Hotel::class,
            'mediable_id' => $hotel->id,
            'type' => 'thumbnail',
            'disk' => 'public',
            'path' => 'samples/hotel1-thumb.jpg'
        ]);

        Media::create([
            'tenant_id' => $tenant->id,
            'hotel_id' => $hotel->id,
            'mediable_type' => Hotel::class,
            'mediable_id' => $hotel->id,
            'type' => 'gallery',
            'disk' => 'public',
            'path' => 'samples/hotel1-1.jpg'
        ]);

        // Room-level media
        Media::create([
            'tenant_id' => $tenant->id,
            'hotel_id' => $hotel->id,
            'room_id' => $room1->id,
            'mediable_type' => Room::class,
            'mediable_id' => $room1->id,
            'type' => 'thumbnail',
            'disk' => 'public',
            'path' => 'samples/room1-thumb.jpg'
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
            'facilities' => ['wifi','breakfast'],
        ]);

        $room3 = Room::create(['hotel_id' => $hotel2->id, 'room_type' => 'Standard', 'total_rooms' => 20, 'price_per_day' => 50]);

        Media::create([
            'tenant_id' => $tenant->id,
            'hotel_id' => $hotel2->id,
            'mediable_type' => Hotel::class,
            'mediable_id' => $hotel2->id,
            'type' => 'thumbnail',
            'disk' => 'public',
            'path' => 'samples/hotel2-thumb.jpg'
        ]);

        // Room-level media for hotel2
        Media::create([
            'tenant_id' => $tenant->id,
            'hotel_id' => $hotel2->id,
            'room_id' => $room3->id,
            'mediable_type' => Room::class,
            'mediable_id' => $room3->id,
            'type' => 'thumbnail',
            'disk' => 'public',
            'path' => 'samples/hotel2-room1-thumb.jpg'
        ]);
    }
}
