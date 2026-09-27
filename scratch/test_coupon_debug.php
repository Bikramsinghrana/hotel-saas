<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\Coupon;
use App\Services\CouponService;

$c = Coupon::where('code', 'FESTIVAL500')->first();
echo "Coupon found: " . ($c ? 'YES (ID: ' . $c->id . ', status: ' . ($c->status ? '1' : '0') . ', start: ' . $c->start_date . ', end: ' . $c->expire_date . ', tenant: ' . $c->tenant_id . ', hotel: ' . $c->hotel_id . ')' : 'NO') . "\n";
echo "isActive: " . ($c && $c->isActive() ? 'YES' : 'NO') . "\n";

$svc = new CouponService();
$res = $svc->validate('FESTIVAL500');
echo "Validate result: " . json_encode($res) . "\n";
