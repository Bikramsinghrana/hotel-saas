<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ThemeMiddleware
{
    /**
     * Handle incoming request to ensure active theme consistency while allowing direct previews.
     */
    public function handle(Request $request, Closure $next)
    {
        $tenant = tenant();

        if ($tenant) {
            // Freshly reload tenant theme from DB to detect any recent changes immediately
            $tenant->loadMissing(['theme', 'subTheme']);
            $themeKey = $tenant->theme ? strtolower($tenant->theme->key) : null;
            $subThemeKey = $tenant->subTheme ? strtolower($tenant->subTheme->key) : null;

            // Sync session and configuration
            session([
                'tenant_id' => $tenant->id,
                'theme'     => $themeKey,
                'sub_theme' => $subThemeKey,
            ]);

            if ($themeKey) {
                config([
                    'app.theme'     => $themeKey,
                    'app.sub_theme' => $subThemeKey,
                ]);
            }

            // Theme redirection on non-AJAX GET requests for internal admin mismatch
            if ($request->isMethod('GET') && !$request->ajax() && !$request->expectsJson()) {
                $path = trim($request->path(), '/');

                // Admin Vertical Operation Routes Mismatch (e.g. attempting to manage hotel bookings when theme is restaurant)
                if ($themeKey && $themeKey !== 'hotel') {
                    if (
                        $path === 'admin/hotels' ||
                        str_starts_with($path, 'admin/hotels/') ||
                        $path === 'admin/rooms' ||
                        str_starts_with($path, 'admin/rooms/') ||
                        $path === 'admin/room-types' ||
                        str_starts_with($path, 'admin/room-types/') ||
                        $path === 'admin/bookings' ||
                        str_starts_with($path, 'admin/bookings/')
                    ) {
                        return redirect()->route('admin.dashboard');
                    }
                }

                if ($themeKey && !in_array($themeKey, ['restaurant', 'resto'])) {
                    if (
                        $path === 'admin/resto' ||
                        str_starts_with($path, 'admin/resto/')
                    ) {
                        return redirect()->route('admin.dashboard');
                    }
                }
            }
        }

        return $next($request);
    }
}
