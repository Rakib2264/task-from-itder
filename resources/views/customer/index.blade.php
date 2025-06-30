{{-- resources/views/customer/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Home - E-commerce Store')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Hero Section --}}
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg p-8 mb-8">
        <div class="max-w-2xl">
            <h1 class="text-4xl font-bold mb-4">Welcome to Our Store</h1>
            <p class="text-xl mb-6">Discover amazing products at great prices</p>
            <a href="#products" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                Shop Now
            </a>
        </div>
    </div>

    {{-- Categories Section --}}
    @if($categories->count() > 0)
    <div class="mb-12">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Shop by Category</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($categories as $category)
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition duration-300 p-4 text-center">
                @if($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-16 h-16 mx-auto mb-3 rounded-full object-cover">
                @else
                    <div class="w-16 h-16 mx-auto mb-3 bg-gray-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-folder text-gray-500 text-xl"></i>
                    </div>
                @endif
                <h3 class="font-semibold text-gray-800">{{ $category->name }}</h3>
                <p class="text-sm text-gray-600 mt-1">{{ $category->products->count() }} items</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Products Section --}}
    <div id="products">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Featured Products</h2>
            <div class="text-sm text-gray-600">
                Showing {{ $products->count() }} of {{ $products->total() }} products
            </div>
        </div>

        @if($products->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($products as $product)
                <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition duration-300 overflow-hidden">
                    <a href="{{ route('products.show', $product) }}">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-3xl"></i>
                            </div>
                        @endif
                    </a>
                    
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded">{{ $product->category->name }}</span>
                            @if($product->stock > 0)
                                <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded">In Stock</span>
                            @else
                                <span class="text-xs text-red-600 bg-red-100 px-2 py-1 rounded">Out of Stock</span>
                            @endif
                        </div>
                        
                        <h3 class="font-semibold text-gray-800 mb-2">
                            <a href="{{ route('products.show', $product) }}" class="hover:text-blue-600 transition duration-300">
                                {{ $product->name }}
                            </a>
                        </h3>
                        
                        @if($product->description)
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($product->description, 80) }}</p>
                        @endif
                        
                        <div class="flex items-center justify-between">
                            <span class="text-xl font-bold text-gray-800">${{ number_format($product->price, 2) }}</span>
                            @auth
                                @if($product->stock > 0)
                                    <button onclick="addToCart({{ $product->id }})" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                                        <i class="fas fa-shopping-cart mr-1"></i> Add to Cart
                                    </button>
                                @else
                                    <button disabled class="bg-gray-400 text-white px-4 py-2 rounded-lg text-sm font-medium cursor-not-allowed">
                                        Out of Stock
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                                    Login to Buy
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-box-open text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No Products Found</h3>
                <p class="text-gray-500">Check back later for new products!</p>
            </div>
        @endif
    </div>
</div>

{{-- Add to Cart JavaScript --}}
@auth
<script>
function addToCart(productId) {
    fetch('{{ route("cart.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Product added to cart successfully!');
        } else {
            alert(data.error || 'Error adding product to cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding product to cart');
    });
}
</script>
@endauth
@endsection