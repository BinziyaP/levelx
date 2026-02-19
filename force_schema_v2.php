<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

echo "Forcing schema updates v2...\n";

// 1. Products Table
if (Schema::hasTable('products')) {
    Schema::table('products', function (Blueprint $table) {
        if (!Schema::hasColumn('products', 'is_best_seller')) {
            $table->boolean('is_best_seller')->default(false)->index();
            echo "Added is_best_seller to products.\n";
        }
        if (!Schema::hasColumn('products', 'total_reviews')) {
            $table->integer('total_reviews')->default(0);
            echo "Added total_reviews to products.\n";
        }
        if (!Schema::hasColumn('products', 'average_rating')) {
             $table->decimal('average_rating', 3, 2)->default(0.00); 
             echo "Added average_rating to products.\n";
        }
    });
}

// 2. Product Reviews
if (!Schema::hasTable('product_reviews')) {
    Schema::create('product_reviews', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->integer('rating')->unsigned(); 
        $table->text('review_text')->nullable();
        $table->timestamps();
        $table->unique(['user_id', 'product_id']);
    });
    echo "Created product_reviews table.\n";
}

// 3. Order Returns
if (!Schema::hasTable('order_returns')) {
    Schema::create('order_returns', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
        $table->string('status')->default('pending')->index(); 
        $table->text('reason');
        $table->decimal('refund_amount', 10, 2)->nullable();
        $table->string('razorpay_refund_id')->nullable();
        $table->timestamps();
    });
    echo "Created order_returns table.\n";
}

// 4. Product Sales History
if (!Schema::hasTable('product_sales_history')) {
    Schema::create('product_sales_history', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
        $table->integer('quantity');
        $table->decimal('revenue', 10, 2);
        $table->timestamp('recorded_at')->useCurrent();
    });
    echo "Created product_sales_history table.\n";
}

// 5. Product Rating History
if (!Schema::hasTable('product_rating_history')) {
    Schema::create('product_rating_history', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
        $table->decimal('average_rating', 3, 2);
        $table->integer('total_reviews');
        $table->timestamp('recorded_at')->useCurrent();
    });
    echo "Created product_rating_history table.\n";
}

// 6. Ranking Settings
if (Schema::hasTable('ranking_settings')) {
    Schema::table('ranking_settings', function (Blueprint $table) {
        if (!Schema::hasColumn('ranking_settings', 'min_sales_for_best_seller')) {
            $table->integer('min_sales_for_best_seller')->default(100);
            echo "Added min_sales_for_best_seller.\n";
        }
        if (!Schema::hasColumn('ranking_settings', 'min_rating_for_best_seller')) {
            $table->decimal('min_rating_for_best_seller', 3, 2)->default(4.50);
            echo "Added min_rating_for_best_seller.\n";
        }
        if (!Schema::hasColumn('ranking_settings', 'min_reviews_for_best_seller')) {
            $table->integer('min_reviews_for_best_seller')->default(10);
            echo "Added min_reviews_for_best_seller.\n";
        }
    });
}

echo "Done.\n";
