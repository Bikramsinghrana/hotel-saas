<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$kernelHttp = $app->make(Illuminate\Contracts\Http\Kernel::class);

// 1. Test /rooms/index
$req1 = Illuminate\Http\Request::create('/rooms/index?check_in=2026-09-28&check_out=2026-09-30&quantity=1', 'GET');
$res1 = $kernelHttp->handle($req1);
echo "1. /rooms/index status: " . $res1->getStatusCode() . "\n";

// 2. Test /rooms/2/checkout
$req2 = Illuminate\Http\Request::create('/rooms/2/checkout?check_in=2026-09-28&check_out=2026-09-30&quantity=1&coupon=HAPPY_HOURS_20', 'GET');
$res2 = $kernelHttp->handle($req2);
echo "2. /rooms/2/checkout status: " . $res2->getStatusCode() . "\n";
$content = $res2->getContent();
if (strpos($content, 'Central GST (CGST') !== false && strpos($content, 'State GST (SGST') !== false) {
    echo ">> Found individual Central GST (CGST) and State GST (SGST) in checkout view chart!\n";
} else {
    echo ">> Warning: GST labels not found in content!\n";
}
