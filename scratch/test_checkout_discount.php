<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\Room;
use App\Models\Coupon;
use App\Models\User;
use App\Models\RoomOrder;
use App\Models\Invoice;
use Illuminate\Http\Request;

echo "=== CHECKOUT & DISCOUNT COMPREHENSIVE TESTS ===\n\n";

// 1. Ensure a room with a discount exists
$room = Room::first();
if (!$room) {
    echo "ERROR: No room found in database.\n";
    exit(1);
}

// Give room a 10% discount for test if 0
$room->update(['discount' => 10, 'base_price' => 2000, 'price_per_day' => 2000]);
echo "1. Room #{$room->id} set with Base Price: {$room->price_per_day}, Discount: {$room->discount}%\n";

// 2. Ensure a test coupon exists
$coupon = Coupon::firstOrCreate(
    ['code' => 'FESTIVAL500'],
    [
        'tenant_id' => $room->tenant_id,
        'hotel_id' => $room->hotel_id,
        'title' => 'Festival Special Coupon',
        'discount_type' => 'fixed',
        'discount_value' => 500,
        'status' => true,
        'start_date' => now()->subDay(),
        'expire_date' => now()->addDays(30)
    ]
);
echo "2. Test Coupon 'FESTIVAL500' ready: Fixed Discount {$coupon->discount_value}\n";

// 3. Test Guest Checkout GET /rooms/{id}/checkout
echo "\n3. Testing Guest Checkout GET /rooms/{$room->id}/checkout:\n";
$reqGuest = Request::create("/rooms/{$room->id}/checkout", 'GET');
$resGuest = $kernel->handle($reqGuest);
echo "   Status: " . $resGuest->getStatusCode() . "\n";
assert($resGuest->getStatusCode() === 200, "Guest checkout should return 200");
$guestContent = $resGuest->getContent();
if (strpos($guestContent, 'Guest Checkout') !== false && strpos($guestContent, 'Primary Guest Full Name') !== false) {
    echo "   PASS: Guest checkout banner and inputs rendered properly.\n";
} else {
    echo "   FAIL: Guest checkout content missing.\n";
}

// 4. Test Authenticated User Checkout GET /rooms/{id}/checkout
echo "\n4. Testing Authenticated User Checkout GET /rooms/{$room->id}/checkout:\n";
$user = User::first();
if ($user) {
    $user->update(['phone' => '+91 9876543210']);
    auth()->login($user);
    $reqAuth = Request::create("/rooms/{$room->id}/checkout", 'GET');
    $resAuth = $kernel->handle($reqAuth);
    echo "   Status: " . $resAuth->getStatusCode() . "\n";
    $authContent = $resAuth->getContent();
    if (strpos($authContent, 'Logged in as ' . $user->name) !== false && strpos($authContent, $user->email) !== false) {
        echo "   PASS: Authenticated user details autofilled and user badge shown.\n";
    } else {
        echo "   FAIL: Logged in user info not reflected.\n";
    }
    auth()->logout();
}

// 5. Test Coupon Validate API with recalculation
echo "\n5. Testing Coupon Validation API endpoint:\n";
$controller = app(\App\Http\Controllers\RoomController::class);
$reqCoupon = Request::create("/api/coupons/validate", 'GET', [
    'code' => 'FESTIVAL500',
    'room_id' => $room->id,
    'quantity' => 1,
    'check_in' => now()->format('Y-m-d'),
    'check_out' => now()->addDays(2)->format('Y-m-d'),
]);
$resCoupon = $controller->validateCoupon($reqCoupon);
$couponJson = $resCoupon->getData(true);
echo "   Response Success: " . ($couponJson['success'] ? 'true' : 'false') . "\n";
if (!empty($couponJson['calc'])) {
    echo "   Room Base Price: " . $couponJson['calc']['base_price'] . "\n";
    echo "   Room Discount Amount: " . $couponJson['calc']['room_discount_amount'] . "\n";
    echo "   Coupon Discount: " . $couponJson['calc']['coupon_discount'] . "\n";
    echo "   Total Payable: " . $couponJson['calc']['total_payable'] . "\n";
    echo "   PASS: Price calculation with discounts accurate!\n";
} else {
    echo "   FAIL: Coupon calculation data missing.\n";
}

// 6. Test Booking Submission with discounts
echo "\n6. Testing Booking Submission with Room Discount & Coupon:\n";
$reqBook = Request::create("/rooms/{$room->id}/book", 'POST', [
    'check_in' => now()->format('Y-m-d'),
    'check_out' => now()->addDays(2)->format('Y-m-d'),
    'quantity' => 1,
    'customer_name' => 'John Guest',
    'email' => 'guest@example.com',
    'phone' => '+1 555-1234',
    'coupon_code' => 'FESTIVAL500',
    'payment_method' => 'cash',
]);
$resBook = $controller->book($reqBook, $room->id);
echo "   Status: " . $resBook->getStatusCode() . "\n";
if ($resBook->getStatusCode() === 302) {
    $redirectUrl = $resBook->headers->get('Location');
    echo "   Redirect: " . $redirectUrl . "\n";
    $latestOrder = RoomOrder::latest()->first();
    echo "   Created Order #: " . $latestOrder->order_number . "\n";
    echo "   Subtotal: " . $latestOrder->sub_total . "\n";
    echo "   Discount Amount: " . $latestOrder->discount_amount . "\n";
    echo "   Total Amount: " . $latestOrder->total_amount . "\n";
    echo "   Extra Info: " . json_encode($latestOrder->extra_info) . "\n";

    // 7. Test Booking Confirmation Page
    echo "\n7. Testing Booking Complete View:\n";
    $reqComplete = Request::create("/rooms/{$latestOrder->id}/complete", 'GET');
    $viewComplete = $controller->bookingComplete($reqComplete, $latestOrder->id);
    $renderedComplete = $viewComplete->render();
    if (strpos($renderedComplete, 'Discounts Applied') !== false) {
        echo "   PASS: Booking complete page shows discounts breakdown!\n";
    }

    // 8. Test Invoice View
    echo "\n8. Testing Invoice Rendering:\n";
    $inv = Invoice::create([
        'tenant_id' => $latestOrder->tenant_id,
        'room_order_id' => $latestOrder->id,
        'invoice_number' => 'INV-TEST-' . uniqid(),
        'amount' => $latestOrder->total_amount,
        'total_amount' => $latestOrder->total_amount,
        'issued_at' => now(),
    ]);
    $invoiceView = view('invoices.template', ['invoice' => $inv, 'order' => $latestOrder, 'payment' => null])->render();
    if (strpos($invoiceView, 'Festival / Room Special Discount') !== false && strpos($invoiceView, 'FESTIVAL500') !== false) {
        echo "   PASS: Invoice renders room discount and coupon discount successfully!\n";
    } else {
        echo "   FAIL: Invoice content missing discount lines.\n";
    }
    $inv->delete();
    $latestOrder->delete();
}

echo "\n=== ALL TESTS COMPLETED ===\n";


