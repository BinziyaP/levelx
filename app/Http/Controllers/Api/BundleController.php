<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BundleDiscountService;

class BundleController extends Controller
{
    protected $bundleService;

    public function __construct(BundleDiscountService $bundleService)
    {
        $this->bundleService = $bundleService;
    }

    public function calculate(Request $request)
    {
        // Expecting { items: { product_id: { quantity: x, ... }, ... } }
        // OR { items: [ { id: 1, quantity: 2 }, ... ] }
        // Let's support the session structure which is usually [id => details]
        // But API usually sends JSON list.
        
        $inputItems = $request->input('items', []);
        
        // Normalize input to [id => ['quantity' => x]]
        $cartItems = [];
        foreach ($inputItems as $key => $val) {
            // Handle array list vs keyed object
            if (is_array($val) && isset($val['id'])) {
                $id = $val['id'];
                $qty = $val['quantity'];
            } elseif (is_numeric($key)) {
                // Should not happen if standardized, but let's assume standard API format:
                // items: [ {id: 1, quantity: 2} ]
                 $id = $val['id'] ?? $key; // Fallback
                 $qty = $val['quantity'] ?? 1;
            } else {
                 // items: { "1": { "quantity": 2 } }
                 $id = $key;
                 $qty = $val['quantity'] ?? $val;
            }
            
            if ($id && $qty > 0) {
                $cartItems[$id] = [
                    'quantity' => $qty,
                    'price' => 0 // Placeholder, Service will fetch real price
                ];
            }
        }

        $result = $this->bundleService->calculate($cartItems);

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}
