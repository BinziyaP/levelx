<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Order;
use App\Events\ReviewSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        // 1. Check if user already reviewed
        $hasReviewed = ProductReview::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();

        if ($hasReviewed) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        // 2. Check if user actually purchased the product
        // Assuming orders have items as JSON or localized table. 
        // Based on previous analysis, Order items are in 'items' JSON column.
        
        $hasPurchased = Order::where('user_id', $user->id)
            ->where(function($query) {
                // Check settings
                $settings = \DB::table('ranking_settings')->first();
                $allowedOrderStatuses = ['completed', 'delivered', 'approved', 'paid'];
                $allowedShippingStatuses = ['delivered'];
                if ($settings && $settings->allow_early_reviews) {
                    $allowedOrderStatuses = array_merge($allowedOrderStatuses, ['processing']);
                    $allowedShippingStatuses = array_merge($allowedShippingStatuses, ['packed', 'shipped']);
                }
                
                // Check both order status AND shipping_status columns
                $query->whereIn('status', $allowedOrderStatuses)
                      ->orWhereIn('shipping_status', $allowedShippingStatuses);
            })
            ->get()
            ->contains(function ($order) use ($product) {
                $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;
                if (!$items) return false;
                
                foreach ($items as $key => $item) {
                     // Check both ID structure possibilities and Key
                     $itemId = $item['product_id'] ?? $item['id'] ?? $key;
                     if ($itemId == $product->id) return true;
                }
                return false;
            });

        // Debugging / Development Bypass: If no orders found, strict check might be annoying.
        // But requirement says "Only users who purchased can review".
        if (!$hasPurchased) {
             return back()->with('error', 'You can only review products you have purchased and received.');
        }

        // 3. Create Review
        $review = ProductReview::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'rating' => $request->rating,
            'review_text' => $request->review_text,
        ]);

        // 4. Dispatch Event
        event(new ReviewSubmitted($review));

        return back()->with('success', 'Thank you for your review!');
    }
}
