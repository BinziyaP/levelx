<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$hasColumns = Schema::hasColumns('products', ['total_sales', 'avg_rating', 'return_rate', 'ranking_score']);
$settingsExist = Schema::hasTable('ranking_settings');
$settingsData = $settingsExist ? DB::table('ranking_settings')->first() : null;

echo "Product Columns: " . ($hasColumns ? "Exist" : "Missing") . "\n";
echo "Ranking Settings Table: " . ($settingsExist ? "Exists" : "Missing") . "\n";
echo "Ranking Settings Data: " . ($settingsData ? "Exists" : "Missing") . "\n";
