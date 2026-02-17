<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
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
