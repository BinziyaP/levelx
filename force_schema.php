<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

echo "Forcing schema updates...\n";

// Products Table
if (Schema::hasTable('products')) {
    Schema::table('products', function (Blueprint $table) {
        if (!Schema::hasColumn('products', 'ranking_score')) {
            echo "Adding ranking columns to products...\n";
            $table->integer('total_sales')->default(0);
            $table->decimal('avg_rating', 3, 2)->default(0);
            $table->decimal('return_rate', 5, 2)->default(0);
            $table->decimal('ranking_score', 10, 2)->default(0);
        } else {
            echo "Product columns already exist.\n";
        }
    });
} else {
    echo "Error: products table missing!\n";
}

// Ranking Settings Table
if (!Schema::hasTable('ranking_settings')) {
    echo "Creating ranking_settings table...\n";
    Schema::create('ranking_settings', function (Blueprint $table) {
        $table->id();
        $table->decimal('sales_weight', 8, 4)->default(1.0000);
        $table->decimal('rating_weight', 8, 4)->default(1.0000);
        $table->decimal('return_weight', 8, 4)->default(1.0000);
        $table->timestamps();
    });

    DB::table('ranking_settings')->insert([
        'sales_weight' => 1.0,
        'rating_weight' => 1.0,
        'return_weight' => 1.0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "ranking_settings table created and seeded.\n";
} else {
    echo "ranking_settings table already exists.\n";
}

echo "Schema updates completed.\n";
