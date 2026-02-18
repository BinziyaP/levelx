<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Razorpay\Api\Api;
use Illuminate\Support\Str;

try {
    echo "Testing Razorpay SDK Connectivity...\n";
    
    $keyId = env('RAZORPAY_KEY_ID');
    $keySecret = env('RAZORPAY_KEY_SECRET');
    
    if (empty($keyId) || empty($keySecret)) {
        die("Error: Razorpay keys not found in .env\n");
    }
    
    echo "Key ID: " . substr($keyId, 0, 8) . "...\n";
    
    $api = new Api($keyId, $keySecret);
    
    $orderData = [
        'receipt'         => 'rcpt_test_' . Str::random(10),
        'amount'          => 100, // 1 rupee
        'currency'        => 'INR',
        'payment_capture' => 1
    ];
    
    $start = microtime(true);
    $razorpayOrder = $api->order->create($orderData);
    $end = microtime(true);
    
    echo "Success! Order ID: " . $razorpayOrder['id'] . "\n";
    echo "Time taken: " . round($end - $start, 4) . " seconds\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
