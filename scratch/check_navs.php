<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Navigation;
use App\Models\Tenant;

echo "=== TENANTS ===\n";
foreach (Tenant::all() as $t) {
    echo "Tenant {$t->id}: {$t->name} (theme_id: {$t->theme_id}, sub_theme_id: {$t->sub_theme_id})\n";
}

echo "\n=== ALL NAVIGATIONS IN DB ===\n";
foreach (Navigation::all() as $n) {
    echo "ID: {$n->id} | Tenant ID: {$n->tenant_id} | Title: {$n->title} | URL: {$n->url}\n";
}
