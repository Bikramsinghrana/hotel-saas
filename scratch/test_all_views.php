<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Tenant;
use App\Models\Theme;
use App\Models\Plan;
use App\Models\Feature;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

echo "=== Testing Multi-Vertical SaaS Architecture ===\n";

$superadmin = User::where('email', 'superadmin@gmail.com')->first();
$hoteladmin = User::where('email', 'hoteladmin@gmail.com')->first();
$restoadmin = User::where('email', 'restoadmin@gmail.com')->first();

$hotelTheme = Theme::where('key', 'hotel')->first();
$hotelSubTheme = \App\Models\SubTheme::where('theme_id', $hotelTheme->id)->where('key', 'luxury')->first();
Tenant::where('id', $hoteladmin->tenant_id)->update(['theme_id' => $hotelTheme->id, 'sub_theme_id' => $hotelSubTheme->id]);

$restoTheme = Theme::where('key', 'restaurant')->first();
$restoSubTheme = \App\Models\SubTheme::where('theme_id', $restoTheme->id)->where('key', 'fine_dining')->first();
Tenant::where('id', $restoadmin->tenant_id)->update(['theme_id' => $restoTheme->id, 'sub_theme_id' => $restoSubTheme->id]);

Auth::login($superadmin);
echo "Logged in as: " . Auth::user()->email . " (is_super_admin: " . (is_super_admin() ? 'YES' : 'NO') . ")\n";

// Test 1: Super Admin Views
$viewsToTest = [
    'superadmin.dashboard' => [
        'stats' => [
            'total_tenants' => Tenant::count(),
            'total_themes' => Theme::count(),
            'total_plans' => Plan::count(),
            'total_features' => Feature::count(),
            'active_subscriptions' => 2,
        ],
        'themes' => Theme::with('subThemes', 'tenants')->get(),
        'recentTenants' => Tenant::with('theme', 'subTheme', 'activeSubscription.plan')->latest()->take(5)->get(),
        'plans' => Plan::with('features', 'subThemes')->get(),
    ],
    'superadmin.themes.index' => [
        'themes' => Theme::with(['subThemes.tenants', 'tenants'])->get(),
    ],
    'superadmin.plans.index' => [
        'plans' => Plan::with(['theme', 'features', 'subThemes', 'subscriptions'])->get(),
        'themes' => Theme::where('status', 'active')->get(),
        'features' => Feature::where('status', 'active')->get(),
        'subThemes' => \App\Models\SubTheme::where('status', 'active')->get(),
    ],
    'superadmin.features.index' => [
        'features' => Feature::with('theme')->orderBy('theme_id')->get(),
        'themes' => Theme::where('status', 'active')->get(),
    ],
    'superadmin.tenants.index' => [
        'tenants' => Tenant::with(['theme', 'subTheme', 'activeSubscription.plan'])->paginate(15),
    ],
    'superadmin.tenants.show' => [
        'tenant' => Tenant::with(['theme', 'subTheme', 'activeSubscription.plan.features', 'featureOverrides.feature', 'subThemeAccesses.subTheme'])->first(),
        'features' => Feature::where('status', 'active')->get(),
        'subThemes' => \App\Models\SubTheme::where('status', 'active')->get(),
    ],
];

foreach ($viewsToTest as $viewName => $data) {
    try {
        $html = View::make($viewName, $data)->render();
        echo "[PASS] View rendered: {$viewName} (" . strlen($html) . " bytes)\n";
    } catch (\Throwable $e) {
        echo "[FAIL] View error in {$viewName}: " . $e->getMessage() . "\n" . $e->getFile() . ":" . $e->getLine() . "\n";
    }
}

// Test 2: Sidebar Component rendering under different vertical contexts
echo "\n=== Testing Modular Sidebars for Different Vertical Tenants ===\n";

// Hotel tenant sidebar
Auth::login($hoteladmin);
session(['tenant_id' => $hoteladmin->tenant_id]);
$hotelTenant = Tenant::with('theme', 'subTheme')->find($hoteladmin->tenant_id);
echo "Hotel Tenant: " . ($hotelTenant ? $hotelTenant->name : 'null') . ", Theme: " . ($hotelTenant && $hotelTenant->theme ? $hotelTenant->theme->key : 'none') . ", SubTheme: " . ($hotelTenant && $hotelTenant->subTheme ? $hotelTenant->subTheme->key : 'none') . "\n";
try {
    $html = View::make('components.admin.sidebar')->render();
    echo "[PASS] Hotel Tenant Sidebar rendered (" . strlen($html) . " bytes)\n";
    if (strpos($html, 'Hotel Operations') !== false) {
        echo "  -> Found Hotel Operations in sidebar\n";
    } else {
        echo "  -> Sidebar snippet:\n" . substr($html, 0, 400) . "\n";
    }
    if (strpos($html, 'Property Master') !== false) {
        echo "  -> Found Property Master in sidebar\n";
    }
    if (strpos($html, 'Site Management') !== false) {
        echo "  -> Found Common Site Management in sidebar\n";
    }
} catch (\Throwable $e) {
    echo "[FAIL] Hotel Sidebar error: " . $e->getMessage() . "\n";
}

// Resto tenant sidebar
Auth::login($restoadmin);
session(['tenant_id' => $restoadmin->tenant_id]);
$restoTenant = Tenant::with('theme', 'subTheme')->find($restoadmin->tenant_id);
echo "Resto Tenant: " . ($restoTenant ? $restoTenant->name : 'null') . ", Theme: " . ($restoTenant && $restoTenant->theme ? $restoTenant->theme->slug : 'none') . ", SubTheme: " . ($restoTenant && $restoTenant->subTheme ? $restoTenant->subTheme->key : 'none') . "\n";
try {
    $html = View::make('components.admin.sidebar')->render();
    echo "[PASS] Restaurant Tenant Sidebar rendered (" . strlen($html) . " bytes)\n";
    if (strpos($html, 'Restaurant Operations') !== false) {
        echo "  -> Found Restaurant Operations in sidebar\n";
    } else {
        echo "  -> Sidebar snippet:\n" . substr($html, 0, 400) . "\n";
    }
    if (strpos($html, 'Menu & Catalog') !== false) {
        echo "  -> Found Menu & Catalog in sidebar\n";
    }
    if (strpos($html, 'Tables & QR Dine-in') !== false) {
        echo "  -> Found Tables in sidebar\n";
    }
    if (strpos($html, 'Kitchen Display (KDS)') !== false) {
        echo "  -> Found KDS in sidebar\n";
    }
} catch (\Throwable $e) {
    echo "[FAIL] Restaurant Sidebar error: " . $e->getMessage() . "\n";
}

echo "\nAll verification checks complete!\n";
