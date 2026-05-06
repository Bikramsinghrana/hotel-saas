<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $hotelId = $request->get('hotel_id');
        $hotel = Hotel::findOrFail($hotelId);
        
        $rooms = Room::where('hotel_id', $hotelId)
            ->latest()
            ->paginate(10);

        return view('hotel.admin.room.index', compact('rooms', 'hotel', 'hotelId'));
    }

    public function store(Request $request)
    {
        $hotelId = $request->input('hotel_id');
        
        Room::create([
            'hotel_id' => $hotelId,
            'room_type' => 'Room ' . strtoupper(Str::random(5)), // Random room "name"
            'total_rooms' => 1,
            'price_per_day' => 100,
            'status' => 'active',
        ]);

        return back()->with('success', 'Room generated successfully!');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return back()->with('success', 'Room deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');
        if ($ids) {
            Room::whereIn('id', $ids)->delete();
            return response()->json(['success' => true, 'message' => 'Selected rooms deleted.']);
        }
        return response()->json(['success' => false], 400);
    }

    public function updateStatus(Request $request, Room $room)
    {
        $request->validate(['status' => 'required|string']);
        $room->update(['status' => $request->status]);
        return response()->json(['success' => true, 'message' => 'Room status updated!']);
    }
}
