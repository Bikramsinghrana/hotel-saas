<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\Option;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;

echo "=== TESTING DYNAMIC OPTIONS ADMIN MODULE ===\n\n";

$app->instance('request', Request::create('/admin/options', 'GET'));

$adminUser = User::first();
if (!$adminUser) {
    echo "ERROR: No user found\n";
    exit(1);
}
auth()->setUser($adminUser);
$tenant = Tenant::first();
if ($tenant) {
    session(['tenant_id' => $tenant->id]);
}


$controller = app(\App\Http\Controllers\Admin\OptionController::class);

// 1. Test Index View (All Settings)
echo "1. Testing GET admin/options (All Settings):\n";
$reqIndex = Request::create('/admin/options', 'GET');
$viewIndex = $controller->index($reqIndex);
$renderedIndex = $viewIndex->render();
echo "   Status: 200 OK (Rendered length: " . strlen($renderedIndex) . " bytes)\n";
assert(strpos($renderedIndex, 'Dynamic Options') !== false, "Index view should render page title");
assert(strpos($renderedIndex, 'Tax & GST System') !== false, "Index view should contain Tax & GST category");
echo "   PASS: Index view rendered successfully!\n";

// 2. Test Category Filtering
echo "\n2. Testing GET admin/options?group=tax_gst:\n";
$reqTax = Request::create('/admin/options', 'GET', ['group' => 'tax_gst']);
$viewTax = $controller->index($reqTax);
$renderedTax = $viewTax->render();
assert(strpos($renderedTax, 'gst_rate') !== false, "Tax view should contain gst_rate");
echo "   PASS: Group filtering works correctly!\n";

// 3. Test Batch Update
echo "\n3. Testing POST admin/options/batch-update:\n";
$reqBatch = Request::create('/admin/options/batch-update', 'POST', [
    'tenant_id' => $tenant ? $tenant->id : null,
    'options' => [
        'gst_rate' => 18.5,
        'pagination_limit' => 16,
        'allow_guest_checkout' => 1
    ]
]);
$resBatch = $controller->batchUpdate($reqBatch);
echo "   Batch update status: " . $resBatch->getStatusCode() . "\n";
echo "   Verified GST Rate via option(): " . option('gst_rate') . "%\n";
echo "   Verified Pagination via option(): " . option('pagination_limit') . "\n";
assert((float)option('gst_rate') === 18.5, "GST Rate should be 18.5");
assert((int)option('pagination_limit') === 16, "Pagination limit should be 16");
echo "   PASS: Batch update and cache invalidation working!\n";

// 4. Test Single AJAX Update
echo "\n4. Testing POST admin/options/update-single (AJAX):\n";
$reqSingle = Request::create('/admin/options/update-single', 'POST', [
    'key' => 'free_cancellation_hours',
    'value' => '72'
]);
$resSingle = $controller->updateSingle($reqSingle);
$jsonSingle = $resSingle->getData(true);
echo "   Response: " . json_encode($jsonSingle) . "\n";
assert($jsonSingle['success'] === true, "Update single should succeed");
echo "   Verified Cancellation Hours via option(): " . option('free_cancellation_hours') . " hours\n";
echo "   PASS: Single option update working!\n";

// 5. Test Creating Brand New Custom Option on the fly
echo "\n5. Testing POST admin/options/store (Create New Option):\n";
$customKey = 'swimming_pool_timings_' . rand(100, 999);
$reqStore = Request::create('/admin/options/store', 'POST', [
    'key' => $customKey,
    'label' => 'Pool Timings',
    'group' => 'hotel',
    'type' => 'string',
    'value' => '06:00 AM - 10:00 PM',
    'description' => 'Daily operating hours for the main infinity pool.',
    'is_autoload' => 1,
    'is_public' => 1
]);
$resStore = $controller->store($reqStore);
echo "   Store status: " . $resStore->getStatusCode() . "\n";
$createdOpt = Option::where('key', $customKey)->first();
assert($createdOpt !== null, "Custom option should exist in database");
echo "   Created Option ID: {$createdOpt->id}, Key: {$createdOpt->key}, Value: " . option($customKey) . "\n";
echo "   PASS: Custom option created successfully!\n";

// 6. Test Deleting Custom Option
echo "\n6. Testing DELETE admin/options/{$createdOpt->id}:\n";
$resDelete = $controller->destroy($createdOpt->id);
echo "   Delete status: " . $resDelete->getStatusCode() . "\n";
$deletedOpt = Option::find($createdOpt->id);
assert($deletedOpt === null, "Option should be soft deleted");
echo "   PASS: Option deleted successfully!\n";

echo "\n=== ALL DYNAMIC OPTIONS MODULE TESTS COMPLETED SUCCESSFULLY! ===\n";
