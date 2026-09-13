<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use App\Models\SubTheme;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ThemeController extends Controller
{
    public function index()
    {
        $themes = Theme::with(['subThemes', 'features', 'tenants'])->get();
        return view('superadmin.themes.index', compact('themes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key'         => 'nullable|string|unique:themes,key',
            'name'        => 'required|string|max:255',
            'icon'        => 'nullable|string',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,inactive',
        ]);

        if (empty($validated['key'])) {
            $validated['key'] = Str::slug($validated['name'], '_');
        }

        Theme::create($validated);
        return back()->with('success', "Vertical theme '{$validated['name']}' created successfully.");
    }

    public function update(Request $request, Theme $theme)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'icon'        => 'nullable|string',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,inactive',
        ]);

        $theme->update($validated);
        return back()->with('success', "Theme '{$theme->name}' updated successfully.");
    }

    public function destroy(Theme $theme)
    {
        if ($theme->tenants()->count() > 0) {
            return back()->withErrors(['error' => "Cannot delete vertical '{$theme->name}' because {$theme->tenants()->count()} merchant tenant(s) are currently using it."]);
        }

        $themeName = $theme->name;
        $theme->delete();
        return back()->with('success', "Vertical '{$themeName}' and associated layouts removed successfully.");
    }

    public function toggleStatus(Theme $theme)
    {
        $theme->status = ($theme->status === 'active') ? 'inactive' : 'active';
        $theme->save();

        return back()->with('success', "Vertical '{$theme->name}' is now {$theme->status}.");
    }

    // -------------------------------------------------------------
    // Sub-Theme / Layout Management
    // -------------------------------------------------------------

    public function storeSubTheme(Request $request, Theme $theme)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'key'           => 'nullable|string',
            'type'          => 'required|string',
            'description'   => 'nullable|string',
            'is_premium'    => 'nullable|boolean',
            'status'        => 'required|in:active,inactive',
            'preview_image' => 'nullable|string',
        ]);

        $key = !empty($validated['key']) ? Str::slug($validated['key'], '_') : Str::slug($validated['name'], '_');

        // Ensure key is unique per theme
        if (SubTheme::where('theme_id', $theme->id)->where('key', $key)->exists()) {
            return back()->withErrors(['error' => "A sub-theme with key '{$key}' already exists for this vertical."]);
        }

        SubTheme::create([
            'theme_id'      => $theme->id,
            'key'           => $key,
            'name'          => $validated['name'],
            'type'          => $validated['type'],
            'description'   => $validated['description'] ?? null,
            'is_premium'    => $request->boolean('is_premium'),
            'status'        => $validated['status'],
            'preview_image' => $validated['preview_image'] ?? null,
        ]);

        return back()->with('success', "Layout '{$validated['name']}' added to vertical '{$theme->name}'.");
    }

    public function updateSubTheme(Request $request, SubTheme $subTheme)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'type'          => 'required|string',
            'description'   => 'nullable|string',
            'is_premium'    => 'nullable|boolean',
            'status'        => 'required|in:active,inactive',
            'preview_image' => 'nullable|string',
        ]);

        $subTheme->update([
            'name'          => $validated['name'],
            'type'          => $validated['type'],
            'description'   => $validated['description'] ?? null,
            'is_premium'    => $request->boolean('is_premium'),
            'status'        => $validated['status'],
            'preview_image' => $validated['preview_image'] ?? null,
        ]);

        return back()->with('success', "Layout '{$subTheme->name}' updated successfully.");
    }

    public function destroySubTheme(SubTheme $subTheme)
    {
        if ($subTheme->tenants()->count() > 0) {
            return back()->withErrors(['error' => "Cannot delete layout '{$subTheme->name}' because {$subTheme->tenants()->count()} merchant tenant(s) are using it."]);
        }

        $subThemeName = $subTheme->name;
        $subTheme->delete();
        return back()->with('success', "Layout '{$subThemeName}' deleted successfully.");
    }

    public function toggleSubThemeStatus(SubTheme $subTheme)
    {
        $subTheme->status = ($subTheme->status === 'active') ? 'inactive' : 'active';
        $subTheme->save();

        return back()->with('success', "Layout '{$subTheme->name}' is now {$subTheme->status}.");
    }
}
