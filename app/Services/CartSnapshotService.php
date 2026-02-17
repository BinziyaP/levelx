<?php

namespace App\Services;

use App\Models\CartSnapshot;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class CartSnapshotService
{
    /**
     * Create a new cart snapshot.
     *
     * @param int|null $userId
     * @param array $items
     * @param array $calculationResult
     * @param string $razorpayOrderId
     * @return CartSnapshot
     */
    public function createSnapshot($userId, array $items, array $calculationResult, string $razorpayOrderId, array $appliedRules = []): CartSnapshot
    {
        return CartSnapshot::create([
            'user_id' => $userId,
            'items' => $items,
            'original_total' => $calculationResult['original_total'],
            'discount_amount' => $calculationResult['discount_amount'],
            'final_total' => $calculationResult['final_total'],
            'razorpay_order_id' => $razorpayOrderId,
            'applied_rules' => $appliedRules,
        ]);
    }

    /**
     * Retrieve a snapshot by Razorpay Order ID.
     *
     * @param string $razorpayOrderId
     * @return CartSnapshot|null
     */
    public function getSnapshotByRazorpayId(string $razorpayOrderId): ?CartSnapshot
    {
        return CartSnapshot::where('razorpay_order_id', $razorpayOrderId)->first();
    }
}
