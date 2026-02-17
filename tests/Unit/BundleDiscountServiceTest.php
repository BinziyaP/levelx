<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\BundleDiscountService;
use App\Models\BundleRule;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BundleDiscountServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        BundleRule::query()->delete();
    }

    public function test_calculate_no_discount_for_single_item()
    {
        // Arrange
        BundleRule::create([
            'name' => 'Starter Bundle',
            'min_items' => 2,
            'max_items' => 3,
            'discount_percentage' => 10,
            'is_active' => true,
        ]);

        $cart = [
            1 => [
                'name' => 'Item 1',
                'quantity' => 1,
                'price' => 1000,
                'image' => 'test.jpg'
            ]
        ];

        $service = new BundleDiscountService();

        // Act
        $result = $service->calculate($cart);

        // Assert
        $this->assertEquals(1000, $result['original_total']);
        $this->assertEquals(0, $result['discount_amount']);
        $this->assertEquals(1000, $result['final_total']);
        $this->assertNull($result['applied_rule']);
    }

    public function test_calculate_discount_for_bundle_range()
    {
        // Arrange
        BundleRule::create([
            'name' => 'Starter Bundle',
            'min_items' => 2,
            'max_items' => 3,
            'discount_percentage' => 10,
            'is_active' => true,
        ]);

        $cart = [
            1 => [
                'name' => 'Item 1',
                'quantity' => 1,
                'price' => 1000,
                'image' => 'test.jpg'
            ],
            2 => [
                'name' => 'Item 2',
                'quantity' => 1,
                'price' => 2000,
                'image' => 'test2.jpg'
            ]
        ];

        $service = new BundleDiscountService();

        // Act
        $result = $service->calculate($cart);

        // Assert
        $this->assertEquals(3000, $result['original_total']);
        $this->assertEquals(300, $result['discount_amount']); // 10% of 3000
        $this->assertEquals(2700, $result['final_total']);
        $this->assertNotNull($result['applied_rule']);
        $this->assertEquals('Starter Bundle', $result['applied_rule']['name']);
    }

    public function test_calculate_best_discount_overlap()
    {
        // Arrange
        BundleRule::create([
            'name' => 'Small Bundle',
            'min_items' => 2,
            'max_items' => 5,
            'discount_percentage' => 5,
            'is_active' => true,
        ]);

        BundleRule::create([
            'name' => 'Big Bundle',
            'min_items' => 4,
            'max_items' => 6,
            'discount_percentage' => 20,
            'is_active' => true,
        ]);

        // 4 items should trigger Big Bundle (20%) because logic prioritizes higher min_items
        $cart = [
            1 => ['quantity' => 2, 'price' => 1000],
            2 => ['quantity' => 2, 'price' => 1000]
        ];

        $service = new BundleDiscountService();

        // Act
        $result = $service->calculate($cart);

        // Assert
        $this->assertEquals(4000, $result['original_total']);
        $this->assertEquals(800, $result['discount_amount']); // 20% of 4000
        $this->assertEquals('Big Bundle', $result['applied_rule']['name']);
    }

    public function test_calculate_product_specific_discount()
    {
        // Mock Product fetching (Partial integration test requiring DB)
        $product = \App\Models\Product::factory()->create(['price' => 1000, 'category_id' => 99]);
        $productId = $product->id;

        // Global Rule (10%)
        BundleRule::create([
            'name' => 'Global',
            'type' => BundleRule::TYPE_GLOBAL,
            'min_items' => 2,
            'max_items' => 5,
            'discount_percentage' => 10,
            'is_active' => true,
        ]);

        // Product Rule (25% for Product)
        BundleRule::create([
            'name' => 'Product 1 Deal',
            'type' => BundleRule::TYPE_PRODUCT,
            'target_id' => $productId,
            'min_items' => 2,
            'max_items' => 5,
            'discount_percentage' => 25,
            'is_active' => true,
        ]);

        $cart = [
            $productId => ['quantity' => 2, 'price' => 1000] // Target Matches Product Rule
        ];

        $service = new BundleDiscountService();
        
        dump(BundleRule::all()->toArray());
        
        $result = $service->calculate($cart);

        dump($result);

        // Should apply 25% (Product Rule) instead of 10% (Global)
        // 2000 * 0.25 = 500 discount
        $this->assertEquals(500, $result['discount_amount']);
        $this->assertEquals('Product 1 Deal', $result['applied_rule']['name'] ?? '');
    }

    public function test_global_wins_if_better()
    {
         $product = \App\Models\Product::factory()->create(['category_id' => 99]);
         $productId = $product->id;

         // Global Rule (30%)
         BundleRule::create([
            'name' => 'Global Huge',
            'type' => BundleRule::TYPE_GLOBAL,
            'min_items' => 2,
            'max_items' => 5,
            'discount_percentage' => 30,
            'is_active' => true,
        ]);

        // Product Rule (10%)
        BundleRule::create([
            'name' => 'Product Small',
            'type' => BundleRule::TYPE_PRODUCT,
            'target_id' => $productId,
            'min_items' => 2,
            'max_items' => 5,
            'discount_percentage' => 10,
            'is_active' => true,
        ]);

        $cart = [
            $productId => ['quantity' => 2, 'price' => 1000]
        ];
        
        $service = new BundleDiscountService();
        $result = $service->calculate($cart);

        // Should apply 30% (Global) because it's better
        // 2000 * 0.30 = 600
        $this->assertEquals(600, $result['discount_amount']);
    }
}
