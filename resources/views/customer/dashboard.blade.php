{{-- resources/views/customer/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        {{-- Welcome Section --}}
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg p-6 mb-8">
            <h1 class="text-3xl font-bold mb-2">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="text-blue-100">Manage your orders, update your profile, and explore new products.</p>
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shopping-bag text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Total Orders</h3>
                        <p class="text-2xl font-bold text-blue-600">{{ auth()->user()->orders->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Approved Orders</h3>
                        <p class="text-2xl font-bold text-green-600">{{ auth()->user()->orders->where('status', 'approved')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-orange-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Cart Items</h3>
                        <p class="text-2xl font-bold text-orange-600">{{ auth()->user()->carts->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('home') }}" class="flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition duration-300">
                    <i class="fas fa-store text-blue-600 text-xl mr-3"></i>
                    <span class="font-medium text-blue-800">Browse Products</span>
                </a>
                
                <a href="{{ route('cart.index') }}" class="flex items-center p-4 bg-orange-50 hover:bg-orange-100 rounded-lg transition duration-300">
                    <i class="fas fa-shopping-cart text-orange-600 text-xl mr-3"></i>
                    <span class="font-medium text-orange-800">View Cart</span>
                </a>
                
                <a href="{{ route('customer.orders') }}" class="flex items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition duration-300">
                    <i class="fas fa-list-alt text-green-600 text-xl mr-3"></i>
                    <span class="font-medium text-green-800">My Orders</span>
                </a>
                
                <button onclick="showProfileModal()" class="flex items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition duration-300">
                    <i class="fas fa-user text-purple-600 text-xl mr-3"></i>
                    <span class="font-medium text-purple-800">Update Profile</span>
                </button>
            </div>
        </div>

        {{-- Recent Orders --}}
        @if(auth()->user()->orders->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">Recent Orders</h2>
                <a href="{{ route('customer.orders') }}" class="text-blue-600 hover:text-blue-800 font-medium">View All</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach(auth()->user()->orders->take(5) as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $order->order_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($order->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($order->status === 'pending')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @elseif($order->status === 'approved')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Approved
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Declined
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-900">View Details</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <i class="fas fa-shopping-bag text-6xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No Orders Yet</h3>
            <p class="text-gray-500 mb-4">Start shopping to see your orders here!</p>
            <a href="{{ route('home') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-300">
                Browse Products
            </a>
        </div>
        @endif
    </div>
</div>

{{-- Profile Update Modal --}}
<div id="profileModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Update Profile</h3>
                    <button onclick="hideProfileModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="profileForm">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" name="name" value="{{ auth()->user()->name }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" name="phone" value="{{ auth()->user()->phone }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea name="address" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ auth()->user()->address }}</textarea>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="hideProfileModal()" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showProfileModal() {
    document.getElementById('profileModal').classList.remove('hidden');
}

function hideProfileModal() {
    document.getElementById('profileModal').classList.add('hidden');
}

// Handle profile form submission
document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/profile/update', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Profile updated successfully!');
            hideProfileModal();
            location.reload();
        } else {
            alert(data.error || 'Error updating profile');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating profile');
    });
});
</script>
@endsection