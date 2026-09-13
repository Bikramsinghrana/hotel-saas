<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\PageController;
use App\Http\Controllers\RoomController;
use Illuminate\Http\Request;

$pageController = app(PageController::class);
$roomController = app(RoomController::class);

echo "1. Testing hotelWelcome():\n";
$req = Request::create('/hotel', 'GET');
$view = $pageController->hotelWelcome($req);
echo "   Rendered hotelWelcome successfully, view: " . $view->name() . "\n";

echo "2. Testing about():\n";
$req = Request::create('/about', 'GET');
$view = $pageController->about($req);
echo "   Rendered about successfully, view: " . $view->name() . "\n";

echo "3. Testing contact():\n";
$req = Request::create('/contact', 'GET');
$view = $pageController->contact($req);
echo "   Rendered contact successfully, view: " . $view->name() . "\n";

echo "4. Testing hotelAbout():\n";
$req = Request::create('/hotel/about', 'GET');
$view = $pageController->hotelAbout($req);
echo "   Rendered hotelAbout successfully, view: " . $view->name() . "\n";

echo "5. Testing hotelContact():\n";
$req = Request::create('/hotel/contact', 'GET');
$view = $pageController->hotelContact($req);
echo "   Rendered hotelContact successfully, view: " . $view->name() . "\n";

echo "6. Testing rooms.checkout (room id 1):\n";
$firstRoom = \App\Models\Room::first();
if ($firstRoom) {
    $req = Request::create('/rooms/' . $firstRoom->id . '/checkout', 'GET');
    $view = $roomController->checkout($req, $firstRoom->id);
    echo "   Rendered checkout successfully for Room ID {$firstRoom->id}, view: " . $view->name() . "\n";
    echo "   Room Title: " . $view->getData()['room']->post_title . "\n";
    echo "   Total Payable: " . $view->getData()['calc']['total_payable'] . "\n";
} else {
    echo "   No rooms found in database.\n";
}

echo "\n>>> ALL DIRECT CONTROLLER TESTS PASSED! <<<\n";
