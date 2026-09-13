<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$urls = [
    '/hotel',
    '/about',
    '/contact',
    '/hotel/about',
    '/hotel/contact',
    '/rooms/index',
    '/rooms/1/checkout',
];

echo "=== TESTING HOTEL PAGES & CHECKOUT ===\n";
$allPassed = true;

foreach ($urls as $url) {
    $request = Illuminate\Http\Request::create($url, 'GET');
    $response = $kernel->handle($request);
    $status = $response->getStatusCode();
    
    if ($status === 200) {
        echo "[PASS] $url -> Status: $status (Length: " . strlen($response->getContent()) . " bytes)\n";
    } else {
        echo "[FAIL] $url -> Status: $status\n";
        echo "       Snippet: " . substr(strip_tags($response->getContent()), 0, 300) . "\n";
        $allPassed = false;
    }
}

if ($allPassed) {
    echo "\n>>> ALL PAGES RETURNED HTTP 200 OK! <<<\n";
    exit(0);
} else {
    echo "\n>>> SOME PAGES FAILED! <<<\n";
    exit(1);
}
