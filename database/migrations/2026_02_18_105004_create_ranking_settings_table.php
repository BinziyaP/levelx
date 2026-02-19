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
        if (!Schema::hasTable('ranking_settings')) {
            Schema::create('ranking_settings', function (Blueprint $table) {
                $table->id();
                $table->decimal('sales_weight', 8, 4)->default(1.0000);
                $table->decimal('rating_weight', 8, 4)->default(1.0000);
                $table->decimal('return_weight', 8, 4)->default(1.0000);
                $table->timestamps();
            });

            // Insert default settings safely
            DB::table('ranking_settings')->insert([
                'sales_weight' => 1.0,
                'rating_weight' => 1.0,
                'return_weight' => 1.0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ranking_settings');
    }
};
