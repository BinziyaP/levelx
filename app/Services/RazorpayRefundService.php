<?php

namespace App\Services;

use Razorpay\Api\Api;
use App\Models\OrderReturn;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RazorpayRefundService
{
    protected $api;

    public function __construct()
    {
        $this->api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    }

    public function processRefund(OrderReturn $return)
    {
        // 1. Idempotency Check
        if ($return->status === 'refunded' || $return->razorpay_refund_id) {
            Log::info("Refund already processed for Return ID: {$return->id}");
            return true;
        }

        // 2. Validate State
        if ($return->status !== 'approved') {
            throw new \Exception("Return request must be approved before refunding.");
        }

        $paymentId = $return->order->payment_id; 
        if (!$paymentId) {
            throw new \Exception("No payment ID found for Order ID: {$return->order_id}");
        }

        return DB::transaction(function () use ($return, $paymentId) {
            try {
                // 3. Call Razorpay API
                // We typically refund the full amount requested, or full payment if not specified
                $refundData = [
                    'payment_id' => $paymentId,
                    'notes' => [
                        'return_id' => $return->id,
                        'reason' => $return->reason
                    ]
                ];

                if ($return->refund_amount) {
                    $refundData['amount'] = $return->refund_amount * 100; // Razorpay expects paise
                }

                $refund = $this->api->refund->create($refundData);

                // 4. Update Database
                $return->update([
                    'status' => 'refunded',
                    'razorpay_refund_id' => $refund->id,
                    'updated_at' => now()
                ]);

                // 5. Trigger Events (handled by observer or caller)
                // e.g. event(new RefundProcessed($return));

                return true;

            } catch (\Exception $e) {
                Log::error("Razorpay Refund Failed: " . $e->getMessage());
                // Re-throw to rollback transaction
                throw $e;
            }
        });
    }
}
