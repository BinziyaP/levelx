
if (!\Illuminate\Support\Facades\Schema::hasColumn('orders', 'original_price')) {
    try {
        \Illuminate\Support\Facades\Schema::table('orders', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->decimal('original_price', 10, 2)->after('total_price')->default(0);
            $table->decimal('discount_amount', 10, 2)->after('original_price')->default(0);
        });
        echo "SUCCESS: Columns 'original_price' and 'discount_amount' added to 'orders'.\n";
    } catch (\Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
} else {
    echo "INFO: Columns already exist.\n";
}
