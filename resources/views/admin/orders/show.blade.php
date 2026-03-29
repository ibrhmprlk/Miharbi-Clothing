@extends('admin.layouts.app')

@section('title', 'Order #' . $order->order_number)

@section('content')
<div x-data="{ 
    statusModal: false,
    trackingModal: false,
    selectedStatus: '{{ $order->status }}'
}">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.orders.index') }}" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-600 hover:bg-slate-50 transition">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Order #{{ $order->order_number }}</h1>
                <p class="text-slate-500 text-sm mt-1">Placed on {{ $order->created_at->format('F d, Y \a\t H:i') }}</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <span class="status-badge status-{{ $order->status }} text-sm">
                <i class="bi bi-circle-fill text-[8px]"></i>
                {{ ucfirst($order->status) }}
            </span>
            
            @if($order->status !== 'delivered' && $order->status !== 'cancelled')
            <button @click="statusModal = true" class="btn-primary">
                <i class="bi bi-arrow-repeat"></i> Update Status
            </button>
            @endif
            
            @if($order->status === 'approved' || $order->status === 'processing')
            <button @click="trackingModal = true" class="btn-primary bg-gradient-to-r from-blue-600 to-blue-500">
                <i class="bi bi-truck"></i> Add Tracking
            </button>
            @endif
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Order Items -->
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="bi bi-box-seam text-indigo-600"></i>
                        Order Items ({{ $order->items->count() }})
                    </h3>
                </div>
                
                <div class="divide-y divide-slate-200">
                    @foreach($order->items as $item)
                    @php
                        $variant  = $item->variant;
                        $image    = $variant?->images?->first();
                        $imageUrl = $image
                            ? (str_starts_with($image->image_url, 'http') ? $image->image_url : asset('storage/' . $image->image_url))
                            : null;
                        $lineTotal     = $item->unit_price * $item->quantity;
                        $originalPrice = $variant?->price ?? $item->unit_price;
                        $hasDiscount   = $originalPrice > $item->unit_price;
                    @endphp
                    <div class="p-6 flex gap-4">
                        <!-- Ürün Görseli -->
                        <div class="w-20 h-24 bg-slate-100 rounded-xl overflow-hidden flex-shrink-0 border border-slate-200">
                            @if($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                    <i class="bi bi-image text-xl"></i>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-1">
                                        {{ $variant?->brand ?? 'Miharbi' }}
                                    </div>
                                    <h4 class="font-bold text-slate-800">{{ $item->product_name }}</h4>
                                    
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        @if($variant?->color)
                                        <span class="px-3 py-1 bg-slate-100 rounded-full text-xs font-semibold text-slate-600 flex items-center gap-1">
                                            <span class="w-2 h-2 rounded-full" style="background: {{ $variant->color_code ?? '#ccc' }}"></span>
                                            {{ $variant->color }}
                                        </span>
                                        @endif
                                        @if($variant?->size)
                                        <span class="px-3 py-1 bg-slate-100 rounded-full text-xs font-semibold text-slate-600">
                                            {{ $variant->size }}
                                        </span>
                                        @endif
                                        <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-xs font-bold">
                                            Qty: {{ $item->quantity }}
                                        </span>
                                        @if($item->status === 'cancelled')
                                        <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-xs font-bold">
                                            <i class="bi bi-x-circle"></i> Cancelled
                                        </span>
                                        @endif
                                    </div>
                                    
                                    @if($variant?->sku)
                                    <div class="text-xs text-slate-400 mt-2 font-mono">SKU: {{ $variant->sku }}</div>
                                    @endif
                                </div>
                                
                                <!-- Fiyat -->
                                <div class="text-right flex-shrink-0">
                                    <div class="font-bold text-slate-800">
                                        {{ number_format($lineTotal, 2, ',', '.') }} ₺
                                    </div>
                                    @if($hasDiscount)
                                    <div class="text-xs text-slate-400 line-through mt-0.5">
                                        {{ number_format($originalPrice * $item->quantity, 2, ',', '.') }} ₺
                                    </div>
                                    @endif
                                    <div class="text-sm text-slate-500 mt-0.5">
                                        {{ $item->quantity }} × {{ number_format($item->unit_price, 2, ',', '.') }} ₺
                                        @if($hasDiscount)
                                        <span class="ml-1 text-xs font-bold text-emerald-600">
                                            -%{{ round((1 - $item->unit_price / $originalPrice) * 100) }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Müşteri Yorumu -->
                            @if($item->review)
                            <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-star-fill text-amber-500"></i>
                                        <span class="font-bold text-slate-800">Customer Review</span>
                                        @if($item->review->is_approved)
                                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">Approved</span>
                                        @else
                                            <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded text-xs font-bold">Pending</span>
                                        @endif
                                    </div>
                                    <form action="{{ route('admin.reviews.destroy', $item->review) }}" method="POST" onsubmit="return confirm('Delete this review?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold flex items-center gap-1">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                                <div class="flex items-center gap-1 mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star-fill" style="font-size:16px; color: {{ $i <= $item->review->rating ? '#fbbf24' : '#e2e8f0' }}"></i>
                                    @endfor
                                    <span class="ml-2 text-sm font-bold text-slate-700">{{ $item->review->rating }}/5</span>
                                </div>
                                <p class="text-slate-700 text-sm">{{ $item->review->comment }}</p>
                                <div class="flex gap-4 mt-3 text-xs text-slate-500">
                                    @if($item->review->rating_quality)
                                    <span>Quality: {{ $item->review->rating_quality }}/5</span>
                                    @endif
                                    @if($item->review->rating_fit)
                                    <span>Fit: {{ $item->review->rating_fit }}/5</span>
                                    @endif
                                    @if($item->review->rating_shipping)
                                    <span>Shipping: {{ $item->review->rating_shipping }}/5</span>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6">
                <h3 class="font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <i class="bi bi-clock-history text-indigo-600"></i>
                    Order Timeline
                </h3>
                
                <div class="space-y-6">
                    @php
                        $statuses   = [
                            'pending'   => ['label' => 'Order Placed',    'icon' => 'bi-cart-check',  'time' => $order->created_at],
                            'approved'  => ['label' => 'Order Approved',  'icon' => 'bi-check-circle','time' => $order->approved_at],
                            'shipped'   => ['label' => 'Shipped',         'icon' => 'bi-truck',       'time' => $order->shipped_at],
                            'delivered' => ['label' => 'Delivered',       'icon' => 'bi-box-seam',    'time' => $order->delivered_at],
                        ];
                        $currentIdx = array_search($order->status, array_keys($statuses));
                    @endphp
                    
                    @foreach($statuses as $status => $info)
                    @php
                        $idx         = array_search($status, array_keys($statuses));
                        $isCompleted = $idx <= $currentIdx;
                        $isCurrent   = $idx === $currentIdx;
                    @endphp
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="timeline-dot {{ $isCompleted ? 'completed' : '' }} {{ $isCurrent ? 'active' : '' }}"></div>
                            @if(!$loop->last)
                            <div class="w-0.5 flex-1 bg-slate-200 mt-2 {{ $isCompleted ? 'bg-green-500' : '' }}"></div>
                            @endif
                        </div>
                        <div class="pb-6">
                            <div class="font-bold text-slate-800 flex items-center gap-2">
                                <i class="bi {{ $info['icon'] }} {{ $isCompleted ? 'text-green-600' : 'text-slate-400' }}"></i>
                                {{ $info['label'] }}
                            </div>
                            @if($info['time'])
                            <div class="text-sm text-slate-500 mt-1">
                                {{ $info['time']->format('d M Y, H:i') }}
                            </div>
                            @endif
                            @if($status === 'shipped' && $order->tracking_number)
                            <div class="mt-2 inline-flex items-center gap-2 text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-full">
                                <i class="bi bi-qr-code-scan"></i> {{ $order->tracking_number }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Right Column -->
        <div class="space-y-6">

            <!-- Customer Info -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6">
                <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="bi bi-person text-indigo-600"></i>
                    Customer
                </h3>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-lg">
                        {{ substr($order->shipping_full_name, 0, 1) }}
                    </div>
                    <div>
                        <div class="font-bold text-slate-800">{{ $order->shipping_full_name }}</div>
                        <div class="text-sm text-slate-500">{{ $order->user?->email ?? $order->guest_email ?? 'Guest Checkout' }}</div>
                    </div>
                </div>
                @if($order->user)
                <div class="pt-4 border-t border-slate-200">
                    <div class="text-xs text-slate-500 uppercase tracking-wider font-bold mb-2">Customer ID</div>
                    <div class="text-sm text-slate-700">#{{ $order->user->id }}</div>
                </div>
                @endif
            </div>

            <!-- Shipping Address -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6">
                <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="bi bi-geo-alt text-indigo-600"></i>
                    Shipping Address
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="font-semibold text-slate-800">{{ $order->shipping_full_name }}</div>
                    <div class="text-slate-600 leading-relaxed">
                        {{ $order->shipping_address }}<br>
                        {{ $order->shipping_district }}, {{ $order->shipping_city }}<br>
                        {{ $order->shipping_zip }}
                    </div>
                    <div class="pt-3 border-t border-slate-200 flex items-center gap-2 text-slate-700">
                        <i class="bi bi-telephone text-slate-400"></i>
                        {{ $order->shipping_phone }}
                    </div>
                </div>
            </div>

            <!-- Tracking Info -->
            @if($order->tracking_number)
            <div class="bg-white border border-slate-200 rounded-2xl p-6">
                <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="bi bi-truck text-indigo-600"></i>
                    Shipping Info
                </h3>
                <div class="space-y-3">
                    <div>
                        <div class="text-xs text-slate-500 uppercase tracking-wider font-bold">Carrier</div>
                        <div class="font-semibold text-slate-800">{{ $order->shipping_company }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 uppercase tracking-wider font-bold">Tracking Number</div>
                        <div class="font-mono text-sm bg-slate-100 px-3 py-2 rounded-lg mt-1">{{ $order->tracking_number }}</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Payment Method -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6">
                <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="bi bi-credit-card text-indigo-600"></i>
                    Payment Method
                </h3>
                @php
                    $pm          = $order->payment_method ?? 'unknown';
                    $isCash      = $pm === 'cash_on_delivery';
                    $pmLabel     = $isCash ? 'Cash on Delivery' : 'Credit / Debit Card';
                    $pmIcon      = $isCash ? 'bi-cash-stack' : 'bi-credit-card-2-front';
                    $pmBg        = $isCash ? 'bg-emerald-50 border-emerald-200' : 'bg-indigo-50 border-indigo-200';
                    $pmIconColor = $isCash ? 'text-emerald-600' : 'text-indigo-600';
                    $pmTextColor = $isCash ? 'text-emerald-700' : 'text-indigo-700';

                    $psLabel = match($order->payment_status ?? 'pending') {
                        'paid'       => 'Paid',
                        'pending'    => 'Pending',
                        'failed'     => 'Failed',
                        'refunded'   => 'Refunded',
                        default      => ucfirst($order->payment_status ?? 'Pending'),
                    };
                    $psBadge = match($order->payment_status ?? 'pending') {
                        'paid'     => 'bg-green-100 text-green-700',
                        'failed'   => 'bg-red-100 text-red-700',
                        'refunded' => 'bg-purple-100 text-purple-700',
                        default    => 'bg-amber-100 text-amber-700',
                    };
                @endphp
                <div class="flex items-center gap-3 p-4 rounded-xl border {{ $pmBg }}">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $isCash ? 'bg-emerald-100' : 'bg-indigo-100' }}">
                        <i class="bi {{ $pmIcon }} text-lg {{ $pmIconColor }}"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-bold text-sm {{ $pmTextColor }}">{{ $pmLabel }}</div>
                        @if(!$isCash)
                            <div class="text-xs text-slate-400 mt-0.5">3D Secure · SSL Encrypted</div>
                        @else
                            <div class="text-xs text-slate-400 mt-0.5">Payment collected at door</div>
                        @endif
                    </div>
                    @if(!($isCash && ($order->payment_status ?? 'pending') === 'pending'))
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $psBadge }}">
                        {{ $psLabel }}
                    </span>
                    @endif
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-slate-800 text-white rounded-2xl p-6">
                <h3 class="font-bold mb-4 flex items-center gap-2">
                    <i class="bi bi-receipt"></i>
                    Order Summary
                </h3>
                @php
                    $subtotal  = $order->subtotal        ?? 0;
                    $shipping  = $order->shipping_cost   ?? 0;
                    $discount  = $order->discount_amount ?? 0;
                    $tax       = $order->tax_amount      ?? 0;
                    $total     = $order->total           ?? 0;
                @endphp
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Subtotal</span>
                        <span>{{ number_format($subtotal, 2, ',', '.') }} ₺</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Shipping</span>
                        <span class="{{ $shipping == 0 ? 'text-green-400' : '' }}">
                            {{ $shipping > 0 ? number_format($shipping, 2, ',', '.').' ₺' : 'Free' }}
                        </span>
                    </div>
                    @if($discount > 0)
                    <div class="flex justify-between text-red-400">
                        <span>Discount</span>
                        <span>-{{ number_format($discount, 2, ',', '.') }} ₺</span>
                    </div>
                    @endif
                    @if($tax > 0)
                    <div class="flex justify-between">
                        <span class="text-slate-400">VAT (18%)</span>
                        <span>{{ number_format($tax, 2, ',', '.') }} ₺</span>
                    </div>
                    @endif
                    <div class="pt-3 border-t border-slate-700 flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span>{{ number_format($total, 2, ',', '.') }} ₺</span>
                    </div>
                </div>

                <!-- Ödeme yöntemi özeti -->
                <div class="mt-4 pt-4 border-t border-slate-700 flex items-center gap-2 text-xs text-slate-400">
                    <i class="bi {{ $isCash ? 'bi-cash-stack text-emerald-400' : 'bi-credit-card text-indigo-400' }}"></i>
                    <span>{{ $pmLabel }}</span>
                    @if(!($isCash && ($order->payment_status ?? 'pending') === 'pending'))
                    <span class="ml-auto px-2 py-0.5 rounded-full text-xs font-bold {{ $psBadge }}">{{ $psLabel }}</span>
                    @endif
                </div>
            </div>

            <!-- Admin Notes -->
            @if($order->admin_note)
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
                <h3 class="font-bold text-amber-800 mb-2 flex items-center gap-2">
                    <i class="bi bi-stickies"></i>
                    Admin Note
                </h3>
                <p class="text-amber-700 text-sm">{{ $order->admin_note }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Status Update Modal -->
    <div x-show="statusModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition.opacity>
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl" @click.away="statusModal = false">
            <h3 class="text-xl font-bold text-slate-800 mb-4">Update Order Status</h3>
            <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                @csrf
                <div class="space-y-3 mb-4">
                    @foreach(['pending', 'approved', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
                    <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition">
                        <input type="radio" name="status" value="{{ $status }}" x-model="selectedStatus" class="text-indigo-600 focus:ring-indigo-500">
                        <span class="status-badge status-{{ $status }} !px-3 !py-1">{{ ucfirst($status) }}</span>
                    </label>
                    @endforeach
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Admin Note (Optional)</label>
                    <textarea name="admin_note" rows="3" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none" placeholder="Add a note about this status change...">{{ $order->admin_note }}</textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="statusModal = false" class="flex-1 px-4 py-3 border border-slate-200 rounded-xl font-semibold text-slate-700 hover:bg-slate-50 transition">Cancel</button>
                    <button type="submit" class="flex-1 btn-primary justify-center">Update Status</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tracking Modal -->
    <div x-show="trackingModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition.opacity>
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl" @click.away="trackingModal = false">
            <h3 class="text-xl font-bold text-slate-800 mb-4">Add Tracking Information</h3>
            <form action="{{ route('admin.orders.tracking', $order) }}" method="POST">
                @csrf
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Shipping Company</label>
                        <input type="text" name="shipping_company" required
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="e.g., Aras Kargo, Yurtiçi Kargo">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tracking Number</label>
                        <input type="text" name="tracking_number" required
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono"
                               placeholder="e.g., 1234567890">
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="trackingModal = false" class="flex-1 px-4 py-3 border border-slate-200 rounded-xl font-semibold text-slate-700 hover:bg-slate-50 transition">Cancel</button>
                    <button type="submit" class="flex-1 btn-primary justify-center">Save & Mark Shipped</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
