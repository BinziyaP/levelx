
if (!\Illuminate\Support\Facades\Schema::hasTable('cart_snapshots')) {
    try {
        \Illuminate\Support\Facades\Schema::create('cart_snapshots', function (\Illuminate\Database\Schema\Blueprint $table) {
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
        echo "SUCCESS: Table 'cart_snapshots' created.\n";
    } catch (\Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
} else {
    echo "INFO: Table 'cart_snapshots' already exists.\n";
}
