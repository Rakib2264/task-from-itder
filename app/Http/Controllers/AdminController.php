<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;

class AdminController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(['auth', 'admin']);
    // }

    public function dashboard()
    {
        $totalCategories = Category::count();
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();

        return view('admin.dashboard', compact(
            'totalCategories', 'totalProducts', 'totalOrders', 'pendingOrders'
        ));
    }
}