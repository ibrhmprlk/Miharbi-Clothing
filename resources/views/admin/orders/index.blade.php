@extends('admin.layouts.app')

@section('title', 'Orders')

@section('content')
<div x-data="{ 
    selectedOrders: [],
    selectAll: false,
    bulkAction: '',
    showDeleteModal: false,
    deleteOrderId: null,
    deleteOrderNumber: ''
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Orders</h1>
            <p class="text-slate-500 mt-1">Manage and track all customer orders</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8 stat-grid">
        <div class="stat-card">
            <div class="flex items-center justify-between mb-2">
                <span class="text-slate-500 text-sm font-semibold">Total Orders</span>
                <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600">
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-slate-800">{{ $stats['total'] }}</div>
        </div>
        
        <div class="stat-card border-amber-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-amber-600 text-sm font-semibold">Pending</span>
                <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center text-amber-600">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-amber-600">{{ $stats['pending'] }}</div>
        </div>
        
        <div class="stat-card border-blue-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-blue-600 text-sm font-semibold">Shipped</span>
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
                    <i class="bi bi-truck"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-blue-600">{{ $stats['shipped'] }}</div>
        </div>
        
        <div class="stat-card border-green-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-green-600 text-sm font-semibold">Delivered</span>
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center text-green-600">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-green-600">{{ $stats['delivered'] }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-slate-200 rounded-2xl p-4 mb-6">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-col lg:flex-row gap-4">
            <div class="relative flex-1">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search by order #, customer name, email..." 
                       class="search-input">
            </div>
            
            <select name="status" class="filter-select lg:w-48" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 text-slate-500 hover:text-slate-700 font-semibold flex items-center gap-2">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            @endif
        </form>
    </div>

    <!-- ✅ BULK ACTION BAR -->
    <div x-show="selectedOrders.length > 0" 
         x-transition
         class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-red-700 font-semibold">
                <i class="bi bi-check-square-fill"></i>
                <span x-text="selectedOrders.length"></span> order selected
            </span>
        </div>
        <form action="{{ route('admin.orders.bulk') }}" method="POST" class="flex items-center gap-3">
            @csrf
            <input type="hidden" name="action" value="delete">
            <template x-for="id in selectedOrders" :key="id">
                <input type="hidden" name="orders[]" :value="id">
            </template>
            <button type="submit" 
                    onclick="return confirm('Are you sure you want to delete selected orders? This action cannot be undone.')"
                    class="btn-danger flex items-center gap-2">
                <i class="bi bi-trash"></i> Delete Selected
            </button>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-left">
                            <input type="checkbox" 
                                   x-model="selectAll" 
                                   @change="selectedOrders = selectAll ? {{ $orders->pluck('id') }} : []"
                                   class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($orders as $order)
                    <tr class="table-row">
                        <td class="px-6 py-4">
                            <input type="checkbox" 
                                   value="{{ $order->id }}" 
                                   x-model="selectedOrders"
                                   class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800">#{{ $order->order_number }}</div>
                            <div class="text-xs text-slate-500 mt-1">
                                @php $firstSku = $order->items->first()?->variant?->sku; @endphp
                                {{ $firstSku ?? 'No SKU' }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-800">{{ $order->shipping_full_name }}</div>
                            <div class="text-sm text-slate-500">{{ $order->user?->email ?? $order->guest_email ?? 'Guest' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-slate-800">{{ $order->items->count() }}</span>
                                <span class="text-slate-500">items</span>
                            </div>
                            <div class="text-xs text-slate-400 mt-1">
                                {{ $order->items->sum('quantity') }} total qty
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800">{{ number_format($order->total, 2, ',', '.') }} ₺</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="status-badge status-{{ $order->status }}">
                                <i class="bi bi-circle-fill text-[8px]"></i>
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-slate-800 font-semibold">{{ $order->created_at->format('d M Y') }}</div>
                            <div class="text-xs text-slate-500">{{ $order->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.orders.show', $order) }}" 
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold hover:bg-slate-700 transition">
                                    View <i class="bi bi-arrow-right"></i>
                                </a>
                                <!-- ✅ DELETE BUTTON -->
                                <button @click="deleteOrderId = {{ $order->id }}; deleteOrderNumber = '{{ $order->order_number }}'; showDeleteModal = true"
                                        class="inline-flex items-center gap-2 px-3 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-semibold hover:bg-red-100 transition border border-red-200">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-inbox text-2xl text-slate-400"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 mb-1">No orders found</h3>
                            <p class="text-slate-500">Try adjusting your filters</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $orders->links() }}
        </div>
        @endif
    </div>

    <!-- ✅ DELETE CONFIRMATION MODAL -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition.opacity>
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl" @click.away="showDeleteModal = false">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-exclamation-triangle text-2xl text-red-600"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Delete Order</h3>
                <p class="text-slate-500">
                    Are you sure you want to delete order <span class="font-bold text-slate-800" x-text="'#' + deleteOrderNumber"></span>? 
                    This action cannot be undone and stock will be restored.
                </p>
            </div>
            <div class="flex gap-3">
                <button @click="showDeleteModal = false" class="flex-1 px-4 py-3 border border-slate-200 rounded-xl font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Cancel
                </button>
                <form :action="'{{ url('admin/orders') }}/' + deleteOrderId" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 transition">
                        Delete Order
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
