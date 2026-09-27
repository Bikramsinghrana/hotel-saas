<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Option;

$all = Option::all(['id', 'tenant_id', 'hotel_id', 'group', 'key', 'label', 'value', 'type', 'is_system']);
foreach ($all as $o) {
    echo "ID: " . str_pad($o->id, 3) . " | Grp: " . str_pad($o->group ?? '', 12) . " | Key: " . str_pad($o->key, 25) . " | Val: " . str_pad($o->value ?? '', 15) . " | Label: {$o->label} (Tenant: " . ($o->tenant_id ?? 'GLOBAL') . ")\n";
}
