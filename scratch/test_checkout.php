<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/rooms/1/checkout', 'GET');
$response = $kernel->handle($request);

echo "Status: " . $response->getStatusCode() . "\n";
if ($response->getStatusCode() >= 400) {
    echo "Content Preview:\n" . substr(strip_tags($response->getContent()), 0, 800) . "\n";
} else {
    echo "Rendered successfully! Length: " . strlen($response->getContent()) . " bytes\n";
}
