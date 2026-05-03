<?php

use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;

if (! function_exists('tenant')) {
    function tenant(): ?Tenant
    {
        return app('tenant') ?? (session('tenant_id') ? Tenant::find(session('tenant_id')) : null);
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
