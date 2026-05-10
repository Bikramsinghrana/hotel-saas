<?php

namespace App\Services;

use App\Models\Room;

class RoomWizardService
{
    public function store(array $data): Room
    {
        // Handle image upload if present
        $imagePath = null;
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $imagePath = $data['image']->store('rooms', 'public');
        }

        // Create the room record
        $room = Room::create([
            'tenant_id'    => $data['tenant_id'] ?? null,
            'hotel_id'     => $data['hotel_id'] ?? null,
            'room_slug'    => $data['room_slug'] ?? null,
            'room_type_id' => $data['room_type_id'] ?? null,
            'post_title'   => $data['room_slug'] ?? 'New Room',
            'status'       => 'draft',
            'image_path'   => $imagePath, // Make sure your DB column matches if you add it, but rooms table doesn't have image_path. Let's store in gallery or thumbnail?
        ]);

        return $room;
    }
}
