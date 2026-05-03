<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ThemeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $theme = session('theme');
        $subTheme = session('sub_theme');

        if ($theme) {
            // you can bind a theme manager or set config values here
            config(['app.theme' => $theme, 'app.sub_theme' => $subTheme]);
        }

        return $next($request);
    }
}
