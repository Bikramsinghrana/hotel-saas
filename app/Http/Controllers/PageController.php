<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Theme;
use App\Models\SubTheme;
use App\Models\Hotel;
use App\Models\Navigation;
use App\Models\Coupon;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $tenant = $this->resolveTenant($request);
        $theme = $tenant ? $tenant->theme : null;

        // If the tenant has no active theme, render the SaaS portal welcome
        if (!$theme) {
            return $this->portalWelcome($tenant);
        }

        $themeKey = strtolower($theme->key);
        $themeFolder = ($themeKey === 'restaurant') ? 'resto' : $themeKey;

        $data = $this->getThemeData($tenant, $theme);

        if (view()->exists("themes.{$themeFolder}.welcome")) {
            return view("themes.{$themeFolder}.welcome", $data);
        }

        if (view()->exists("themes.{$themeKey}.welcome")) {
            return view("themes.{$themeKey}.welcome", $data);
        }

        return view('welcome', $data);
    }

    public function about(Request $request)
    {
        $tenant = $this->resolveTenant($request);
        $theme = $tenant ? $tenant->theme : null;
        $themeKey = $theme ? strtolower($theme->key) : 'hotel';
        $themeFolder = ($themeKey === 'restaurant') ? 'resto' : 'hotel';

        $data = $this->getThemeData($tenant, $theme);

        if (view()->exists("themes.{$themeFolder}.about")) {
            return view("themes.{$themeFolder}.about", $data);
        }

        return view('themes.hotel.about', $data);
    }

    public function hotelAbout(Request $request)
    {
        $tenant = $this->resolveTenant($request);
        $theme = Theme::where('key', 'hotel')->first();
        $data = $this->getThemeData($tenant, $theme);

        return view('themes.hotel.about', $data);
    }

    public function contact(Request $request)
    {
        $tenant = $this->resolveTenant($request);
        $theme = $tenant ? $tenant->theme : null;
        $themeKey = $theme ? strtolower($theme->key) : 'hotel';
        $themeFolder = ($themeKey === 'restaurant') ? 'resto' : 'hotel';

        $data = $this->getThemeData($tenant, $theme);

        if (view()->exists("themes.{$themeFolder}.contact")) {
            return view("themes.{$themeFolder}.contact", $data);
        }

        return view('themes.hotel.contact', $data);
    }

    public function hotelContact(Request $request)
    {
        $tenant = $this->resolveTenant($request);
        $theme = Theme::where('key', 'hotel')->first();
        $data = $this->getThemeData($tenant, $theme);

        return view('themes.hotel.contact', $data);
    }

    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        return back()->with('success', 'Thank you for reaching out! Our hotel concierge will get back to you shortly.');
    }

    public function hotelWelcome(Request $request, ?string $subtheme = null)
    {
        $tenant = $this->resolveTenant($request);
        
        // Ensure hotel tenant context
        if (!$tenant || ($tenant->theme && $tenant->theme->key !== 'hotel')) {
            $hotelTenant = Tenant::with(['theme', 'subTheme'])->whereHas('theme', function($q) {
                $q->where('key', 'hotel');
            })->first();
            if ($hotelTenant) {
                $tenant = $hotelTenant;
                session(['tenant_id' => $tenant->id]);
            }
        }

        $theme = Theme::where('key', 'hotel')->first();
        $data = $this->getThemeData($tenant, $theme);
        $data['selectedSubTheme'] = $subtheme ?? $tenant?->subTheme?->key ?? 'luxury';

        return view('themes.hotel.welcome', $data);
    }

    public function restoWelcome(Request $request, ?string $subtheme = null)
    {
        $tenant = $this->resolveTenant($request);

        // Ensure restaurant tenant context
        if (!$tenant || ($tenant->theme && !in_array($tenant->theme->key, ['restaurant', 'resto']))) {
            $restoTenant = Tenant::with(['theme', 'subTheme'])->whereHas('theme', function($q) {
                $q->whereIn('key', ['restaurant', 'resto']);
            })->first();
            if ($restoTenant) {
                $tenant = $restoTenant;
                session(['tenant_id' => $tenant->id]);
            }
        }

        $theme = Theme::whereIn('key', ['restaurant', 'resto'])->first();
        $data = $this->getThemeData($tenant, $theme);
        $data['selectedSubTheme'] = $subtheme ?? $tenant?->subTheme?->key ?? 'fine_dining';

        return view('themes.resto.welcome', $data);
    }

    public function themeWelcome(Request $request, string $theme, ?string $subtheme = null)
    {
        $themeKey = strtolower($theme);

        if ($themeKey === 'hotel') {
            return $this->hotelWelcome($request, $subtheme);
        }

        if ($themeKey === 'resto' || $themeKey === 'restaurant') {
            return $this->restoWelcome($request, $subtheme);
        }

        $tenant = $this->resolveTenant($request);
        $themeModel = Theme::where('key', $themeKey)->first();
        $themeFolder = ($themeKey === 'restaurant') ? 'resto' : $themeKey;
        $data = $this->getThemeData($tenant, $themeModel);
        $data['selectedSubTheme'] = $subtheme;

        if (view()->exists("themes.{$themeFolder}.welcome")) {
            return view("themes.{$themeFolder}.welcome", $data);
        }

        return view('welcome', $data);
    }

    private function resolveTenant(Request $request): ?Tenant
    {
        if (!class_exists(Tenant::class)) {
            return null;
        }

        try {
            $host = $request->getHost();

            // 1. Try matching by host (handles localhost, 127.0.0.1, custom domains with or without http://)
            $cleanHost = preg_replace('#^https?://#', '', $host);
            $cleanHost = preg_replace('#:\d+$#', '', $cleanHost);

            $tenant = Tenant::with(['theme', 'subTheme'])
                ->where(function($q) use ($cleanHost) {
                    $q->where('domain', $cleanHost)
                      ->orWhere('domain', 'like', '%' . $cleanHost . '%')
                      ->orWhereRaw("REPLACE(REPLACE(domain, 'http://', ''), 'https://', '') = ?", [$cleanHost]);
                })->first();

            if ($tenant) {
                session(['tenant_id' => $tenant->id]);
                return $tenant;
            }

            // 2. Check session tenant
            if (session()->has('tenant_id')) {
                $sessionTenant = Tenant::with(['theme', 'subTheme'])->find(session('tenant_id'));
                if ($sessionTenant) {
                    return $sessionTenant;
                }
            }

            // 3. Fallback to authenticated user's tenant
            if (auth()->check() && auth()->user()->tenant_id) {
                $userTenant = Tenant::with(['theme', 'subTheme'])->find(auth()->user()->tenant_id);
                if ($userTenant) {
                    session(['tenant_id' => $userTenant->id]);
                    return $userTenant;
                }
            }

            // 4. Fallback to first available tenant
            $tenant = Tenant::with(['theme', 'subTheme'])->first();
            if ($tenant) {
                session(['tenant_id' => $tenant->id]);
            }
            return $tenant;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function getThemeData(?Tenant $tenant, ?Theme $theme): array
    {
        $hotels = $this->getHotels($tenant);
        $navigations = $this->getNavigations($tenant);
        $offers = $this->getOffers($tenant);

        return compact('tenant', 'theme', 'hotels', 'navigations', 'offers');
    }

    private function getHotels(?Tenant $tenant)
    {
        if (class_exists(Hotel::class)) {
            try {
                $query = Hotel::query();
                if ($tenant) {
                    $query->where('tenant_id', $tenant->id);
                }
                $hotels = $query->where('status', 'active')->limit(6)->get();
                if ($hotels->isNotEmpty()) {
                    return $hotels;
                }
            } catch (\Throwable $e) {
            }
        }

        return collect([
            (object)['name' => 'Royal Heritage Sanctuary', 'address' => ['city' => 'Udaipur'], 'rating' => 4.9, 'base_price' => 5400.00, 'media' => collect([(object)['path' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&h=600&fit=crop']])],
            (object)['name' => 'Seaside Azure Resort', 'address' => ['city' => 'Goa'], 'rating' => 4.8, 'base_price' => 3800.00, 'media' => collect([(object)['path' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&h=600&fit=crop']])],
            (object)['name' => 'Highland Pine Lodge', 'address' => ['city' => 'Manali'], 'rating' => 4.7, 'base_price' => 2900.00, 'media' => collect([(object)['path' => 'https://images.unsplash.com/photo-1501117716987-c8e42d5b3e1b?w=800&h=600&fit=crop']])],
        ]);
    }

    private function getNavigations(?Tenant $tenant)
    {
        if (class_exists(Navigation::class)) {
            try {
                $query = Navigation::active()->ordered();
                if ($tenant) {
                    $query->where('tenant_id', $tenant->id);
                }
                return $query->get();
            } catch (\Throwable $e) {
            }
        }
        return collect();
    }

    private function getOffers(?Tenant $tenant)
    {
        if (class_exists(Coupon::class)) {
            try {
                $now = now()->startOfDay();
                return Coupon::where('status', true)
                    ->where(function($q) use ($now) {
                        $q->where('start_date', '<=', $now)->orWhereNull('start_date');
                    })
                    ->where(function($q) use ($now) {
                        $q->where('expire_date', '>=', $now)->orWhereNull('expire_date');
                    })
                    ->where(function($q) use ($tenant) {
                        if ($tenant) {
                            $q->where('tenant_id', $tenant->id);
                        }
                    })
                    ->latest()
                    ->get();
            } catch (\Throwable $e) {
            }
        }
        return collect();
    }
}
