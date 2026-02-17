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
        Schema::dropIfExists('bundle_rules');
        Schema::create('bundle_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type', 50)->default('global'); // global, category, product
            $table->unsignedBigInteger('target_id')->nullable();
            $table->integer('min_items');
            $table->integer('max_items');
            $table->decimal('discount_percentage', 5, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['type', 'target_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bundle_rules');
    }
};
