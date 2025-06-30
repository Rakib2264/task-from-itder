<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        // Validate the request
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:100' // Added max limit
        ]);

        // Get authenticated user
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'error' => 'Authentication required',
                'login_url' => route('login')
            ], 401);
        }

        // Find product with lock to prevent race conditions
        $product = Product::where('id', $validated['product_id'])
                         ->where('status', true) // Only active products
                         ->lockForUpdate()
                         ->firstOrFail();

        // Check stock availability
        if ($product->stock < $validated['quantity']) {
            return response()->json([
                'error' => 'Insufficient stock',
                'available_stock' => $product->stock,
                'max_allowed' => min($product->stock, 100)
            ], 400);
        }

        // Begin database transaction
        DB::beginTransaction();

        // Check for existing cart item
        $cartItem = Cart::firstOrNew([
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);

        // Calculate new quantity
        $newQuantity = $cartItem->exists ? 
                      $cartItem->quantity + $validated['quantity'] : 
                      $validated['quantity'];

        // Verify stock again with new quantity
        if ($product->stock < $newQuantity) {
            DB::rollBack();
            return response()->json([
                'error' => 'Total quantity exceeds available stock',
                'available_stock' => $product->stock,
                'current_quantity' => $cartItem->exists ? $cartItem->quantity : 0,
                'max_additional' => $product->stock - ($cartItem->exists ? $cartItem->quantity : 0)
            ], 400);
        }

        // Update or create cart item
        $cartItem->fill([
            'quantity' => $newQuantity,
            'price' => $product->price,
            'total' => $product->price * $newQuantity
        ])->save();

        // Commit transaction
        DB::commit();

        // Calculate updated cart info
        $cartCount = Cart::where('user_id', $user->id)->count();
        $grandTotal = Cart::where('user_id', $user->id)->sum('total');

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully',
            'cart_count' => $cartCount,
            'grand_total' => $grandTotal,
            'item_total' => $cartItem->total,
            'product_stock' => $product->stock - $newQuantity // Remaining stock
        ]);

    } catch (ValidationException $e) {
        return response()->json([
            'error' => 'Validation error',
            'errors' => $e->errors()
        ], 422);
    } catch (ModelNotFoundException $e) {
        return response()->json([
            'error' => 'Product not found or unavailable'
        ], 404);
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Cart store error: ' . $e->getMessage());
        return response()->json([
            'error' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}
    public function update(Request $request, Cart $cart)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Authorization check
        if ($cart->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Stock availability check
        if ($cart->product->stock < $request->quantity) {
            return response()->json([
                'error' => 'Insufficient stock',
                'available_stock' => $cart->product->stock
            ], 400);
        }

        // Update cart item
        $cart->update([
            'quantity' => $request->quantity,
            'total' => $cart->price * $request->quantity
        ]);

        // Calculate updated totals
        $itemTotal = $cart->total;
        $grandTotal = Cart::where('user_id', auth()->id())->sum('total');
        $cartCount = Cart::where('user_id', auth()->id())->count();

        return response()->json([
            'success' => 'Cart updated successfully',
            'item_total' => $itemTotal,
            'grand_total' => $grandTotal,
            'cart_count' => $cartCount,
            'product_stock' => $cart->product->stock
        ]);
    }

    public function destroy(Cart $cart)
    {
        // Authorization check
        if ($cart->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cart->delete();
        
        // Calculate updated totals
        $grandTotal = Cart::where('user_id', auth()->id())->sum('total');
        $cartCount = Cart::where('user_id', auth()->id())->count();

        return response()->json([
            'success' => 'Item removed from cart',
            'grand_total' => $grandTotal,
            'cart_count' => $cartCount
        ]);
    }

    public function clear()
    {
        Cart::where('user_id', auth()->id())->delete();
        
        return response()->json([
            'success' => 'Cart cleared successfully',
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