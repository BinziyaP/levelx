<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\OrderReturn;
use App\Models\ReturnLog;
use App\Services\ReturnService;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    protected $returnService;

    public function __construct(ReturnService $returnService)
    {
        $this->returnService = $returnService;
    }

    /**
     * List disputes for the seller's products.
     */
    public function index()
    {
        $returns = OrderReturn::with(['order'])
            ->where('seller_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('seller.returns.index', compact('returns'));
    }

    /**
     * Show dispute detail for seller.
     */
    public function show(OrderReturn $return)
    {
        if ($return->seller_id !== auth()->id()) {
            abort(403);
        }

        $return->load(['order', 'evidences', 'logs.changer']);
        return view('seller.returns.show', compact('return'));
    }

    /**
     * Seller response to a dispute.
     */
    public function respond(Request $request, OrderReturn $return)
    {
        if ($return->seller_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $this->returnService->respond(
            $return,
            $request->message,
            auth()->id()
        );

        return back()->with('success', 'Your response has been added to the case history.');
    }
}
