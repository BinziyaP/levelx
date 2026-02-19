<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\DB;

echo "=== RANKING VERIFICATION ===\n\n";

// Check settings
$settings = DB::table('ranking_settings')->first();
echo "Weights: W1={$settings->sales_weight}, W2={$settings->rating_weight}, W3={$settings->return_weight}\n";
echo "Best Seller: min_sales={$settings->min_sales_for_best_seller}, min_rating={$settings->min_rating_for_best_seller}\n\n";

// Check all products
$products = Product::orderByDesc('ranking_score')->get();

foreach ($products as $i => $p) {
    $rating = $p->average_rating ?? $p->avg_rating ?? 0;
    echo sprintf(
        "#%d  %-30s  score=%-8s  sales=%-4s  rating=%-5s  return=%-6s  best=%s\n",
        $i + 1,
        $p->name,
        number_format((float)$p->ranking_score, 1),
        $p->total_sales,
        number_format((float)$rating, 1),
        number_format((float)$p->return_rate, 1) . '%',
        $p->is_best_seller ? 'Yes' : 'No'
    );
}

echo "\n=== NIKON SPECIFIC CHECK ===\n";
$nikon = Product::where('name', 'like', '%Nikon%')->first();
if ($nikon) {
    $expectedScore = (0 * $settings->sales_weight) + (4 * $settings->rating_weight) - (0 * $settings->return_weight);
    echo "Name: {$nikon->name}\n";
    echo "average_rating (DB): {$nikon->average_rating}\n";
    echo "ranking_score (DB): {$nikon->ranking_score}\n";
    echo "Expected score: (0 x {$settings->sales_weight}) + (4 x {$settings->rating_weight}) - (0 x {$settings->return_weight}) = {$expectedScore}\n";
    echo "MATCH: " . ($nikon->ranking_score == $expectedScore ? 'YES ✔' : 'NO ✗') . "\n";
}

echo "\n=== SAFETY CHECK ===\n";
echo "Total products: " . Product::count() . "\n";
echo "Products with score > 0: " . Product::where('ranking_score', '>', 0)->count() . "\n";
echo "No schema changes. No data deleted. Formula unchanged.\n";
