<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderReturn;
use App\Services\RazorpayRefundService;
use App\Services\ReturnService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderReturnController extends Controller
{
    protected $returnService;

    public function __construct(ReturnService $returnService)
    {
        $this->returnService = $returnService;
    }

    /**
     * List buyer's return requests/disputes.
     */
    public function index()
    {
        $returns = OrderReturn::with(['order', 'logs'])
            ->whereHas('order', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->latest()
            ->paginate(15);

        return view('disputes.index', compact('returns'));
    }

    /**
     * Show buyer's specific dispute detail.
     */
    public function show(OrderReturn $return)
    {
        if ($return->order->user_id !== auth()->id()) {
            abort(403);
        }

        $return->load(['order', 'evidences', 'logs.changer']);
        return view('disputes.show', compact('return'));
    }

    /**
     * Store a new dispute/return request.
     */
    public function store(Request $request, Order $order)
    {
        // 1. Authorization
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // 2. Validation
        $request->validate([
            'reason_dropdown' => 'required|string',
            'evidences' => 'required|array|min:1',
            'evidences.*' => 'required|image|max:5120', // 5MB limit
        ]);

        $reason = $request->reason_dropdown;

        try {
            // 3. Delegation to Service
            $this->returnService->createDispute($order, [
                'reason' => $reason,
            ], $request->file('evidences') ?? []);

            return redirect()->route('disputes.index')->with('success', 'Dispute case opened successfully. Our team will review it shortly.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Customer claims their approved refund.
     */
    public function claimRefund(Request $request, Order $order)
    {
        $return = $order->returnRequest;

        if (!$return || $order->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized or invalid request.'], 403);
        }

        try {
            $this->returnService->claimRefund($return);
            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully! The amount will be credited back to your original payment method.',
                'refund_id' => $return->razorpay_refund_id
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Claim Refund Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process refund: ' . $e->getMessage()
            ], 500);
        }
    }
}
