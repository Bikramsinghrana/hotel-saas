<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $tenant = Tenant::where('domain', $host)->first();

        if ($tenant) {
            session(['tenant_id' => $tenant->id, 'theme' => $tenant->theme?->key, 'sub_theme' => $tenant->subTheme?->key]);
            app()->instance('tenant', $tenant);
        }

        return $next($request);
    }
}
