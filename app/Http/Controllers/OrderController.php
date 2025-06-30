<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,declined'
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Order status updated successfully');
    }
public function store(Request $request)
{
    $user = auth()->user();
    
    $request->validate([
        'shipping_address' => 'required|string',
        'notes' => 'nullable|string',
    ]);

    // Get the user's cart items
    $carts = $user->carts()->with('product')->get();
    
    if ($carts->isEmpty()) {
        return back()->with('error', 'Your cart is empty');
    }

    // Calculate total amount
    $totalAmount = $carts->sum(function($cart) {
        return $cart->price * $cart->quantity;
    });


    // Create order
    $order = Order::create([
        'user_id' => $user->id,
        'order_number' => 'ORD-' . strtoupper(uniqid()),
        'total_amount' => $totalAmount,
        'status' => 'pending',
        'shipping_address' => $request->shipping_address,
        'notes' => $request->notes,
    ]);

    // Add order items
    foreach ($carts as $cart) {
        $order->items()->create([
            'product_id' => $cart->product_id,
            'quantity' => $cart->quantity,
            'price' => $cart->price,
        ]);
        
        // Update product stock
        $cart->product->decrement('stock', $cart->quantity);
    }

    // Clear the cart
    $user->carts()->delete();

    return redirect()->route('customer.orders')->with('success', 'Order placed successfully!');
}

}