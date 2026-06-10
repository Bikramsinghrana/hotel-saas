<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant;
use Illuminate\Http\Request;

$request = Request::create('http://localhost:8000/rooms/index');

$host = $request->getHost();
$httpHost = $request->getHttpHost();

echo "Host: '$host', HttpHost: '$httpHost'\n";

$tenant = Tenant::where(function($q) use ($host, $httpHost) {
    $q->where('domain', $host)
      ->orWhere('domain', $httpHost)
      ->orWhere('domain', 'like', '%' . $host . '%')
      ->orWhere('domain', 'like', '%' . $httpHost . '%');
})->first();

if ($tenant) {
    echo "Tenant resolved by middleware query: ID={$tenant->id}, Name={$tenant->name}\n";
} else {
    echo "Tenant NOT resolved by middleware query.\n";
}
