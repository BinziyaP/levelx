<?php

namespace App\Services;

use App\Models\BundleRule;
use App\Models\Product;

class BundleDiscountService
{
    /**
     * Calculate discount for the given cart items based on strict business logic.
     *
     * Rules:
     * 1. Minimum 2 items, Maximum 6 items. Outside this range -> No discount.
     * 2. Item Count Rules: Base discount based on total quantity (e.g., 2-3 items -> 5%).
     * 3. Category Variety Rules: Bonus based on unique categories (e.g., 2 cats -> +3%).
     * 4. Discounts are summable (Base + Bonus).
     *
     * @param array $cartItems
     * @param \Illuminate\Support\Collection|null $rules
     * @return array
     */
    public function calculate(array $cartItems, $rules = null): array
    {
        $originalTotal = 0;
        $totalItems = 0;
        $uniqueProducts = 0;
        $uniqueCategories = [];
        
        // 1. Calculate Baselines
        // We need detailed product info for categories
        $productIds = array_keys($cartItems);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($cartItems as $id => $item) {
            $qty = $item['quantity'];
            
            // Security: Always use DB price if available
            if (isset($products[$id])) {
                $price = $products[$id]->price;
                $uniqueCategories[$products[$id]->category_id] = true;
            } else {
                // Fallback or skip if product invalid (e.g. deleted)
                $price = $item['price'] ?? 0; 
            }
            
            $originalTotal += $price * $qty;
            $totalItems += $qty;
            $uniqueProducts++;
        }
        
        $uniqueCategoryCount = count($uniqueCategories);

        // 2. Strict Limits Validation
        // Bundle discount requires at least 2 DIFFERENT products (not duplicates of the same)
        if ($uniqueProducts < 2 || $totalItems < 2 || $totalItems > 6) {
            return [
                'original_total' => round($originalTotal, 2),
                'discount_amount' => 0.00,
                'final_total' => round($originalTotal, 2),
                'applied_rule' => null,
                'breakdown' => [
                    'message' => $uniqueProducts < 2 
                        ? 'Bundle discount requires at least 2 different products.' 
                        : 'Bundle must contain between 2 and 6 items.'
                ]
            ];
        }

        // 3. Fetch Rules if not injected
        if ($rules === null) {
            $rules = BundleRule::where('is_active', true)->get();
        }

        $totalDiscountPercentage = 0;
        $appliedRulesList = [];

        // 4. Apply Item Count Rules (Base Discount)
        // These rules are fetched from the database 'bundle_rules' table.
        // They are NOT hardcoded. Admin can change min/max/percentage in DB.
        $itemCountRule = $rules->where('type', 'item_count')
            ->where('min_items', '<=', $totalItems)
            ->where('max_items', '>=', $totalItems)
            ->sortByDesc('discount_percentage') // Take best if overlaps (though shouldn't overlap ideally)
            ->first();

        if ($itemCountRule) {
            $totalDiscountPercentage += $itemCountRule->discount_percentage;
            $appliedRulesList[] = $itemCountRule->name . " ({$itemCountRule->discount_percentage}%)";
        }

        // 5. Apply Category Variety Rules (Bonus Discount)
        $categoryRule = $rules->where('type', 'category_variety')
            ->where('min_items', '<=', $uniqueCategoryCount) // min_items here means min_unique_categories
            ->sortByDesc('min_items') // Take the highest tier reached
            ->first();

        if ($categoryRule) {
            $totalDiscountPercentage += $categoryRule->discount_percentage;
            $appliedRulesList[] = $categoryRule->name . " (+{$categoryRule->discount_percentage}%)";
        }

        // 6. Calculate Final Amounts
        // Cap discount if needed (e.g. max 100% - sanity check)
        $totalDiscountPercentage = min($totalDiscountPercentage, 100);
        
        $discountAmount = ($originalTotal * $totalDiscountPercentage) / 100;
        $finalTotal = $originalTotal - $discountAmount;

        // Construct applied rule string for DB storage (simple string or json)
        // Existing system uses 'applied_rule' which seems to be a single object or array in JSON.
        // We'll return a composite object to fit existing structure but richer.
        
        $compositeRule = null;
        if ($totalDiscountPercentage > 0) {
            $compositeRule = [
                'name' => 'Bundle Discount (' . implode(' + ', $appliedRulesList) . ')',
                'discount_percentage' => $totalDiscountPercentage,
                'breakdown' => $appliedRulesList
            ];
        }

        return [
            'original_total' => round($originalTotal, 2),
            'discount_amount' => round($discountAmount, 2),
            'final_total' => round($finalTotal, 2),
            'applied_rule' => $compositeRule,
            'meta' => [
                'item_count' => $totalItems,
                'category_count' => $uniqueCategoryCount,
                'total_percentage' => $totalDiscountPercentage
            ]
        ];
    }
}
