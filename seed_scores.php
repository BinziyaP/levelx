<?php
// seed_scores.php - Run with: php artisan tinker seed_scores.php

use App\Models\Product;
use App\Models\ProductSalesHistory;
use Illuminate\Support\Facades\DB;

$settings = DB::table('ranking_settings')->first();
$w1 = $settings->sales_weight ?? 5;
$w2 = $settings->rating_weight ?? 4;
$w3 = $settings->return_weight ?? 2;

echo "Weights: Sales={$w1}, Rating={$w2}, Return={$w3}\n\n";

$products = Product::all();

foreach ($products as $product) {
    // Get sales from history
    $totalSales = ProductSalesHistory::where('product_id', $product->id)->sum('quantity');
    $returnRate = $product->return_rate ?? 0;
    $rating = $product->average_rating ?? 0;

    // Update total_sales from history
    $product->total_sales = $totalSales;

    // Calculate score: (sales * W1) + (rating * W2) - (returnRate * W3)
    $score = ($totalSales * $w1) + ($rating * $w2) - ($returnRate * $w3);
    $product->ranking_score = $score;

    // Best seller check
    $minSales = $settings->min_sales_for_best_seller ?? 5;
    $minRating = $settings->min_rating_for_best_seller ?? 4.5;
    $product->is_best_seller = ($totalSales >= $minSales) || ($rating >= $minRating && $product->total_reviews > 0);

    $product->save();

    echo str_pad($product->name, 30) 
         . " Sales=" . str_pad($totalSales, 4) 
         . " Rating=" . str_pad(number_format($rating, 1), 4)
         . " Score=" . str_pad(number_format($score, 1), 8) 
         . " BestSeller=" . ($product->is_best_seller ? "YES" : "no") . "\n";
}

echo "\nDone! Visit /admin/dashboard to see the ranked list.\n";
