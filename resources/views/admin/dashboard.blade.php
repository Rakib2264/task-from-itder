@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $totalCategories }}</h4>
                        <p class="card-text">Categories</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-tags fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $totalProducts }}</h4>
                        <p class="card-text">Products</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $totalOrders }}</h4>
                        <p class="card-text">Total Orders</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-shopping-bag fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $pendingOrders }}</h4>
                        <p class="card-text">Pending Orders</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-block mb-2">
                            <i class="fas fa-plus"></i> Add Category
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.products.create') }}" class="btn btn-success btn-block mb-2">
                            <i class="fas fa-plus"></i> Add Product
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-info btn-block mb-2">
                            <i class="fas fa-list"></i> View Orders
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('home') }}" class="btn btn-secondary btn-block mb-2" target="_blank">
                            <i class="fas fa-external-link-alt"></i> View Site
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection