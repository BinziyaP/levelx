<?php

use Illuminate\Support\Facades\Http;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

$apiKey = env('TRACKCOURIER_API_KEY');

$trackingNumber = '13381331576026'; 
$courierVariations = ['Delhivery', 'delhivery', 'DELHIVERY', 'Delhivery Express'];

echo "Testing Delhivery ($trackingNumber):\n";
foreach ($courierVariations as $courier) {
    try {
        $response = Http::withHeaders([
            'X-API-Key' => $apiKey
        ])->get("https://api.trackcourier.io/v1/track", [
            'tracking_number' => $trackingNumber,
            'courier' => $courier
        ]);
        
        echo "  '$courier' -> " . $response->status() . "\n";
    } catch (\Exception $e) {
        echo "  '$courier' -> Error: " . $e->getMessage() . "\n";
    }
}

$speedPostNumber = 'PP380158391IN';
$speedPostVariations = ['Speed Post', 'speed-post', 'speedpost'];

echo "\nTesting Speed Post ($speedPostNumber):\n";
foreach ($speedPostVariations as $courier) {
    try {
        $response = Http::withHeaders([
            'X-API-Key' => $apiKey
        ])->get("https://api.trackcourier.io/v1/track", [
            'tracking_number' => $speedPostNumber,
            'courier' => $courier
        ]);
        
        echo "  '$courier' -> " . $response->status() . "\n";
    } catch (\Exception $e) {
        echo "  '$courier' -> Error: " . $e->getMessage() . "\n";
    }
}
