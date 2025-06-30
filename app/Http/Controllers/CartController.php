<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::where('user_id', auth()->id())->with('product')->get();
        return view('customer.cart', compact('carts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);
        
        if ($product->stock < $request->quantity) {
            return response()->json(['error' => 'Insufficient stock'], 400);
        }

        $existingCart = Cart::where('user_id', auth()->id())
                           ->where('product_id', $request->product_id)
                           ->first();

        if ($existingCart) {
            $existingCart->update([
                'quantity' => $existingCart->quantity + $request->quantity,
                'price' => $product->price
            ]);
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $product->price
            ]);
        }

        return response()->json(['success' => 'Product added to cart']);
    }

    public function update(Request $request, Cart $cart)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        if ($cart->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($cart->product->stock < $request->quantity) {
            return response()->json(['error' => 'Insufficient stock'], 400);
        }

        $cart->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => 'Cart updated',
            'total' => $cart->total,
            'grand_total' => Cart::where('user_id', auth()->id())->sum(\DB::raw('quantity * price'))
        ]);
    }

    public function destroy(Cart $cart)
    {
        if ($cart->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cart->delete();
        return response()->json(['success' => 'Item removed from cart']);
    }
}