<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::where('user_id', auth()->id())->with('product')->get();
        return view('customer.cart', compact('carts'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1|max:100'
            ]);

            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'error' => 'Authentication required',
                    'login_url' => route('login')
                ], 401);
            }

            DB::beginTransaction();

            $product = Product::where('id', $validated['product_id'])
                             ->where('status', true)
                             ->lockForUpdate()
                             ->firstOrFail();

            if ($product->stock < $validated['quantity']) {
                return response()->json([
                    'error' => 'Insufficient stock',
                    'available_stock' => $product->stock
                ], 400);
            }

            $cartItem = Cart::firstOrNew([
                'user_id' => $user->id,
                'product_id' => $product->id
            ]);

            $newQuantity = $cartItem->exists ? 
                          $cartItem->quantity + $validated['quantity'] : 
                          $validated['quantity'];

            if ($product->stock < $newQuantity) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Total quantity exceeds available stock',
                    'available_stock' => $product->stock
                ], 400);
            }

            // Calculate total price
            $totalPrice = $product->price * $newQuantity;

            $cartItem->fill([
                'quantity' => $newQuantity,
                'price' => $product->price,
                'total' => $totalPrice // Ensure total is calculated and saved
            ])->save();

            DB::commit();

            $cartCount = Cart::where('user_id', $user->id)->count();
            $grandTotal = Cart::where('user_id', $user->id)->sum('total');

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully',
                'cart_count' => $cartCount,
                'grand_total' => $grandTotal,
                'item_total' => $totalPrice,
                'product_stock' => $product->stock - $newQuantity
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Cart error: '.$e->getMessage());
            return response()->json([
                'error' => 'Server error: '.$e->getMessage()
            ], 500);
        }
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
            return response()->json([
                'error' => 'Insufficient stock',
                'available_stock' => $cart->product->stock
            ], 400);
        }

        // Calculate new total
        $newTotal = $cart->price * $request->quantity;

        $cart->update([
            'quantity' => $request->quantity,
            'total' => $newTotal // Update total column
        ]);

        $grandTotal = Cart::where('user_id', auth()->id())->sum('total');
        $cartCount = Cart::where('user_id', auth()->id())->count();

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'item_total' => $newTotal,
            'grand_total' => $grandTotal,
            'cart_count' => $cartCount,
            'product_stock' => $cart->product->stock - $request->quantity
        ]);
    }

    public function destroy(Cart $cart)
    {
        if ($cart->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cart->delete();
        
        $grandTotal = Cart::where('user_id', auth()->id())->sum('total');
        $cartCount = Cart::where('user_id', auth()->id())->count();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'grand_total' => $grandTotal,
            'cart_count' => $cartCount
        ]);
    }

    public function clear()
    {
        Cart::where('user_id', auth()->id())->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully',
            'grand_total' => 0,
            'cart_count' => 0
        ]);
    }

    public function count()
    {
        $count = auth()->check() ? auth()->user()->carts->count() : 0;
        return response()->json(['count' => $count]);
    }
}