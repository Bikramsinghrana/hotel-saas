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
use App\Services\PriceCalculationService;

echo "=== TESTING ROOM 3 SEARCH, BOOKING & CHECKOUT CALCULATION ===\n\n";

$tenant = Tenant::first();
if ($tenant) {
    session(['tenant_id' => $tenant->id]);
}

$user = User::first();
if ($user) {
    auth()->setUser($user);
}

$hotel = Hotel::firstOrCreate(['tenant_id' => $tenant?->id, 'name' => 'Royal Palace']);
$room3 = Room::firstOrCreate(
    ['id' => 3],
    [
        'tenant_id' => $tenant?->id,
        'hotel_id' => $hotel->id,
        'post_title' => 'Executive Suite Room 3',
        'price_per_day' => 5000,
        'discount' => 10, // 10% room discount
        'status' => 1
    ]
);

// Create a Coupon
$coupon = Coupon::firstOrCreate(
    ['code' => 'STAY20', 'tenant_id' => $tenant?->id],
    [
        'title' => '20% Stay Discount',
        'discount_type' => 'percentage',
        'discount_value' => 20,
        'validity_type' => 'all',
        'status' => true
    ]
);

$controller = new RoomController();

// 1. Test Rooms Index View with 3 Nights (2026-09-27 to 2026-09-30)
echo "1. Testing /rooms/index page search:\n";
$indexReq = Request::create('/rooms/index?name=&check_in=2026-09-27&check_out=2026-09-30&room_type_id=&adults=1&children=0&total_rooms=1', 'GET');
$indexView = $controller->index($indexReq);
$indexHtml = $indexView->render();
echo "   - Rooms index page rendered: PASS (Status 200)\n";
assert(strpos($indexHtml, 'BookingSummary') !== false || strpos($indexHtml, 'BookingSystem') !== false, "BookingSystem script should be present");
assert(strpos($indexHtml, 'removeCouponFromUI') !== false, "removeCouponFromUI should be present");
echo "   - BookingSystem & Remove Coupon UI: PASS\n\n";

// 2. Test Checkout Init redirect with search parameters & coupon
echo "2. Testing /rooms/checkout-init from search page:\n";
$bookingData = json_encode([
    'rooms' => [
        [
            'id' => 3,
            'name' => 'Executive Suite Room 3',
            'price' => 5000,
            'discount' => 10,
            'quantity' => 1,
            'extraServices' => []
        ]
    ],
    'coupon' => 'STAY20',
    'check_in' => '2026-09-27',
    'check_out' => '2026-09-30',
    'nights' => 3,
    'total' => 10800
]);

$initReq = Request::create('/rooms/checkout-init', 'POST', ['booking_data' => $bookingData]);
$redirectResponse = $controller->checkoutInit($initReq);
$targetUrl = $redirectResponse->getTargetUrl();
echo "   - Redirect Target URL: {$targetUrl}\n";
assert(strpos($targetUrl, 'check_in=2026-09-27') !== false, "Target URL must contain check_in");
assert(strpos($targetUrl, 'check_out=2026-09-30') !== false, "Target URL must contain check_out");
assert(strpos($targetUrl, 'coupon=STAY20') !== false, "Target URL must contain coupon code");
echo "   - Parameter Forwarding: PASS\n\n";

// 3. Test /rooms/3/checkout calculation for 3 nights
echo "3. Testing /rooms/3/checkout calculation breakdown:\n";
$checkoutReq = Request::create('/rooms/3/checkout?check_in=2026-09-27&check_out=2026-09-30&quantity=1&coupon=STAY20', 'GET');
$checkoutView = $controller->checkout($checkoutReq, 3);
$calc = $checkoutView->getData()['calc'];

$expectedBase = (float)($room3->price_per_day ?: $room3->base_price ?: 0);
$expectedRoomDiscount = $expectedBase * ((float)($room3->discount ?? 0) / 100) * 3;
$expectedRoomSubtotal = ($expectedBase * 3) - $expectedRoomDiscount;
$expectedCouponDiscount = $expectedRoomSubtotal * 0.20;
$expectedTotalPayable = $expectedRoomSubtotal - $expectedCouponDiscount;

echo "   - Base Price per Night: ₹" . number_format($calc['base_price'], 2) . "\n";
echo "   - Nights: " . $calc['nights'] . " (Expected: 3)\n";
echo "   - Room Original Total: ₹" . number_format($calc['room_original_total'], 2) . "\n";
echo "   - Room Discount Total: ₹" . number_format($calc['room_discount_amount'], 2) . "\n";
echo "   - Room Subtotal: ₹" . number_format($calc['room_total'], 2) . " (Expected: ₹" . number_format($expectedRoomSubtotal, 2) . ")\n";
echo "   - Coupon Applied: " . $calc['coupon_code'] . "\n";
echo "   - Coupon Discount (20%): ₹" . number_format($calc['coupon_discount'], 2) . " (Expected: ₹" . number_format($expectedCouponDiscount, 2) . ")\n";
echo "   - Total Discount: ₹" . number_format($calc['total_discount'], 2) . "\n";
echo "   - Total Payable: ₹" . number_format($calc['total_payable'], 2) . " (Expected: ₹" . number_format($expectedTotalPayable, 2) . ")\n";

assert($calc['nights'] == 3, "Nights must be 3");
assert(abs($calc['room_total'] - $expectedRoomSubtotal) < 0.01, "Room subtotal must match");
assert(abs($calc['coupon_discount'] - $expectedCouponDiscount) < 0.01, "Coupon discount must match");
assert(abs($calc['total_payable'] - $expectedTotalPayable) < 0.01, "Total payable must match");
echo "   -> PASS: Exact stay calculation verified 100%!\n\n";

// 4. Test Removing Coupon on /rooms/3/checkout
echo "4. Testing /rooms/3/checkout without coupon (Removed):\n";
$noCouponReq = Request::create('/rooms/3/checkout?check_in=2026-09-27&check_out=2026-09-30&quantity=1&coupon=none', 'GET');
$noCouponView = $controller->checkout($noCouponReq, 3);
$noCalc = $noCouponView->getData()['calc'];

echo "   - Coupon Code: " . var_export($noCalc['coupon_code'], true) . "\n";
echo "   - Coupon Discount: ₹" . number_format($noCalc['coupon_discount'], 2) . "\n";
echo "   - Total Payable: ₹" . number_format($noCalc['total_payable'], 2) . " (Expected: ₹" . number_format($expectedRoomSubtotal, 2) . ")\n";
assert(is_null($noCalc['coupon_code']), "Coupon code must be null when removed");
assert($noCalc['coupon_discount'] == 0, "Coupon discount must be 0");
assert(abs($noCalc['total_payable'] - $expectedRoomSubtotal) < 0.01, "Total payable must match subtotal");
echo "   -> PASS: Coupon removal verified 100%!\n\n";

echo "=== ALL ROOM 3 SEARCH & CHECKOUT TESTS PASSED ===\n";
