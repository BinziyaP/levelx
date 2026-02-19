<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\User;
use App\Models\ProductReview;
use App\Models\Order;
use App\Models\OrderReturn;
use Illuminate\Support\Facades\DB;
use App\Events\ReviewSubmitted;

echo "--- STARTING VERIFICATION ---\n";

// 1. Setup Test Data
$user = User::first();
if (!$user) {
    $user = User::factory()->create();
    echo "Created Test User: {$user->id}\n";
} else {
    echo "Using User: {$user->id}\n";
}

$product = Product::first();
if (!$product) {
    die("No products found. Please seed data.\n");
}
echo "Testing with Product: {$product->name} (ID: {$product->id})\n";

// 2. Test Best Seller Logic (Sales)
echo "\n[Test 1] Best Seller via Sales...\n";
// Increase threshold to verify it's NOT best seller first
DB::table('ranking_settings')->updateOrInsert(['id' => 1], [
    'min_sales_for_best_seller' => 99999,
    'updated_at' => now()
]);
$product->update(['total_sales' => 100]); // Reset
$product->determineBestSellerStatus();
echo "Status (High Threshold): " . ($product->fresh()->is_best_seller ? "YES" : "NO") . "\n";

// Lower threshold
DB::table('ranking_settings')->update(['min_sales_for_best_seller' => 50]);
$product->determineBestSellerStatus();
echo "Status (Low Threshold): " . ($product->fresh()->is_best_seller ? "YES" : "NO") . "\n";

// 3. Test Review Aggregation
echo "\n[Test 2] Review Aggregation...\n";
// Clear existing reviews for this product/user to avoid unique constraint
ProductReview::where('product_id', $product->id)->where('user_id', $user->id)->delete();

$initialCount = $product->fresh()->total_reviews;
$initialRating = $product->fresh()->average_rating; // Using average_rating

echo "Initial: Count=$initialCount, Rating=$initialRating\n";

// Create Review Manually (Controller does validation, here we test Event)
$review = ProductReview::create([
    'product_id' => $product->id,
    'user_id' => $user->id,
    'rating' => 5,
    'review_text' => 'Great product!',
]);

// Dispatch Event manually (Controller does this)
event(new ReviewSubmitted($review));

$product = $product->fresh();
echo "After Review: Count={$product->total_reviews}, Rating={$product->average_rating}\n";

if ($product->total_reviews == $initialCount + 1) {
    echo "SUCCESS: Review count incremented.\n";
} else {
    echo "FAILURE: Review count not updated.\n";
}

// 4. Test Rating History Snapshot
$history = \App\Models\ProductRatingHistory::where('product_id', $product->id)->latest('recorded_at')->first();
if ($history) {
    echo "SUCCESS: Rating History recorded (Rating: {$history->average_rating})\n";
} else {
    echo "FAILURE: No Rating History found.\n";
}

// 5. Test Return Request Logic
echo "\n[Test 3] Return Request...\n";
$order = Order::where('user_id', $user->id)->first();
if (!$order) {
    $order = Order::create([
        'user_id' => $user->id,
        'total_price' => 100,
        'status' => 'pending',
        'shipping_status' => 'pending',
        'payment_status' => 'paid',
        'payment_id' => 'pay_test_123'
    ]);
    echo "Created Test Order: {$order->id}\n";
}

// Ensure no existing return
OrderReturn::where('order_id', $order->id)->delete();

// Create Return
$return = OrderReturn::create([
    'order_id' => $order->id,
    'status' => 'pending',
    'reason' => 'Test reason',
    'refund_amount' => $order->total_price
]);

echo "Return Created (Status: {$return->status})\n";

if ($order->fresh()->returnRequest) {
    echo "SUCCESS: Order has return request.\n";
} else {
    echo "FAILURE: Relationship issue.\n";
}

echo "\n--- VERIFICATION COMPLETE ---\n";
