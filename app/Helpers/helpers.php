<?php

use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;

if (! function_exists('tenant')) {
    function tenant(): ?Tenant
    {
        // Priority 1: session (most reliable after login sets it)
        if (session()->has('tenant_id')) {
            return Tenant::find(session('tenant_id'));
        }

        // Priority 2: authenticated user's tenant_id (handles cases where session wasn't set)
        if (auth()->check() && auth()->user()->tenant_id) {
            $t = Tenant::find(auth()->user()->tenant_id);
            if ($t) {
                // Backfill the session so subsequent requests are fast
                session(['tenant_id' => $t->id]);
                return $t;
            }
        }

        // Priority 3: service container (set by a TenantMiddleware, if present)
        if (app()->bound('tenant')) {
            return app('tenant');
        }

        return null;
    }
}

if (! function_exists('isSingleHotel')) {
    function isSingleHotel(): bool
    {
        $t = tenant();
        return $t && $t->subTheme && $t->subTheme->type === 'single_hotel';
    }
}

if (! function_exists('themeView')) {
    function themeView(string $view, array $data = [])
    {
        $theme = config('app.theme') ?? session('theme');
        if ($theme && view()->exists("themes.$theme.$view")) {
            return view("themes.$theme.$view", $data);
        }
        return view($view, $data);
    }
}

if (! function_exists('uploadFile')) {
    function uploadFile($file, $path = 'uploads', $disk = 'public')
    {
        return $file->store($path, $disk);
    }
}

if (! function_exists('cacheRemember')) {
    function cacheRemember(string $key, $ttl, callable $cb)
    {
        return Cache::remember($key, $ttl, $cb);
    }
}
