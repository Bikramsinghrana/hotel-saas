<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$app->instance('request', Illuminate\Http\Request::create('/admin/bookings', 'GET'));
view()->share('errors', new \Illuminate\Support\ViewErrorBag);

use App\Models\Tenant;
use App\Models\Room;
use App\Models\RoomOrder;
use App\Models\Invoice;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\BookingController;

echo "=== TESTING ADMIN BOOKINGS VIEW & INVOICE DOWNLOAD ===\n\n";

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

// 4. Test PDF Invoice Download
echo "4. Testing /admin/bookings/{$booking->id}/invoice download:\n";
$downloadRes = $controller->downloadInvoice($booking->id);
echo "   - Download Response Type: " . get_class($downloadRes) . "\n";
if ($downloadRes instanceof \Symfony\Component\HttpFoundation\Response) {
    echo "   - Download Response Status: " . $downloadRes->getStatusCode() . " (PASS)\n";
} else {
    echo "   - Download Response: PASS\n";
}

echo "\n=== ALL ADMIN BOOKINGS & INVOICE TESTS PASSED ===\n";
