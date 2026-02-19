<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Services\ProductRankingService;
use App\Models\Product;

$service = new ProductRankingService();

// 1. Set weights to favor SALES
echo "Testing SALES heavy weighting...\n";
DB::table('ranking_settings')->updateOrInsert(['id' => 1], [
    'sales_weight' => 10.0,
    'rating_weight' => 0.0,
    'return_weight' => 0.0,
    'updated_at' => now()
]);

// Recalculate
$service = new ProductRankingService(); // Reload weights
$service->recalculateAll();

// Check top product
$topSalesProduct = Product::orderBy('ranking_score', 'desc')->first();
echo "Top Product (Sales): " . ($topSalesProduct->name ?? 'None') . " (Score: " . ($topSalesProduct->ranking_score ?? 0) . ")\n";

// 2. Set weights to favor RATINGS
echo "Testing RATINGS heavy weighting...\n";
DB::table('ranking_settings')->updateOrInsert(['id' => 1], [
    'sales_weight' => 0.0,
    'rating_weight' => 10.0,
    'return_weight' => 0.0,
    'updated_at' => now()
]);

// Recalculate
$service = new ProductRankingService(); // Reload weights
$service->recalculateAll();

// Check top product
$topRatedProduct = Product::orderBy('ranking_score', 'desc')->first();
echo "Top Product (Rating): " . ($topRatedProduct->name ?? 'None') . " (Score: " . ($topRatedProduct->ranking_score ?? 0) . ")\n";

// Reset to Default
echo "Resetting to default...\n";
DB::table('ranking_settings')->updateOrInsert(['id' => 1], [
    'sales_weight' => 1.0,
    'rating_weight' => 1.0,
    'return_weight' => 1.0,
    'updated_at' => now()
]);
$service = new ProductRankingService();
$service->recalculateAll();
echo "Done.\n";
