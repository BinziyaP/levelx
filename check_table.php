<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$columns = Schema::getColumnListing('ranking_settings');
print_r($columns);

// Also try to add the column manually via raw SQL to enforce it if missing
if (!in_array('allow_early_reviews', $columns)) {
    echo "Column missing. Attempting to add...\n";
    try {
        DB::statement("ALTER TABLE ranking_settings ADD COLUMN allow_early_reviews BOOLEAN DEFAULT 0");
        echo "Column added successfully.\n";
    } catch (\Exception $e) {
        echo "Error adding column: " . $e->getMessage() . "\n";
    }
} else {
    echo "Column already exists.\n";
}
