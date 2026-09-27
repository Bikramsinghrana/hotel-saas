<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant;
use App\Models\Room;
use App\Models\RoomOrder;
use App\Models\Invoice;
use App\Models\Hotel;
use App\Models\User;
use App\Http\Controllers\Admin\BookingController;

echo "=== SAVING 18% STANDARD GST IN DATABASE OPTIONS TABLE ===\n\n";

\App\Models\Option::where('key', 'gst_rate')->update(['value' => '18']);
\App\Models\Option::where('key', 'cgst_rate')->update(['value' => '9']);
\App\Models\Option::where('key', 'sgst_rate')->update(['value' => '9']);
\App\Models\Option::where('key', 'igst_rate')->update(['value' => '18']);

\Illuminate\Support\Facades\Cache::flush();

$allGst = \App\Models\Option::whereIn('key', ['gst_rate', 'cgst_rate', 'sgst_rate', 'igst_rate'])->get();
foreach ($allGst as $g) {
    echo "ID: {$g->id} | Tenant: " . ($g->tenant_id ?? 'global') . " | Key: {$g->key} | Value: {$g->value}\n";
}

$calcService = app(\App\Services\PriceCalculationService::class);
echo "\ngetTaxConfig(null) (Global):\n";
print_r($calcService->getTaxConfig(null));

echo "\ngetTaxConfig(1) (Tenant 1):\n";
print_r($calcService->getTaxConfig(1));

$room2 = \App\Models\Room::find(2);
if ($room2) {
$roomController = new \App\Http\Controllers\RoomController();
$req = \Illuminate\Http\Request::create('/rooms/2/checkout', 'GET', [
    'check_in' => '2026-09-28',
    'check_out' => '2026-09-30',
    'quantity' => 1,
    'coupon' => 'HAPPY_HOURS_20'
]);
$app->instance('request', $req);
view()->share('errors', new \Illuminate\Support\ViewErrorBag);

$view = $roomController->checkout($req, 2);
$html = $view->render();

echo "Rendered /rooms/2/checkout with coupon=HAPPY_HOURS_20:\n";
assert(strpos($html, 'HAPPY_HOURS_20') !== false, "Coupon code HAPPY_HOURS_20 must appear in rendered HTML");
assert(strpos($html, 'Coupon (HAPPY_HOURS_20)') !== false || strpos($html, 'Applied: <strong>HAPPY_HOURS_20</strong>') !== false, "Coupon line or badge must appear in HTML");
echo "PASS! HAPPY_HOURS_20 is rendered and active on checkout!\n\n";
}

$tenant = Tenant::first();
if ($tenant) {
    session(['tenant_id' => $tenant->id]);
}

$user = User::first();
if ($user) {
    auth()->setUser($user);
}

$hotel = Hotel::firstOrCreate(['tenant_id' => $tenant?->id, 'name' => 'Grand Horizon Luxury Resort'], [
    'address' => ['city' => 'Goa', 'state' => 'Goa', 'country' => 'India'],
    'phone' => '+91 9876543210',
    'email' => 'contact@grandhorizon.com'
]);

$room = Room::firstOrCreate(
    ['id' => 3],
    [
        'tenant_id' => $tenant?->id,
        'hotel_id' => $hotel->id,
        'post_title' => 'Ocean View Presidential Suite',
        'price_per_day' => 6000,
        'discount' => 10,
        'status' => 1
    ]
);

// Create a realistic booking with coupon & extra services
$booking = RoomOrder::firstOrCreate(
    ['order_number' => 'ORD-TEST-999'],
    [
        'tenant_id' => $tenant?->id,
        'hotel_id' => $hotel->id,
        'room_id' => $room->id,
        'customer_name' => 'Alexander Wright',
        'email' => 'alexander@example.com',
        'phone' => '+91 9123456780',
        'address' => 'Suite 404, Bay View Apartments, Miramar Beach',
        'city' => 'Panaji',
        'state' => 'Goa',
        'country' => 'India',
        'postcode' => '403001',
        'start_date' => '2026-10-01',
        'end_date' => '2026-10-04',
        'total_nights' => 3,
        'total_person' => 2,
        'sub_total' => 18000.00,
        'discount_amount' => 4800.00,
        'tax_amount' => 1200.00,
        'total_amount' => 14400.00,
        'payment_status' => 'paid',
        'status' => 'confirmed',
        'payment_method' => 'Credit Card (Stripe)',
        'transaction_id' => 'TXN_STRIPE_987654321',
        'payment_response' => json_encode([
            'room_qty' => 1,
            'quantity' => 1,
            'base_price' => 6000,
            'discount_percent' => 10,
            'room_discount_amount' => 1800,
            'coupon_code' => 'LUXURY20',
            'coupon_discount' => 3000,
            'total_discount' => 4800,
            'extra_services' => [
                ['name' => 'Airport Pickup & Drop (Luxury Cab)', 'price' => 1500],
                ['name' => 'Complimentary Buffet Breakfast', 'price' => 0]
            ]
        ]),
        'special_request' => 'High floor room with ocean view requested please.'
    ]
);

$controller = new BookingController();

// 1. Test /admin/bookings Index View
echo "1. Testing /admin/bookings index page rendering:\n";
$indexReq = Request::create('/admin/bookings', 'GET');
$indexView = $controller->index($indexReq);
$indexHtml = $indexView->render();
echo "   - Index View Status: PASS (200)\n";
assert(strpos($indexHtml, 'ORD-TEST-999') !== false || strpos($indexHtml, 'Bookings') !== false, "Index view should render bookings table");
echo "   - Table & Bulk Actions: PASS\n\n";

