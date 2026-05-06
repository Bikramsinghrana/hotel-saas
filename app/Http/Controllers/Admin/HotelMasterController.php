<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HotelMasterRequest;
use App\Models\HotelMaster;
use App\Services\HotelMasterService;
use Illuminate\Http\Request;

class HotelMasterController extends Controller
{
    protected $service;

    public function __construct(HotelMasterService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $type = $request->input('type', 'amenity');
        $masters = $this->service->listByType($type);

        return view('hotel.admin.masters.index', compact('masters', 'type'));
    }

    public function store(HotelMasterRequest $request)
    {
        $this->service->create($request->validated());
        return back()->with('success', ucfirst(str_replace('_', ' ', $request->type)) . ' created successfully!');
    }

    public function update(HotelMasterRequest $request, HotelMaster $hotelMaster)
    {
        $this->service->update($hotelMaster, $request->validated());
        return back()->with('success', ucfirst(str_replace('_', ' ', $request->type)) . ' updated successfully!');
    }

    public function destroy(HotelMaster $hotelMaster)
    {
        $type = $hotelMaster->type;
        $this->service->delete($hotelMaster);
        return back()->with('success', ucfirst(str_replace('_', ' ', $type)) . ' deleted successfully!');
    }
}
