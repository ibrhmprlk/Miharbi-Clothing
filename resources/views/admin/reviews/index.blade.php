@extends('admin.layouts.app')

@section('title', 'Reviews')

@section('content')
<div x-data="{ 
    selectedReviews: [],
    selectAll: false,
    bulkAction: ''
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Reviews</h1>
            <p class="text-slate-500 mt-1">Moderate customer reviews and ratings</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="stat-card">
            <div class="flex items-center justify-between mb-2">
                <span class="text-slate-500 text-sm font-semibold">Total Reviews</span>
                <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600">
                    <i class="bi bi-star"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-slate-800">{{ $stats['total'] }}</div>
        </div>
        
        <div class="stat-card border-amber-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-amber-600 text-sm font-semibold">Pending</span>
                <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center text-amber-600">
                    <i class="bi bi-clock"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-amber-600">{{ $stats['pending'] }}</div>
        </div>
        
        <div class="stat-card border-green-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-green-600 text-sm font-semibold">Approved</span>
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center text-green-600">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-green-600">{{ $stats['approved'] }}</div>
        </div>
        
        <div class="stat-card border-purple-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-purple-600 text-sm font-semibold">Avg Rating</span>
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center text-purple-600">
                    <i class="bi bi-star-fill"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-purple-600">{{ number_format($stats['avg_rating'], 1) }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-slate-200 rounded-2xl p-4 mb-6">
        <form action="{{ route('admin.reviews.index') }}" method="GET" class="flex flex-col lg:flex-row gap-4">
            <div class="relative flex-1">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search reviews, products, customers..." 
                       class="search-input">
            </div>
            
            <select name="status" class="filter-select lg:w-40" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
            </select>
            
            <select name="rating" class="filter-select lg:w-32" onchange="this.form.submit()">
                <option value="">All Ratings</option>
                @for($i = 5; $i >= 1; $i--)
                <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} Stars</option>
                @endfor
            </select>
            
            @if(request()->hasAny(['search', 'status', 'rating']))
                <a href="{{ route('admin.reviews.index') }}" class="px-4 py-2 text-slate-500 hover:text-slate-700 font-semibold flex items-center gap-2">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Bulk Actions -->
    <div x-show="selectedReviews.length > 0" 
         x-transition
         class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="font-semibold text-indigo-800" x-text="selectedReviews.length + ' reviews selected'"></span>
        </div>
        
        <form action="{{ route('admin.reviews.bulk') }}" method="POST" class="flex items-center gap-3">
            @csrf
            <template x-for="id in selectedReviews" :key="id">
                <input type="hidden" name="reviews[]" :value="id">
            </template>
            
            <select name="action" x-model="bulkAction" required class="filter-select bg-white">
                <option value="">Select Action</option>
                <option value="approve">Approve Selected</option>
                <option value="delete">Delete Selected</option>
            </select>
            
            <button type="submit" 
                    :disabled="!bulkAction"
                    class="btn-primary"
                    onclick="return confirm('Are you sure?')">
                Apply
            </button>
        </form>
    </div>

    <!-- Reviews Grid -->
    <div class="grid gap-4">
        @forelse($reviews as $review)
        <div class="review-card" :class="{ 'ring-2 ring-indigo-500': selectedReviews.includes({{ $review->id }}) }">
            <div class="flex items-start gap-4">
                <div class="checkbox-wrapper pt-1">
                    <input type="checkbox" 
                           value="{{ $review->id }}" 
                           x-model="selectedReviews"
                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                </div>
                
                <div class="flex-1">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center text-indigo-600 font-bold">
                                {{ substr($review->user->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="font-bold text-slate-800">{{ $review->user->name }}</div>
                                <div class="text-xs text-slate-500">{{ $review->created_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            @if($review->is_approved)
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Approved</span>
                            @else
                                <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold">Pending</span>
                            @endif
                            
                            {{-- DÜZELTME 1: inline style ile yıldız gösterimi --}}
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star-fill" style="font-size:16px; color: {{ $i <= $review->rating ? '#fbbf24' : '#e2e8f0' }}"></i>
                                @endfor
                                <span class="ml-1 text-xs font-bold text-slate-600">{{ $review->rating }}/5</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-slate-50 rounded-xl p-4 mb-3">
                        <div class="flex items-center gap-2 mb-2 text-sm">
                            <i class="bi bi-box-seam text-slate-400"></i>
                            <span class="font-semibold text-slate-700">Order #{{ $review->orderItem->order->order_number }}</span>
                            <span class="text-slate-400">•</span>
                            <span class="text-slate-600">{{ $review->urun->urun_adi ?? 'Unknown Product' }}</span>
                        </div>
                        
                        @if($review->comment)
                            <p class="text-slate-700">{{ $review->comment }}</p>
                        @else
                            <p class="text-slate-400 italic text-sm">No comment provided.</p>
                        @endif

                        {{-- DÜZELTME 2: alt yıldız gösterimi de inline style --}}
                        <div class="flex items-center gap-1 mt-3 pt-3 border-t border-slate-200">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star-fill" style="font-size:14px; color: {{ $i <= $review->rating ? '#fbbf24' : '#e2e8f0' }}"></i>
                            @endfor
                            <span class="ml-2 text-sm font-bold text-slate-700">{{ $review->rating }}/5</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end gap-2">
                        @if(!$review->is_approved)
                        <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="btn-success flex items-center gap-2">
                                <i class="bi bi-check-lg"></i> Approve
                            </button>
                        </form>
                        @endif
                        
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="inline" onsubmit="return confirm('Delete this review permanently?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger flex items-center gap-2">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12 bg-white border border-slate-200 rounded-2xl">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="bi bi-star text-2xl text-slate-400"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-1">No reviews found</h3>
            <p class="text-slate-500">Reviews will appear here when customers submit them</p>
        </div>
        @endforelse
    </div>
    
    @if($reviews->hasPages())
    <div class="mt-6">
        {{ $reviews->links() }}
    </div>
    @endif
</div>
@endsection
