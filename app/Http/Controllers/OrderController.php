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

            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // Check if Razorpay keys are set
            $keyId = env('RAZORPAY_KEY_ID');
            $keySecret = env('RAZORPAY_KEY_SECRET');

            if (empty($keyId) || empty($keySecret)) {
                return redirect()->back()->with('error', 'Razorpay Keys are missing. Please configure .env file.');
            }

            // Real Razorpay Order Creation
            $api = new Api($keyId, $keySecret);
            
            $razorpayAmount = $total * 100;

            $orderData = [
                'receipt'         => 'rcptid_' . Str::random(10),
                'amount'          => $razorpayAmount, 
                'currency'        => 'INR',
                'payment_capture' => 1 // Auto capture
            ];

            $razorpayOrder = $api->order->create($orderData);

            return view('payment.index', [
                'order_id' => $razorpayOrder['id'],
                'amount' => $razorpayAmount, // Send the actual payable amount to the view (100 paise)
                'key' => $keyId,
                'cart' => $cart,
                'total' => $total // Keep the visual total as the real cart total
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
            $cart = session()->get('cart');
            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            DB::beginTransaction();

            $order = Order::create([
                'user_id' => auth()->id(),
                'items' => $cart,
                'total_price' => $total,
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
            foreach ($cart as $id => $details) {
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
        $orders = \App\Models\Order::where('user_id', auth()->id())->latest()->get();
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
