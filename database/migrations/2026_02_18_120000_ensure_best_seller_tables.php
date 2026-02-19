<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Add Best Seller & Rating columns to Products
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'is_best_seller')) {
                    $table->boolean('is_best_seller')->default(false)->index();
                }
                if (!Schema::hasColumn('products', 'total_reviews')) {
                    $table->integer('total_reviews')->default(0);
                }
                // Determine which rating column to use. Project has 'avg_rating'.
                // If 'average_rating' is requested but 'avg_rating' exists, we might just stick to avg_rating?
                // Or aliasing? Let's just double check and use 'average_rating' if needed for new logic, 
                // but implementation plan said 'average_rating'. 
                // However, previous steps added 'avg_rating'. Let's check if 'average_rating' exists.
                if (!Schema::hasColumn('products', 'average_rating')) {
                     $table->decimal('average_rating', 3, 2)->default(0.00); 
                }
            });
        }

        // 2. Product Reviews - ONLY if not exists
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
        }

        // 3. Order Returns - ONLY if not exists
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
        }

        // 4. Product Sales History - ONLY if not exists
        if (!Schema::hasTable('product_sales_history')) {
            Schema::create('product_sales_history', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->integer('quantity');
                $table->decimal('revenue', 10, 2);
                $table->timestamp('recorded_at')->useCurrent();
            });
        }

        // 5. Product Rating History - ONLY if not exists
        if (!Schema::hasTable('product_rating_history')) {
            Schema::create('product_rating_history', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->decimal('average_rating', 3, 2);
                $table->integer('total_reviews');
                $table->timestamp('recorded_at')->useCurrent();
            });
        }
        
        // 6. Ranking Settings - Add columns if missing
         if (Schema::hasTable('ranking_settings')) {
            Schema::table('ranking_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('ranking_settings', 'min_sales_for_best_seller')) {
                    $table->integer('min_sales_for_best_seller')->default(100);
                }
                if (!Schema::hasColumn('ranking_settings', 'min_rating_for_best_seller')) {
                    $table->decimal('min_rating_for_best_seller', 3, 2)->default(4.50);
                }
                if (!Schema::hasColumn('ranking_settings', 'min_reviews_for_best_seller')) {
                    $table->integer('min_reviews_for_best_seller')->default(10);
                }
            });
        }
    }

    public function down()
    {
       // simplified down
    }
};
