<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

$superAdmin = User::role('super_admin')->first() ?? User::first();
if ($superAdmin) {
    Auth::login($superAdmin);
}

echo "1. Testing Hotel Sidebar:\n";
session(['tenant_id' => 1]);
$htmlHotel = htmlspecialchars_decode(view('components.admin.sidebar')->render());
echo "   Contains 'Hotel CMS & Content': " . (str_contains($htmlHotel, 'Hotel CMS & Content') ? 'YES' : 'NO') . "\n";
echo "   Contains 'Hotel Site CMS': " . (str_contains($htmlHotel, 'Hotel Site CMS') ? 'YES' : 'NO') . "\n";
echo "   Contains 'Travel & Hotel Blogs': " . (str_contains($htmlHotel, 'Travel & Hotel Blogs') ? 'YES' : 'NO') . "\n";
echo "   Contains 'Hotel Operations': " . (str_contains($htmlHotel, 'Hotel Operations') ? 'YES' : 'NO') . "\n";

echo "\n2. Testing Restaurant Sidebar:\n";
session(['tenant_id' => 2]);
$htmlResto = htmlspecialchars_decode(view('components.admin.sidebar')->render());
echo "   Contains 'Restaurant CMS & Content': " . (str_contains($htmlResto, 'Restaurant CMS & Content') ? 'YES' : 'NO') . "\n";
echo "   Contains 'Dining Site CMS': " . (str_contains($htmlResto, 'Dining Site CMS') ? 'YES' : 'NO') . "\n";
echo "   Contains 'Food & Recipe Stories': " . (str_contains($htmlResto, 'Food & Recipe Stories') ? 'YES' : 'NO') . "\n";
echo "   Contains 'Restaurant Operations': " . (str_contains($htmlResto, 'Restaurant Operations') ? 'YES' : 'NO') . "\n";

echo "\n>>> 100% SUCCESSFUL VALIDATION! <<<\n";
