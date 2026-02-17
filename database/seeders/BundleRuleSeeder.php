<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BundleRule;

class BundleRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BundleRule::truncate();

        BundleRule::create([
            'name' => 'Starter Bundle',
            'min_items' => 2,
            'max_items' => 3,
            'discount_percentage' => 10,
            'is_active' => true,
        ]);

        BundleRule::create([
            'name' => 'Mega Bundle',
            'type' => BundleRule::TYPE_GLOBAL,
            'min_items' => 4,
            'max_items' => 6,
            'discount_percentage' => 20,
            'is_active' => true,
        ]);

        // Category Rule: Buy 2 Electronics (Cat ID 1) get 15% off
        BundleRule::create([
            'name' => 'Electronics Deal',
            'type' => BundleRule::TYPE_CATEGORY,
            'target_id' => 1, 
            'min_items' => 2,
            'max_items' => 5,
            'discount_percentage' => 15,
            'is_active' => true,
        ]);

        // Product Rule: Buy 3 of Product ID 1 get 25% off
        BundleRule::create([
            'name' => 'Flash Sale Item 1',
            'type' => BundleRule::TYPE_PRODUCT,
            'target_id' => 1,
            'min_items' => 3,
            'max_items' => 10,
            'discount_percentage' => 25,
            'is_active' => true,
        ]);
    }
}
