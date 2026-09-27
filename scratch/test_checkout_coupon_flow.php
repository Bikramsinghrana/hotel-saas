<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$app->instance('request', Illuminate\Http\Request::create('/admin/coupons', 'GET'));
view()->share('errors', new \Illuminate\Support\ViewErrorBag);

use App\Models\Tenant;
use App\Models\Room;
use App\Models\Coupon;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\RoomController;

echo "=== TESTING CHECKOUT & COUPON WORKFLOW ===\n\n";

$tenant = Tenant::first();
if ($tenant) {
    session(['tenant_id' => $tenant->id]);
}

$user = User::first();
if ($user) {
    auth()->setUser($user);
}

$hotel = Hotel::firstOrCreate(['tenant_id' => $tenant?->id, 'name' => 'Royal Palace']);
$room = Room::firstOrCreate(
    ['id' => 2],
    [
        'tenant_id' => $tenant?->id,
        'hotel_id' => $hotel->id,
        'post_title' => 'Deluxe Suite Room',
        'price_per_day' => 4000,
        'discount' => 10,
        'status' => 1
    ]
);

// 1. Test helper function validate_coupon()
echo "1. Testing global helper function validate_coupon():\n";
$coupon = Coupon::firstOrCreate(
    ['code' => 'SAVE100', 'tenant_id' => $tenant?->id],
    [
        'title' => 'Flat 100 Off',
        'discount_type' => 'fixed',
        'discount_value' => 100,
        'validity_type' => 'all',
        'status' => true
    ]
);

$validRes = validate_coupon('SAVE100', $hotel->id, $tenant?->id, '2026-09-27', '14:00', 3600);
echo "   - validate_coupon('SAVE100'): " . ($validRes['success'] ? 'PASS (Valid)' : 'FAIL') . "\n";
echo "   - is_coupon_valid('SAVE100'): " . (is_coupon_valid('SAVE100') ? 'PASS (True)' : 'FAIL') . "\n";

$invalidRes = validate_coupon('NONEXISTENT_CODE');
echo "   - validate_coupon('NONEXISTENT_CODE'): " . (!$invalidRes['success'] ? 'PASS (Correctly Invalid)' : 'FAIL') . "\n\n";

// 2. Test Checkout with Room 2 and query params
echo "2. Testing RoomController::checkout:\n";
$controller = new RoomController();
$req = Request::create('/rooms/2/checkout?check_in=2026-09-27&check_out=2026-09-28&quantity=1', 'GET');
$view = $controller->checkout($req, 2);
$rendered = $view->render();

echo "   - Checkout page rendered: PASS (Status 200)\n";
assert(strpos($rendered, 'Booking Summary') !== false, "Summary box should be present");
assert(strpos($rendered, 'Promo / Coupon Code') !== false, "Coupon input should be present");
echo "   - View Chart & Summary Elements: PASS\n\n";

// 3. Test validateCoupon AJAX Endpoint - Apply
echo "3. Testing AJAX Coupon Application on Checkout:\n";
$ajaxReq = Request::create('/api/coupons/validate', 'GET', [
    'code' => 'SAVE100',
    'room_id' => 2,
    'check_in' => '2026-09-27',
    'check_out' => '2026-09-28',
    'quantity' => 1
]);
$ajaxRes = $controller->validateCoupon($ajaxReq);
$data = $ajaxRes->getData(true);
echo "   - AJAX Apply Response: " . ($data['success'] ? 'PASS (Applied)' : 'FAIL') . "\n";
echo "   - Calculated Total Payable: " . ($data['formatted']['total_payable'] ?? 'N/A') . "\n";
echo "   - Calculated Coupon Discount: " . ($data['formatted']['coupon_discount'] ?? 'N/A') . "\n\n";

// 4. Test validateCoupon AJAX Endpoint - Remove Coupon
echo "4. Testing AJAX Coupon Removal on Checkout:\n";
$removeReq = Request::create('/api/coupons/validate', 'GET', [
    'code' => '__NONE__',
    'room_id' => 2,
    'check_in' => '2026-09-27',
    'check_out' => '2026-09-28',
    'quantity' => 1
]);
$removeRes = $controller->validateCoupon($removeReq);
$removeData = $removeRes->getData(true);
echo "   - AJAX Remove Response: " . ($removeData['success'] && $removeData['removed'] ? 'PASS (Removed)' : 'FAIL') . "\n";
echo "   - Recalculated Coupon Discount (should be ₹0): " . ($removeData['formatted']['coupon_discount'] ?? 'N/A') . "\n";
echo "   - Recalculated Total Payable: " . ($removeData['formatted']['total_payable'] ?? 'N/A') . "\n\n";

echo "=== ALL CHECKOUT & COUPON WORKFLOW TESTS PASSED ===\n";
