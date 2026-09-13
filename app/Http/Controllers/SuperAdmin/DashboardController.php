<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Theme;
use App\Models\SubTheme;
use App\Models\Feature;
use App\Models\Plan;
use App\Models\Subscription;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_tenants'       => Tenant::count(),
            'active_subscriptions'=> Subscription::where('status', 'active')->count(),
            'total_themes'        => Theme::count(),
            'total_sub_themes'    => SubTheme::count(),
            'total_features'      => Feature::count(),
            'total_plans'         => Plan::count(),
        ];

        $recentTenants = Tenant::with(['theme', 'subTheme', 'activeSubscription.plan'])
            ->latest()
            ->take(5)
            ->get();

        return view('superadmin.dashboard', compact('stats', 'recentTenants'));
    }
}
