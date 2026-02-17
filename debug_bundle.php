<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BundleRule;
use App\Models\Product;
use App\Services\BundleDiscountService;
use Illuminate\Support\Facades\DB;

try {
    // Verify Fillable
    $dummy = new BundleRule();
    echo "Fillable attributes: " . implode(', ', $dummy->getFillable()) . "\n";

    // Setup
    BundleRule::query()->delete(); 

    // Create Product (or find existing one to use)
    $product = Product::first();
    if (!$product) {
        $product = Product::create([
            'name' => 'Debug Product',
            'price' => 1000,
            'category_id' => 99,
            'user_id' => 1, // Validation might require this
            'image' => 'test.jpg'
        ]);
    }
    $productId = $product->id;
    echo "Using Product ID: $productId, Category ID: {$product->category_id}\n";

    // Create Rules
    $r1Id = DB::table('bundle_rules')->insertGetId([
        'name' => 'Global',
        'type' => 'global',
        'min_items' => 2,
        'max_items' => 5,
        'discount_percentage' => 10,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Created Global Rule ID: $r1Id\n";

    $r2Id = DB::table('bundle_rules')->insertGetId([
        'name' => 'Product Deal',
        'type' => 'product',
        'target_id' => $productId,
        'min_items' => 2,
        'max_items' => 5,
        'discount_percentage' => 25,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Created Product Rule ID: $r2Id with target_id: $productId\n";

    // Verify DB
    echo "Rules in DB:\n";
    $rules = DB::table('bundle_rules')->get();
    foreach($rules as $r) {
        echo "Rule {$r->id}: Type={$r->type}, Target={$r->target_id}, Discount={$r->discount_percentage}\n";
    }

    $service = new BundleDiscountService();
    $cart = [
        $productId => ['quantity' => 2, 'price' => 1000]
    ];

    echo "Calculating for Cart: " . json_encode($cart) . "\n";
    $result = $service->calculate($cart);

    echo "Result:\n";
    print_r($result);

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
