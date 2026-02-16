<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Product;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::where('user_id', auth()->id())->with('product')->latest()->get();
        return view('wishlist.index', compact('wishlists'));
    }

    public function store($productId)
    {
        $exists = Wishlist::where('user_id', auth()->id())->where('product_id', $productId)->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Product is already in your wishlist.');
        }

        Wishlist::create([
            'user_id' => auth()->id(),
            'product_id' => $productId,
        ]);

        return redirect()->back()->with('success', 'Product added to wishlist.');
    }

    public function destroy($id)
    {
        $wishlist = Wishlist::findOrFail($id);
        
        if ($wishlist->user_id !== auth()->id()) {
           abort(403);
        }

        $wishlist->delete();

        return redirect()->back()->with('success', 'Product removed from wishlist.');
    }
}
