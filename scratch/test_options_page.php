<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

$user = User::first();
if ($user) {
    Auth::login($user);
}

$request = Illuminate\Http\Request::create('/admin/options', 'GET');
$response = $app->make(Illuminate\Contracts\Http\Kernel::class)->handle($request);

echo "Admin options response status: " . $response->getStatusCode() . "\n";
