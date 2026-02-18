<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

try {
    echo "Testing Laravel HTTP Client with Razorpay (Forced IPv4, Timeout 60s)...\n";
    
    $keyId = env('RAZORPAY_KEY_ID');
    $keySecret = env('RAZORPAY_KEY_SECRET');
    
    if (empty($keyId) || empty($keySecret)) {
        die("Error: Razorpay keys not found in .env\n");
    }
    
    $orderData = [
        'receipt'         => 'rcpt_ipv4_' . Str::random(10),
        'amount'          => 100, // 1 rupee
        'currency'        => 'INR',
        'payment_capture' => 1
    ];
    
    $start = microtime(true);
    
    $response = Http::withBasicAuth($keyId, $keySecret)
        ->withOptions([
            'connect_timeout' => 30,
            'timeout' => 60,
            'force_ip_resolve' => 'v4' 
        ])
        ->post('https://api.razorpay.com/v1/orders', $orderData);

    $end = microtime(true);
    
    if ($response->failed()) {
        echo "Failed! Status: " . $response->status() . "\n";
        echo "Body: " . $response->body() . "\n";
    } else {
        $data = $response->json();
        echo "Success! Order ID: " . $data['id'] . "\n";
        echo "Time taken: " . round($end - $start, 4) . " seconds\n";
    }
    
} catch (\Exception $e) {
    echo "Exception Code: " . $e->getCode() . "\n";
    echo "Exception Message: " . $e->getMessage() . "\n";
    if (method_exists($e, 'getResponse') && $e->getResponse()) {
        echo "Response Body: " . $e->getResponse()->getBody() . "\n";
    }
}
