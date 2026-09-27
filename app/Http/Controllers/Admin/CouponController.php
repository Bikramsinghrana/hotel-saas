<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Hotel;
use Illuminate\Http\Request;
use App\Helpers\HotelPath;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'coupon');
        $coupons = Coupon::where('type', $type)
            ->where('tenant_id', tenant()->id)
            ->latest()
            ->paginate(15);

        return view('themes.hotel.admin.coupons.index', compact('coupons', 'type'));
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'coupon');
        $hotels = Hotel::where('tenant_id', tenant()->id)->get();
        return view('themes.hotel.admin.coupons.create', compact('type', 'hotels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'required|in:coupon,offer',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'expire_date' => 'nullable|date|after_or_equal:start_date',
            'hotel_id' => 'nullable|exists:hotels,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',

            // Scheduling and Validity
            'validity_type' => 'nullable|string|in:all,date,days,time,days_time,custom',
            'applicable_days' => 'nullable|array',
            'applicable_days.*' => 'in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'time_slot' => 'nullable|string|max:100',
            'min_spend' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
        ]);

        $validityType = $request->input('validity_type', 'all');
        $validated['validity_type'] = $validityType;

        // Clean values according to chosen validity type
        if ($validityType === 'all') {
            $validated['start_date'] = null;
            $validated['expire_date'] = null;
            $validated['applicable_days'] = null;
            $validated['start_time'] = null;
            $validated['end_time'] = null;
            $validated['time_slot'] = 'all_day';
        } elseif ($validityType === 'date') {
            $validated['applicable_days'] = null;
            $validated['start_time'] = null;
            $validated['end_time'] = null;
            $validated['time_slot'] = 'all_day';
        } elseif ($validityType === 'days') {
            $validated['start_date'] = null;
            $validated['expire_date'] = null;
            $validated['start_time'] = null;
            $validated['end_time'] = null;
            $validated['time_slot'] = 'all_day';
        } elseif ($validityType === 'time') {
            $validated['start_date'] = null;
            $validated['expire_date'] = null;
            $validated['applicable_days'] = null;
        } elseif ($validityType === 'days_time') {
            $validated['start_date'] = null;
            $validated['expire_date'] = null;
        }

        $validated['tenant_id'] = tenant()->id;
        $validated['status'] = $request->has('status');

        if ($request->hasFile('image')) {
            $validated['image'] = uploadImage($request->file('image'), 'coupons');
        }

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index', ['type' => $validated['type']])
            ->with('success', ucfirst($validated['type']) . ' created successfully with selected validity rule.');
    }

    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        $hotels = Hotel::where('tenant_id', tenant()->id)->get();
        $type = $coupon->type;
        return view('themes.hotel.admin.coupons.edit', compact('coupon', 'hotels', 'type'));
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'expire_date' => 'nullable|date|after_or_equal:start_date',
            'hotel_id' => 'nullable|exists:hotels,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',

            // Scheduling and Validity
            'validity_type' => 'nullable|string|in:all,date,days,time,days_time,custom',
            'applicable_days' => 'nullable|array',
            'applicable_days.*' => 'in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'time_slot' => 'nullable|string|max:100',
            'min_spend' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
        ]);

        $validityType = $request->input('validity_type', $coupon->validity_type ?? 'all');
        $validated['validity_type'] = $validityType;

        // Clean values according to chosen validity type
        if ($validityType === 'all') {
            $validated['start_date'] = null;
            $validated['expire_date'] = null;
            $validated['applicable_days'] = null;
            $validated['start_time'] = null;
            $validated['end_time'] = null;
            $validated['time_slot'] = 'all_day';
        } elseif ($validityType === 'date') {
            $validated['applicable_days'] = null;
            $validated['start_time'] = null;
            $validated['end_time'] = null;
            $validated['time_slot'] = 'all_day';
        } elseif ($validityType === 'days') {
            $validated['start_date'] = null;
            $validated['expire_date'] = null;
            $validated['start_time'] = null;
            $validated['end_time'] = null;
            $validated['time_slot'] = 'all_day';
        } elseif ($validityType === 'time') {
            $validated['start_date'] = null;
            $validated['expire_date'] = null;
            $validated['applicable_days'] = null;
        } elseif ($validityType === 'days_time') {
            $validated['start_date'] = null;
            $validated['expire_date'] = null;
        } else {
            if (!$request->has('applicable_days')) {
                $validated['applicable_days'] = null;
            }
        }

        $validated['status'] = $request->has('status');

        if ($request->hasFile('image')) {
            $validated['image'] = uploadImage($request->file('image'), 'coupons');
        }

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index', ['type' => $coupon->type])
            ->with('success', ucfirst($coupon->type) . ' updated successfully.');
    }

    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $type = $coupon->type;
        $coupon->delete();

        return redirect()->route('admin.coupons.index', ['type' => $type])
            ->with('success', ucfirst($type) . ' deleted successfully.');
    }
}
