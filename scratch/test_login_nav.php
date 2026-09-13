<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/login', 'GET');
$response = $kernel->handle($request);
$content = $response->getContent();

echo "Status: " . $response->getStatusCode() . "\n";

// Count occurrences of nav link texts in the navbar
preg_match_all('#<ul class="navbar-nav[^"]*">(.*?)</ul>#s', $content, $matches);
$navHtml = $matches[0][0] ?? '';

echo "Navbar HTML:\n" . $navHtml . "\n";

$homeCount = substr_count($navHtml, '>Home<');
$aboutCount = substr_count($navHtml, '>About Us<');
$roomsCount = substr_count($navHtml, '>Rooms & Suites<');
$contactCount = substr_count($navHtml, '>Contact<');

echo "\nLink Counts in Header:\n";
echo "- Home: $homeCount\n";
echo "- About Us: $aboutCount\n";
echo "- Rooms & Suites: $roomsCount\n";
echo "- Contact: $contactCount\n";

if ($homeCount === 1 && $aboutCount === 1 && $roomsCount === 1 && $contactCount === 1) {
    echo "\n>>> PERFECT! EXACTLY 1 OCCURRENCE OF EACH HEADER LINK! <<<\n";
    exit(0);
} else {
    echo "\n>>> DUPLICATE HEADER DETECTED! <<<\n";
    exit(1);
}
