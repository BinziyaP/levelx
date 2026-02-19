<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderReturn;
use App\Services\RazorpayRefundService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderReturnController extends Controller
{
    public function store(Request $request, Order $order)
    {
        // 1. Authorization
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // 2. Validation
        $request->validate([
            'reason' => 'required|string|max:500',
            'custom_reason' => 'nullable|string|max:500',
        ]);

        // Use custom reason if "other" was selected
        $reason = $request->reason === 'other' ? $request->custom_reason : $request->reason;
        
        if (empty($reason)) {
            return back()->with('error', 'Please provide a reason.');
        }

        // 3. Status Check - Allow cancel for pending, return for delivered
        if (!in_array($order->status, ['pending', 'paid']) && $order->shipping_status !== 'delivered') {
            return back()->with('error', 'This order cannot be cancelled or returned at this time.');
        }

        // 4. Create Return Request
        OrderReturn::create([
            'order_id' => $order->id,
            'status' => 'pending',
            'reason' => $reason,
            'refund_amount' => $order->total_amount ?? $order->total_price,
        ]);

        // 5. Update order status based on context
        if ($order->shipping_status === 'delivered') {
            $order->update(['status' => 'return_requested']);
        } else {
            $order->update(['status' => 'cancellation_requested']);
        }

        // 6. Recalculate ranking for products in this order
        try {
            $rankingService = app(\App\Services\ProductRankingService::class);
            $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;
            if (is_array($items)) {
                foreach ($items as $key => $item) {
                    $pid = $item['product_id'] ?? $item['id'] ?? $key;
                    if ($pid) $rankingService->updateProductStats($pid);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Ranking update on return error: ' . $e->getMessage());
        }

        if ($order->shipping_status === 'delivered') {
            return back()->with('success', 'Return request submitted successfully. We will process it shortly.');
        } else {
            return back()->with('success', 'Cancellation request submitted successfully.');
        }
    }

    /**
     * Customer claims their refund after admin approval.
     */
    public function claimRefund(Order $order)
    {
        // 1. Authorization
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // 2. Check return request is approved
        $returnRequest = $order->returnRequest;
        if (!$returnRequest || $returnRequest->status !== 'approved') {
            return back()->with('error', 'No approved refund available for this order.');
        }

        try {
            // 3. Process refund via Razorpay
            $refundService = new RazorpayRefundService();
            $refundService->processRefund($returnRequest);

            // 4. Update order status
            $order->update(['status' => 'cancelled']);

            // 5. Recalculate ranking for products in this order
            try {
                $rankingService = app(\App\Services\ProductRankingService::class);
                $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;
                if (is_array($items)) {
                    foreach ($items as $key => $item) {
                        $pid = $item['product_id'] ?? $item['id'] ?? $key;
                        if ($pid) $rankingService->updateProductStats($pid);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Ranking update on refund error: ' . $e->getMessage());
            }

            return back()->with('success', 'Refund of ₹' . number_format($returnRequest->refund_amount, 2) . ' processed successfully! It will reflect in your account within 5-7 business days.');

        } catch (\Exception $e) {
            return back()->with('error', 'Refund failed: ' . $e->getMessage());
        }
    }
}
