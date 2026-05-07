<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Helpers\HotelPath;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    public function index()
    {
        $roomTypes = RoomType::where('tenant_id', tenant()->id)->latest()->paginate(10);
        return view(HotelPath::view('admin.room_types.index'), compact('roomTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_type' => 'required|string|max:255',
        ]);

        RoomType::create([
            'tenant_id' => tenant()->id,
            'room_type' => $request->room_type,
            'status' => 'active',
        ]);

        return back()->with('success', 'Accommodation Type created successfully.');
    }

    public function update(Request $request, RoomType $roomType)
    {
        $request->validate([
            'room_type' => 'required|string|max:255',
        ]);

        $roomType->update($request->only(['room_type', 'status']));

        return back()->with('success', 'Accommodation Type updated successfully.');
    }

    public function destroy(RoomType $roomType)
    {
        $roomType->delete();
        return back()->with('success', 'Accommodation Type deleted successfully.');
    }
}
