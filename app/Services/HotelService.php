<?php

namespace App\Services;

use App\Repositories\HotelRepository;
use App\Models\Hotel;

class HotelService
{
    public function __construct(protected HotelRepository $repo)
    {
    }

    public function create(array $data): Hotel
    {
        return $this->repo->create($data);
    }

    // Add business logic: pricing, availability, etc.
}
