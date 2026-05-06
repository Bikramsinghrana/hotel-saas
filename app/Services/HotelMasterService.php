<?php

namespace App\Services;

use App\Models\HotelMaster;

class HotelMasterService
{
    public function listByType(string $type)
    {
        return HotelMaster::where('tenant_id', tenant()->id)
            ->where('type', $type)
            ->latest()
            ->paginate(10);
    }

    public function create(array $data)
    {
        $data['tenant_id'] = tenant()->id;
        return HotelMaster::create($data);
    }

    public function update(HotelMaster $master, array $data)
    {
        $master->update($data);
        return $master;
    }

    public function delete(HotelMaster $master)
    {
        return $master->delete();
    }
}
