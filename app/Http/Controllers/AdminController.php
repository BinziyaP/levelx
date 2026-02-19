<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // 1. Global Metrics
        // Revenue (All time, 30 days, 7 days)
        $totalRevenue = \App\Models\ProductSalesHistory::sum('revenue');
        $revenue30Days = \App\Models\ProductSalesHistory::where('recorded_at', '>=', now()->subDays(30))->sum('revenue');
        $revenue7Days = \App\Models\ProductSalesHistory::where('recorded_at', '>=', now()->subDays(7))->sum('revenue');

        // Total Units Sold
        $totalUnitsSold = \App\Models\ProductSalesHistory::sum('quantity');

        // Average Platform Rating & Total Reviews
        // "Using existing: product_rating_history" - we can take the latest snapshot or just use Products table for current state.
        // Products table is more efficient for "Current State". 
        $avgRating = \App\Models\Product::avg('average_rating');
        $totalReviews = \App\Models\Product::sum('total_reviews');

        // 2. Top 5 Best Selling Products (By Revenue)
        $topSellingProducts = \App\Models\ProductSalesHistory::selectRaw('product_id, sum(revenue) as total_revenue, sum(quantity) as total_units')
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->with(['product']) // Ensure product relationship exists
            ->take(5)
            ->get();

        // 3. Top 5 Highest Rated Products
        $topRatedProducts = \App\Models\Product::orderByDesc('average_rating')
            ->orderByDesc('total_reviews')
            ->take(5)
            ->get();

        // 4. All Products Sorted by Ranking Score
        $rankedProducts = \App\Models\Product::with('user')
            ->orderByDesc('ranking_score')
            ->get();

        return view('admin.dashboard', compact(
            'totalRevenue', 'revenue30Days', 'revenue7Days', 
            'totalUnitsSold', 'avgRating', 'totalReviews',
            'topSellingProducts', 'topRatedProducts', 'rankedProducts'
        ));
    }

    // Sellers
    public function sellers()
    {
        $sellers = \App\Models\User::where('role', 'seller')->get();
        return view('admin.sellers', compact('sellers'));
    }

    public function approveSeller($id)
    {
        $seller = \App\Models\User::findOrFail($id);
        $seller->update(['status' => 'active']);
        return redirect()->back()->with('success', 'Seller approved successfully.');
    }

    public function declineSeller($id)
    {
        $seller = \App\Models\User::findOrFail($id);
        $seller->update(['status' => 'declined']);
        return redirect()->back()->with('success', 'Seller declined.');
    }

    public function deleteSeller($id)
    {
        $seller = \App\Models\User::findOrFail($id);
        $seller->delete();
        return redirect()->back()->with('success', 'Seller deleted successfully.');
    }

    // Buyers / Orders Management
    public function buyers()
    {
        // Fetch all orders with their associated users
        $orders = \App\Models\Order::with('user')->latest()->get();
        return view('admin.buyers', compact('orders'));
    }

    public function fraudOrders()
    {
        $orders = \App\Models\Order::with('user')
            ->where('is_suspicious', true)
            ->latest()
            ->get();
        return view('admin.fraud.orders', compact('orders'));
    }

    public function showOrder($id)
    {
        $order = \App\Models\Order::with(['user', 'fraudLogs.rule'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function approveOrder($id)
    {
        $order = \App\Models\Order::findOrFail($id);
        // Approve clears suspicion and sets status to approved
        $order->update([
            'status' => 'approved',
            'is_suspicious' => false
        ]);
        return redirect()->back()->with('success', 'Order accepted successfully.');
    }

    public function declineOrder($id)
    {
        $order = \App\Models\Order::findOrFail($id);
        $order->update(['status' => 'declined']);
        return redirect()->back()->with('success', 'Order declined.');
    }

    public function deleteOrder($id)
    {
        $order = \App\Models\Order::findOrFail($id);
        $order->delete();
        return redirect()->back()->with('success', 'Order deleted successfully.');
    }

    // Products
    public function products()
    {
        $products = \App\Models\Product::with('user')->get();
        return view('admin.products', compact('products'));
    }

    public function approveProduct($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        $product->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Product approved.');
    }

    public function declineProduct($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        $product->update(['status' => 'declined']);
        return redirect()->back()->with('success', 'Product declined.');
    }

    public function deleteProduct($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        $product->delete();
        return redirect()->back()->with('success', 'Product deleted.');
    }
}
