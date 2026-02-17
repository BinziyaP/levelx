<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\BundleRule;
use App\Services\BundleDiscountService;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SimpleBundleTest extends TestCase
{
    use DatabaseTransactions;

    public function test_simple_rule_retrieval()
    {
        BundleRule::query()->delete();

        $rule = BundleRule::create([
            'name' => 'Simple',
            'type' => 'product',
            'target_id' => 999,
            'min_items' => 1,
            'max_items' => 10,
            'discount_percentage' => 50,
            'is_active' => true,
        ]);

        $fetched = BundleRule::all();
        $cart = [999 => ['quantity' => 5, 'price' => 100]];
        
        $debug = "Fetched Rules:\n" . json_encode($fetched->toArray(), JSON_PRETTY_PRINT) . "\n";
        $debug .= "Cart:\n" . json_encode($cart, JSON_PRETTY_PRINT) . "\n";

        // Service Check
        $service = new BundleDiscountService();
        //$cart defined above
        
        //$debug .= "Cart:\n" ... removed duplicate
        
        $result = $service->calculate($cart, $fetched);
        
        $debug .= "Result:\n" . json_encode($result, JSON_PRETTY_PRINT) . "\n";
        file_put_contents('simple_debug.txt', $debug);

        // 5 * 100 = 500 total. 50% discount = 250.
        $this->assertEquals(250, $result['discount_amount'], "Service should apply discount");
    }
}
