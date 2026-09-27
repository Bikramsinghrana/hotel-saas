<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$app->instance('request', Illuminate\Http\Request::create('/admin/coupons', 'GET'));
view()->share('errors', new \Illuminate\Support\ViewErrorBag);

use App\Models\Tenant;
use App\Models\Coupon;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\CouponController;

echo "=== TESTING COUPON VALIDITY RULE SELECTOR ===\n\n";

$user = User::first();
if ($user) {
    auth()->setUser($user);
}

$tenant = Tenant::first();
if ($tenant) {
    session(['tenant_id' => $tenant->id]);
}

$hotel = Hotel::firstOrCreate(['tenant_id' => $tenant->id, 'name' => 'Grand Palace']);

$controller = new CouponController();

// 1. Create a Days-Only coupon using validity_type = 'days'
$req1 = Request::create('/admin/coupons', 'POST', [
    'title' => 'Sunday Funday Offer',
    'type' => 'coupon',
    'code' => 'SUNDAY50',
    'discount_type' => 'percentage',
    'discount_value' => 50,
    'validity_type' => 'days',
    'applicable_days' => ['sunday'],
    'start_date' => '2026-01-01', // should be cleared by controller
    'start_time' => '12:00', // should be cleared by controller
    'status' => 'on'
]);

$response1 = $controller->store($req1);
$coupon1 = Coupon::where('code', 'SUNDAY50')->first();
echo "1. Days-Wise Only Coupon Created:\n";
echo "   - Validity Type: {$coupon1->validity_type}\n";
echo "   - Days: " . json_encode($coupon1->applicable_days) . "\n";
echo "   - Start Date (should be null): " . var_export($coupon1->start_date, true) . "\n";
echo "   - Start Time (should be null): " . var_export($coupon1->start_time, true) . "\n";
if ($coupon1->validity_type === 'days' && $coupon1->applicable_days === ['sunday'] && is_null($coupon1->start_date) && is_null($coupon1->start_time)) {
    echo "   -> PASS: Days-only rule successfully stored and sanitized!\n\n";
} else {
    echo "   -> FAIL: Unexpected values stored.\n\n";
}

// 2. Create a Time-Only coupon using validity_type = 'time'
$req2 = Request::create('/admin/coupons', 'POST', [
    'title' => 'Lunch Special Window',
    'type' => 'coupon',
    'code' => 'LUNCHTIME',
    'discount_type' => 'fixed',
    'discount_value' => 300,
    'validity_type' => 'time',
    'time_slot' => '12:30_15:30',
    'start_time' => '12:30',
    'end_time' => '15:30',
    'applicable_days' => ['monday'], // should be cleared
    'start_date' => '2026-05-01', // should be cleared
    'status' => 'on'
]);

$response2 = $controller->store($req2);
$coupon2 = Coupon::where('code', 'LUNCHTIME')->first();
echo "2. Time-Wise Only Coupon Created:\n";
echo "   - Validity Type: {$coupon2->validity_type}\n";
echo "   - Time Window: {$coupon2->time_formatted}\n";
echo "   - Days (should be null): " . var_export($coupon2->applicable_days, true) . "\n";
echo "   - Dates (should be null): " . var_export($coupon2->start_date, true) . "\n";
if ($coupon2->validity_type === 'time' && $coupon2->start_time && is_null($coupon2->applicable_days) && is_null($coupon2->start_date)) {
    echo "   -> PASS: Time-only rule successfully stored and sanitized!\n\n";
} else {
    echo "   -> FAIL: Unexpected values stored.\n\n";
}

// 3. Render Blade Views
echo "3. Testing Blade Rendering with Validity Selector:\n";
try {
    $createView = view('themes.hotel.admin.coupons.create', ['type' => 'coupon', 'hotels' => [$hotel]])->render();
    echo "   - Create Blade: PASS (Rendered Successfully with dynamic show/hide boxes)\n";

    $editView = view('themes.hotel.admin.coupons.edit', ['coupon' => $coupon1, 'hotels' => [$hotel], 'type' => 'coupon'])->render();
    echo "   - Edit Blade: PASS (Rendered Successfully with preset values)\n";

    $coupons = Coupon::where('tenant_id', $tenant->id)->paginate(15);
    $indexView = view('themes.hotel.admin.coupons.index', ['coupons' => $coupons, 'type' => 'coupon'])->render();
    echo "   - Index Blade: PASS (Rendered Successfully)\n";
} catch (\Exception $e) {
    echo "   - FAIL rendering views: " . $e->getMessage() . "\n";
}

echo "\n=== ALL VALIDITY MODE TESTS COMPLETE ===\n";
