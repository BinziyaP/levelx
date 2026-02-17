<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        
        $bundleService = new \App\Services\BundleDiscountService();
        $calculation = $bundleService->calculate($cart);

        return view('cart.index', compact('cart', 'calculation'));
    }

    public function add($id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price,
                "image" => $product->image
            ];
        }

        session()->put('cart', $cart);

        if(auth()->check()) {
            \App\Models\Cart::updateOrCreate(
                ['user_id' => auth()->id()],
                ['items' => $cart]
            );
        }

        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function remove(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);

                if(auth()->check()) {
                    \App\Models\Cart::updateOrCreate(
                        ['user_id' => auth()->id()],
                        ['items' => $cart]
                    );
                }
            }
            session()->flash('success', 'Product removed successfully');
        }
        return redirect()->back();
    }
}
