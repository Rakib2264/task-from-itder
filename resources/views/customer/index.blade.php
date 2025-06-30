@extends('layouts.app')

@section('title', 'Home - E-commerce Store')

@section('content')
<div class="container py-4">
    {{-- Hero Section --}}
    <div class="bg-primary text-white rounded-3 p-5 mb-4" style="background: linear-gradient(to right, #0d6efd, #6f42c1);">
        <div class="col-lg-8">
            <h1 class="display-4 fw-bold mb-3">Welcome to Our Store</h1>
            <p class="lead mb-4">Discover amazing products at great prices</p>
            <a href="#products" class="btn btn-light btn-lg text-primary fw-bold">
                Shop Now
            </a>
        </div>
    </div>

    {{-- Categories Section --}}
    @if($categories->count() > 0)
    <div class="mb-5">
        <h2 class="display-5 fw-bold text-dark mb-4">Shop by Category</h2>
        <div class="row g-3">
            @foreach($categories as $category)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card h-100 shadow-sm hover-shadow transition">
                    <div class="card-body text-center">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="img-fluid rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-folder text-muted fs-4"></i>
                            </div>
                        @endif
                        <h5 class="fw-semibold text-dark mb-1">{{ $category->name }}</h5>
                        <p class="text-muted small">{{ $category->products->count() }} items</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Products Section --}}
    <div id="products">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="display-5 fw-bold text-dark">Featured Products</h2>
            <div class="text-muted small">
                Showing {{ $products->count() }} of {{ $products->total() }} products
            </div>
        </div>

        @if($products->count() > 0)
            <div class="row g-4">
                @foreach($products as $product)
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <a href="{{ route('products.show', $product) }}">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-image text-muted fs-1"></i>
                                </div>
                            @endif
                        </a>
                        
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge bg-primary bg-opacity-10 text-primary">{{ $product->category->name }}</span>
                                @if($product->stock > 0)
                                    <span class="badge bg-success bg-opacity-10 text-success">In Stock</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger">Out of Stock</span>
                                @endif
                            </div>
                            
                            <h5 class="card-title fw-semibold">
                                <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark hover-primary">
                                    {{ $product->name }}
                                </a>
                            </h5>
                            
                            @if($product->description)
                                <p class="card-text text-muted small mb-3">{{ Str::limit($product->description, 80) }}</p>
                            @endif
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fs-5 fw-bold text-dark">${{ number_format($product->price, 2) }}</span>
                                @auth
                                    @if($product->stock > 0)
                                        <button onclick="addToCart({{ $product->id }})" class="btn btn-primary btn-sm">
                                            <i class="fas fa-shopping-cart me-1"></i> Add to Cart
                                        </button>
                                    @else
                                        <button class="btn btn-secondary btn-sm" disabled>
                                            Out of Stock
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-lock me-1"></i> Login to Buy
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-4 d-flex justify-content-center">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                    <i class="fas fa-box-open text-muted fs-1"></i>
                </div>
                <h3 class="h4 fw-semibold text-dark mb-2">No Products Found</h3>
                <p class="text-muted">Check back later for new products!</p>
            </div>
        @endif
    </div>
</div>

{{-- Add to Cart JavaScript --}}
@auth
<script>
function addToCart(productId) {
    const button = event.currentTarget;
    const originalHTML = button.innerHTML;
    
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
            product_id: productId,
            quantity: 1
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
            updateCartCount(data.cart_count);
            
            // Show toast notification
            showToast('success', data.message || 'Product added to cart!');
        } else {
            showToast('error', data.error || 'Failed to add product to cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', error.message || 'Error adding product to cart');
    })
    .finally(() => {
        button.innerHTML = originalHTML;
        button.disabled = false;
    });
}

// Function to update cart count everywhere
function updateCartCount(count) {
    // Update navbar badge
    const badge = document.getElementById('cart-count-badge');
    if (badge) {
        badge.textContent = count;
        badge.classList.add('animate__animated', 'animate__bounceIn');
        setTimeout(() => {
            badge.classList.remove('animate__animated', 'animate__bounceIn');
        }, 1000);
    }
    
    // Update any other elements that show cart count
    const cartCountElements = document.querySelectorAll('.cart-count');
    cartCountElements.forEach(el => {
        el.textContent = count;
    });
}

// Improved toast notification
function showToast(type, message) {
    // Remove any existing toasts
    const existingToasts = document.querySelectorAll('.custom-toast');
    existingToasts.forEach(toast => toast.remove());
    
    const toast = document.createElement('div');
    toast.className = `custom-toast position-fixed top-4 end-4 z-50 px-4 py-3 rounded shadow-lg text-white ${
        type === 'success' ? 'bg-success' : 'bg-danger'
    }`;
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close btn-close-white ms-3" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>

<!-- Toast Notification -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="cartToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <strong class="me-auto">Success</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage"></div>
    </div>
</div>
@endauth

<style>
    .hover-shadow {
        transition: box-shadow 0.3s ease;
    }
    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .hover-primary:hover {
        color: #0d6efd !important;
    }
    .transition {
        transition: all 0.3s ease;
    }
</style>
@endsection