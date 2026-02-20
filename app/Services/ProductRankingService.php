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
    /**
     * Valid order statuses that count as a completed sale.
     * We exclude cancelled and requested statuses as per requirements.
     */
    private $validSaleStatuses = [
        'paid', 'approved', 'processing', 'packed', 'shipped', 'delivered', 'completed'
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
     * Calculate return ratio for an order.
     * Returns a float between 0 and 1.
     */
    private function getReturnRatio($order)
    {
        $returnRequest = $order->returnRequest;
        
        // A return only affects ranking if it is 'resolved' 
        // AND has a resolution type of 'full_refund' or 'partial_refund'
        if (!$returnRequest || $returnRequest->status !== 'resolved') {
            return 0;
        }

        if (!in_array($returnRequest->resolution_type, ['full_refund', 'partial_refund'])) {
            return 0;
        }

        // Weighted return impact: refund_amount / total_price
        $total = (float) $order->total_price;
        if ($total <= 0) return 1;

        $refund = (float) $returnRequest->refund_amount;
        $ratio = $refund / $total;

        return min(max($ratio, 0), 1);
    }

    public function updateProductStats($productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return;
        }

        // Calculate Total Sales and Returns from orders
        // Use eager loading for returnRequest and filter orders for performance
        $sales = 0;
        $returns = 0;

        // Optimization: Instead of Order::all(), we fetch orders in chunks with eager loading
        Order::with('returnRequest')->chunk(200, function ($orders) use ($product, &$sales, &$returns) {
            foreach ($orders as $order) {
                if (!$this->isOrderASale($order)) {
                    continue;
                }

                $items = $order->items;
                if (is_string($items)) {
                    $items = json_decode($items, true);
                }
                if (!is_array($items)) continue;

                $returnRatio = $this->getReturnRatio($order);

                foreach ($items as $key => $item) {
                    $itemId = $item['product_id'] ?? $item['id'] ?? $key;
                    if ($itemId == $product->id) {
                        $qty = isset($item['quantity']) ? (int)$item['quantity'] : 1;
                        $sales += $qty;

                        if ($returnRatio > 0) {
                            $returns += ($qty * $returnRatio);
                        }
                    }
                }
            }
        });

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

        // Optimization: Eager load returnRequest to avoid duplicate queries
        Order::with('returnRequest')->chunk(200, function ($orders) use (&$stats) {
            foreach ($orders as $order) {
                if (!$this->isOrderASale($order)) {
                    continue;
                }

                $returnRatio = $this->getReturnRatio($order);

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
                    if ($returnRatio > 0) {
                        $stats[$pid]['returns'] += ($qty * $returnRatio);
                    }
                }
            }
        });

        // STEP 2: Loop ALL products in chunks to update ranking_score
        $updated = 0;

        Product::chunk(200, function ($products) use ($stats, &$updated) {
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
