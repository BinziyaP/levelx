
if (!\Illuminate\Support\Facades\Schema::hasColumn('bundle_rules', 'discount_type')) {
    try {
        \Illuminate\Support\Facades\Schema::table('bundle_rules', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->string('discount_type')->default('percentage')->after('discount_percentage');
            $table->decimal('discount_value', 10, 2)->nullable()->after('discount_type');
        });
        echo "SUCCESS: Columns added to 'bundle_rules'.\n";
    } catch (\Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
} else {
    echo "INFO: Columns already exist.\n";
}
