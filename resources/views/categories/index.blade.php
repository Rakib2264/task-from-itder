<!-- resources/views/admin/categories/index.blade.php -->
@extends('layouts.app')

@section('title', 'Categories Management')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Categories</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Category
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Products Count</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>
                                @if($category->image)
                                    <img src="{{ asset('storage/' . $category->image) }}" 
                                         alt="{{ $category->name }}" class="img-thumbnail" style="width: 50px;">
                                @else
                                    <span class="text-muted">No Image</span>
                                @endif
                            </td>
                            <td>{{ $category->name }}</td>
                            <td>
                                <span class="badge {{ $category->status ? 'bg-success' : 'bg-danger' }}">
                                    {{ $category->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ $category->products_count ?? 0 }}</td>
                            <td>{{ $category->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.categories.edit', $category) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No categories found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{ $categories->links() }}
    </div>
</div>
@endsection