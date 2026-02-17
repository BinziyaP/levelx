$product = App\Models\Product::first();
$pid = $product ? $product->id : 1;
// Create rule
try {
    $rule = App\Models\BundleRule::create([
        'name' => 'Tinker Test Rule',
        'type' => 'product',
        'target_id' => $pid,
        'min_items' => 1,
        'max_items' => 10,
        'discount_percentage' => 50,
        'is_active' => true
    ]);
    echo "Rule Created: " . $rule->id . "\n";
} catch (\Exception $e) {
    echo "Rule Create Error: " . $e->getMessage() . "\n";
}

$service = new App\Services\BundleDiscountService();
$cart = [
    $pid => ['quantity' => 2, 'price' => 1000]
];
$result = $service->calculate($cart);
echo "CALCULATION_RESULT: " . json_encode($result) . "\n";
exit;
