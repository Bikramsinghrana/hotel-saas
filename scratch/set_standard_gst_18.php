<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Option;
use App\Services\PriceCalculationService;
use Illuminate\Support\Facades\Cache;

echo "--- Current GST Options in DB ---\n";
$opts = Option::whereIn('key', ['gst_rate', 'cgst_rate', 'sgst_rate', 'igst_rate', 'enable_gst'])->get();
foreach ($opts as $o) {
    echo "ID: {$o->id} | Tenant: " . ($o->tenant_id ?? 'NULL') . " | Key: {$o->key} | Value: {$o->value}\n";
}

// Update or create standard 18% GST in database options table
Option::updateOrCreate(['key' => 'gst_rate', 'tenant_id' => null], ['value' => '18']);
Option::updateOrCreate(['key' => 'cgst_rate', 'tenant_id' => null], ['value' => '9']);
Option::updateOrCreate(['key' => 'sgst_rate', 'tenant_id' => null], ['value' => '9']);
Option::updateOrCreate(['key' => 'igst_rate', 'tenant_id' => null], ['value' => '18']);
Option::updateOrCreate(['key' => 'enable_gst', 'tenant_id' => null], ['value' => '1']);

// Update all existing tenant overrides to standard 18% (9% CGST + 9% SGST)
Option::where('key', 'gst_rate')->update(['value' => '18']);
Option::where('key', 'cgst_rate')->update(['value' => '9']);
Option::where('key', 'sgst_rate')->update(['value' => '9']);
Option::where('key', 'igst_rate')->update(['value' => '18']);
Option::where('key', 'enable_gst')->update(['value' => '1']);

Cache::flush();

echo "\n--- Updated GST Options in DB ---\n";
$opts = Option::whereIn('key', ['gst_rate', 'cgst_rate', 'sgst_rate', 'igst_rate', 'enable_gst'])->get();
foreach ($opts as $o) {
    echo "ID: {$o->id} | Tenant: " . ($o->tenant_id ?? 'NULL') . " | Key: {$o->key} | Value: {$o->value}\n";
}

$room = \App\Models\Room::find(2);
if ($room) {
    $service = app(PriceCalculationService::class);
    $calc = $service->calculate($room, '2026-09-28', '2026-09-30', 1, 'HAPPY_HOURS_20');
    echo json_encode($calc, JSON_PRETTY_PRINT) . "\n";
}
