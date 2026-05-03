<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $tenant = null;
        $theme = null;
        $hotels = collect();
        $useDummy = false;

        // Resolve tenant from session or hostname when Tenant model exists
        if (class_exists(\App\Models\Tenant::class)) {
            try {
                $host = $request->getHost();
                $tenant = \App\Models\Tenant::where('domain', $host)->first();
                if (! $tenant && session()->has('tenant_id')) {
                    $tenant = \App\Models\Tenant::find(session('tenant_id'));
                }
            } catch (\Throwable $e) {
                $tenant = null;
            }
        }

        // Load theme if available
        if ($tenant && class_exists(\App\Models\Theme::class)) {
            try {
                $theme = $tenant->theme_id ? \App\Models\Theme::find($tenant->theme_id) : null;
            } catch (\Throwable $e) {
                $theme = null;
            }
        }

        // Load hotels (tenant-scoped if tenant found)
        if (class_exists(\App\Models\Hotel::class)) {
            try {
                $query = \App\Models\Hotel::query();
                if ($tenant) {
                    $query->where('tenant_id', $tenant->id);
                }
                $hotels = $query->where('status', 'active')->limit(6)->get();
            } catch (\Throwable $e) {
                $hotels = collect();
            }
        }

        // Fallback dummy hotels when none available or models not installed
        if (empty($hotels) || $hotels->isEmpty()) {
            $useDummy = true;
            $hotels = collect([
                (object)['name' => 'Seaside Resort', 'address' => ['city' => 'Beachville'], 'rating' => 4.5, 'base_price' => 149.00, 'media' => collect([(object)['path' => 'https://images.unsplash.com/photo-1501117716987-c8e42d5b3e1b?w=800&h=600&fit=crop']])],
                (object)['name' => 'City Center Hotel', 'address' => ['city' => 'Metro City'], 'rating' => 4.0, 'base_price' => 99.00, 'media' => collect([(object)['path' => 'https://images.unsplash.com/photo-1526778548025-fa2f459cd5c0?w=800&h=600&fit=crop']])],
                (object)['name' => 'Mountain Lodge', 'address' => ['city' => 'Highpeak'], 'rating' => 4.7, 'base_price' => 179.00, 'media' => collect([(object)['path' => 'https://images.unsplash.com/photo-1501117716987-c8e42d5b3e1b?w=800&h=600&fit=crop&sig=2']])],
                (object)['name' => 'Urban Boutique', 'address' => ['city' => 'Downtown'], 'rating' => 4.3, 'base_price' => 129.00, 'media' => collect([(object)['path' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800&h=600&fit=crop']])],
                (object)['name' => 'Country Inn', 'address' => ['city' => 'Riverside'], 'rating' => 4.1, 'base_price' => 89.00, 'media' => collect([(object)['path' => 'https://images.unsplash.com/photo-1505691723518-34a2c7b7a4b9?w=800&h=600&fit=crop']])],
                (object)['name' => 'Lakeview Hotel', 'address' => ['city' => 'Lakeside'], 'rating' => 4.6, 'base_price' => 159.00, 'media' => collect([(object)['path' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?w=800&h=600&fit=crop']])],
            ]);
        }

        return view('welcome', compact('hotels', 'tenant', 'theme', 'useDummy'));
    }
}
