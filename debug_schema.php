<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "Checking 'products' table columns:\n";
$columns = Schema::getColumnListing('products');
print_r($columns);

echo "\nChecking if 'product_reviews' table exists: " . (Schema::hasTable('product_reviews') ? 'YES' : 'NO') . "\n";
