<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Create fraud_rules table
        Schema::create('fraud_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_name');
            $table->enum('rule_type', ['cart_value', 'multiple_orders', 'same_ip']);
            $table->decimal('threshold_value', 10, 2);
            $table->integer('time_window_minutes')->nullable()->comment('For time-based rules');
            $table->integer('weight')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Create fraud_logs table
        Schema::create('fraud_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('rule_id')->constrained('fraud_rules')->onDelete('cascade');
            $table->integer('score_added');
            $table->text('message');
            $table->timestamps();
        });

        // 3. Modify orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('fraud_score')->default(0);
            $table->boolean('is_suspicious')->default(false);
            $table->string('ip_address')->nullable();
            
            // Add Indexes for performance
            $table->index(['user_id', 'created_at']);
            $table->index('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['ip_address']);
            $table->dropColumn(['fraud_score', 'is_suspicious', 'ip_address']);
        });

        Schema::dropIfExists('fraud_logs');
        Schema::dropIfExists('fraud_rules');
    }
};
