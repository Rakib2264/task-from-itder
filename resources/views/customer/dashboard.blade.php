@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container py-4">
    <div class="row">
        {{-- Welcome Section --}}
        <div class="col-12 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h1 class="card-title h2">Welcome {{ auth()->user()->name ?? '' }}</h1>
                </div>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-blue-50 p-3 rounded-circle">
                            <i class="fas fa-shopping-bag text-primary fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title mb-1">Total Orders</h5>
                            <p class="h4 text-primary mb-0">{{ auth()->user()->orders->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-green-50 p-3 rounded-circle">
                            <i class="fas fa-check-circle text-success fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title mb-1">Approved Orders</h5>
                            <p class="h4 text-success mb-0">{{ auth()->user()->orders->where('status', 'approved')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-orange-50 p-3 rounded-circle">
                            <i class="fas fa-shopping-cart text-warning fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title mb-1">Cart Items</h5>
                            <p class="h4 text-warning mb-0">{{ auth()->user()->carts->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6 col-md-3">
                            <a href="{{ route('home') }}" class="btn btn-outline-primary w-100 text-start">
                                <i class="fas fa-store me-2"></i> Browse Products
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-success w-100 text-start">
                                <i class="fas fa-shopping-cart me-2"></i> View Cart
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Orders</h5>

                </div>
                
                @if(auth()->user()->orders->count() > 0)
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order Number</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(auth()->user()->orders->take(5) as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>${{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            @if($order->status === 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @elseif($order->status === 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-danger">Declined</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="card-body text-center py-5">
                        <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                        <h5 class="card-title">No Orders Yet</h5>
                        <p class="card-text text-muted mb-4">Start shopping to see your orders here!</p>
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            Browse Products
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection