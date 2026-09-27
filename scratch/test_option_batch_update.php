<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Option;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

$user = User::first();
if ($user) {
    Auth::login($user);
}

// Test batchUpdate with gst_rate = 18
$request = Illuminate\Http\Request::create('/admin/options/batch-update', 'POST', [
    'options' => [
        'gst_rate' => '18',
        'tax_enabled' => '1',
        'tax_calculation_type' => 'exclusive',
    ]
]);

$controller = app(\App\Http\Controllers\Admin\OptionController::class);
$response = $controller->batchUpdate($request);

echo "Batch update executed.\n";

$rates = Option::whereIn('key', ['gst_rate', 'cgst_rate', 'sgst_rate', 'igst_rate'])->get(['key', 'value', 'tenant_id']);
foreach ($rates as $r) {
    echo "Key: {$r->key} | Value: {$r->value} | Tenant: " . ($r->tenant_id ?? 'GLOBAL') . "\n";
}
