<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;

$tenants = Tenant::with(['theme', 'subTheme'])->get();
foreach ($tenants as $t) {
    echo "Tenant ID: {$t->id} | Name: {$t->name} | Domain: {$t->domain} | Theme ID: {$t->theme_id} (" . ($t->theme?->name ?? 'None') . " [{$t->theme?->key}]) | SubTheme: " . ($t->subTheme?->name ?? 'None') . "\n";
}
