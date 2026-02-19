<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\DB;

echo "--- DEBUGGING BEST SELLER STATUS ---\n";

// 1. Check Settings
$settings = DB::table('ranking_settings')->first();
echo "Settings:\n";
echo " - Min Sales: " . ($settings->min_sales_for_best_seller ?? 'N/A') . "\n";
echo " - Min Rating: " . ($settings->min_rating_for_best_seller ?? 'N/A') . "\n";
echo " - Min Reviews: " . ($settings->min_reviews_for_best_seller ?? 'N/A') . "\n";

// 2. Process Products
$products = Product::all();
$count = 0;
echo "\nProcessing {$products->count()} products...\n";

foreach ($products as $product) {
    echo "ID {$product->id}: Sales={$product->total_sales}, Rating=" . ($product->average_rating ?? $product->avg_rating) . ", Reviews={$product->total_reviews} ... ";
    
    // Force Recalculate
    $isBestSeller = $product->determineBestSellerStatus();
    
    echo ($isBestSeller ? "[BEST SELLER]" : "[No]") . "\n";
    if ($isBestSeller) $count++;
}

echo "\nTotal Best Sellers: $count\n";

// 3. Optional: Force a product to be best seller for demo
if ($count == 0 && $products->count() > 0) {
    echo "\nNo best sellers found. Forcing first product to be best seller for DEMO purposes.\n";
    $first = $products->first();
    // Update its stats to meet criteria (temporarily or permanently?)
    // Let's just update the flag purely or update stats. 
    // Updating stats is better logic.
    $first->update([
        'total_sales' => $settings->min_sales_for_best_seller + 1,
        'is_best_seller' => true
    ]);
    echo "Updated Product {$first->id} ({$first->name}) to have {$first->total_sales} sales. It should now be a Best Seller.\n";
}