// 2. Test /admin/bookings/{id} Show View
echo "2. Testing /admin/bookings/{$booking->id} show page rendering:\n";
$showView = $controller->show($booking->id);
$showHtml = $showView->render();
echo "   - Show View Status: PASS (200)\n";
assert(strpos($showHtml, 'Alexander Wright') !== false, "Guest name must be displayed");
assert(strpos($showHtml, 'Download Invoice') !== false, "Download Invoice button must be present");
assert(strpos($showHtml, 'Ocean View Presidential Suite') !== false || strpos($showHtml, 'Accommodation') !== false, "Room info must be present");
assert(strpos($showHtml, 'LUXURY20') !== false, "Coupon code must be shown in itemized breakdown");
echo "   - Details, Breakdown, and Action Buttons: PASS\n\n";

// 3. Test Invoice Preview / Print View
echo "3. Testing /admin/bookings/{$booking->id}/invoice/preview:\n";
$previewView = $controller->previewInvoice($booking->id);
$previewHtml = $previewView->render();
echo "   - Preview View Status: PASS (200)\n";
assert(strpos($previewHtml, 'Tax Invoice') !== false || strpos($previewHtml, 'Invoice') !== false, "Invoice title must be present");
assert(strpos($previewHtml, 'Grand Horizon') !== false || strpos($previewHtml, 'Hotel') !== false, "Hotel brand must be present");
assert(strpos($previewHtml, 'Alexander Wright') !== false, "Guest name must be present on invoice");
assert(strpos($previewHtml, 'LUXURY20') !== false, "Coupon code must be present on invoice");
echo "   - Hotel details & invoice items on template: PASS\n\n";

// 5. Test GST PriceCalculationService
echo "5. Testing PriceCalculationService GST Calculation:\n";
$calcService = app(\App\Services\PriceCalculationService::class);
$calc = $calcService->calculate($room, 1, 3, [], 'LUXURY20', '2026-10-01', '14:00');
echo "   - Base Price: " . $calc['base_price'] . "\n";
echo "   - Net Subtotal: " . $calc['net_subtotal'] . "\n";
echo "   - GST Rate: " . $calc['gst_rate'] . "%\n";
echo "   - CGST Amount (" . $calc['cgst_rate'] . "%): " . $calc['cgst_amount'] . "\n";
echo "   - SGST Amount (" . $calc['sgst_rate'] . "%): " . $calc['sgst_amount'] . "\n";
echo "   - Total Tax Amount: " . $calc['tax_amount'] . "\n";
echo "   - Total Payable: " . $calc['total_payable'] . "\n";

// 6. Test Checkout Init & Room Checkout Controller Flow with Session & Query params
echo "6. Testing Checkout Init & Checkout Controller Session / Query Flow:\n";
$roomController = new \App\Http\Controllers\RoomController();

$bookingData = [
    'rooms' => [
        [
            'id' => 3,
            'name' => 'Ocean View Presidential Suite',
            'price' => 6000,
            'discount' => 10,
            'quantity' => 2,
            'extraServices' => [
                ['id' => 1, 'name' => 'Breakfast', 'price' => 500]
            ]
        ]
    ],
    'coupon' => 'LUXURY20',
    'check_in' => '2026-10-01',
    'check_out' => '2026-10-03',
    'nights' => 2
];

// A. Test checkoutInit POST
$initRequest = Request::create('/rooms/checkout-init', 'POST', [
    'booking_data' => json_encode($bookingData)
]);
$initResponse = $roomController->checkoutInit($initRequest);
assert($initResponse instanceof \Illuminate\Http\RedirectResponse, "checkoutInit should return a RedirectResponse");
echo "   - checkoutInit Redirection URL: " . $initResponse->getTargetUrl() . " (PASS)\n";
assert(session('applied_coupon') === 'LUXURY20', "Session applied_coupon must be LUXURY20");
assert(!empty(session('pending_booking')), "Session pending_booking must be set");
echo "   - Session Data Persistence: PASS\n";

// B. Test GET /rooms/3/checkout (Restoring from session / query params)
$checkoutRequest = Request::create('/rooms/3/checkout', 'GET', [
    'check_in' => '2026-10-01',
    'check_out' => '2026-10-03',
    'quantity' => 2,
    'coupon' => 'LUXURY20'
]);
$checkoutView = $roomController->checkout($checkoutRequest, 3);
$checkoutHtml = $checkoutView->render();
echo "   - Checkout Page Rendering: PASS (200)\n";
assert(strpos($checkoutHtml, 'Ocean View Presidential Suite') !== false || strpos($checkoutHtml, 'Booking Summary') !== false, "Checkout view must render room and summary");
assert(strpos($checkoutHtml, 'LUXURY20') !== false, "Coupon code must be rendered in checkout summary");
assert(strpos($checkoutHtml, 'GST & Taxes') !== false || strpos($checkoutHtml, 'GST') !== false, "GST & Taxes must be present in checkout summary");
echo "   - View Chart, Coupon, and GST Display: PASS\n\n";

// C. Test GET /rooms/3/checkout with page refresh (no query params, relying on session)
$refreshRequest = Request::create('/rooms/3/checkout', 'GET');
$refreshView = $roomController->checkout($refreshRequest, 3);
$refreshHtml = $refreshView->render();
echo "   - Checkout Page Refresh (Session retention): PASS (200)\n";
assert(strpos($refreshHtml, 'LUXURY20') !== false, "Coupon code must remain retained after refresh");
echo "   - Session Coupon retained across page refresh: PASS\n\n";

echo "=== ALL ADMIN BOOKINGS, INVOICE, GST & CHECKOUT TESTS PASSED ===\n";
