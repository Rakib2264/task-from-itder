{{-- resources/views/customer/product-detail.blade.php --}}
@extends('layouts.app')

@section('title', $product->name . ' - Product Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        {{-- Breadcrumb --}}
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <i class="fas fa-home mr-2"></i>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-sm font-medium text-gray-500">{{ $product->category->name }}</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-sm font-medium text-gray-500">{{ $product->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Product Image --}}
            <div class="space-y-4">
                <div class="aspect-square rounded-lg overflow-hidden bg-gray-100">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-6xl"></i>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Product Information --}}
            <div class="space-y-6">
                {{-- Category Badge --}}
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-tag mr-1"></i>
                        {{ $product->category->name }}
                    </span>
                </div>

                {{-- Product Name --}}
                <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>

                {{-- Price --}}
                <div class="flex items-center space-x-4">
                    <span class="text-3xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                </div>

                {{-- Stock Status --}}
                <div class="flex items-center space-x-2">
                    @if($product->stock > 0)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>
                            In Stock ({{ $product->stock }} available)
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-1"></i>
                            Out of Stock
                        </span>
                    @endif
                </div>

                {{-- Description --}}
                @if($product->description)
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Description</h3>
                    <p class="text-gray-600 leading-relaxed">{{ $product->description }}</p>
                </div>
                @endif

                {{-- Add to Cart Section --}}
                @auth
                    @if($product->stock > 0)
                        <div class="space-y-4">
                            <div class="flex items-center space-x-4">
                                <label for="quantity" class="text-sm font-medium text-gray-700">Quantity:</label>
                                <div class="relative">
                                    <select id="quantity" class="block w-20 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @for($i = 1; $i <= min(10, $product->stock); $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <div class="flex space-x-4">
                                <button onclick="addToCart()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-300 flex items-center justify-center">
                                    <i class="fas fa-shopping-cart mr-2"></i>
                                    Add to Cart
                                </button>
                                
                                <button onclick="buyNow()" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition duration-300 flex items-center justify-center">
                                    <i class="fas fa-bolt mr-2"></i>
                                    Buy Now
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-100 border border-gray-300 rounded-lg p-4">
                            <p class="text-gray-600 text-center">This product is currently out of stock.</p>
                        </div>
                    @endif
                @else
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-blue-800 text-center mb-3">Please login to purchase this product</p>
                        <div class="flex space-x-3">
                            <a href="{{ route('login') }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-300 text-center">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition duration-300 text-center">
                                Register
                            </a>
                        </div>
                    </div>
                @endauth

                {{-- Product Details --}}
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Details</h3>
                    <dl class="grid grid-cols-1 gap-4">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Category</dt>
                            <dd class="text-sm text-gray-900">{{ $product->category->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">SKU</dt>
                            <dd class="text-sm text-gray-900">{{ $product->slug }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Stock</dt>
                            <dd class="text-sm text-gray-900">{{ $product->stock }} units</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="text-sm text-gray-900">
                                @if($product->status)
                                    <span class="text-green-600">Active</span>
                                @else
                                    <span class="text-red-600">Inactive</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Related Products --}}
        @php
            $relatedProducts = App\Models\Product::where('category_id', $product->category_id)
                                                ->where('id', '!=', $product->id)
                                                ->where('status', true)
                                                ->limit(4)
                                                ->get();
        @endphp

        @if($relatedProducts->count() > 0)
        <div class="mt-16">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">Related Products</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                    @include('customer.partials.product-card')
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endsection
