<?php

namespace App\Repositories;

use App\Models\Hotel;
use App\Models\Media;
use Illuminate\Support\Facades\DB;

class HotelRepository
{
    public function find($id)
    {
        return Hotel::with('media')->find($id);
    }

    public function createBaseHotel(array $data)
    {
        return Hotel::create($data);
    }

    public function updateBaseHotel(Hotel $hotel, array $data)
    {
        $hotel->update($data);
        return $hotel;
    }

    /**
     * Add media using the polymorphic relationship
     */
    public function addMedia(Hotel $hotel, array $data)
    {
        return $hotel->media()->create($data);
    }

    public function deleteMedia($mediaId)
    {
        $media = Media::find($mediaId);
        if ($media) {
            if (file_exists(base_path($media->path))) {
                unlink(base_path($media->path));
            }
            $media->delete();
        }
    }

    public function createRoom(Hotel $hotel, array $data)
    {
        return $hotel->rooms()->create($data);
    }
}
