<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\ReturnLog;
use App\Models\ReturnEvidence;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReturnService
{
    /**
     * Allowed status transitions for the dispute/return state machine.
     */
    private $allowedTransitions = [
        'pending' => ['under_review', 'rejected'],
        'under_review' => ['approved', 'rejected', 'resolved'], // 'approved' kept for back-compat
        'approved' => [], // Final state
        'rejected' => [], // Final state
        'resolved' => [], // Final state
    ];

    /**
     * Create a new dispute/return request.
     */
    public function createDispute(Order $order, array $data, array $evidences = [])
    {
        return DB::transaction(function () use ($order, $data, $evidences) {
            // 1. Validate eligibility
            if (!$this->validateEligibility($order)) {
                throw new \Exception("This order is not eligible for a dispute.");
            }

            // 2. Identify Seller (from items)
            $sellerId = $this->resolveSellerId($order);

            // 3. Create OrderReturn
            $return = OrderReturn::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'reason' => $data['reason'],
                'seller_id' => $sellerId,
                'refund_amount' => 0, // Set during resolution
            ]);

            // 4. Handle Evidences
            foreach ($evidences as $file) {
                $path = $file->store('return-evidence', 'public');
                ReturnEvidence::create([
                    'order_return_id' => $return->id,
                    'file_path' => $path,
                    'file_type' => $file->getMimeType(),
                ]);
            }

            // 5. Initial Log
            $this->logHistory($return, null, 'pending', auth()->id(), "Dispute raised by buyer.");

            return $return;
        });
    }

    /**
     * Seller response to a dispute.
     * Log history and auto-transition to under_review if pending.
     */
    public function respond(OrderReturn $return, string $message, $sellerId)
    {
        return DB::transaction(function () use ($return, $message, $sellerId) {
            $oldStatus = $return->status;
            
            // 1. Log the response
            $this->logHistory($return, $oldStatus, $oldStatus, $sellerId, "Seller response: " . $message);
            
            // 2. Clear "pending" status if applicable
            if ($oldStatus === 'pending') {
                $return->status = 'under_review';
                $return->save();
                $this->logHistory($return, $oldStatus, 'under_review', $sellerId, "Case automatically moved to under review after seller response.");
            }
            
            return $return;
        });
    }

    /**
     * Transition a dispute status with validation.
     */
    public function transitionStatus(OrderReturn $return, string $newStatus, string $note = null, $resolverId = null)
    {
        return DB::transaction(function () use ($return, $newStatus, $note, $resolverId) {
            $oldStatus = $return->status;

            if (!$this->validateTransition($oldStatus, $newStatus)) {
                throw new \Exception("Invalid status transition from {$oldStatus} to {$newStatus}.");
            }

            $return->status = $newStatus;
            
            if ($newStatus === 'resolved' || $newStatus === 'approved' || $newStatus === 'rejected') {
                $return->resolved_by = $resolverId ?? auth()->id();
                $return->resolved_at = now();
            }

            $return->save();

            $this->logHistory($return, $oldStatus, $newStatus, auth()->id(), $note);

            return $return;
        });
    }

    /**
     * Resolve a dispute with specific refund logic.
     */
    public function resolve(OrderReturn $return, array $resolutionData)
    {
        return DB::transaction(function () use ($return, $resolutionData) {
            $type = $resolutionData['resolution_type']; // full_refund, partial_refund, no_refund
            $amount = 0;

            if ($type === 'full_refund') {
                $amount = $return->order->total_price;
            } elseif ($type === 'partial_refund') {
                $amount = $resolutionData['refund_amount'];
                if ($amount >= $return->order->total_price) {
                    $type = 'full_refund';
                    $amount = $return->order->total_price;
                }
            }

            // 1. Update Resolution details
            $return->resolution_type = $type;
            $return->refund_amount = $amount;
            
            // 2. Transition to resolved
            $this->transitionStatus($return, 'resolved', $resolutionData['note'] ?? "Dispute resolved as {$type}.");

            // 3. Update Order status if refunded (but don't process Razorpay yet)
            if ($amount > 0) {
                $return->order->update(['shipping_status' => 'returned']);
            }

            // 4. Trigger Product Ranking update
            try {
                $rankingService = app(ProductRankingService::class);
                foreach ($return->order->items as $key => $item) {
                    $pid = $item['product_id'] ?? $item['id'] ?? $key;
                    if ($pid) $rankingService->updateProductStats($pid);
                }
            } catch (\Exception $e) {
                Log::warning("Ranking update failed after dispute resolution: " . $e->getMessage());
            }

            return $return;
        });
    }

    /**
     * Public method for customer to claim their refund.
     */
    public function claimRefund(OrderReturn $return)
    {
        return DB::transaction(function () use ($return) {
            if ($return->status !== 'resolved') {
                throw new \Exception("Case must be resolved before claiming refund.");
            }

            if ($return->refund_amount <= 0) {
                throw new \Exception("No refund amount approved for this case.");
            }

            if ($return->razorpay_refund_id) {
                throw new \Exception("Refund has already been claimed and processed.");
            }

            // Process the actual Razorpay refund
            $this->applyRefund($return);

            // Trigger log
            $this->logHistory($return, 'resolved', 'resolved', auth()->id(), "Refund claimed by customer.");

            return true;
        });
    }

    /**
     * Helper to process the actual Razorpay refund.
     */
    private function applyRefund(OrderReturn $return)
    {
        try {
            // Reusing existing RazorpayRefundService
            $refundService = new RazorpayRefundService();
            $refundService->processRefund($return);
        } catch (\Exception $e) {
            Log::error("Razorpay Refund Failed for return {$return->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Validate if a status transition is allowed.
     */
    public function validateTransition(string $oldStatus, string $newStatus)
    {
        if (!isset($this->allowedTransitions[$oldStatus])) return false;
        return in_array($newStatus, $this->allowedTransitions[$oldStatus]);
    }

    /**
     * Check if an order is eligible for a dispute or return.
     * In this unified system, all active orders (pending to delivered) 
     * can use this professional workflow.
     */
    public function validateEligibility(Order $order)
    {
        // 1. Must be in a valid progressive status (not already cancelled/failed)
        $validStatuses = ['pending', 'packed', 'shipped', 'delivered'];
        if (!in_array($order->shipping_status, $validStatuses)) return false;

        // 2. Must not have an existing active dispute/return
        if ($order->returnRequest()->whereNotIn('status', ['rejected'])->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Log a history entry for audit trail.
     */
    public function logHistory(OrderReturn $return, $oldStatus, $newStatus, $userId, $note)
    {
        return ReturnLog::create([
            'order_return_id' => $return->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => $userId,
            'note' => $note,
        ]);
    }

    private function resolveSellerId(Order $order)
    {
        $items = $order->items;
        if (!is_array($items) || empty($items)) return null;

        // In this marketplace, we assume one seller per order or prioritize the first item's seller
        // The ID is often the key of the items array
        $ids = array_keys($items);
        $productId = null;
        
        if (!empty($ids) && (is_numeric($ids[0]) || strlen($ids[0]) > 0)) {
            $productId = $ids[0];
        }

        // Fallback to internal keys if the above failed or didn't look like an ID
        if (!$productId || !is_numeric($productId)) {
            $firstItem = reset($items);
            $productId = $firstItem['product_id'] ?? $firstItem['id'] ?? $productId;
        }

        if ($productId) {
            $product = \App\Models\Product::find($productId);
            return $product ? $product->user_id : null;
        }

        return null;
    }
}
