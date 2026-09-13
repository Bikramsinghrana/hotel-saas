<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$previewUrls = [
    '/resto/fast_food',
    '/resto/fine_dining',
    '/resto/cafe',
    '/hotel/luxury',
    '/hotel/budget',
    '/themes/restaurant/fast_food',
    '/themes/hotel/luxury',
];

echo "=== TESTING DIRECT THEME PREVIEWS WITHOUT ACTIVE THEME ===\n";

foreach ($previewUrls as $url) {
    $request = Illuminate\Http\Request::create($url, 'GET');
    $response = $kernel->handle($request);
    $status = $response->getStatusCode();

    if ($status === 200) {
        echo "[SUCCESS 200] $url -> Rendered Length: " . strlen($response->getContent()) . " bytes\n";
    } else {
        echo "[STATUS $status] $url\n";
    }
}
