<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderReturn;
use App\Services\ReturnService;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    protected $returnService;

    public function __construct(ReturnService $returnService)
    {
        $this->returnService = $returnService;
    }

    /**
     * List all disputes.
     */
    public function index()
    {
        $disputes = OrderReturn::with(['order.user', 'seller'])
            ->latest()
            ->paginate(20);

        return view('admin.disputes.index', compact('disputes'));
    }

    /**
     * Show dispute detail for admin.
     */
    public function show(OrderReturn $return)
    {
        $return->load(['order.user', 'seller', 'evidences', 'logs.changer']);
        // The view expects $dispute, but OrderReturn is bound to $return in route
        $dispute = $return;
        return view('admin.disputes.show', compact('dispute'));
    }

    /**
     * Update status (e.g., mark as under review).
     */
    public function updateStatus(Request $request, OrderReturn $return)
    {
        $request->validate(['status' => 'required|string']);

        try {
            $this->returnService->transitionStatus($return, $request->status, $request->note);
            return back()->with('success', "Case status updated to " . ucfirst(str_replace('_', ' ', $request->status)));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Resolve the case with refund options.
     */
    public function resolve(Request $request, OrderReturn $return)
    {
        $request->validate([
            'resolution_type' => 'required|in:full_refund,partial_refund',
            'refund_amount' => 'required_if:resolution_type,partial_refund|nullable|numeric|min:0',
            'note' => 'nullable|string|max:2000',
        ]);

        try {
            $this->returnService->resolve($return, $request->all());
            return redirect()->route('admin.disputes.index')->with('success', 'Case resolved successfully.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Dispute Resolution Exception: " . $e->getMessage());
            return back()->with('error', 'Failed to resolve dispute: ' . $e->getMessage());
        }
    }
}
