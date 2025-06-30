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
        dd($request->all());
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,approved,declined'
        ]);

        Order::create($request->all());

        return back()->with('success', 'Order created successfully');
    }
}