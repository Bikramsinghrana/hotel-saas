<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Theme;
use App\Models\SubTheme;
use App\Models\Tenant;

class SettingController extends Controller
{
    public function index()
    {
        // 1. Authorize action check
        authorize_action('manage tenant settings');

        // Eager load subthemes
        $themes = Theme::with('subThemes')->get();
        $tenant = tenant();
        $activeThemeId = $tenant ? $tenant->theme_id : null;
        $activeSubThemeId = $tenant ? $tenant->sub_theme_id : null;

        return view('admin.settings.index', compact('themes', 'activeThemeId', 'activeSubThemeId'));
    }

    public function activateMainTheme(Request $request)
    {
        // 1. Authorize action check
        authorize_action('manage tenant settings');

        $request->validate([
            'theme_id' => 'required|exists:themes,id'
        ]);

        $theme  = Theme::find($request->theme_id);
        $tenant = tenant();

        // Fallback to authenticated user's tenant or first tenant for Super Admin
        if (!$tenant) {
            $user = auth()->user();
            if ($user && $user->tenant_id) {
                $tenant = Tenant::find($user->tenant_id);
            } elseif (is_super_admin()) {
                $tenant = Tenant::where('theme_id', $theme->id)->first() ?? Tenant::first();
            }

            if ($tenant) {
                session(['tenant_id' => $tenant->id]);
            }
        }

        if (!$tenant) {
            return response()->json([
                'message' => 'No active tenant found. Please create or link a tenant first.',
            ], 400);
        }

        // 2. Theme entitlement & subscription check (Super Admin automatically bypasses)
        if (!can_manage_theme($theme, $tenant)) {
            return response()->json([
                'message' => "🔒 The vertical '{$theme->name}' is not unlocked in your current subscription plan. Please upgrade your plan to access this industry theme.",
            ], 403);
        }

        try {
            // Set Main Theme
            $tenant->theme_id = $theme->id;

            // Get First Accessible Sub Theme From Selected Theme
            $subTheme = SubTheme::where('theme_id', $theme->id)
                ->where('status', 'active')
                ->first();

            // Save Sub Theme ID In Tenant Table
            $tenant->sub_theme_id = $subTheme?->id;
            $tenant->save();

            // Refresh Session
            session([
                'tenant_id' => $tenant->id,
                'theme'     => $theme->key,
                'sub_theme' => $subTheme?->key,
            ]);

            return response()->json([
                'message' => '✅ Industry set to <strong>' . $theme->name . '</strong> and default layout activated!',
                'reload'  => true,
            ]);
        } catch (\Throwable $e) {
            \Log::error('activateMainTheme failed', [
                'error'  => $e->getMessage(),
                'tenant' => $tenant->id
            ]);

            return response()->json([
                'message' => 'Could not save the theme. Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function activateTheme(Request $request)
    {
        // 1. Authorize action check
        authorize_action('manage tenant settings');

        $request->validate([
            'sub_theme_id' => 'required|exists:sub_themes,id'
        ]);

        $subTheme = SubTheme::find($request->sub_theme_id);
        $tenant = tenant();

        if (!$tenant) {
            $user = auth()->user();
            if ($user && $user->tenant_id) {
                $tenant = Tenant::find($user->tenant_id);
            } elseif (is_super_admin()) {
                $tenant = Tenant::where('sub_theme_id', $subTheme->id)->first() ?? Tenant::first();
            }

            if ($tenant) {
                session(['tenant_id' => $tenant->id]);
            }
        }

        if (!$tenant) {
            return response()->json(['message' => 'No active tenant found.'], 400);
        }

        // 2. Sub-theme entitlement check (Super Admin automatically bypasses)
        if (!can_manage_subtheme($subTheme, $tenant)) {
            return response()->json([
                'message' => "🔒 The layout '{$subTheme->name}' is a premium design not included in your current subscription. Please upgrade your plan or contact support.",
            ], 403);
        }

        $tenant->theme_id = $subTheme->theme_id;
        $tenant->sub_theme_id = $subTheme->id;
        $tenant->save();

        // Refresh session so theme/sub_theme keys are available immediately
        session([
            'tenant_id' => $tenant->id,
            'theme'     => $subTheme->theme->key ?? null,
            'sub_theme' => $subTheme->key ?? null
        ]);

        return response()->json([
            'message' => '🎉 Layout <strong>' . $subTheme->name . '</strong> activated! Your dashboard is now fully unlocked.',
            'reload'  => true
        ]);
    }
}
