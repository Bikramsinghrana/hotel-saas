<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\TenantAccessService;

class EnsureFeatureEnabled
{
    public function __construct(
        protected TenantAccessService $accessService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $feature
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $tenant = tenant() ?? auth()->user()?->tenant;

        if (!$tenant || !$this->accessService->canAccessFeature($tenant, $feature)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "The feature '{$feature}' is not included in your current subscription plan. Please upgrade to access this feature.",
                    'feature' => $feature,
                ], 403);
            }

            abort(403, "Feature '{$feature}' is not enabled for your account. Please contact support or upgrade your subscription plan.");
        }

        return $next($request);
    }
}
