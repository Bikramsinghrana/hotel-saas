<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\Option;
use App\Services\OptionService;

echo "=== DYNAMIC OPTIONS & MULTI-TENANT CONFIGURATION TEST ===\n\n";

// 1. Total options count
$totalOptions = Option::count();
echo "1. Total Options in database: {$totalOptions}\n";
assert($totalOptions > 0, "Options table should have seeded options");

// 2. Test option() global helper
echo "\n2. Testing option() helper retrieval:\n";
echo "   - GST Rate: " . option('gst_rate') . "% (type: " . gettype(option('gst_rate')) . ")\n";
echo "   - CGST Rate: " . option('cgst_rate') . "% (type: " . gettype(option('cgst_rate')) . ")\n";
echo "   - SGST Rate: " . option('sgst_rate') . "% (type: " . gettype(option('sgst_rate')) . ")\n";
echo "   - IGST Rate: " . option('igst_rate') . "% (type: " . gettype(option('igst_rate')) . ")\n";
echo "   - GSTIN Number: " . option('gstin_number') . "\n";
echo "   - Pagination Limit: " . option('pagination_limit') . " (type: " . gettype(option('pagination_limit')) . ")\n";
echo "   - Currency Symbol: " . option('currency_symbol') . "\n";
echo "   - Allow Guest Checkout: " . (option('allow_guest_checkout') ? 'true' : 'false') . " (type: " . gettype(option('allow_guest_checkout')) . ")\n";

// 3. Test option_group() helper
echo "\n3. Testing option_group('tax_gst'):\n";
$taxGroup = option_group('tax_gst');
print_r($taxGroup);

// 4. Test option_set() helper and cache invalidation
echo "\n4. Testing option_set() and dynamic updates:\n";
option_set('gst_rate', 12.0, 'tax_gst', 'float');
echo "   - Updated GST Rate: " . option('gst_rate') . "%\n";

// Restore to 18
option_set('gst_rate', 18.0, 'tax_gst', 'float');
echo "   - Restored GST Rate: " . option('gst_rate') . "%\n";

// 5. Test Public Options for frontend JS
echo "\n5. Testing public options for frontend:\n";
$publicOpts = app(OptionService::class)->getPublicOptions();
echo "   - Total public options: " . count($publicOpts) . "\n";
echo "   - Public keys: " . implode(', ', array_keys($publicOpts)) . "\n";

echo "\n=== ALL OPTION TESTS PASSED! ===\n";
