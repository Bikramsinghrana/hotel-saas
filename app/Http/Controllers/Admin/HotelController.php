<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('tenant_id', tenant()->id)
            ->with(['media'])
            ->latest()
            ->paginate(10);

        return view('hotel.admin.management.index', compact('hotels'));
    }

    public function destroy(Hotel $hotel)
    {
        // Simple delete for now
        $hotel->delete();
        return back()->with('success', 'Hotel deleted successfully.');
    }
}
