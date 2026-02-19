<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderReturn;
use App\Services\RazorpayRefundService;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    protected $refundService;

    public function __construct(RazorpayRefundService $refundService)
    {
        $this->refundService = $refundService;
    }

    public function index()
    {
        $returns = OrderReturn::with('order.user')->latest()->paginate(10);
        return view('admin.returns.index', compact('returns'));
    }

    public function approve(OrderReturn $return)
    {
        if ($return->status !== 'pending') {
            return back()->with('error', 'Only pending returns can be approved.');
        }

        // Just mark as approved — customer will claim refund from their side
        $return->update(['status' => 'approved']);

        // Recalculate ranking for products in this order
        try {
            $order = $return->order;
            $rankingService = app(\App\Services\ProductRankingService::class);
            $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;
            if (is_array($items)) {
                foreach ($items as $key => $item) {
                    $pid = $item['product_id'] ?? $item['id'] ?? $key;
                    if ($pid) $rankingService->updateProductStats($pid);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Ranking update on return approval error: ' . $e->getMessage());
        }

        return back()->with('success', 'Return approved. Customer can now claim their refund.');
    }

    public function reject(OrderReturn $return, Request $request)
    {
        $return->update([
            'status' => 'rejected',
            // 'reason' => $request->reason // Optionally add admin note
        ]);
        
        return back()->with('success', 'Return request rejected.');
    }
}
