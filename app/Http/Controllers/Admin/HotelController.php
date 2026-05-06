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

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');
        if ($ids) {
            Hotel::whereIn('id', $ids)->delete();
            return response()->json(['success' => true, 'message' => 'Selected hotels deleted successfully!']);
        }
        return response()->json(['success' => false, 'message' => 'No hotels selected.'], 400);
    }

    public function updateStatus(Request $request, Hotel $hotel)
    {
        $request->validate(['status' => 'required|string']);
        $hotel->update(['status' => $request->status]);
        return response()->json(['success' => true, 'message' => 'Hotel status updated to ' . $request->status]);
    }
}
