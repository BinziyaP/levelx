
if (!Schema::hasColumn('products', 'quantity')) {
    try {
        Schema::table('products', function ($table) {
            $table->integer('quantity')->default(0)->after('price');
        });
        echo "SUCCESS: Column 'quantity' added to 'products'.\n";
    } catch (\Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
} else {
    echo "INFO: Column 'quantity' already exists.\n";
}
