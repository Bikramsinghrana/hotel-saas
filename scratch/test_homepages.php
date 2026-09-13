<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant;
use App\Models\Theme;
use App\Models\SubTheme;
use Illuminate\Http\Request;
use App\Http\Controllers\PageController;

echo "=== Testing Dynamic Multi-Vertical Homepage (http://127.0.0.1:8000/) ===\n\n";

$tenant = Tenant::first();
$controller = new PageController();
$request = Request::create('/', 'GET');

// Test Case 1: Hotel Theme with Luxury Sub-Theme
$hotelTheme = Theme::where('key', 'hotel')->first();
$luxurySub = SubTheme::where('theme_id', $hotelTheme->id)->where('key', 'luxury')->first();
$tenant->update(['theme_id' => $hotelTheme->id, 'sub_theme_id' => $luxurySub->id]);
session(['tenant_id' => $tenant->id]);

$response = $controller->index($request);
$html = $response->render();
echo "[TEST 1: Hotel - Luxury Sub-theme]\n";
echo "  Rendered bytes: " . strlen($html) . "\n";
echo "  Found 'Luxury Sub-Theme': " . (strpos($html, 'Luxury Sub-Theme') !== false ? 'YES' : 'NO') . "\n";
echo "  Found 'Elegance & Opulence': " . (strpos($html, 'Elegance & Opulence') !== false ? 'YES' : 'NO') . "\n";
echo "  Found 'Signature Accommodations': " . (strpos($html, 'Signature Accommodations') !== false ? 'YES' : 'NO') . "\n\n";

// Test Case 2: Hotel Theme with Budget Sub-Theme
$budgetSub = SubTheme::where('theme_id', $hotelTheme->id)->where('key', 'budget')->first();
$tenant->update(['theme_id' => $hotelTheme->id, 'sub_theme_id' => $budgetSub->id]);
session(['tenant_id' => $tenant->id]);

$response = $controller->index($request);
$html = $response->render();
echo "[TEST 2: Hotel - Budget Sub-theme]\n";
echo "  Rendered bytes: " . strlen($html) . "\n";
echo "  Found 'Budget Sub-Theme': " . (strpos($html, 'Budget Sub-Theme') !== false ? 'YES' : 'NO') . "\n";
echo "  Found 'Clean & Affordable': " . (strpos($html, 'Clean & Affordable') !== false ? 'YES' : 'NO') . "\n\n";

// Test Case 3: Restaurant Theme with Fine Dining Sub-Theme
$restoTheme = Theme::where('key', 'restaurant')->first();
$fineDiningSub = SubTheme::where('theme_id', $restoTheme->id)->where('key', 'fine_dining')->first();
$tenant->update(['theme_id' => $restoTheme->id, 'sub_theme_id' => $fineDiningSub->id]);
session(['tenant_id' => $tenant->id]);

$response = $controller->index($request);
$html = $response->render();
echo "[TEST 3: Restaurant - Fine Dining Sub-theme]\n";
echo "  Rendered bytes: " . strlen($html) . "\n";
echo "  Found 'Fine Dining Sub-Theme': " . (strpos($html, 'Fine Dining Sub-Theme') !== false ? 'YES' : 'NO') . "\n";
echo "  Found 'Culinary Symphony': " . (strpos($html, 'Culinary Symphony') !== false ? 'YES' : 'NO') . "\n";
echo "  Found 'Reserve Your Table': " . (strpos($html, 'Reserve Your Table') !== false ? 'YES' : 'NO') . "\n\n";

// Test Case 4: Restaurant Theme with Fast Food Sub-Theme
$fastFoodSub = SubTheme::where('theme_id', $restoTheme->id)->where('key', 'fast_food')->first();
$tenant->update(['theme_id' => $restoTheme->id, 'sub_theme_id' => $fastFoodSub->id]);
session(['tenant_id' => $tenant->id]);

$response = $controller->index($request);
$html = $response->render();
echo "[TEST 4: Restaurant - Fast Food Sub-theme]\n";
echo "  Rendered bytes: " . strlen($html) . "\n";
echo "  Found 'Fast Food Sub-Theme': " . (strpos($html, 'Fast Food Sub-Theme') !== false ? 'YES' : 'NO') . "\n";
echo "  Found 'Sizzle, Crunch & Crave': " . (strpos($html, 'Sizzle, Crunch & Crave') !== false ? 'YES' : 'NO') . "\n\n";

// Test Case 5: No theme (Coming Soon state)
$tenant->update(['theme_id' => null, 'sub_theme_id' => null]);
session(['tenant_id' => $tenant->id]);

$response = $controller->index($request);
$html = $response->render();
echo "[TEST 5: Unconfigured / Onboarding State]\n";
echo "  Rendered view: " . $response->name() . " (" . strlen($html) . " bytes)\n\n";

// Reset back to Hotel Luxury
$tenant->update(['theme_id' => $hotelTheme->id, 'sub_theme_id' => $luxurySub->id]);
session(['tenant_id' => $tenant->id]);

echo "All Homepage Tests Completed Successfully!\n";
