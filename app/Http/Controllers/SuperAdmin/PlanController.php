<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Theme;
use App\Models\Feature;
use App\Models\SubTheme;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::with(['theme', 'features', 'subThemes', 'subscriptions'])->get();
        $themes = Theme::where('status', 'active')->get();
        $features = Feature::where('status', 'active')->get();
        $subThemes = SubTheme::where('status', 'active')->get();

        return view('superadmin.plans.index', compact('plans', 'themes', 'features', 'subThemes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'theme_id'       => 'nullable|exists:themes,id',
            'price'          => 'required|numeric|min:0',
            'billing_period' => 'required|in:monthly,yearly,lifetime',
            'trial_days'     => 'nullable|integer|min:0',
            'description'    => 'nullable|string',
            'status'         => 'required|in:active,inactive',
            'is_featured'    => 'nullable|boolean',
            'feature_ids'    => 'nullable|array',
            'feature_ids.*'  => 'exists:features,id',
            'sub_theme_ids'  => 'nullable|array',
            'sub_theme_ids.*'=> 'exists:sub_themes,id',
        ]);

        $validated['slug'] = Str::slug($request->name) . '-' . Str::random(4);
        $validated['is_featured'] = $request->has('is_featured');
        $validated['trial_days'] = $request->input('trial_days', 0);

        $plan = Plan::create($validated);

        if (!empty($validated['feature_ids'])) {
            $plan->features()->sync($validated['feature_ids']);
        }

        if (!empty($validated['sub_theme_ids'])) {
            $plan->subThemes()->sync($validated['sub_theme_ids']);
        }

        return redirect()->route('superadmin.plans.index')->with('success', 'Subscription plan created successfully.');
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'theme_id'       => 'nullable|exists:themes,id',
            'price'          => 'required|numeric|min:0',
            'billing_period' => 'required|in:monthly,yearly,lifetime',
            'trial_days'     => 'nullable|integer|min:0',
            'description'    => 'nullable|string',
            'status'         => 'required|in:active,inactive',
            'is_featured'    => 'nullable|boolean',
            'feature_ids'    => 'nullable|array',
            'feature_ids.*'  => 'exists:features,id',
            'sub_theme_ids'  => 'nullable|array',
            'sub_theme_ids.*'=> 'exists:sub_themes,id',
        ]);

        $validated['is_featured'] = $request->has('is_featured');
        $validated['trial_days'] = $request->input('trial_days', 0);

        $plan->update($validated);

        $plan->features()->sync($request->input('feature_ids', []));
        $plan->subThemes()->sync($request->input('sub_theme_ids', []));

        return redirect()->route('superadmin.plans.index')->with('success', 'Subscription plan updated successfully.');
    }

    public function destroy(Plan $plan)
    {
        if ($plan->subscriptions()->where('status', 'active')->exists()) {
            return redirect()->route('superadmin.plans.index')->with('error', 'Cannot delete a plan with active tenant subscriptions.');
        }

        $plan->features()->detach();
        $plan->subThemes()->detach();
        $plan->delete();

        return redirect()->route('superadmin.plans.index')->with('success', 'Plan deleted successfully.');
    }
}
