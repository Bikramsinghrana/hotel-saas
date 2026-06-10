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
        $httpHost = $request->getHttpHost();

        $tenant = Tenant::where(function($q) use ($host, $httpHost) {
            $q->where('domain', $host)
              ->orWhere('domain', $httpHost)
              ->orWhere('domain', 'like', '%' . $host . '%')
              ->orWhere('domain', 'like', '%' . $httpHost . '%');
        })->first();

        if ($tenant) {
            session(['tenant_id' => $tenant->id, 'theme' => $tenant->theme?->key, 'sub_theme' => $tenant->subTheme?->key]);
            app()->instance('tenant', $tenant);
        }

        return $next($request);
    }
}
