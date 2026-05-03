<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Theme;
use App\Models\SubTheme;

class SettingController extends Controller
{
    public function index()
    {
        // Eager load subthemes
        $themes = Theme::with('subThemes')->get();
        $tenant = tenant();
        $activeThemeId = $tenant ? $tenant->theme_id : null;
        $activeSubThemeId = $tenant ? $tenant->sub_theme_id : null;

        return view('admin.settings.index', compact('themes', 'activeThemeId', 'activeSubThemeId'));
    }

    public function activateMainTheme(Request $request)
    {
        $request->validate([
            'theme_id' => 'required|exists:themes,id'
        ]);

        $theme = Theme::find($request->theme_id);
        $tenant = tenant();

        if (!$tenant) {
            return response()->json(['message' => 'No active tenant found.'], 400);
        }

        $tenant->theme_id = $theme->id;
        
        // If the current sub_theme doesn't belong to the newly selected main theme, reset it to the first available
        if (!$tenant->sub_theme_id || SubTheme::find($tenant->sub_theme_id)->theme_id != $theme->id) {
            $firstSubTheme = $theme->subThemes()->first();
            $tenant->sub_theme_id = $firstSubTheme ? $firstSubTheme->id : null;
        }

        $tenant->save();

        return response()->json([
            'message' => 'Industry theme selected successfully!',
            'reload' => true
        ]);
    }

    public function activateTheme(Request $request)
    {
        $request->validate([
            'sub_theme_id' => 'required|exists:sub_themes,id'
        ]);

        $subTheme = SubTheme::find($request->sub_theme_id);
        $tenant = tenant();

        if (!$tenant) {
            return response()->json(['message' => 'No active tenant found.'], 400);
        }

        $tenant->theme_id = $subTheme->theme_id;
        $tenant->sub_theme_id = $subTheme->id;
        $tenant->save();

        return response()->json([
            'message' => 'Layout activated successfully! The dashboard layout has been updated.',
            'reload' => true
        ]);
    }
}
