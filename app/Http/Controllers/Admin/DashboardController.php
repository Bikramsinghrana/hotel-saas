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
        $hotels = Hotel::count();
        $bookings = Booking::count();
        $users = User::count();

        return view('admin.dashboard', compact('hotels','bookings','users'));
    }
}
