@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Order Details - {{ $order->order_number }}</h2>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>

    <div class="row">
        <!-- Order Information -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Order Information</h5>
                    @if($order->status == 'pending')
                        <div class="btn-group">
                            <form action="{{ route('admin.orders.status', $order) }}" 
                                  method="POST" class="d-inline me-2">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="fas fa-check"></i> Approve Order
                                </button>
                            </form>
                            <form action="{{ route('admin.orders.status', $order) }}" 
                                  method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="declined">
                                <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to decline this order?')">
                                    <i class="fas fa-times"></i> Decline Order
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                            <p><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y h:i A') }}</p>
                            <p><strong>Status:</strong> 
                                @if($order->status == 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($order->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-danger">Declined</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Amount:</strong> 
                                <span class="h5 text-primary">${{ number_format($order->total_amount, 2) }}</span>
                            </p>
                        </div>
                    </div>
                    
                    @if($order->notes)
                    <div class="mt-3">
                        <strong>Customer Notes:</strong>
                        <p class="text-muted">{{ $order->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Order Items -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product->image)
                                                <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                     alt="{{ $item->product->name }}" 
                                                     class="img-thumbnail me-3" 
                                                     style="width: 50px; height: 50px;">
                                            @endif
                                            <div>
                                                <strong>{{ $item->product->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $item->product->category->name }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>${{ number_format($item->price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td><strong>${{ number_format($item->total, 2) }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-active">
                                    <th colspan="3" class="text-end">Total Amount:</th>
                                    <th>${{ number_format($order->total_amount, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $order->user->name }}</p>
                    <p><strong>Email:</strong> {{ $order->user->email }}</p>
                    @if($order->user->phone)
                        <p><strong>Phone:</strong> {{ $order->user->phone }}</p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Shipping Address</h5>
                </div>
                <div class="card-body">
                    <p>{{ $order->shipping_address }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-refresh page every 30 seconds for pending orders
    @if($order->status == 'pending')
        setTimeout(function() {
            location.reload();
        }, 30000);
    @endif
</script>
@endsection