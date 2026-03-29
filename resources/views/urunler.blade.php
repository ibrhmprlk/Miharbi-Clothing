<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
        
        body { background-color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; color: #1e293b; margin: 0; overflow-x: hidden; }
        
        html { overflow-y: scroll; }

        .admin-nav { 
            background: white; 
            border-bottom: 1px solid #e2e8f0; 
            position: sticky; 
            top: 0; 
            z-index: 9999; 
            width: 100%;
        }
        
        .nav-item { 
            display: flex; align-items: center; gap: 8px; padding: 12px 16px; border-radius: 12px; 
            font-size: 14px; font-weight: 600; color: #64748b; transition: 0.2s; text-decoration: none; 
        }
        .nav-item:hover { background: #eef2ff; color: #4f46e5; }
        .nav-item.active { background: #4f46e5; color: white; }

        .search-container { position: relative; max-width: 672px; margin: 0 auto 40px auto; }
        .search-input { width: 100%; padding: 14px 100px 14px 52px; background: white; border: 1px solid #e2e8f0; border-radius: 24px; font-size: 14px; transition: 0.3s; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
        .search-input:focus { outline: none; border-color: #4f46e5; box-shadow: 0 8px 20px rgba(79, 70, 229, 0.1); }
        
        .search-clear-btn { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: #f1f5f9; color: #64748b; padding: 6px 16px; border-radius: 16px; font-size: 11px; font-weight: 800; text-transform: uppercase; transition: 0.2s; border: none; cursor: pointer; display: flex; align-items: center; gap: 4px; }
        .search-clear-btn:hover { background: #ef4444; color: white; }

        /* PRODUCT CARD — sabit width kaldırıldı */
        .product-card { background: white; border: 1px solid #e2e8f0; border-radius: 20px; overflow: hidden; transition: all 0.4s; height: 100%; display: flex; flex-direction: column; position: relative; width: 100%; }
        .product-card:hover { transform: translateY(-6px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.08); }

        .carousel-container { position: relative; overflow: hidden; aspect-ratio: 1/1.2; background: #f1f5f9; }
        .image-scroll-container { display: flex; overflow-x: auto; scroll-snap-type: x mandatory; height: 100%; scrollbar-width: none; -ms-overflow-style: none; }
        .image-scroll-container::-webkit-scrollbar { display: none; }
        .scroll-slide { flex: 0 0 100%; scroll-snap-align: start; height: 100%; }
        .scroll-slide img { width: 100%; height: 100%; object-fit: cover; }

        .nav-btn { 
            position: absolute; top: 50%; transform: translateY(-50%); z-index: 50; 
            background: rgba(255,255,255,0.9); width: 34px; height: 34px; border-radius: 10px; 
            display: flex; align-items: center; justify-content: center; 
            transition: all 0.3s; border: 1px solid #e2e8f0; cursor: pointer; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); color: #1e293b;
        }
        
        @media (min-width: 1024px) {
            .nav-btn { opacity: 0; }
            .product-card:hover .nav-btn { opacity: 1; }
        }
        
        @media (max-width: 1023px) {
            .nav-btn { opacity: 1 !important; width: 30px; height: 30px; border-radius: 8px; }
        }
        
        .color-swatch-container { position: absolute; bottom: 12px; left: 12px; z-index: 40; background: rgba(255,255,255,0.95); padding: 6px; border-radius: 20px; display: flex; gap: 5px; border: 1px solid #f1f5f9; backdrop-filter: blur(4px); }
        .color-circle { width: 14px; height: 14px; border-radius: 50%; border: 1px solid rgba(0,0,0,0.1); transition: 0.2s; }
        .color-circle.active { outline: 2px solid #4f46e5; outline-offset: 1px; transform: scale(1.1); }

        .size-pill { display: flex; align-items: center; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; background: white; }
        .size-label { padding: 4px 8px; background: #f8fafc; border-right: 1px solid #e2e8f0; font-size: 11px; font-weight: 800; color: #1e293b; }
        .stock-count { padding: 4px 8px; font-size: 11px; font-weight: 700; color: #4f46e5; }

        .status-badge { position: absolute; top: 12px; right: 12px; z-index: 50; padding: 6px 12px; border-radius: 12px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .status-active { background: #10b981; color: white; }
        .status-inactive { background: #ef4444; color: white; }

        .hide-scrollbar::-webkit-scrollbar { display: none !important; }
        .hide-scrollbar { -ms-overflow-style: none !important; scrollbar-width: none !important; }

        [x-cloak] { display: none !important; }
    </style>
</head>

<body x-data="{ mobileMenu: false }">

<!-- Header -->
<header class="admin-nav shadow-sm">
    <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-20">
        <a href="{{ url('adminpanel') }}" class="flex items-center gap-2 hover:opacity-80 transition no-underline">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                <i class="bi bi-cpu-fill text-xl"></i>
            </div>
            <span class="font-bold text-slate-900 tracking-tight text-base md:text-lg">Miharbi Clothing Admin</span>
        </a>

        <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 text-slate-600 rounded-xl hover:bg-slate-100 transition-all">
            <i class="bi" :class="mobileMenu ? 'bi-x-lg' : 'bi-list'" style="font-size: 1.8rem;"></i>
        </button>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center gap-2">
            <a href="{{ route('create') }}" class="nav-item"><i class="bi bi-plus-lg"></i> Add Product</a>
            <a href="{{ route('categories') }}" class="nav-item"><i class="bi bi-tags"></i> Categories</a>
            <a href="{{ route('urunler') }}" class="nav-item"><i class="bi bi-box-seam"></i> Products</a>
            
            <a href="{{ route('admin.orders.index') }}" class="nav-item relative">
                <i class="bi bi-cart-check"></i> Orders
                @php $pendingOrders = \App\Models\Order::where('status', 'pending')->count(); @endphp
                @if($pendingOrders > 0)
                    <span class="badge">{{ $pendingOrders }}</span>
                @endif
            </a>
            
            <a href="{{ route('admin.reviews.index') }}" class="nav-item relative">
                <i class="bi bi-star"></i> Reviews
                @php $pendingReviews = \App\Models\Review::where('is_approved', false)->count(); @endphp
                @if($pendingReviews > 0)
                    <span class="badge">{{ $pendingReviews }}</span>
                @endif
            </a>
            
            <a href="{{ route('about') }}" class="nav-item"><i class="bi bi-info-circle"></i> About</a>
            
            <a href="{{ url('/') }}" class="nav-item group transition-all duration-300 border border-transparent hover:text-rose-600 hover:bg-rose-50 ml-4">
                <i class="bi bi-box-arrow-right text-lg group-hover:translate-x-1 transition-transform"></i>
                <span class="font-bold">Log Out</span>
            </a>
        </nav>
    </div>

    <!-- Mobile Navigation -->
    <nav x-show="mobileMenu" x-cloak x-transition class="md:hidden absolute left-0 right-0 top-20 bg-white border-b border-slate-200 shadow-2xl p-4 space-y-2">
        <a href="{{ route('create') }}" class="nav-item"><i class="bi bi-plus-lg"></i> Add Product</a>
        <a href="{{ route('categories') }}" class="nav-item"><i class="bi bi-tags"></i> Categories</a>
        <a href="{{ route('urunler') }}" class="nav-item"><i class="bi bi-box-seam"></i> Products</a>
        
        <a href="{{ route('admin.orders.index') }}" class="nav-item relative justify-between">
            <span class="flex items-center gap-2"><i class="bi bi-cart-check"></i> Orders</span>
            @if($pendingOrders > 0)
                <span class="badge">{{ $pendingOrders }}</span>
            @endif
        </a>
        
        <a href="{{ route('admin.reviews.index') }}" class="nav-item relative justify-between">
            <span class="flex items-center gap-2"><i class="bi bi-star"></i> Reviews</span>
            @if($pendingReviews > 0)
                <span class="badge">{{ $pendingReviews }}</span>
            @endif
        </a>
        
        <a href="{{ route('about') }}" class="nav-item"><i class="bi bi-info-circle"></i> About</a>
        
        <a href="{{ url('/') }}" class="nav-item text-rose-600 font-bold border-t border-slate-100 pt-2 mt-2">
            <i class="bi bi-box-arrow-right"></i> Log Out
        </a>
    </nav>
</header>

<div class="max-w-7xl mx-auto px-4 py-8" x-data="{ showFilters: false }">

    <!-- Search & Filter Bar -->
    <div class="flex flex-col md:flex-row gap-4 mb-10">
        <div class="search-container group flex-1 !mb-0">
            <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition text-lg"></i>
            <form action="{{ route('urunler') }}" method="GET" id="searchForm">
                <input type="text" name="q" id="searchInput" value="{{ request('q') }}" placeholder="Search name, category, brand or SKU..." class="search-input">
                @if(request('q') || request('cat') || request('size') || request('min') || request('max') || request('status') || request('brand') || request('collection'))
                    <button type="button" onclick="window.location.href='{{ route('urunler') }}'" class="search-clear-btn">
                        <i class="bi bi-x-lg"></i>
                        <span>Reset</span>
                    </button>
                @endif
            </form>
        </div>

        <button @click="showFilters = true" class="px-6 py-3.5 bg-white border-2 border-indigo-50 rounded-2xl font-bold text-slate-700 flex items-center justify-center gap-2 shadow-sm hover:border-indigo-200 transition-all">
            <i class="bi bi-sliders2 text-indigo-600"></i>
            <span>Filter Options</span>
        </button>
    </div>

    <!-- Filter Modal -->
    <div x-show="showFilters" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[10001] bg-slate-900/60 backdrop-blur-sm" x-cloak>
        
        <div @click.away="showFilters = false" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="w-full max-w-sm h-full bg-white shadow-2xl p-6 overflow-y-auto">
            
            <div class="flex items-center justify-between mb-8 border-b pb-4">
                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2"><i class="bi bi-sliders2 text-indigo-500"></i> Filters</h2>
                <button @click="showFilters = false" class="text-slate-400 hover:text-rose-500 transition text-2xl"><i class="bi bi-x-lg"></i></button>
            </div>

            <form action="{{ route('urunler') }}" method="GET" class="space-y-8">
                <input type="hidden" name="q" value="{{ request('q') }}">

                <div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-4">Product Status</span>
                    <div class="space-y-2">
                        @foreach(['1' => 'Active Products', '0' => 'Inactive Products', '' => 'Show All'] as $val => $label)
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="status" value="{{ $val }}" {{ request('status') == $val ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <span class="text-sm font-semibold group-hover:text-indigo-600 transition">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                @if(isset($brands) && $brands->count() > 0)
                <div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-4">Brands</span>
                    <div class="space-y-2 max-h-40 overflow-y-auto pr-2 hide-scrollbar">
                        @foreach($brands as $brand)
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="brand[]" value="{{ $brand }}" {{ is_array(request('brand')) && in_array($brand, request('brand')) ? 'checked' : '' }} class="rounded text-indigo-600">
                            <span class="text-sm font-semibold group-hover:text-indigo-600 transition">{{ $brand }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(isset($collections) && $collections->count() > 0)
                <div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-4">Collections</span>
                    <div class="space-y-2 max-h-40 overflow-y-auto pr-2 hide-scrollbar">
                        @foreach($collections as $col)
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="collection[]" value="{{ $col }}" {{ is_array(request('collection')) && in_array($col, request('collection')) ? 'checked' : '' }} class="rounded text-indigo-600">
                            <span class="text-sm font-semibold group-hover:text-indigo-600 transition">{{ $col }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-4">Categories</span>
                    <div class="space-y-2">
                        @foreach($categories as $cat)
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="cat[]" value="{{ $cat->id }}" {{ is_array(request('cat')) && in_array($cat->id, request('cat')) ? 'checked' : '' }} class="rounded text-indigo-600">
                            <span class="text-sm font-semibold group-hover:text-indigo-600 transition">{{ $cat->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-4">Size</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach($sizes as $size)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="size[]" value="{{ $size }}" {{ is_array(request('size')) && in_array($size, request('size')) ? 'checked' : '' }} class="hidden peer">
                            <div class="px-3 py-1.5 border rounded-lg text-xs font-bold peer-checked:bg-indigo-600 peer-checked:text-white transition">{{ $size }}</div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-4">Price Range</span>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" name="min" placeholder="Min" value="{{ request('min') }}" class="w-full p-3 border rounded-xl text-xs outline-none focus:border-indigo-500">
                        <input type="number" name="max" placeholder="Max" value="{{ request('max') }}" class="w-full p-3 border rounded-xl text-xs outline-none focus:border-indigo-500">
                    </div>
                </div>

                <button type="submit" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-bold text-sm shadow-xl hover:bg-black transition transform active:scale-95">Apply Filters</button>
            </form>
        </div>
    </div>

    <!-- Empty State -->
    @if($urunler->isEmpty() && request()->anyFilled(['q', 'cat', 'brand', 'collection', 'size', 'min', 'max', 'status']))
        <div class="flex flex-col items-center justify-center py-20 bg-white rounded-3xl border-2 border-dashed border-slate-200">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                <i class="bi bi-search text-3xl text-slate-300"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800">Product not found</h3>
            <p class="text-slate-500 mt-2">No products match your selected filters.</p>
            <button onclick="window.location.href='{{ route('urunler') }}'" class="mt-6 px-6 py-2 bg-indigo-600 text-white font-bold rounded-xl">Clear Filters</button>
        </div>
    @elseif($urunler->isEmpty())
        <div class="text-center py-20">
            <p class="text-slate-400 italic">No products have been added to the system yet.</p>
        </div>
    @else
        <main class="space-y-16">
            @foreach($urunler->groupBy('category_name') as $categoryName => $groupedItems)

           
            <section class="relative" x-data="{ expanded: false }">

                <div class="flex items-center justify-between mb-6 px-2">
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                        <span class="w-2 h-8 bg-indigo-600 rounded-full"></span>
                        {{ $categoryName }}
                    </h2>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $groupedItems->count() }} Products</span>
                </div>

                {{-- İLK 4 — her zaman görünür --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 px-2 pb-2">
                    @foreach($groupedItems->take(4) as $item)
                    @php
                        $uniqueId = $item->id . '-' . str_replace('#', '', $item->color_code);
                        $images = $item->images;
                    @endphp
                    <div class="product-card shadow-sm" id="p-card-{{ $uniqueId }}">

                        <div class="status-badge {{ $item->is_active ? 'status-active' : 'status-inactive' }}">
                            <i class="bi {{ $item->is_active ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} mr-1"></i>
                            {{ $item->is_active ? 'Active' : 'Inactive' }}
                        </div>

                        <div class="carousel-container group/main relative">
                            <div class="image-scroll-container" id="scroll-{{ $uniqueId }}">
                                @foreach($images as $img)
                                <div class="scroll-slide"
                                     data-variant-id="{{ $img->variant_id ?? $item->first_variant->id }}"
                                     data-price="{{ number_format($item->first_variant->price, 2) }}"
                                     data-sku="{{ $item->first_variant->sku ?? 'NO-CODE' }}"
                                     data-discount="{{ $item->first_variant->discount_price ? number_format($item->first_variant->discount_price, 2) : '' }}"
                                     data-all-sizes='{!! json_encode($item->variants->map(fn($v)=>["size"=>$v->size,"stock"=>$v->stock,"sku"=>$v->sku])) !!}'>
                                    <img src="{{ $img->image_url }}" alt="{{ $item->name }}" loading="lazy">
                                </div>
                                @endforeach
                            </div>
                            @if($images->count() > 1)
                            <button type="button" class="nav-btn left-[10px]" onclick="btnScroll('scroll-{{ $uniqueId }}', -1)">
                                <i class="bi bi-chevron-left text-lg"></i>
                            </button>
                            <button type="button" class="nav-btn right-[10px]" onclick="btnScroll('scroll-{{ $uniqueId }}', 1)">
                                <i class="bi bi-chevron-right text-lg"></i>
                            </button>
                            @endif
                            <div class="color-swatch-container">
                                @foreach($item->all_colors as $color)
                                <div class="color-circle {{ $color == $item->color_code ? 'active' : '' }}" style="background-color: {{ $color }}"></div>
                                @endforeach
                            </div>
                        </div>

                        <div class="p-4 flex flex-col flex-1 space-y-3 justify-between">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded uppercase">{{ $item->category_name }}</span>
                                    <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-1 rounded font-mono">SKU: {{ $item->first_variant->sku ?? 'NO-CODE' }}</span>
                                </div>
                                <h3 class="text-sm font-bold text-slate-800 leading-tight">
                                    {{ $item->name }}
                                    <span class="text-slate-400">({{ $item->color_name }})</span>
                                </h3>
                                <div class="text-xs text-slate-600 space-y-1">
                                    @if(!empty($item->brand)) <div><span class="text-slate-400">Brand:</span> <span class="font-medium text-slate-700">{{ $item->brand }}</span></div> @endif
                                    @if(!empty($item->collection)) <div><span class="text-slate-400">Collection:</span> <span class="font-medium text-slate-700">{{ $item->collection }}</span></div> @endif
                                </div>
                                <div class="flex flex-wrap gap-2 pt-1">
                                    @foreach($item->variants as $v)
                                    <div class="size-pill">
                                        <div class="size-label">Size: {{ $v->size }}</div>
                                        <div class="stock-count">Stock: {{ $v->stock }}</div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mt-4 flex items-end justify-between border-t pt-4">
                                <div class="price-box flex flex-col text-right">
                                    @if($item->first_variant->discount_price)
                                    <span class="text-[10px] text-slate-400 line-through">{{ number_format($item->first_variant->price, 2) }} ₺</span>
                                    <span class="text-base font-black text-slate-900">{{ number_format($item->first_variant->discount_price, 2) }} ₺</span>
                                    @else
                                    <span class="text-base font-black text-slate-900">{{ number_format($item->first_variant->price, 2) }} ₺</span>
                                    @endif
                                </div>
                                <a href="{{ route('urundetay', $item->first_variant->id) }}" class="w-10 h-10 bg-slate-50 text-slate-400 hover:text-indigo-600 rounded-xl flex items-center justify-center border transition-all">
                                    <i class="bi bi-pencil-square text-lg"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- KALAN ÜRÜNLER — expanded true olunca açılır --}}
                @if($groupedItems->count() > 4)
                <div x-show="expanded"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     x-cloak
                     class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 px-2 pt-6 pb-2">
                    @foreach($groupedItems->slice(4) as $item)
                    @php
                        $uniqueId = $item->id . '-' . str_replace('#', '', $item->color_code);
                        $images = $item->images;
                    @endphp
                    <div class="product-card shadow-sm" id="p-card-{{ $uniqueId }}">

                        <div class="status-badge {{ $item->is_active ? 'status-active' : 'status-inactive' }}">
                            <i class="bi {{ $item->is_active ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} mr-1"></i>
                            {{ $item->is_active ? 'Active' : 'Inactive' }}
                        </div>

                        <div class="carousel-container group/main relative">
                            <div class="image-scroll-container" id="scroll-{{ $uniqueId }}">
                                @foreach($images as $img)
                                <div class="scroll-slide"
                                     data-variant-id="{{ $img->variant_id ?? $item->first_variant->id }}"
                                     data-price="{{ number_format($item->first_variant->price, 2) }}"
                                     data-sku="{{ $item->first_variant->sku ?? 'NO-CODE' }}"
                                     data-discount="{{ $item->first_variant->discount_price ? number_format($item->first_variant->discount_price, 2) : '' }}"
                                     data-all-sizes='{!! json_encode($item->variants->map(fn($v)=>["size"=>$v->size,"stock"=>$v->stock,"sku"=>$v->sku])) !!}'>
                                    <img src="{{ $img->image_url }}" alt="{{ $item->name }}" loading="lazy">
                                </div>
                                @endforeach
                            </div>
                            @if($images->count() > 1)
                            <button type="button" class="nav-btn left-[10px]" onclick="btnScroll('scroll-{{ $uniqueId }}', -1)">
                                <i class="bi bi-chevron-left text-lg"></i>
                            </button>
                            <button type="button" class="nav-btn right-[10px]" onclick="btnScroll('scroll-{{ $uniqueId }}', 1)">
                                <i class="bi bi-chevron-right text-lg"></i>
                            </button>
                            @endif
                            <div class="color-swatch-container">
                                @foreach($item->all_colors as $color)
                                <div class="color-circle {{ $color == $item->color_code ? 'active' : '' }}" style="background-color: {{ $color }}"></div>
                                @endforeach
                            </div>
                        </div>

                        <div class="p-4 flex flex-col flex-1 space-y-3 justify-between">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded uppercase">{{ $item->category_name }}</span>
                                    <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-1 rounded font-mono">SKU: {{ $item->first_variant->sku ?? 'NO-CODE' }}</span>
                                </div>
                                <h3 class="text-sm font-bold text-slate-800 leading-tight">
                                    {{ $item->name }}
                                    <span class="text-slate-400">({{ $item->color_name }})</span>
                                </h3>
                                <div class="text-xs text-slate-600 space-y-1">
                                    @if(!empty($item->brand)) <div><span class="text-slate-400">Brand:</span> <span class="font-medium text-slate-700">{{ $item->brand }}</span></div> @endif
                                    @if(!empty($item->collection)) <div><span class="text-slate-400">Collection:</span> <span class="font-medium text-slate-700">{{ $item->collection }}</span></div> @endif
                                </div>
                                <div class="flex flex-wrap gap-2 pt-1">
                                    @foreach($item->variants as $v)
                                    <div class="size-pill">
                                        <div class="size-label">Size: {{ $v->size }}</div>
                                        <div class="stock-count">Stock: {{ $v->stock }}</div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mt-4 flex items-end justify-between border-t pt-4">
                                <div class="price-box flex flex-col text-right">
                                    @if($item->first_variant->discount_price)
                                    <span class="text-[10px] text-slate-400 line-through">{{ number_format($item->first_variant->price, 2) }} ₺</span>
                                    <span class="text-base font-black text-slate-900">{{ number_format($item->first_variant->discount_price, 2) }} ₺</span>
                                    @else
                                    <span class="text-base font-black text-slate-900">{{ number_format($item->first_variant->price, 2) }} ₺</span>
                                    @endif
                                </div>
                                <a href="{{ route('urundetay', $item->first_variant->id) }}" class="w-10 h-10 bg-slate-50 text-slate-400 hover:text-indigo-600 rounded-xl flex items-center justify-center border transition-all">
                                    <i class="bi bi-pencil-square text-lg"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Show More / Less butonu --}}
                <div class="flex justify-center mt-6 pb-2">
                    <button @click="expanded = !expanded"
                        class="flex items-center gap-2 px-6 py-3 bg-white border-2 border-slate-200 hover:border-indigo-400 hover:bg-indigo-50 text-slate-600 hover:text-indigo-600 rounded-2xl font-bold text-sm transition-all duration-200 shadow-sm group">
                        <template x-if="!expanded">
                            <span class="flex items-center gap-2">
                                <i class="bi bi-grid text-base"></i>
                                <span>Show all {{ $groupedItems->count() }} products</span>
                                <i class="bi bi-chevron-down text-xs group-hover:translate-y-0.5 transition-transform duration-200"></i>
                            </span>
                        </template>
                        <template x-if="expanded">
                            <span class="flex items-center gap-2">
                                <i class="bi bi-chevron-up text-base"></i>
                                <span>Show less</span>
                            </span>
                        </template>
                    </button>
                </div>
                @endif

            </section>
            @endforeach
        </main>
    @endif

</div>

<script>
    function syncData(id) {
        const container = document.getElementById('scroll-' + id);
        if(!container) return;
        const slides = container.querySelectorAll('.scroll-slide');
        if(slides.length === 0) return;
        const index = Math.round(container.scrollLeft / slides[0].offsetWidth);
        const slide = slides[index];
        if (!slide) return;
        const data = slide.dataset;

        const skuBox = document.getElementById('sku-' + id);
        if(skuBox) skuBox.innerText = data.sku;

        const pBox = document.getElementById('price-' + id);
        if(pBox) {
            pBox.innerHTML = data.discount 
                ? `<span class="text-[10px] text-slate-400 line-through leading-none">${data.price} ₺</span>
                   <span class="text-base font-black text-slate-900 leading-none">${data.discount} ₺</span>`
                : `<span class="text-base font-black text-slate-900 leading-none">${data.price} ₺</span>`;
        }

        const infoBox = document.getElementById('info-' + id);
        if(infoBox) {
            try {
                const sizes = JSON.parse(data.allSizes);
                infoBox.innerHTML = `<div class="flex flex-wrap gap-2">` + 
                    sizes.map(s => `
                        <div class="size-pill">
                            <div class="size-label">size: ${s.size}</div>
                            <div class="stock-count">stock: ${s.stock}</div>
                        </div>`).join('') + 
                    `</div>`;
            } catch (e) {
                console.error("Beden verisi okunamadı:", e);
            }
        }

        const editBtn = document.querySelector(`#p-card-${id} a[href*='/urundetay/']`);
        if(editBtn && data.variantId) {
            editBtn.href = '/urundetay/' + data.variantId;
        }
    }

    function btnScroll(id, dir) { 
        const c = document.getElementById(id); 
        if(!c) return;
        const scrollAmount = c.offsetWidth;
        const currentScroll = c.scrollLeft;
        const maxScroll = c.scrollWidth - scrollAmount;

        if (dir === 1 && currentScroll >= maxScroll - 10) {
            c.scrollTo({ left: 0, behavior: 'smooth' });
        } else if (dir === -1 && currentScroll <= 10) {
            c.scrollTo({ left: maxScroll, behavior: 'smooth' });
        } else {
            c.scrollBy({ left: scrollAmount * dir, behavior: 'smooth' }); 
        }

        const uniqueId = id.replace('scroll-', '');
        setTimeout(() => syncData(uniqueId), 250);
    }
</script>

</body>
</html>
