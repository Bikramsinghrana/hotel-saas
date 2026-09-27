<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$app->instance('request', Illuminate\Http\Request::create('/admin/coupons', 'GET'));
view()->share('errors', new \Illuminate\Support\ViewErrorBag);

use App\Models\Coupon;

use App\Models\User;
use App\Models\Tenant;
use App\Services\CouponService;
use Carbon\Carbon;
use Illuminate\Http\Request;

echo "=== TESTING DAY-WISE & TIME-WINDOW COUPON SYSTEM ===\n\n";

$user = User::first();
auth()->setUser($user);
$tenant = Tenant::first();
if ($tenant) {
    session(['tenant_id' => $tenant->id]);
}

$couponService = new CouponService();

// 1. Test Day-wise coupon validation
echo "1. Testing Days-Wise Coupon (Only Valid on Sunday and Monday):\n";
$dayCoupon = Coupon::create([
    'tenant_id' => $tenant ? $tenant->id : 1,
    'code' => 'SUNDAY_MONDAY_SPECIAL',
    'title' => 'Sunday & Monday Special',
    'discount_type' => 'percentage',
    'discount_value' => 25,
    'applicable_days' => ['sunday', 'monday'],
    'status' => true,
]);

// Test on a Sunday (2026-09-27 is Sunday)
$sundayCheck = $couponService->validate('SUNDAY_MONDAY_SPECIAL', null, $tenant->id, '2026-09-27');
echo "   Checking on Sunday (2026-09-27): " . ($sundayCheck['success'] ? 'PASS (Valid)' : 'FAIL: ' . $sundayCheck['message']) . "\n";
assert($sundayCheck['success'] === true, "Coupon should be valid on Sunday");

// Test on a Tuesday (2026-09-29 is Tuesday)
$tuesdayCheck = $couponService->validate('SUNDAY_MONDAY_SPECIAL', null, $tenant->id, '2026-09-29');
echo "   Checking on Tuesday (2026-09-29): " . (!$tuesdayCheck['success'] ? 'PASS (Correctly Rejected: ' . $tuesdayCheck['message'] . ')' : 'FAIL: should reject') . "\n";
assert($tuesdayCheck['success'] === false, "Coupon should NOT be valid on Tuesday");

// 2. Test Time-Window Coupon (Valid 12:30 to 15:30)
echo "\n2. Testing Time-Slot Coupon (Valid 12:30 to 15:30):\n";
$timeCoupon = Coupon::create([
    'tenant_id' => $tenant ? $tenant->id : 1,
    'code' => 'LUNCH_1230_1530',
    'title' => 'Lunch Special 12:30 to 3:30 PM',
    'discount_type' => 'fixed',
    'discount_value' => 300,
    'start_time' => '12:30:00',
    'end_time' => '15:30:00',
    'time_slot' => '12:30_15:30',
    'status' => true,
]);

// Test at 13:00 (1:00 PM) - Inside window
$validTimeCheck = $couponService->validate('LUNCH_1230_1530', null, $tenant->id, now()->format('Y-m-d'), '13:00:00');
echo "   Checking at 13:00 (1:00 PM): " . ($validTimeCheck['success'] ? 'PASS (Valid)' : 'FAIL: ' . $validTimeCheck['message']) . "\n";
assert($validTimeCheck['success'] === true, "Coupon should be valid at 13:00");

// Test at 18:00 (6:00 PM) - Outside window
$invalidTimeCheck = $couponService->validate('LUNCH_1230_1530', null, $tenant->id, now()->format('Y-m-d'), '18:00:00');
echo "   Checking at 18:00 (6:00 PM): " . (!$invalidTimeCheck['success'] ? 'PASS (Correctly Rejected: ' . $invalidTimeCheck['message'] . ')' : 'FAIL: should reject') . "\n";
assert($invalidTimeCheck['success'] === false, "Coupon should NOT be valid at 18:00");

// 3. Test Admin CouponController Store with Days & Time Slot
echo "\n3. Testing Admin CouponController::store with Days and Time Window:\n";
$controller = app(\App\Http\Controllers\Admin\CouponController::class);

$reqStore = Request::create('/admin/coupons', 'POST', [
    'title' => 'Happy Hours 20% Off',
    'code' => 'HAPPY_HOURS_20',
    'type' => 'coupon',
    'discount_type' => 'percentage',
    'discount_value' => 20,
    'applicable_days' => ['friday', 'saturday', 'sunday'],
    'time_slot' => '16:00_19:00',
    'start_time' => '16:00',
    'end_time' => '19:00',
    'min_spend' => 1000,
    'usage_limit' => 100,
    'status' => 'on'
]);

$resStore = $controller->store($reqStore);
echo "   Store Response Status: " . $resStore->getStatusCode() . "\n";
$created = Coupon::where('code', 'HAPPY_HOURS_20')->first();
assert($created !== null, "Created coupon should exist in database");
echo "   Created Coupon ID: {$created->id}\n";
echo "   Applicable Days in DB: " . json_encode($created->applicable_days) . "\n";
echo "   Days Formatted: " . $created->days_formatted . "\n";
echo "   Time Formatted: " . $created->time_formatted . "\n";
echo "   PASS: Admin Store works with days and time windows!\n";

// 4. Test Admin Views Rendering
echo "\n4. Testing Admin Blade Views Rendering:\n";
$reqIndex = Request::create('/admin/coupons?type=coupon', 'GET');
$viewIndex = $controller->index($reqIndex);
$renderedIndex = $viewIndex->render();
assert(strpos($renderedIndex, 'HAPPY_HOURS_20') !== false, "Index view should contain created coupon code");
assert(strpos($renderedIndex, 'Time Window') !== false, "Index view should have Time Window header");
echo "   PASS: Index view rendered successfully with day & time columns!\n";

$viewCreate = $controller->create(Request::create('/admin/coupons/create?type=coupon', 'GET'));
$renderedCreate = $viewCreate->render();
assert(strpos($renderedCreate, 'Select Applicable Days of Week') !== false, "Create view should have days wise selector");
assert(strpos($renderedCreate, 'Preset Time Slot Dropdown') !== false, "Create view should have time slot dropdown");
echo "   PASS: Create view rendered successfully with days & time presets!\n";

$viewEdit = $controller->edit($created->id);
$renderedEdit = $viewEdit->render();
assert(strpos($renderedEdit, 'HAPPY_HOURS_20') !== false, "Edit view should display coupon code");
echo "   PASS: Edit view rendered successfully!\n";

// Cleanup
$dayCoupon->delete();
$timeCoupon->delete();
$created->delete();

echo "\n=== ALL DAYS & TIME COUPON TESTS PASSED! ===\n";
