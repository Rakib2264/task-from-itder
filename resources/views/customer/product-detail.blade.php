@extends('layouts.app')

@section('title', $product->name . ' - Product Details')

@section('content')
<div class="container py-4">
    <div class="row">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fas fa-home me-1"></i> Home</a></li>
                <li class="breadcrumb-item">{{ $product->category->name }}</li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="col-lg-6 mb-4 mb-lg-0">
            {{-- Product Image --}}
            <div class="card">
                <div class="card-body p-0">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid w-100">
                    @else
                        <div class="d-flex align-items-center justify-content-center bg-light" style="height: 400px;">
                            <i class="fas fa-image fa-4x text-muted"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            {{-- Product Information --}}
            <div class="card">
                <div class="card-body">
                    {{-- Category Badge --}}
                    <span class="badge bg-primary mb-3">
                        <i class="fas fa-tag me-1"></i>
                        {{ $product->category->name }}
                    </span>

                    {{-- Product Name --}}
                    <h1 class="h2 mb-3">{{ $product->name }}</h1>

                    {{-- Price --}}
                    <h3 class="h4 mb-3 text-primary">${{ number_format($product->price, 2) }}</h3>

                    {{-- Stock Status --}}
                    <div class="mb-4">
                        @if($product->stock > 0)
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i>
                                In Stock ({{ $product->stock }} available)
                            </span>
                        @else
                            <span class="badge bg-danger">
                                <i class="fas fa-times-circle me-1"></i>
                                Out of Stock
                            </span>
                        @endif
                    </div>

                    {{-- Description --}}
                    @if($product->description)
                    <div class="mb-4">
                        <h4 class="h5">Description</h4>
                        <p class="text-muted">{{ $product->description }}</p>
                    </div>
                    @endif

                    {{-- Add to Cart Section --}}
                    @auth
                        @if($product->stock > 0)
                            <div class="mb-4">
                                <div class="row g-3 align-items-center mb-3">
                                    <div class="col-auto">
                                        <label for="quantity" class="col-form-label">Quantity:</label>
                                    </div>
                                    <div class="col-auto">
                                        <select id="quantity" class="form-select" style="width: 80px;">
                                            @for($i = 1; $i <= min(10, $product->stock); $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <button onclick="addToCart()" class="btn btn-primary w-100">
                                            <i class="fas fa-shopping-cart me-2"></i>
                                            Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning mb-4">
                                <p class="mb-0">This product is currently out of stock.</p>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info mb-4">
                            <p class="mb-3">Please login to purchase this product</p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <a href="{{ route('login') }}" class="btn btn-primary w-100">
                                        Login
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('register') }}" class="btn btn-secondary w-100">
                                        Register
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endauth

                    {{-- Product Details --}}
                    <div class="border-top pt-3">
                        <h4 class="h5 mb-3">Product Details</h4>
                        <dl class="row">
                            <dt class="col-sm-4">Category</dt>
                            <dd class="col-sm-8">{{ $product->category->name }}</dd>
                            
                            <dt class="col-sm-4">SKU</dt>
                            <dd class="col-sm-8">{{ $product->slug }}</dd>
                            
                            <dt class="col-sm-4">Stock</dt>
                            <dd class="col-sm-8">{{ $product->stock }} units</dd>
                            
                            <dt class="col-sm-4">Status</dt>
                            <dd class="col-sm-8">
                                @if($product->status)
                                    <span class="text-success">Active</span>
                                @else
                                    <span class="text-danger">Inactive</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
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
    <div class="row mt-5">
        <div class="col-12">
            <h2 class="h3 mb-4">Related Products</h2>
            <div class="row">
                @foreach($relatedProducts as $relatedProduct)
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card h-100">
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            @if($relatedProduct->image)
                                <img src="{{ asset('storage/' . $relatedProduct->image) }}" alt="{{ $relatedProduct->name }}" class="img-fluid" style="max-height: 100%;">
                            @else
                                <i class="fas fa-image fa-3x text-muted"></i>
                            @endif
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $relatedProduct->name }}</h5>
                            <p class="card-text text-primary">${{ number_format($relatedProduct->price, 2) }}</p>
                            <a href="{{ route('products.show', $relatedProduct) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function addToCart() {
    const quantity = document.getElementById('quantity').value;
    const button = event.currentTarget;
    const originalText = button.innerHTML;
    
    // Show loading state
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Adding...';
    button.disabled = true;
    
    fetch('{{ route("cart.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            product_id: {{ $product->id }},
            quantity: quantity
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update cart count in navbar
            if (data.cart_count) {
                document.getElementById('cart-count-badge').textContent = data.cart_count;
            }
            
            // Show success toast
            const toast = new bootstrap.Toast(document.getElementById('toastNotification'));
            document.getElementById('toastMessage').textContent = data.message || 'Product added to cart!';
            toast.show();
        } else {
            throw new Error(data.error || 'Failed to add product to cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Show error toast
        const toast = new bootstrap.Toast(document.getElementById('toastNotification'));
        document.getElementById('toastMessage').textContent = error.message || 'Error adding product to cart';
        document.getElementById('toastNotification').classList.remove('bg-success');
        document.getElementById('toastNotification').classList.add('bg-danger');
        toast.show();
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}
</script>
@endpush