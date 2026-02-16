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
            $table->string('brand')->nullable()->after('description');
            $table->foreignId('category_id')->nullable()->after('brand')->constrained()->nullOnDelete();
            
            // Indexes for performance
            $table->index('brand');
            $table->index('price');
            $table->index('name');
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
            $table->dropForeign(['category_id']);
            $table->dropColumn(['brand', 'category_id']);
            $table->dropIndex(['brand', 'price', 'name']);
        });
    }
};
