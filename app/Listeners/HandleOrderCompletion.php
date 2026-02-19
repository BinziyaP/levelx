<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use App\Models\ProductSalesHistory;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleOrderCompletion
{
    public function handle(OrderCompleted $event)
    {
        $order = $event->order;
        
        // Loop through order items
        // Assuming items are stored as JSON in 'items' column
        if (!is_array($order->items)) {
            $items = json_decode($order->items, true);
        } else {
            $items = $order->items;
        }

        if (!$items) return;

        foreach ($items as $item) {
            $productId = $item['product_id'] ?? $item['id'] ?? null;
            if (!$productId) continue;

            $quantity = $item['quantity'] ?? 1;
            $price = $item['price'] ?? 0;
            $revenue = $quantity * $price;

            $product = Product::find($productId);
            if (!$product) continue;

            // 1. Record Sales History
            ProductSalesHistory::create([
                'product_id' => $productId,
                'quantity' => $quantity,
                'revenue' => $revenue,
                'recorded_at' => now(),
            ]);

            // 2. Update Aggregates (Sales count already handled by existing logic usually, but let's ensure compliance)
            // Existing logic updates `total_sales` in `ProductRankingService` or similar.
            // Requirement says: "Update aggregates". 
            // Let's increment safe.
            $product->increment('total_sales', $quantity);

            // 3. Update Best Seller Status
            $product->determineBestSellerStatus();
        }
    }
}
