<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth')->except(['index', 'show']);
    // }

    public function index()
    {
        $categories = Category::where('status', true)->get();
        $products = Product::where('status', true)->with('category')->paginate(12);
        return view('customer.index', compact('categories', 'products'));
    }

    public function dashboard()
    {
        return view('customer.dashboard');
    }

    public function show(Product $product)
    {
        return view('customer.product-detail', compact('product'));
    }
}
