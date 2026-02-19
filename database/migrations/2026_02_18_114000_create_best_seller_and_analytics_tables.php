<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Add Best Seller & Rating columns to Products (Additive)
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'is_best_seller')) {
                    $table->boolean('is_best_seller')->default(false)->index();
                }
                if (!Schema::hasColumn('products', 'total_reviews')) {
                    $table->integer('total_reviews')->default(0);
                }
                // Check if avg_rating exists (it might from previous steps, but ensure type/default)
                // If it exists, we assume it's correct. If not, add it.
                if (!Schema::hasColumn('products', 'average_rating')) {
                     $table->decimal('average_rating', 3, 2)->default(0.00); 
                }
            });
        }

        // 2. Product Reviews Table
        if (!Schema::hasTable('product_reviews')) {
            Schema::create('product_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->integer('rating')->unsigned(); // 1-5
                $table->text('review_text')->nullable();
                $table->timestamps();
                
                // Ensure one review per user per product
                $table->unique(['user_id', 'product_id']);
            });
        }

        // 3. Order Returns Table
        if (!Schema::hasTable('order_returns')) {
            Schema::create('order_returns', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->string('status')->default('pending')->index(); // pending, approved, rejected, refunded
                $table->text('reason');
                $table->decimal('refund_amount', 10, 2)->nullable();
                $table->string('razorpay_refund_id')->nullable();
                $table->timestamps();
            });
        }

        // 4. Product Sales History (Analytics)
        if (!Schema::hasTable('product_sales_history')) {
            Schema::create('product_sales_history', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->integer('quantity');
                $table->decimal('revenue', 10, 2);
                $table->timestamp('recorded_at')->useCurrent();
            });
        }

        // 5. Product Rating History (Analytics Snapshot)
        if (!Schema::hasTable('product_rating_history')) {
            Schema::create('product_rating_history', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->decimal('average_rating', 3, 2);
                $table->integer('total_reviews');
                $table->timestamp('recorded_at')->useCurrent();
            });
        }

        // 6. Update Ranking Settings (Thresholds)
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
        // Safe down migration - dropping tables created here
        Schema::dropIfExists('product_rating_history');
        Schema::dropIfExists('product_sales_history');
        Schema::dropIfExists('order_returns');
        Schema::dropIfExists('product_reviews');
        
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn(['is_best_seller', 'total_reviews', 'average_rating']);
            });
        }
         if (Schema::hasTable('ranking_settings')) {
            Schema::table('ranking_settings', function (Blueprint $table) {
                $table->dropColumn(['min_sales_for_best_seller', 'min_rating_for_best_seller', 'min_reviews_for_best_seller']);
            });
        }
    }
};
