<?php
// seed_test_analytics.php - Run with: php artisan tinker seed_test_analytics.php

use App\Models\Product;
use App\Models\ProductSalesHistory;
use App\Models\ProductRatingHistory;
use App\Models\ProductReview;

echo "=== Seeding Test Analytics Data ===\n\n";

$products = Product::all();
echo "Found " . $products->count() . " products.\n\n";

// 1. Seed Sales History (random realistic data)
echo "--- Seeding Product Sales History ---\n";
foreach ($products as $product) {
    // Random number of sales records (1-5 per product)
    $numRecords = rand(1, 5);
    for ($i = 0; $i < $numRecords; $i++) {
        $qty = rand(1, 10);
        $revenue = $qty * $product->price;
        $daysAgo = rand(0, 60); // within last 60 days

        ProductSalesHistory::create([
            'product_id' => $product->id,
            'quantity' => $qty,
            'revenue' => $revenue,
            'recorded_at' => now()->subDays($daysAgo),
        ]);
    }
    echo "  {$product->name}: seeded {$numRecords} sales records\n";
}

// 2. Update product ratings with random values (simulating reviews)
echo "\n--- Updating Product Ratings ---\n";
foreach ($products as $product) {
    $rating = round(rand(25, 50) / 10, 1); // 2.5 to 5.0
    $reviews = rand(1, 30);
    
    $product->update([
        'average_rating' => $rating,
        'avg_rating' => $rating,
        'total_reviews' => $reviews,
    ]);

    // Also seed rating history snapshot
    ProductRatingHistory::create([
        'product_id' => $product->id,
        'average_rating' => $rating,
        'total_reviews' => $reviews,
        'recorded_at' => now(),
    ]);

    echo "  {$product->name}: rating={$rating}, reviews={$reviews}\n";
}

// 3. Recalculate ALL ranking scores using the ProductRankingService
echo "\n--- Recalculating Ranking Scores ---\n";
$ranker = new \App\Services\ProductRankingService();
$ranker->recalculateAll();
echo "Done! Scores recalculated.\n";

// 4. Show final results
echo "\n=== FINAL RANKING (Sorted by Score) ===\n";
echo str_pad("RANK", 6) . str_pad("PRODUCT", 30) . str_pad("SCORE", 10) . str_pad("SALES", 8) . str_pad("RATING", 10) . str_pad("RETURN%", 10) . "BEST SELLER\n";
echo str_repeat("-", 84) . "\n";

$ranked = Product::orderByDesc('ranking_score')->get();
$rank = 1;
foreach ($ranked as $p) {
    echo str_pad("#" . $rank, 6) 
         . str_pad(substr($p->name, 0, 28), 30) 
         . str_pad(number_format($p->ranking_score, 1), 10) 
         . str_pad($p->total_sales, 8) 
         . str_pad(number_format($p->average_rating, 1), 10) 
         . str_pad(number_format($p->return_rate, 1) . "%", 10) 
         . ($p->is_best_seller ? "⭐ YES" : "No") . "\n";
    $rank++;
}

echo "\n✅ Test data seeded! Visit /admin/dashboard to see the analytics.\n";
