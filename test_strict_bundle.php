<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\BundleDiscountService;
use App\Models\Product;

// Ensure we have products
$p1 = Product::firstOrCreate(['id' => 1], ['name' => 'P1', 'price' => 100, 'category_id' => 1]);
$p2 = Product::firstOrCreate(['id' => 2], ['name' => 'P2', 'price' => 100, 'category_id' => 2]);
$p3 = Product::firstOrCreate(['id' => 3], ['name' => 'P3', 'price' => 100, 'category_id' => 3]);
$p4 = Product::firstOrCreate(['id' => 4], ['name' => 'P4', 'price' => 100, 'category_id' => 1]);

$service = new BundleDiscountService();

echo "--- TEST 1: 1 Item (Should be 0 discount) ---\n";
$cart1 = [1 => ['quantity' => 1, 'price' => 100]];
$res1 = $service->calculate($cart1);
echo "Total Items: " . $res1['meta']['item_count'] . "\n";
echo "Discount: " . $res1['discount_amount'] . "\n";
echo "Message: " . ($res1['breakdown']['message'] ?? 'None') . "\n\n";

echo "--- TEST 2: 2 Items (Base Discount) ---\n";
$cart2 = [1 => ['quantity' => 2, 'price' => 100]];
$res2 = $service->calculate($cart2);
echo "Total Items: " . $res2['meta']['item_count'] . "\n";
echo "Discount: " . $res2['discount_amount'] . " (Expected: ~5% on 200 = 10)\n";
echo "Applied: " . ($res2['applied_rule']['name'] ?? 'None') . "\n\n";

echo "--- TEST 3: 2 Items, 2 Categories (Base + Bonus) ---\n";
$cart3 = [
    1 => ['quantity' => 1, 'price' => 100], // Cat 1
    2 => ['quantity' => 1, 'price' => 100]  // Cat 2
];
$res3 = $service->calculate($cart3);
echo "Total Items: " . $res3['meta']['item_count'] . "\n";
echo "Cats: " . $res3['meta']['category_count'] . "\n";
echo "Percentage: " . $res3['meta']['total_percentage'] . "% (Expected: 5+3 = 8%)\n";
echo "Discount: " . $res3['discount_amount'] . " (Expected: 16)\n\n";

echo "--- TEST 4: 7 Items (Should be 0 discount - Limit exceeded) ---\n";
$cart4 = [1 => ['quantity' => 7, 'price' => 100]];
$res4 = $service->calculate($cart4);
echo "Total Items: " . $res4['meta']['item_count'] . " (Wait, meta might not set if return early)\n";
if (!isset($res4['meta'])) {
    echo "Early return confirmed.\n";
    echo "Message: " . ($res4['breakdown']['message'] ?? 'None') . "\n";
} else {
    echo "Discount: " . $res4['discount_amount'] . "\n";
}
