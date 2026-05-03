<?php

namespace App\Repositories;

use App\Models\Hotel;

class HotelRepository
{
    public function create(array $data): Hotel
    {
        return Hotel::create($data);
    }

    public function findForTenant(int $tenantId)
    {
        return Hotel::where('tenant_id', $tenantId)->get();
    }
}
