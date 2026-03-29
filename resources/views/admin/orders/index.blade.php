@extends('admin.layouts.app')

@section('title', 'Orders')

@section('content')
<div x-data="{ 
    selectedOrders: [],
    selectAll: false,
    bulkAction: ''
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
                            <a href="{{ route('admin.orders.show', $order) }}" 
                               class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold hover:bg-slate-700 transition">
                                View <i class="bi bi-arrow-right"></i>
                            </a>
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
</div>
@endsection