<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomType;

class RoomTypeSeeder extends Seeder
{
    public function run()
    {
        RoomType::insert([
            [
                'tenant_id' => 1,
                'room_type' => 'Standard Room',
                'image_url' => 'uploads/room-type/standard.jpg',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => 1,
                'room_type' => 'Couple Room',
                'image_url' => 'uploads/room-type/couple.jpg',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => 1,
                'room_type' => 'Sweet Room',
                'image_url' => 'uploads/room-type/suite.jpg',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => 1,
                'room_type' => 'Deluxe Room',
                'image_url' => 'uploads/room-type/deluxe.jpg',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
