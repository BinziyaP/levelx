<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$p = App\Models\Product::where('name', 'like', 'Nikon%')->first();
echo "name=" . $p->name . "\n";
echo "ranking_score=" . $p->ranking_score . "\n";
echo "average_rating=" . $p->average_rating . "\n";
echo "total_sales=" . $p->total_sales . "\n";
echo "return_rate=" . $p->return_rate . "\n";
echo "is_best_seller=" . ($p->is_best_seller ? 'Yes' : 'No') . "\n";

$s = DB::table('ranking_settings')->first();
$expected = (0 * $s->sales_weight) + (4 * $s->rating_weight) - (0 * $s->return_weight);
echo "expected_score=" . $expected . "\n";
echo "match=" . ($p->ranking_score == $expected ? 'YES' : 'NO') . "\n";

// Count products with non-zero scores
$nonzero = App\Models\Product::where('ranking_score', '>', 0)->count();
echo "products_with_score_gt_0=" . $nonzero . "\n";
