<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function checkout()
    {
        try {
            $cart = session()->get('cart');

            if (!$cart) {
                return redirect()->back()->with('error', 'Your cart is empty!');
            }

            // Calculate Bundle Discount
            $bundleService = new \App\Services\BundleDiscountService();
            $calculation = $bundleService->calculate($cart);
            
            $finalTotal = $calculation['final_total']; // This is in INR

            // Check if Razorpay keys are set
            $keyId = env('RAZORPAY_KEY_ID');
            $keySecret = env('RAZORPAY_KEY_SECRET');

            if (empty($keyId) || empty($keySecret)) {
                return redirect()->back()->with('error', 'Razorpay Keys are missing. Please configure .env file.');
            }

            // Real Razorpay Order Creation (Using HTTP Client to avoid SDK timeout issues)
            // $api = new Api($keyId, $keySecret);
            
            $razorpayAmount = $finalTotal * 100; // Convert to paise
            
            $orderData = [
                'receipt'         => 'rcptid_' . Str::random(10),
                'amount'          => $razorpayAmount, 
                'currency'        => 'INR',
                'payment_capture' => 1 // Auto capture
            ];
            
            // $razorpayOrder = $api->order->create($orderData);

            $response = \Illuminate\Support\Facades\Http::withBasicAuth($keyId, $keySecret)
                ->withOptions([
                    'connect_timeout' => 30,
                    'timeout' => 60,
                    'force_ip_resolve' => 'v4'
                ])
                ->post('https://api.razorpay.com/v1/orders', $orderData);

            if ($response->failed()) {
                throw new \Exception('Razorpay Error: ' . $response->body());
            }

            $razorpayOrder = $response->json();

            // Create Cart Snapshot
            $snapshotService = new \App\Services\CartSnapshotService();
            $snapshotService->createSnapshot(
                auth()->id(),
                $cart,
                $calculation,
                $razorpayOrder['id'],
                $calculation['applied_rule'] ? [$calculation['applied_rule']] : []
            );

            return view('payment.index', [
                'order_id' => $razorpayOrder['id'],
                'amount' => $razorpayAmount, // Send the actual payable amount to the view (100 paise)
                'key' => $keyId,
                'cart' => $cart,
                'total' => $finalTotal, // Show the final discounted total
                'original_total' => $calculation['original_total'],
                'discount_amount' => $calculation['discount_amount'],
                'applied_rule' => $calculation['applied_rule']
            ]);

        } catch (\Exception $e) {
            Log::error('Razorpay Order Creation Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Razorpay Error: ' . $e->getMessage());
        }
    }

    public function paymentCallback(Request $request)
    {
        $input = $request->all();
        $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));

        try {
            $attributes = [
                'razorpay_order_id' => $input['razorpay_order_id'],
                'razorpay_payment_id' => $input['razorpay_payment_id'],
                'razorpay_signature' => $input['razorpay_signature']
            ];

            $api->utility->verifyPaymentSignature($attributes);

            // Payment Successful - Create Order
            
            // Try to find snapshot first (New Flow)
            $snapshotService = new \App\Services\CartSnapshotService();
            $snapshot = $snapshotService->getSnapshotByRazorpayId($input['razorpay_order_id']);

            if ($snapshot) {
                $items = $snapshot->items;
                $finalTotal = $snapshot->final_total;
                $originalTotal = $snapshot->original_total;
                $discountAmount = $snapshot->discount_amount;
            } else {
                // Fallback for backward compatibility (or if snapshot missing)
                $items = session()->get('cart');
                if (!$items) {
                    throw new \Exception("Cart session expired and no snapshot found.");
                }
                $finalTotal = 0;
                foreach ($items as $item) {
                    $finalTotal += $item['price'] * $item['quantity'];
                }
                $originalTotal = $finalTotal;
                $discountAmount = 0;
            }

            DB::beginTransaction();

            $order = Order::create([
                'user_id' => auth()->id(),
                'items' => $items,
                'total_price' => $finalTotal,
                'original_price' => $originalTotal,
                'discount_amount' => $discountAmount,
                'status' => 'paid',
                'payment_id' => $input['razorpay_payment_id'],
                'ip_address' => $request->ip(),
            ]);

            // Fraud Evaluation
            try {
                $fraudService = new \App\Services\Fraud\FraudScoringService();
                $fraudService->evaluate($order);
            } catch (\Exception $e) {
                Log::error('Fraud Evaluation Error: ' . $e->getMessage());
            }

            // Notify Sellers
            foreach ($items as $id => $details) {
                $product = \App\Models\Product::find($id);
                if ($product && $product->user) {
                    $product->user->notify(new \App\Notifications\PaymentReceived([
                        'amount' => $details['price'] * $details['quantity'],
                        'buyer_name' => auth()->user()->name,
                        'product_name' => $details['name'],
                        'order_id' => $input['razorpay_order_id'] ?? 'N/A',
                        'payment_id' => $input['razorpay_payment_id'] ?? 'N/A',
                        'internal_order_id' => $order->id
                    ]));
                }
            }

            session()->forget('cart');

            // Also clear the persistent cart from database
            if (auth()->check()) {
                \App\Models\Cart::where('user_id', auth()->id())->delete();
            }

            DB::commit();

            if ($order->is_suspicious) {
                 return redirect()->route('orders.index')->with('warning', 'Your order is being reviewed for security verification.');
            }

            return redirect()->route('orders.index')->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Razorpay Payment Verification Error: ' . $e->getMessage());
            return redirect()->route('cart.index')->with('error', 'Payment verification failed: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $orders = \App\Models\Order::where('user_id', auth()->id())->with('returnRequest')->latest()->get();
        return view('orders.index', compact('orders'));
    }

    public function trackOrder($id)
    {
        $order = \App\Models\Order::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        // If tracking number exists, try to fetch fresh data
        if ($order->tracking_number && $order->courier_code) {
             try {
                $apiKey = env('TRACKCOURIER_API_KEY');
                
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'X-API-Key' => $apiKey
                ])->get("https://api.trackcourier.io/v1/track", [
                    'tracking_number' => $order->tracking_number,
                    'courier' => $order->courier_code
                ]);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $order->tracking_response = $responseData;

                    // Update shipment status from API if available (Handle PascalCase)
                    if (isset($responseData['data']['ShipmentState'])) {
                        $apiStatus = strtolower($responseData['data']['ShipmentState']);
                        if (in_array($apiStatus, ['pending', 'packed', 'shipped', 'delivered'])) {
                            $order->shipping_status = $apiStatus;
                        }
                    }
                    
                    $order->save();
                }
            } catch (\Exception $e) {
                // Log error but continue to show existing data
                Log::error('TrackCourier Buyer API Error: ' . $e->getMessage());
            }
        }

        return view('orders.track', compact('order'));
    }
}
