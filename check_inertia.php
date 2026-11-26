<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = Illuminate\Http\Request::create('/promotions');
$response = app()->handle($request);
$content = $response->getContent();

// Extract the Inertia page JSON from the HTML
if (preg_match('/data-page="(.*?)"/', $content, $matches)) {
    $page = json_decode(html_entity_decode($matches[1]), true);
    
    // Dump the first group's first promotion
    if (isset($page['props']['groups'][0]['promotions'][0])) {
        echo json_encode($page['props']['groups'][0]['promotions'][0], JSON_PRETTY_PRINT);
    } else {
        echo "No promotions found in first group.";
    }
} else {
    echo "Could not find Inertia data in response.";
}
