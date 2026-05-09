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

        $theme  = Theme::find($request->theme_id);
        $tenant = tenant();

        // Debug-friendly null guard
        if (!$tenant) {

            $user = auth()->user();

            if ($user && $user->tenant_id) {

                $tenant = \App\Models\Tenant::find($user->tenant_id);

                session([
                    'tenant_id' => $user->tenant_id
                ]);
            }
        }

        if (!$tenant) {

            return response()->json([
                'message' => 'No active tenant found. Your user account may not be linked to a tenant.',
            ], 400);
        }

        try {

            // Set Main Theme
            $tenant->theme_id = $theme->id;

            // Get First Sub Theme From Selected Theme
            $subTheme = SubTheme::where('theme_id', $theme->id)->first();

            // Save Sub Theme ID In Tenant Table
            $tenant->sub_theme_id = $subTheme?->id;
            $tenant->save();

            // Refresh Session
            session([
                'tenant_id' => $tenant->id,
                'theme' => $theme->key,
                'sub_theme' => $subTheme?->key,
            ]);

            return response()->json([
                'message' => '✅ Industry set to <strong>' . $theme->name . '</strong> and default layout activated!',
                'reload'  => true,
            ]);
        } catch (\Throwable $e) {

            \Log::error('activateMainTheme failed', [
                'error' => $e->getMessage(),
                'tenant' => $tenant->id
            ]);

            return response()->json([
                'message' => 'Could not save the theme. Error: ' . $e->getMessage(),
            ], 500);
        }
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

        // Refresh session so theme/sub_theme keys are available immediately
        session(['tenant_id' => $tenant->id, 'theme' => $subTheme->theme->key ?? null, 'sub_theme' => $subTheme->key ?? null]);

        return response()->json([
            'message' => '🎉 Layout <strong>' . $subTheme->name . '</strong> activated! Your dashboard is now fully unlocked.',
            'reload' => true
        ]);
    }
}
