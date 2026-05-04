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

        // ── Debug-friendly null guard ─────────────────────────────────────────
        if (!$tenant) {
            // Try one more time directly from the authenticated user
            $user = auth()->user();
            if ($user && $user->tenant_id) {
                $tenant = \App\Models\Tenant::find($user->tenant_id);
                session(['tenant_id' => $user->tenant_id]);
            }
        }

        if (!$tenant) {
            return response()->json([
                'message' => 'No active tenant found. Your user account may not be linked to a tenant. Please contact support.',
            ], 400);
        }
        // ─────────────────────────────────────────────────────────────────────

        try {
            $tenant->theme_id = $theme->id;

            // Reset sub_theme if it doesn't belong to this main theme
            if ($tenant->sub_theme_id) {
                $currentSub = SubTheme::find($tenant->sub_theme_id);
                if (!$currentSub || $currentSub->theme_id != $theme->id) {
                    $tenant->sub_theme_id = null;
                }
            }

            $tenant->save();
            session(['tenant_id' => $tenant->id]);

            return response()->json([
                'message' => '✅ Industry set to <strong>' . $theme->name . '</strong>! Now choose a layout below.',
                'reload'  => true,
            ]);

        } catch (\Throwable $e) {
            \Log::error('activateMainTheme failed', ['error' => $e->getMessage(), 'tenant' => $tenant->id]);
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

        // Refresh session
        session(['tenant_id' => $tenant->id]);

        return response()->json([
            'message' => '🎉 Layout <strong>' . $subTheme->name . '</strong> activated! Your dashboard is now fully unlocked.',
            'reload' => true
        ]);
    }
}
