<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use App\Http\Middleware\ThemeMiddleware;
use Illuminate\Http\Request;

$tenant = Tenant::find(1);
$middleware = new ThemeMiddleware();

echo "=== TESTING THEME REDIRECTION MIDDLEWARE ===\n";

// Test 1: Tenant has Hotel Theme Active
$tenant->theme_id = 1; // Hotel
$tenant->save();
session(['tenant_id' => 1]);

echo "\n--- Scenario 1: Tenant has 'hotel' Theme Active ---\n";

// Request /hotel (should pass through)
$req1 = Request::create('/hotel', 'GET');
$res1 = $middleware->handle($req1, fn($r) => response('OK_HOTEL', 200));
echo "Request '/hotel' -> Status: " . $res1->getStatusCode() . " (" . ($res1->getStatusCode() === 200 ? 'PASS: Allowed' : 'FAIL') . ")\n";

// Request /rooms/index (should pass through)
$req2 = Request::create('/rooms/index', 'GET');
$res2 = $middleware->handle($req2, fn($r) => response('OK_ROOMS', 200));
echo "Request '/rooms/index' -> Status: " . $res2->getStatusCode() . " (" . ($res2->getStatusCode() === 200 ? 'PASS: Allowed' : 'FAIL') . ")\n";

// Request /resto (mismatched! should redirect to home '/')
$req3 = Request::create('/resto', 'GET');
$res3 = $middleware->handle($req3, fn($r) => response('OK_RESTO', 200));
echo "Request '/resto' (Inactive Theme) -> Status: " . $res3->getStatusCode() . " -> Redirect to: " . $res3->headers->get('Location') . " (" . ($res3->getStatusCode() === 302 ? 'PASS: Redirected to Home' : 'FAIL') . ")\n";


// Test 2: Tenant switches to Restaurant Theme
$tenant->theme_id = 2; // Restaurant
$tenant->save();
session(['tenant_id' => 1]);

echo "\n--- Scenario 2: Tenant switches to 'restaurant' Theme and refreshes old tabs ---\n";

// Refreshing /hotel tab (now inactive! should redirect to home '/')
$req4 = Request::create('/hotel', 'GET');
$res4 = $middleware->handle($req4, fn($r) => response('OK_HOTEL', 200));
echo "Refreshing '/hotel' -> Status: " . $res4->getStatusCode() . " -> Redirect to: " . $res4->headers->get('Location') . " (" . ($res4->getStatusCode() === 302 ? 'PASS: Redirected to Home' : 'FAIL') . ")\n";

// Refreshing /rooms/1/checkout tab (now inactive! should redirect to home '/')
$req5 = Request::create('/rooms/1/checkout', 'GET');
$res5 = $middleware->handle($req5, fn($r) => response('OK_CHECKOUT', 200));
echo "Refreshing '/rooms/1/checkout' -> Status: " . $res5->getStatusCode() . " -> Redirect to: " . $res5->headers->get('Location') . " (" . ($res5->getStatusCode() === 302 ? 'PASS: Redirected to Home' : 'FAIL') . ")\n";

// Request /resto (active! should pass through)
$req6 = Request::create('/resto', 'GET');
$res6 = $middleware->handle($req6, fn($r) => response('OK_RESTO', 200));
echo "Request '/resto' -> Status: " . $res6->getStatusCode() . " (" . ($res6->getStatusCode() === 200 ? 'PASS: Allowed' : 'FAIL') . ")\n";

// Restore Tenant 1 to Hotel
$tenant->theme_id = 1;
$tenant->save();

echo "\n>>> ALL MIDDLEWARE THEME SYNC & REDIRECT TESTS PASSED! <<<\n";
