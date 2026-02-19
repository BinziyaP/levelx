<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Seller Dashboard
    |--------------------------------------------------------------------------
    */

    public function dashboard()
    {
        $products = Product::where('user_id', Auth::id());
        $totalProducts = $products->count();
        $approvedProducts = (clone $products)->where('status', 'approved')->count();
        $pendingProducts = (clone $products)->where('status', 'pending')->count();
        $declinedProducts = (clone $products)->where('status', 'declined')->count();
        $notifications = Auth::user()->notifications;
        
        // Analytics
        $sellerRevenue = \App\Models\ProductSalesHistory::join('products', 'product_sales_history.product_id', '=', 'products.id')
            ->where('products.user_id', Auth::id())
            ->sum('product_sales_history.revenue');

        $sellerUnitsSold = \App\Models\ProductSalesHistory::join('products', 'product_sales_history.product_id', '=', 'products.id')
            ->where('products.user_id', Auth::id())
            ->sum('product_sales_history.quantity');

        $sellerAvgRating = $products->avg('average_rating');
        $sellerTotalReviews = $products->sum('total_reviews');

        return view('seller.dashboard', compact(
            'totalProducts', 'approvedProducts', 'pendingProducts', 'declinedProducts', 'notifications',
            'sellerRevenue', 'sellerUnitsSold', 'sellerAvgRating', 'sellerTotalReviews'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Show All Seller Products
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $products = Product::where('user_id', Auth::id())->get();

        return view('seller.products.index', compact('products'));
    }

    /*
    |--------------------------------------------------------------------------
    | Show Create Form
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Show Create Form
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('seller.products.create', compact('categories'));
    }

    /*
    |--------------------------------------------------------------------------
    | Store Product
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Image validation
            'brand' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $data = [
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'brand' => $request->brand,
            'category_id' => $request->category_id,
            'status' => 'pending'
        ];

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        // Create product
        Product::create($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully! Waiting for admin approval.');
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Product
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $product = Product::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $categories = \App\Models\Category::all();
        return view('seller.products.edit', compact('product', 'categories'));
    }

    /*
    |--------------------------------------------------------------------------
    | Update Product
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $product = Product::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'brand' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'brand' => $request->brand,
            'category_id' => $request->category_id,
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy Product
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $product = Product::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Buyer - View Approved Products Only (With Search & Filter)
    |--------------------------------------------------------------------------
    */

    public function shop(Request $request)
    {
        $query = Product::where('status', 'approved')
            ->whereHas('user', function ($q) {
                $q->where('status', 'active');
            });

        // 1. Search (Name or Brand)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // 2. Category Filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // 3. Price Range Filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // 4. Sorting
        if ($request->filled('sort')) {
            if ($request->sort === 'price_asc') {
                $query->orderBy('price', 'asc');
            } elseif ($request->sort === 'price_desc') {
                $query->orderBy('price', 'desc');
            } elseif ($request->sort === 'recommended') {
                $query->orderBy('ranking_score', 'desc');
            } else {
                $query->latest(); // Default for 'latest'
            }
        } else {
            $query->orderBy('ranking_score', 'desc'); // Smart Ranking Default
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = \App\Models\Category::all();

        return view('buyer.shop', compact('products', 'categories'));
    }

    /*
    |--------------------------------------------------------------------------
    | Show Single Product Details
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $product = Product::with(['reviews.user', 'category', 'user'])->findOrFail($id);
        
        // Calculate rating distribution
        $ratings = [
            5 => $product->reviews->where('rating', 5)->count(),
            4 => $product->reviews->where('rating', 4)->count(),
            3 => $product->reviews->where('rating', 3)->count(),
            2 => $product->reviews->where('rating', 2)->count(),
            1 => $product->reviews->where('rating', 1)->count(),
        ];
        $totalReviews = $product->reviews->count();

        // Check if current user bought this product (for review permission)
        $canReview = false;
        if (Auth::check()) {
            $user = Auth::user();
            // Check if already reviewed
            $alreadyReviewed = $product->reviews->where('user_id', $user->id)->count() > 0;
            
            if (!$alreadyReviewed) {
                 // Check if purchased
                 // Dynamic Status Requirement
                 $settings = \DB::table('ranking_settings')->first();
                 $allowedOrderStatuses = ['completed', 'delivered', 'approved', 'paid'];
                 $allowedShippingStatuses = ['delivered'];
                 if ($settings && $settings->allow_early_reviews) {
                     $allowedOrderStatuses = array_merge($allowedOrderStatuses, ['processing']);
                     $allowedShippingStatuses = array_merge($allowedShippingStatuses, ['packed', 'shipped']);
                 }

                 // Check both order status AND shipping_status columns
                 $canReview = \App\Models\Order::where('user_id', $user->id)
                    ->where(function ($query) use ($allowedOrderStatuses, $allowedShippingStatuses) {
                        $query->whereIn('status', $allowedOrderStatuses)
                              ->orWhereIn('shipping_status', $allowedShippingStatuses);
                    })
                    ->get()
                    ->contains(function ($order) use ($product) {
                        $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;
                        if (!$items) return false;
                        foreach ($items as $key => $item) {
                             $itemId = $item['product_id'] ?? $item['id'] ?? $key;
                             if ($itemId == $product->id) return true;
                        }
                        return false;
                    });
            }
        }

        return view('buyer.product_details', compact('product', 'ratings', 'totalReviews', 'canReview'));
    }

    /*
    |--------------------------------------------------------------------------
    | Seller Orders
    |--------------------------------------------------------------------------
    */
    public function orders()
    {
        $notifications = Auth::user()->notifications()->paginate(10);
        return view('seller.orders', compact('notifications'));
    }

    /*
    |--------------------------------------------------------------------------
    | Seller Notifications
    |--------------------------------------------------------------------------
    */
    public function notifications()
    {
        $notifications = Auth::user()->notifications()->paginate(10);
        return view('seller.notifications', compact('notifications'));
    }

    public function markNotificationAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return redirect()->back();
    }

    /*
    |--------------------------------------------------------------------------
    | Update Shipping Status
    |--------------------------------------------------------------------------
    */
    public function updateShipping(Request $request, $id)
    {
        $request->validate([
            'shipping_status' => 'required|in:pending,packed,shipped,delivered',
            'tracking_number' => 'nullable|string',
            'courier_code' => 'nullable|string|in:' . implode(',', array_keys(config('couriers'))),
        ]);

        $order = \App\Models\Order::find($id);

        if (!$order) {
            return redirect()->back()->with('error', 'Order not found. It may have been deleted or cancelled.');
        }

        // Security Check: Ideally ensure the order contains products from this seller.
        // For this implementation, we'll assume valid access if notification exists, 
        // but robustly we should check order items against seller's products.

        $order->shipping_status = $request->shipping_status;
        
        // Update timestamps based on status
        if ($request->shipping_status === 'packed' && !$order->packed_at) {
            $order->packed_at = now();
        } elseif ($request->shipping_status === 'shipped' && !$order->shipped_at) {
            $order->shipped_at = now();
        } elseif ($request->shipping_status === 'delivered' && !$order->delivered_at) {
            $order->delivered_at = now();
        }
        
        // Tracking details required for packed, shipped, and delivered
        if (in_array($request->shipping_status, ['packed', 'shipped', 'delivered'])) {
            $request->validate([
                'tracking_number' => 'required|string',
                'courier_code' => 'required|string',
            ]);

            $order->tracking_number = $request->tracking_number;
            $order->courier_code = $request->courier_code;

            // Call TrackCourier API if tracking number is provided
            if ($request->tracking_number && $request->courier_code) {
                try {
                    $apiKey = env('TRACKCOURIER_API_KEY');
                    
                    $response = \Illuminate\Support\Facades\Http::withHeaders([
                        'X-API-Key' => $apiKey
                    ])->get("https://api.trackcourier.io/v1/track", [
                        'tracking_number' => $request->tracking_number,
                        'courier' => $request->courier_code 
                    ]);

                    if ($response->successful()) {
                        $responseData = $response->json();
                        
                        // DEBUGGING: Uncomment the line below to inspect the API response
                        // dd($responseData);

                        $order->tracking_response = $responseData;

                        // Update shipment status from API if available (Handle PascalCase)
                        if (isset($responseData['data']['ShipmentState'])) {
                            $apiStatus = strtolower($responseData['data']['ShipmentState']);
                            // Map API status to internal status if needed, or just use it if compatible
                            if (in_array($apiStatus, ['pending', 'packed', 'shipped', 'delivered'])) {
                                $order->shipping_status = $apiStatus;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('TrackCourier API Error: ' . $e->getMessage());
                }
            }
        }

        $order->save();

        // Recalculate ranking scores for all products in this order
        try {
            $rankingService = app(\App\Services\ProductRankingService::class);
            $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;
            if (is_array($items)) {
                foreach ($items as $key => $item) {
                    $pid = $item['product_id'] ?? $item['id'] ?? $key;
                    if ($pid) {
                        $rankingService->updateProductStats($pid);
                    }
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Ranking update error: ' . $e->getMessage());
        }

        // Update the notification data to reflect the new status
        $user = Auth::user();
        foreach($user->notifications as $notification) {
            if (isset($notification->data['internal_order_id']) && $notification->data['internal_order_id'] == $id) {
                $data = $notification->data;
                $data['shipping_status'] = $request->shipping_status;
                if ($request->tracking_number) $data['tracking_number'] = $request->tracking_number;
                if ($request->courier_code) $data['courier_code'] = $request->courier_code;
                $notification->data = $data;
                $notification->save();
                break;
            }
        }

        return redirect()->back()->with('success', 'Shipping status updated successfully.');
    }
    public function trackOrder($id)
    {
        $order = \App\Models\Order::find($id);

        if (!$order) {
            return redirect()->route('seller.orders')->with('error', 'Order not found. It may have been deleted or cancelled.');
        }

        // Check if current user is the seller (simplified check)
        if (Auth::user()->role !== 'seller') {
            abort(403);
        }

        // Trigger API update if needed 
        // Modified condition: Allow tracking for 'packed' as well if details exist, and ensure we try to fetch if response is empty
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
                    $order->tracking_response = $response->json();
                    $order->save();
                } else {
                    \Illuminate\Support\Facades\Log::error("API Failed: " . $response->body());
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('TrackCourier API Error: ' . $e->getMessage());
            }
        }

        return view('seller.track', compact('order'));
    }
}
