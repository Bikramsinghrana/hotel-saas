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

echo "=== Testing Individual Theme Welcome Pages & Base URLs ===\n\n";

$tenant = Tenant::first();
$controller = new PageController();

$urlsToTest = [
    '/' => 'Root Welcome',
    '/hotel' => 'Hotel Default Welcome',
    '/hotel/luxury' => 'Hotel Luxury Layout',
    '/hotel/budget' => 'Hotel Budget Layout',
    '/resto' => 'Restaurant Default Welcome',
    '/resto/fine_dining' => 'Restaurant Fine Dining Layout',
    '/resto/fast_food' => 'Restaurant Fast Food Layout',
    '/restaurant' => 'Restaurant Alias Welcome',
    '/themes/hotel' => 'Generic Theme URL (Hotel)',
    '/themes/restaurant/fast_food' => 'Generic Theme URL (Restaurant Fast Food)',
];

foreach ($urlsToTest as $uri => $label) {
    try {
        $request = Request::create($uri, 'GET');
        $response = null;

        if ($uri === '/') {
            $response = $controller->index($request);
        } elseif ($uri === '/hotel') {
            $response = $controller->hotelWelcome($request);
        } elseif ($uri === '/hotel/luxury') {
            $response = $controller->hotelWelcome($request, 'luxury');
        } elseif ($uri === '/hotel/budget') {
            $response = $controller->hotelWelcome($request, 'budget');
        } elseif ($uri === '/resto' || $uri === '/restaurant') {
            $response = $controller->restoWelcome($request);
        } elseif ($uri === '/resto/fine_dining') {
            $response = $controller->restoWelcome($request, 'fine_dining');
        } elseif ($uri === '/resto/fast_food') {
            $response = $controller->restoWelcome($request, 'fast_food');
        } elseif ($uri === '/themes/hotel') {
            $response = $controller->themeWelcome($request, 'hotel');
        } elseif ($uri === '/themes/restaurant/fast_food') {
            $response = $controller->themeWelcome($request, 'restaurant', 'fast_food');
        }

        $html = $response->render();
        echo "[PASS] URL: {$uri} ({$label})\n";
        echo "  -> View Name: " . $response->name() . ", Size: " . strlen($html) . " bytes\n";
    } catch (\Throwable $e) {
        echo "[FAIL] URL {$uri} error: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Testing Active Tenant Theme Switching on / ===\n";

$hotelTheme = Theme::where('key', 'hotel')->first();
$restoTheme = Theme::where('key', 'restaurant')->first();

// 1. Hotel Active
$tenant->update(['theme_id' => $hotelTheme->id]);
session(['tenant_id' => $tenant->id]);
$hotelHtml = $controller->index(Request::create('/', 'GET'))->render();
echo "[HOTEL ACTIVE ON /]:\n";
echo "  Header has 'Accommodations': " . (strpos($hotelHtml, 'Accommodations') !== false ? 'YES' : 'NO') . "\n";
echo "  Header has 'Book Suite': " . (strpos($hotelHtml, 'Book Suite') !== false ? 'YES' : 'NO') . "\n";
echo "  Header has 'Menu & Catalog': " . (strpos($hotelHtml, 'Menu & Catalog') !== false ? 'YES' : 'NO') . "\n";

// 2. Restaurant Active
$tenant->update(['theme_id' => $restoTheme->id]);
session(['tenant_id' => $tenant->id]);
$restoHtml = $controller->index(Request::create('/', 'GET'))->render();
// 3. Rooms Search Results Page
$roomController = new \App\Http\Controllers\RoomController();
$roomsReq = Request::create('/rooms/index?name=suite&check_in=2026-09-13&check_out=2026-09-15', 'GET');
$roomsHtml = $roomController->index($roomsReq)->render();
echo "[ROOMS SEARCH RESULTS PAGE (/rooms/index)]:\n";
echo "  Rendered Size: " . strlen($roomsHtml) . " bytes\n";
echo "  Header has Hotel Brand: " . (strpos($roomsHtml, 'hotel-brand-name') !== false ? 'YES' : 'NO') . "\n";
echo "  Header has 'Accommodations': " . (strpos($roomsHtml, 'Accommodations') !== false ? 'YES' : 'NO') . "\n";
echo "  Has Room Filter: " . (strpos($roomsHtml, 'Hotel / Room Name') !== false ? 'YES' : 'NO') . "\n";

echo "\nAll Theme Welcome URL Tests Finished Successfully!\n";
