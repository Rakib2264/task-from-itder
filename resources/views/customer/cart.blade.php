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
        const maxStock = parseInt(input.attr('max'));
        
        if ($(this).hasClass('increase-qty')) {
            if (quantity < maxStock) {
                quantity++;
            } else {
                showToast('error', 'Cannot exceed available stock');
                return;
            }
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
        const maxStock = parseInt($(this).attr('max'));
        
        if (quantity > maxStock) {
            showToast('error', 'Cannot exceed available stock');
            $(this).val(maxStock);
            updateCartItem(cartId, maxStock);
        } else if (quantity > 0) {
            updateCartItem(cartId, quantity);
        } else {
            $(this).val(1);
            updateCartItem(cartId, 1);
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
            clearCart();
        }
    });
 updateCartItem();
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
                $(`tr[data-cart-id="${cartId}"] .item-total`).text(`$${response.item_total.toFixed(2)}`);
                
                // Update grand totals
                $('#subtotal').text(`$${response.grand_total.toFixed(2)}`);
                $('#grand-total').text(`$${response.grand_total.toFixed(2)}`);
                
                // Update cart count in navbar
                $('#cart-count').text(response.cart_count);
                
                // Update the modal total if it's open
                if ($('#checkoutModal').hasClass('show')) {
                    $('.modal-body .alert strong').text(`Order Total: $${response.grand_total.toFixed(2)}`);
                }
                
                showToast('success', response.message);
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showToast('error', response.error);
            }
        });
    }

    function removeCartItem(cartId) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: `/cart/${cartId}`,
            method: 'DELETE',
            success: function(response) {
                // Remove the row
                $(`tr[data-cart-id="${cartId}"]`).remove();
                
                // Update grand totals
                $('#subtotal').text(`$${response.grand_total.toFixed(2)}`);
                $('#grand-total').text(`$${response.grand_total.toFixed(2)}`);
                
                // Update cart count in navbar
                $('#cart-count').text(response.cart_count);
                
                // Update item count in header
                const itemCount = parseInt($('.card-header h5').text().match(/\d+/)[0]) - 1;
                $('.card-header h5').text(`Cart Items (${itemCount})`);
                
                // If cart is empty, reload to show empty cart message
                if (itemCount === 0) {
                    location.reload();
                }
                
                showToast('success', response.message);
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showToast('error', response.error);
            }
        });
    }

    function clearCart() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '/cart/clear',
            method: 'DELETE',
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showToast('error', response.error);
            }
        });
    }

    function showToast(type, message) {
        // Remove any existing toasts
        $('.alert-toast').remove();
        
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const toast = `
            <div class="alert ${alertClass} alert-dismissible fade show alert-toast position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
                <i class="fas ${icon} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('body').append(toast);
        
        setTimeout(function() {
            $('.alert-toast').fadeOut(400, function() {
                $(this).remove();
            });
        }, 3000);
    }
});
</script>
@endpush