<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FeatureController extends Controller
{
    public function index()
    {
        $features = Feature::with('theme')->orderBy('theme_id')->get();
        $themes = Theme::where('status', 'active')->get();

        return view('superadmin.features.index', compact('features', 'themes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'theme_id'    => 'nullable|exists:themes,id',
            'name'        => 'required|string|max:255',
            'key'         => 'required|string|max:255|unique:features,key',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,inactive',
        ]);

        $validated['key'] = Str::snake(Str::lower($validated['key']));

        Feature::create($validated);

        return redirect()->route('superadmin.features.index')->with('success', 'Feature created successfully.');
    }

    public function update(Request $request, Feature $feature)
    {
        $validated = $request->validate([
            'theme_id'    => 'nullable|exists:themes,id',
            'name'        => 'required|string|max:255',
            'key'         => 'required|string|max:255|unique:features,key,' . $feature->id,
            'description' => 'nullable|string',
            'status'      => 'required|in:active,inactive',
        ]);

        $validated['key'] = Str::snake(Str::lower($validated['key']));

        $feature->update($validated);

        return redirect()->route('superadmin.features.index')->with('success', 'Feature updated successfully.');
    }

    public function destroy(Feature $feature)
    {
        $feature->plans()->detach();
        $feature->overrides()->delete();
        $feature->delete();

        return redirect()->route('superadmin.features.index')->with('success', 'Feature deleted successfully.');
    }
}
