<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $tenant = tenant();

        // If no industry (main theme) is selected, force the admin to setup first
        if (!$tenant || !$tenant->theme_id) {
            return redirect()->route('admin.settings.index')
                ->with('setup_info', 'Welcome! Please select your primary industry to unlock your dashboard.');
        }

        // If no sub-theme (layout) is selected yet, still allow dashboard but hint them
        $hotels = Hotel::count();
        $bookings = Booking::count();
        $users = User::count();
        // Set layout according to path (admin views use layouts.admin)
        $layout = 'layouts.admin';

        return view('admin.dashboard', compact('hotels', 'bookings', 'users', 'layout'));
    }
}
