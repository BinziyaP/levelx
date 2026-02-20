<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Extend order_returns table
        Schema::table('order_returns', function (Blueprint $table) {
            if (!Schema::hasColumn('order_returns', 'seller_id')) {
                $table->foreignId('seller_id')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('order_returns', 'resolution_type')) {
                $table->enum('resolution_type', ['full_refund', 'partial_refund', 'no_refund'])->nullable()->after('status');
            }
            if (!Schema::hasColumn('order_returns', 'resolved_by')) {
                $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('order_returns', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable();
            }
        });

        // 2. Create return_logs table (audit trail)
        if (!Schema::hasTable('return_logs')) {
            Schema::create('return_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_return_id')->constrained('order_returns')->onDelete('cascade');
                $table->string('old_status')->nullable();
                $table->string('new_status');
                $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
                $table->text('note')->nullable();
                $table->timestamps();
            });
        }

        // 3. Create return_evidences table
        if (!Schema::hasTable('return_evidences')) {
            Schema::create('return_evidences', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_return_id')->constrained('order_returns')->onDelete('cascade');
                $table->string('file_path');
                $table->string('file_type')->nullable(); // image, pdf, etc.
                $table->timestamps();
            });
        }

        // 4. Drop previous standalone disputes tables (if they exist)
        Schema::dropIfExists('dispute_evidences');
        Schema::dropIfExists('dispute_logs');
        Schema::dropIfExists('disputes');
    }

    public function down()
    {
        Schema::dropIfExists('return_evidences');
        Schema::dropIfExists('return_logs');
        
        Schema::table('order_returns', function (Blueprint $table) {
            $table->dropColumn(['seller_id', 'resolution_type', 'resolved_by', 'resolved_at']);
        });
    }
};
