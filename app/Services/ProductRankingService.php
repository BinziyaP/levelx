<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class ProductRankingService
{
    private $salesWeight;
    private $ratingWeight;
    private $returnWeight;

    /**
     * Valid order statuses that count as a completed sale.
     * 'approved' = admin approved the order (payment confirmed)
     * 'paid' = payment completed
     * 'delivered' / 'shipped' / 'packed' = fulfillment stages
     * 'returned' = was sold then returned (counts as sale AND return)
     */
    private $validSaleStatuses = [
        'paid', 'approved', 'delivered', 'shipped', 'packed', 'returned',
        'processing', 'completed',
        'return_requested', 'cancellation_requested', 'cancelled'
    ];

    public function __construct()
    {
        $this->loadWeights();
    }

    private function loadWeights()
    {
        $settings = DB::table('ranking_settings')->first();
        if ($settings) {
            $this->salesWeight = (float) ($settings->sales_weight ?? 1.0);
            $this->ratingWeight = (float) ($settings->rating_weight ?? 1.0);
            $this->returnWeight = (float) ($settings->return_weight ?? 1.0);
        } else {
            // Default weights
            $this->salesWeight = 1.0;
            $this->ratingWeight = 1.0;
            $this->returnWeight = 1.0;
        }
    }

    /**
     * Check if an order counts as a sale based on status OR shipping_status.
     */
    private function isOrderASale($order)
    {
        // Count as sale if order status is in valid list
        if (in_array($order->status, $this->validSaleStatuses)) {
            return true;
        }

        // Also count if shipping_status indicates fulfillment has started
        $shippingStatus = $order->shipping_status ?? null;
        if (in_array($shippingStatus, ['packed', 'shipped', 'delivered'])) {
            return true;
        }

        return false;
    }

    /**
     * Check if an order is a return.
     * Checks order status AND the order_returns table for approved returns.
     */
    private function isOrderReturned($order)
    {
        // Check order status for any return/cancel-related status
        $returnStatuses = ['returned', 'return_requested', 'cancelled', 'cancellation_requested'];
        if (in_array($order->status, $returnStatuses)) {
            return true;
        }

        if (($order->shipping_status ?? null) === 'returned') {
            return true;
        }

        // Also check if there's an approved return request in the order_returns table
        $hasApprovedReturn = \App\Models\OrderReturn::where('order_id', $order->id)
            ->whereIn('status', ['approved', 'refunded'])
            ->exists();

        return $hasApprovedReturn;
    }

    public function updateProductStats($productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return;
        }

        // Calculate Total Sales and Returns from orders
        $sales = 0;
        $returns = 0;

        $orders = Order::all();

        foreach ($orders as $order) {
            if (!$this->isOrderASale($order)) {
                continue;
            }

            $items = $order->items;
            if (is_string($items)) {
                $items = json_decode($items, true);
            }
            if (!is_array($items)) continue;

            foreach ($items as $key => $item) {
                $itemId = $item['product_id'] ?? $item['id'] ?? $key;
                if ($itemId == $product->id) {
                    $qty = isset($item['quantity']) ? (int)$item['quantity'] : 1;
                    $sales += $qty;

                    if ($this->isOrderReturned($order)) {
                        $returns += $qty;
                    }
                }
            }
        }

        // Use the ACTUAL average_rating from the product (set by HandleReviewSubmission)
        $avgRating = (float) ($product->average_rating ?? $product->avg_rating ?? 0);

        // Calculate Return Rate
        $returnRate = ($sales > 0) ? ($returns / $sales) * 100 : 0;

        // Calculate Score: (Sales × W1) + (Rating × W2) − (ReturnRate × W3)
        $score = ($sales * $this->salesWeight) + ($avgRating * $this->ratingWeight) - ($returnRate * $this->returnWeight);

        // Update Product quietly (no events triggered)
        $product->update([
            'total_sales' => $sales,
            'return_rate' => $returnRate,
            'ranking_score' => $score
        ]);

        // Update best seller status
        $product->determineBestSellerStatus();

        return $product;
    }

    public function recalculateAll()
    {
        // STEP 1: Aggregate sales/returns from ALL orders in one pass
        $stats = []; // product_id => ['sales' => 0, 'returns' => 0]

        Order::chunk(100, function ($orders) use (&$stats) {
            foreach ($orders as $order) {
                if (!$this->isOrderASale($order)) {
                    continue;
                }

                $isReturned = $this->isOrderReturned($order);

                $items = $order->items;
                if (is_string($items)) $items = json_decode($items, true);
                if (!is_array($items)) continue;

                foreach ($items as $key => $item) {
                    $pid = $item['product_id'] ?? $item['id'] ?? $key;
                    if ($pid === null) continue;

                    $qty = isset($item['quantity']) ? (int)$item['quantity'] : 1;

                    if (!isset($stats[$pid])) {
                        $stats[$pid] = ['sales' => 0, 'returns' => 0];
                    }

                    $stats[$pid]['sales'] += $qty;
                    if ($isReturned) {
                        $stats[$pid]['returns'] += $qty;
                    }
                }
            }
        });

        // STEP 2: Loop ALL products in chunks to update ranking_score
        // This ensures products with reviews but no sales are also updated
        $updated = 0;

        Product::chunk(100, function ($products) use ($stats, &$updated) {
            foreach ($products as $product) {
                $sales = 0;
                $returns = 0;

                // Use aggregated order stats if this product appeared in orders
                if (isset($stats[$product->id])) {
                    $sales = $stats[$product->id]['sales'];
                    $returns = $stats[$product->id]['returns'];
                }

                // Read ACTUAL average_rating from the product
                $avgRating = (float) ($product->average_rating ?? $product->avg_rating ?? 0);

                // Calculate return rate
                $returnRate = ($sales > 0) ? ($returns / $sales) * 100 : 0;

                // Calculate score
                $score = ($sales * $this->salesWeight) + ($avgRating * $this->ratingWeight) - ($returnRate * $this->returnWeight);

                // Update product quietly
                Product::where('id', $product->id)->update([
                    'total_sales' => $sales,
                    'return_rate' => $returnRate,
                    'ranking_score' => $score
                ]);

                // Update best seller status
                $product->refresh();
                $product->determineBestSellerStatus();

                $updated++;
            }
        });

        return $updated;
    }
}
