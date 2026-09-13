<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

file_put_contents(storage_path('logs/laravel.log'), '');

echo "=== 1. TESTING HOTEL (DOMAIN: management-saas) URLS ===\n";

$hotelUrls = [
    '/',
    '/hotel',
    '/about',
    '/contact',
    '/hotel/about',
    '/hotel/contact',
    '/rooms/index',
    '/rooms/1/checkout',
    '/login',
];

foreach ($hotelUrls as $url) {
    $request = Illuminate\Http\Request::create("http://management-saas" . $url, 'GET');
    $response = $kernel->handle($request);
    $status = $response->getStatusCode();
    echo "[HTTP $status] $url\n";
}

echo "\n=== 2. TESTING RESTAURANT (DOMAIN: resto.localhost) URLS ===\n";

$restoUrls = [
    '/',
    '/resto',
    '/restaurant',
    '/about',
    '/contact',
];

foreach ($restoUrls as $url) {
    $request = Illuminate\Http\Request::create("http://resto.localhost" . $url, 'GET');
    $response = $kernel->handle($request);
    $status = $response->getStatusCode();
    echo "[HTTP $status] $url\n";
}

$logContent = file_get_contents(storage_path('logs/laravel.log'));
echo "\n=== LOG CONTENT CHECK ===\n";
if (empty(trim($logContent))) {
    echo "SUCCESS: Log file is completely clean with 0 errors!\n";
} else {
    echo "Log output:\n" . $logContent . "\n";
}
