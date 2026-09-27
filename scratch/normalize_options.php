<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Option;
use Illuminate\Support\Facades\Cache;

Option::where('group', 'booking')->update(['group' => 'hotel']);
Option::whereIn('key', ['gst_rate', 'cgst_rate', 'sgst_rate', 'igst_rate', 'tax_enabled', 'currency_symbol', 'currency_code'])->update(['is_public' => true]);

Cache::flush();
echo "Updated options database groups and public flags successfully.\n";
