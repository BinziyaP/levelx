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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'total_sales')) {
                $table->integer('total_sales')->default(0);
            }
            if (!Schema::hasColumn('products', 'avg_rating')) {
                $table->decimal('avg_rating', 3, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'return_rate')) {
                $table->decimal('return_rate', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'ranking_score')) {
                $table->decimal('ranking_score', 10, 2)->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['total_sales', 'avg_rating', 'return_rate', 'ranking_score']);
        });
    }
};
