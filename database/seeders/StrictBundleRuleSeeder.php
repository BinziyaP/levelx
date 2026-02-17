<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BundleRule;
use Illuminate\Support\Facades\DB;

class StrictBundleRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // specific logic for strict bundle builder
        // Clear existing rules to avoid confusion
        BundleRule::truncate();

        // 1. Item Count Rules
        // 2-3 Items = 5%
        BundleRule::create([
            'name' => 'Buy 2-3 Items, Get 5% Off',
            'type' => 'item_count', 
            'min_items' => 2,
            'max_items' => 3,
            'discount_type' => 'percentage',
            'discount_value' => 5.00,
            'discount_percentage' => 5.00, // Keep for backward compat if needed
            'is_active' => true,
        ]);

        // 4-6 Items = 10%
        BundleRule::create([
            'name' => 'Buy 4-6 Items, Get 10% Off',
            'type' => 'item_count',
            'min_items' => 4,
            'max_items' => 6,
            'discount_type' => 'percentage',
            'discount_value' => 10.00,
            'discount_percentage' => 10.00,
            'is_active' => true,
        ]);

        // 2. Category Variety Rules
        // 2 Unique Categories = +3%
        BundleRule::create([
            'name' => 'Variety Bonus: 2 Categories',
            'type' => 'category_variety',
            'min_items' => 2, // Interpreted as min unique categories
            'max_items' => 100,
            'discount_type' => 'percentage',
            'discount_value' => 3.00,
            'discount_percentage' => 3.00,
            'is_active' => true,
        ]);
        
        // 3 Unique Categories = +5%
        BundleRule::create([
            'name' => 'Variety Bonus: 3 Categories',
            'type' => 'category_variety',
            'min_items' => 3, // Interpreted as min unique categories
            'max_items' => 100,
            'discount_type' => 'percentage',
            'discount_value' => 5.00,
            'discount_percentage' => 5.00,
            'is_active' => true,
        ]);
    }
}
