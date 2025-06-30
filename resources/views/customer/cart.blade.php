{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'E-Commerce') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-store me-2"></i>E-Commerce
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Home</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-1"></i>Admin Dashboard
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('cart.index') }}">
                                    <i class="fas fa-shopping-cart me-1"></i>Cart
                                    <span class="badge bg-warning text-dark" id="cart-count">0</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('customer.orders') }}">
                                    <i class="fas fa-box me-1"></i>My Orders
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('customer.dashboard') }}">
                                    <i class="fas fa-user me-1"></i>Dashboard
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link text-light">
                                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                                </button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-4">
        <div class="container">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>E-Commerce Store</h5>
                    <p>Your one-stop shop for everything you need.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; {{ date('Y') }} E-Commerce. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    @stack('scripts')
</body>
</html>

{{-- resources/views/customer/cart.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-shopping-cart me-2"></i>Shopping Cart</h2>
            <a href="{{ route('home') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i>Continue Shopping
            </a>
        </div>

        @if($carts->count() > 0)
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Cart Items ({{ $carts->count() }})</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($carts as $cart)
                                            <tr data-cart-id="{{ $cart->id }}">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($cart->product->image)
                                                            <img src="{{ asset('storage/' . $cart->product->image) }}" 
                                                                 alt="{{ $cart->product->name }}" 
                                                                 class="me-3" 
                                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-light me-3 d-flex align-items-center justify-content-center" 
                                                                 style="width: 60px; height: 60px;">
                                                                <i class="fas fa-image text-muted"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <h6 class="mb-1">{{ $cart->product->name }}</h6>
                                                            <small class="text-muted">{{ $cart->product->category->name }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="fw-bold">${{ number_format($cart->price, 2) }}</span>
                                                </td>
                                                <td>
                                                    <div class="input-group" style="width: 120px;">
                                                        <button class="btn btn-outline-secondary btn-sm decrease-qty" 
                                                                type="button" data-cart-id="{{ $cart->id }}">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <input type="number" 
                                                               class="form-control form-control-sm text-center quantity-input" 
                                                               value="{{ $cart->quantity }}" 
                                                               min="1" 
                                                               max="{{ $cart->product->stock }}"
                                                               data-cart-id="{{ $cart->id }}">
                                                        <button class="btn btn-outline-secondary btn-sm increase-qty" 
                                                                type="button" data-cart-id="{{ $cart->id }}">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                    <small class="text-muted d-block mt-1">Stock: {{ $cart->product->stock }}</small>
                                                </td>
                                                <td>
                                                    <span class="fw-bold item-total">${{ number_format($cart->total, 2) }}</span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-outline-danger btn-sm remove-item" 
                                                            data-cart-id="{{ $cart->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <span>Subtotal:</span>
                                <span class="fw-bold" id="subtotal">${{ number_format($carts->sum('total'), 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Shipping:</span>
                                <span class="text-success">Free</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="h5">Total:</span>
                                <span class="h5 fw-bold text-primary" id="grand-total">${{ number_format($carts->sum('total'), 2) }}</span>
                            </div>
                            
                            <button type="button" class="btn btn-primary w-100 mb-3" data-bs-toggle="modal" data-bs-target="#checkoutModal">
                                <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                            </button>
                            
                            <button type="button" class="btn btn-outline-danger w-100" id="clear-cart">
                                <i class="fas fa-trash-alt me-2"></i>Clear Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-shopping-cart fa-5x text-muted"></i>
                </div>
                <h3 class="text-muted mb-3">Your cart is empty</h3>
                <p class="text-muted mb-4">Looks like you haven't added any items to your cart yet.</p>
                <a href="{{ route('home') }}" class="btn btn-primary">
                    <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                </a>
            </div>
        @endif
    </div>
</div>

{{-- Checkout Modal --}}
<div class="modal fade" id="checkoutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Checkout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('orders.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="shipping_address" class="form-label">Shipping Address *</label>
                        <textarea class="form-control" 
                                  id="shipping_address" 
                                  name="shipping_address" 
                                  rows="3" 
                                  required 
                                  placeholder="Enter your complete shipping address">{{ auth()->user()->address }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Order Notes (Optional)</label>
                        <textarea class="form-control" 
                                  id="notes" 
                                  name="notes" 
                                  rows="2" 
                                  placeholder="Any special instructions for your order"></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Order Total: ${{ number_format($carts->sum('total'), 2) }}</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Place Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Update quantity
    $('.increase-qty, .decrease-qty').click(function() {
        const cartId = $(this).data('cart-id');
        const input = $(`.quantity-input[data-cart-id="${cartId}"]`);
        let quantity = parseInt(input.val());
        
        if ($(this).hasClass('increase-qty')) {
            quantity++;
        } else {
            quantity = Math.max(1, quantity - 1);
        }
        
        input.val(quantity);
        updateCartItem(cartId, quantity);
    });

    // Direct quantity input change
    $('.quantity-input').change(function() {
        const cartId = $(this).data('cart-id');
        const quantity = parseInt($(this).val());
        
        if (quantity > 0) {
            updateCartItem(cartId, quantity);
        }
    });

    // Remove item
    $('.remove-item').click(function() {
        const cartId = $(this).data('cart-id');
        
        if (confirm('Are you sure you want to remove this item?')) {
            removeCartItem(cartId);
        }
    });

    // Clear cart
    $('#clear-cart').click(function() {
        if (confirm('Are you sure you want to clear your entire cart?')) {
            $('.remove-item').each(function() {
                const cartId = $(this).data('cart-id');
                removeCartItem(cartId, false);
            });
            location.reload();
        }
    });

    function updateCartItem(cartId, quantity) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: `/cart/${cartId}`,
            method: 'PATCH',
            data: { quantity: quantity },
            success: function(response) {
                // Update item total
                $(`.item-total[data-cart-id="${cartId}"]`).text(`$${response.total}`);
                
                // Update grand total
                $('#subtotal').text(`$${response.grand_total}`);
                $('#grand-total').text(`$${response.grand_total}`);
                
                // Show success message
                showToast('success', response.success);
            },
            error: function(xhr) {
                const response = JSON.parse(xhr.responseText);
                showToast('error', response.error);
            }
        });
    }

    function removeCartItem(cartId, reload = true) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: `/cart/${cartId}`,
            method: 'DELETE',
            success: function(response) {
                if (reload) {
                    location.reload();
                }
            },
            error: function(xhr) {
                const response = JSON.parse(xhr.responseText);
                showToast('error', response.error);
            }
        });
    }

    function showToast(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const toast = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
                <i class="fas ${icon} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('body').append(toast);
        
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 3000);
    }
});
</script>
@endpush