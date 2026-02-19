<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ranking_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('ranking_settings', 'allow_early_reviews')) {
                $table->boolean('allow_early_reviews')->default(false);
            }
        });
    }

    public function down()
    {
        Schema::table('ranking_settings', function (Blueprint $table) {
            $table->dropColumn('allow_early_reviews');
        });
    }
};
