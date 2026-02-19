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
        if (!Schema::hasTable('cart_snapshots')) {
            Schema::create('cart_snapshots', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->json('items');
                $table->decimal('original_total', 10, 2);
                $table->decimal('discount_amount', 10, 2)->default(0);
                $table->decimal('final_total', 10, 2);
                $table->string('razorpay_order_id')->unique();
                $table->json('applied_rules')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_snapshots');
    }
};
