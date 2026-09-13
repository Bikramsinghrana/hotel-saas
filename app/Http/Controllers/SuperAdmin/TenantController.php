<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Feature;
use App\Models\SubTheme;
use App\Models\TenantFeatureOverride;
use App\Models\TenantSubThemeAccess;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with(['theme', 'subTheme', 'activeSubscription.plan'])->paginate(15);
        return view('superadmin.tenants.index', compact('tenants'));
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['theme', 'subTheme', 'activeSubscription.plan.features', 'featureOverrides.feature', 'subThemeAccesses.subTheme']);
        $features = Feature::where('status', 'active')->get();
        $subThemes = SubTheme::where('status', 'active')->get();

        return view('superadmin.tenants.show', compact('tenant', 'features', 'subThemes'));
    }

    public function toggleFeatureOverride(Request $request, Tenant $tenant)
    {
        $request->validate([
            'feature_id' => 'required|exists:features,id',
            'is_enabled' => 'required|boolean',
        ]);

        TenantFeatureOverride::updateOrCreate(
            ['tenant_id' => $tenant->id, 'feature_id' => $request->feature_id],
            ['is_enabled' => $request->is_enabled]
        );

        return back()->with('success', 'Feature override updated successfully.');
    }

    public function toggleSubThemeOverride(Request $request, Tenant $tenant)
    {
        $request->validate([
            'sub_theme_id' => 'required|exists:sub_themes,id',
            'is_allowed'   => 'required|boolean',
        ]);

        TenantSubThemeAccess::updateOrCreate(
            ['tenant_id' => $tenant->id, 'sub_theme_id' => $request->sub_theme_id],
            ['is_allowed' => $request->is_allowed]
        );

        return back()->with('success', 'Sub-theme access override updated successfully.');
    }
}
