<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorites - Miharbi Clothing</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        * { box-sizing: border-box; }
        body { background-color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; color: #1e293b; margin: 0; overflow-x: hidden; }
        html { overflow-y: scroll; scroll-behavior: smooth; }
        .hide-scrollbar::-webkit-scrollbar { display: none !important; }
        .hide-scrollbar { -ms-overflow-style: none !important; scrollbar-width: none !important; }

        .admin-nav { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 9999; width: 100%; }
        .nav-item { display: flex; align-items: center; gap: 8px; padding: 12px 16px; border-radius: 12px; font-size: 14px; font-weight: 600; color: #64748b; transition: 0.2s; text-decoration: none; cursor: pointer; }
        .nav-item:hover { background: #eef2ff; color: #4f46e5; }
        .nav-item.active { background: #4f46e5; color: white; }

        .search-container { position: relative; max-width: 672px; margin: 0 auto 40px auto; }
        .search-input { width: 100%; padding: 14px 100px 14px 52px; background: white; border: 1px solid #e2e8f0; border-radius: 24px; font-size: 14px; transition: 0.3s; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
        .search-input:focus { outline: none; border-color: #4f46e5; box-shadow: 0 8px 20px rgba(79,70,229,0.1); }
        .search-clear-btn { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: #f1f5f9; color: #64748b; padding: 6px 16px; border-radius: 16px; font-size: 11px; font-weight: 800; text-transform: uppercase; transition: 0.2s; border: none; cursor: pointer; display: flex; align-items: center; gap: 4px; }
        .search-clear-btn:hover { background: #ef4444; color: white; }

        .product-card { background: white; border: 1px solid #e2e8f0; border-radius: 20px; overflow: hidden; transition: all 0.4s; height: 100%; display: flex; flex-direction: column; position: relative; flex-shrink: 0; width: 280px; }
        @media (min-width: 768px) { .product-card { width: 320px; } }
        .product-card:hover { transform: translateY(-6px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.08); }

        .carousel-container { position: relative; overflow: hidden; aspect-ratio: 1/1.2; background: #f1f5f9; }
        .image-scroll-container { display: flex; overflow-x: auto; scroll-snap-type: x mandatory; height: 100%; scrollbar-width: none; -ms-overflow-style: none; }
        .image-scroll-container::-webkit-scrollbar { display: none; }
        .scroll-slide { flex: 0 0 100%; scroll-snap-align: start; height: 100%; }
        .scroll-slide img { width: 100%; height: 100%; object-fit: cover; }

        .nav-btn { position: absolute; top: 50%; transform: translateY(-50%); z-index: 50; background: rgba(255,255,255,0.9); width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center; justify-content: center; transition: all 0.3s; border: 1px solid #e2e8f0; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.1); color: #1e293b; }
        @media (min-width: 1024px) { .nav-btn { opacity: 0; } .product-card:hover .nav-btn { opacity: 1; } }
        @media (max-width: 1023px) { .nav-btn { opacity: 1 !important; width: 30px; height: 30px; border-radius: 8px; } }

        .color-swatch-container { position: absolute; bottom: 12px; left: 12px; z-index: 40; background: rgba(255,255,255,0.95); padding: 6px; border-radius: 20px; display: flex; gap: 5px; border: 1px solid #f1f5f9; backdrop-filter: blur(4px); }
        .color-circle { width: 14px; height: 14px; border-radius: 50%; border: 1px solid rgba(0,0,0,0.1);transition: 0.2s; }
        .color-circle.active { outline: 2px solid #4f46e5; outline-offset: 1px; transform: scale(1.1); }

        .size-pill { display: flex; align-items: center; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; background: white; }
        .size-label { padding: 4px 8px; background: #f8fafc; border-right: 1px solid #e2e8f0; font-size: 11px; font-weight: 800; color: #1e293b; }
        .stock-count { padding: 4px 8px; font-size: 11px; font-weight: 700; color: #4f46e5; }

        .modal-image-container { display: flex; overflow-x: auto; scroll-snap-type: x mandatory; height: 100%; border-radius: 24px; scroll-behavior: smooth; }
        .modal-image-slide { min-width: 100%; scroll-snap-align: start; height: 100%; }
        .modal-image-slide img { width: 100%; height: 100%; object-fit: cover; }
        .qv-variant-btn { display: flex; flex-direction: column; align-items: start; gap: 2px; padding: 10px 16px; border-radius: 14px; border: 2px solid #f1f5f9; background: white; transition: all 0.15s ease-out; cursor: pointer; }
        .qv-variant-btn.active { border-color: #4f46e5; background: #eef2ff; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(79,70,229,0.1); }
        .quickview-overlay { position: fixed; inset: 0; z-index: 20000; display: flex; align-items: center; justify-content: center; padding: 20px; background: rgba(15,23,42,0.4); backdrop-filter: blur(4px); }

        .scroll-to-top { position: fixed; bottom: 24px; right: 24px; width: 50px; height: 50px; background: #4f46e5; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; cursor: pointer; box-shadow: 0 8px 20px rgba(79,70,229,0.4); transition: all 0.3s ease; z-index: 9998; opacity: 0; transform: translateY(100px); pointer-events: none; }
        .scroll-to-top.visible { opacity: 1; transform: translateY(0); pointer-events: auto; }
        .scroll-to-top:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(79,70,229,0.5); background: #4338ca; }
        .scroll-to-top:active { transform: translateY(-2px); }

        .toast-container { position: fixed; top: 16px; right: 16px; left: 16px; z-index: 99999; display: flex; flex-direction: column; gap: 10px; pointer-events: none; }
        @media (min-width: 640px) { .toast-container { top: 24px; right: 24px; left: auto; } }
        .toast { background: white; border-radius: 14px; padding: 14px 16px; box-shadow: 0 20px 40px -10px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 12px; width: 100%; transform: translateY(-100%); opacity: 0; transition: all 0.4s cubic-bezier(0.175,0.885,0.32,1.275); pointer-events: auto; border-left: 4px solid; }
        @media (min-width: 640px) { .toast { min-width: 320px; max-width: 400px; border-radius: 16px; padding: 16px 20px; transform: translateX(100%); } }
        .toast.show { transform: translateY(0); opacity: 1; }
        @media (min-width: 640px) { .toast.show { transform: translateX(0); } }
        .toast.success { border-left-color: #10b981; }
        .toast.error { border-left-color: #ef4444; }
        .toast-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
        @media (min-width: 640px) { .toast-icon { width: 40px; height: 40px; border-radius: 12px; font-size: 20px; } }
        .toast.success .toast-icon { background: #d1fae5; color: #059669; }
        .toast.error .toast-icon { background: #fee2e2; color: #dc2626; }
        .toast-content { flex: 1; min-width: 0; }
        .toast-title { font-weight: 700; font-size: 13px; color: #0f172a; margin-bottom: 2px; }
        @media (min-width: 640px) { .toast-title { font-size: 14px; } }
        .toast-message { font-size: 12px; color: #64748b; line-height: 1.4; }
        @media (min-width: 640px) { .toast-message { font-size: 13px; } }
        .toast-close { width: 26px; height: 26px; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #94a3b8; cursor: pointer; transition: all 0.2s; flex-shrink: 0; }
        @media (min-width: 640px) { .toast-close { width: 28px; height: 28px; border-radius: 8px; } }
        .toast-close:hover { background: #f1f5f9; color: #64748b; }

        .spinner { width: 18px; height: 18px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.8s linear infinite; }
        @media (min-width: 640px) { .spinner { width: 20px; height: 20px; } }
        @keyframes spin { to { transform: rotate(360deg); } }

        .review-badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 8px; background: #fef3c7; color: #d97706; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .review-section { max-height: 300px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
        .review-section::-webkit-scrollbar { width: 6px; }
        .review-section::-webkit-scrollbar-track { background: transparent; }
        .review-section::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
        .review-item { padding: 16px; border-bottom: 1px solid #f1f5f9; transition: background 0.2s; }
        .review-item:hover { background: #f8fafc; }
        .review-item:last-child { border-bottom: none; }
        .star-rating { display: flex; gap: 2px; }
        .star-rating i { color: #fbbf24; font-size: 12px; }
        .star-rating i.empty { color: #e2e8f0; }

        [x-cloak] { display: none !important; }

        @keyframes slideOut {
            0% { opacity: 1; transform: translateX(0) scale(1); }
            100% { opacity: 0; transform: translateX(-100%) scale(0.9); }
        }
    </style>
</head>
<body x-data="appData()" x-init="initApp()" @toast.window="addToast($event.detail.type, $event.detail.title, $event.detail.message)">

<!-- Toast -->
<div class="toast-container">
    <template x-for="toast in toasts" :key="toast.id">
        <div class="toast" :class="toast.type + ' show'" x-show="true"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="toast-icon"><i class="bi" :class="toast.type === 'success' ? 'bi-check-lg' : 'bi-exclamation-triangle-fill'"></i></div>
            <div class="toast-content">
                <div class="toast-title" x-text="toast.title"></div>
                <div class="toast-message" x-text="toast.message"></div>
            </div>
            <div class="toast-close" @click="removeToast(toast.id)"><i class="bi bi-x-lg"></i></div>
        </div>
    </template>
</div>

<!-- Header -->
<header class="admin-nav shadow-sm" x-data="{ mobileMenu: false }">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between h-20">
            <a href="{{ url('/dashboard') }}" class="flex items-center gap-2 hover:opacity-80 transition no-underline">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg"><i class="bi bi-shop text-xl"></i></div>
                <span class="font-bold text-slate-900 tracking-tight text-base md:text-lg">Miharbi Clothing</span>
            </a>
            <button @click="mobileMenu = !mobileMenu" type="button" class="md:hidden p-2.5 rounded-xl text-slate-600 hover:bg-slate-100 transition-all">
                <i class="bi" :class="mobileMenu ? 'bi-x-lg' : 'bi-list'" style="font-size: 1.8rem;"></i>
            </button>
            <nav class="hidden md:flex items-center gap-2">
                <a href="{{ url('/dashboard') }}" class="nav-item"><i class="bi bi-grid-1x2"></i>Dashboard</a>
                <a href="{{ url('favorites') }}" class="nav-item "><i class="bi bi-heart"></i> Favorites</a>
                <a href="{{ url('mycart') }}" class="nav-item"><i class="bi bi-cart3"></i> My Cart</a>
                <a href="{{ url('myorders') }}" class="nav-item"><i class="bi bi-bag"></i> My Orders</a>
                @auth
                <div class="relative ml-2" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-100 transition-all duration-200">
                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600"><i class="bi bi-person-fill"></i></div>
                        <span class="text-sm font-medium text-slate-700">{{ Auth::user()->name }}</span>
                        <i class="bi bi-chevron-down text-xs text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-1 z-50">
                        <div class="px-4 py-2 border-b border-slate-100">
                            <p class="text-sm font-medium text-slate-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors"><i class="bi bi-person-gear mr-2"></i>Profile Settings</a>
                        <div class="border-t border-slate-100 mt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"><i class="bi bi-box-arrow-right mr-2"></i>Log Out</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endauth
            </nav>
        </div>
        <nav x-show="mobileMenu" x-cloak @click.away="mobileMenu = false"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 -translate-y-2" x-transition:enter-end="transform opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="transform opacity-100 translate-y-0" x-transition:leave-end="transform opacity-0 -translate-y-2"
             class="md:hidden px-4 pb-4 space-y-1 bg-white border-t mt-2">
            <a href="{{ url('/dashboard') }}" class="nav-item block mt-2" @click="mobileMenu = false"><i class="bi bi-grid-1x2"></i> Dashboard</a>
            <a href="{{ url('favorites') }}" class="nav-item  block" @click="mobileMenu = false"><i class="bi bi-heart"></i> Favorites</a>
            <a href="{{ url('mycart') }}" class="nav-item block" @click="mobileMenu = false"><i class="bi bi-cart3"></i> My Cart</a>
            <a href="{{ url('myorders') }}" class="nav-item block" @click="mobileMenu = false"><i class="bi bi-bag"></i> My Orders</a>
            @auth
            <div class="border-t pt-2 mt-2">
                <div class="flex items-center gap-3 px-3 py-2">
                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600"><i class="bi bi-person-fill text-lg"></i></div>
                    <div><p class="font-medium text-slate-900">{{ Auth::user()->name }}</p><p class="text-xs text-slate-500">{{ Auth::user()->email }}</p></div>
                </div>
                <a href="{{ route('profile.edit') }}" class="nav-item block" @click="mobileMenu = false"><i class="bi bi-person-gear"></i>Profile Settings</a>
                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit" class="nav-item w-full text-left text-red-600 bg-transparent border-0" @click="mobileMenu = false"><i class="bi bi-box-arrow-right"></i>Log Out</button>
                </form>
            </div>
            @endauth
        </nav>
    </div>
</header>

<button @click="scrollToTop()" :class="showScrollTop ? 'visible' : ''" class="scroll-to-top" aria-label="Scroll to top">
    <i class="bi bi-arrow-up"></i>
</button>

<!-- PRODUCTS SECTION -->
<div id="products-section" class="max-w-7xl mx-auto px-4 py-8">

    <!-- Search & Filter Bar -->
    <div class="flex flex-col md:flex-row gap-4 mb-10">
        <div class="search-container group flex-1 !mb-0">
            <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition text-lg"></i>
            <form action="{{ route('favorites') }}" method="GET" id="searchForm">
                <input type="text" name="q" id="searchInput" value="{{ request('q') }}" placeholder="Search name, category, brand or SKU..." class="search-input">
                @if(request('q') || request('cat') || request('size') || request('min') || request('max') || request('brand') || request('collection'))
                    <button type="button" onclick="window.location.href='{{ route('favorites') }}'" class="search-clear-btn">
                        <i class="bi bi-x-lg"></i><span>Reset</span>
                    </button>
                @endif
            </form>
        </div>
        <button @click="showFilters = true" class="px-6 py-3.5 bg-white border-2 border-indigo-50 rounded-2xl font-bold text-slate-700 flex items-center justify-center gap-2 shadow-sm hover:border-indigo-200 transition-all">
            <i class="bi bi-sliders2 text-indigo-600"></i><span>Filter Options</span>
        </button>
    </div>

    <!-- Filter Modal -->
    <div x-show="showFilters"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[10001] bg-slate-900/60 backdrop-blur-sm" x-cloak>
        <div @click.away="showFilters = false"
             x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
             class="w-full max-w-sm h-full bg-white shadow-2xl p-6 overflow-y-auto">
            <div class="flex items-center justify-between mb-8 border-b pb-4">
                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2"><i class="bi bi-sliders2 text-indigo-500"></i> Filters</h2>
                <button @click="showFilters = false" class="text-slate-400 hover:text-rose-500 transition text-2xl"><i class="bi bi-x-lg"></i></button>
            </div>
            <form action="{{ route('favorites') }}" method="GET" class="space-y-8">
                <input type="hidden" name="q" value="{{ request('q') }}">
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

    @if($urunler->isEmpty() && request()->anyFilled(['q', 'cat', 'brand', 'collection', 'size', 'min', 'max']))
        <div class="flex flex-col items-center justify-center py-20 bg-white rounded-3xl border-2 border-dashed border-slate-200">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4"><i class="bi bi-search text-3xl text-slate-300"></i></div>
            <h3 class="text-xl font-bold text-slate-800">Product not found</h3>
            <p class="text-slate-500 mt-2">No products match your selected filters.</p>
            <button onclick="window.location.href='{{ route('favorites') }}'" class="mt-6 px-6 py-2 bg-indigo-600 text-white font-bold rounded-xl">Clear Filters</button>
        </div>
    @elseif($urunler->isEmpty())
        <div class="flex flex-col items-center justify-center py-32">
            <div class="w-24 h-24 bg-indigo-50 rounded-full flex items-center justify-center mb-6"><i class="bi bi-heart text-4xl text-indigo-300"></i></div>
            <h3 class="text-2xl font-bold text-slate-700">No favorites yet</h3>
            <p class="text-slate-400 mt-2 mb-8">Products you heart will appear here.</p>
            <a href="{{ url('/dashboard') }}" class="px-8 py-3 bg-indigo-600 text-white font-bold rounded-2xl shadow-lg hover:bg-indigo-700 transition">Browse Products</a>
        </div>
    @else

    <main class="space-y-16" x-data="favoritesManager()" x-init="initFavoritesManager()">

        @foreach($urunler->groupBy('category_name') as $categoryName => $groupedItems)
        @php
            $categorySlug = Str::slug($categoryName ?: 'uncategorized');
            $categoryKey  = 'cat_' . $categorySlug . '_' . $loop->index;
            $firstFour    = $groupedItems->take(4);
            $remaining    = $groupedItems->slice(4);
            $hasMore      = $groupedItems->count() > 4;
        @endphp

        <section id="{{ $categoryKey }}"
            class="relative group/section"
            x-show="categories['{{ $categoryKey }}'] && categories['{{ $categoryKey }}'].visible"
            x-transition:leave="transition ease-in duration-500"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-8"
            x-data="{
                expanded: false,
                canScroll: false,
                checkScroll() {
                    this.$nextTick(() => {
                        const row = this.$refs.row;
                        if (row) this.canScroll = row.scrollWidth > row.clientWidth;
                    });
                },
                scrollNext() { this.$refs.row.scrollBy({ left: 400, behavior: 'smooth' }); },
                scrollPrev() { this.$refs.row.scrollBy({ left: -400, behavior: 'smooth' }); }
            }"
            x-init="setTimeout(() => checkScroll(), 300)"
            @resize.window="checkScroll()">

            <!-- Kategori Başlığı -->
            <div class="flex items-center justify-between mb-6 px-2">
                <h2 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span class="w-2 h-8 bg-indigo-600 rounded-full"></span>
                    {{ $categoryName ?: 'Collection' }}
                </h2>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                    <span x-text="categories['{{ $categoryKey }}'] ? categories['{{ $categoryKey }}'].count : 0"></span> Products
                </span>
            </div>

            <!-- Scroll Butonları -->
            <button x-show="canScroll" x-cloak @click="scrollPrev()"
                class="absolute left-[-20px] top-[40%] z-50 w-11 h-11 bg-white shadow-xl rounded-full hidden lg:flex items-center justify-center border border-slate-100 hover:bg-indigo-50 opacity-0 group-hover/section:opacity-100 transition-all">
                <i class="bi bi-chevron-left text-indigo-600 font-bold text-xl"></i>
            </button>
            <button x-show="canScroll" x-cloak @click="scrollNext()"
                class="absolute right-[-20px] top-[40%] z-50 w-11 h-11 bg-white shadow-xl rounded-full hidden lg:flex items-center justify-center border border-slate-100 hover:bg-indigo-50 opacity-0 group-hover/section:opacity-100 transition-all">
                <i class="bi bi-chevron-right text-indigo-600 font-bold text-xl"></i>
            </button>

            <!-- İlk 4 Ürün -->
            <div x-ref="row" class="flex gap-6 overflow-x-auto hide-scrollbar snap-x px-2 pb-4">
                @foreach($firstFour as $item)
                @php
                    $uniqueId       = $item->id . '-' . str_replace('#', '', $item->color_code ?? '');
                    $images         = $item->images;
                    $firstVariantId = $item->first_variant->id ?? null;
                    $favInGroup     = $item->variants->whereIn('id', $userFavorites ?? [])->first();
                    $cardVariantId  = $favInGroup ? $favInGroup->id : $firstVariantId;
                    $avgRating      = $item->reviews_avg_rating ?? 0;
                    $reviewCount    = $item->reviews_count ?? 0;
                @endphp
                <div class="product-card shadow-sm" id="p-card-{{ $uniqueId }}"
                     x-data="productCard({{ $cardVariantId }}, true, {{ $avgRating }}, {{ $reviewCount }}, 0, '{{ $categoryKey }}')"
                     @favorite-updated.window="handleFavoriteUpdate($event.detail)"
                     x-show="!isRemoved"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95 -translate-x-full">

                    <div class="carousel-container group/main relative">
                        <button @click="toggleFavoriteDebounced()"
                            :class="isFavorite ? 'bg-red-500 text-white border-red-500' : 'bg-white/90 backdrop-blur-sm text-slate-400 hover:text-red-500 border-slate-100'"
                            class="absolute top-2 right-2 sm:top-3 sm:right-3 z-30 w-8 h-8 sm:w-9 sm:h-9 rounded-full shadow-lg border flex items-center justify-center transition-all duration-300 hover:scale-110 active:scale-95">
                            <svg xmlns="http://www.w3.org/2000/svg" :fill="isFavorite ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 sm:w-5 sm:h-5 transition-transform duration-300" :class="isFavorite ? 'scale-110' : 'scale-100'">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                            </svg>
                        </button>
                        <div class="absolute top-2 left-2 z-20" x-show="reviewCount > 0">
                            <div class="review-badge"><i class="bi bi-star-fill"></i><span x-text="avgRating.toFixed(1)"></span><span x-text="'(' + reviewCount + ')'" class="opacity-75"></span></div>
                        </div>
                        <div class="image-scroll-container" id="scroll-{{ $uniqueId }}">
                            @foreach($images as $img)
                            <div class="scroll-slide"><img src="{{ $img->image_url }}" alt="{{ $item->name }}" loading="lazy"></div>
                            @endforeach
                        </div>
                        @if($images->count() > 1)
                        <button type="button" class="nav-btn left-[10px]" onclick="btnScroll('scroll-{{ $uniqueId }}', -1)"><i class="bi bi-chevron-left text-lg"></i></button>
                        <button type="button" class="nav-btn right-[10px]" onclick="btnScroll('scroll-{{ $uniqueId }}', 1)"><i class="bi bi-chevron-right text-lg"></i></button>
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
                            <h3 class="text-sm font-bold text-slate-800 leading-tight">{{ $item->name }} <span class="text-slate-400">({{ $item->color_name }})</span></h3>
                            <div class="text-xs text-slate-600 space-y-1">
                                @if(!empty($item->brand))<div><span class="text-slate-400">Brand:</span> <span class="font-medium text-slate-700">{{ $item->brand }}</span></div>@endif
                                @if(!empty($item->collection))<div><span class="text-slate-400">Collection:</span> <span class="font-medium text-slate-700">{{ $item->collection }}</span></div>@endif
                            </div>
                            <div class="flex flex-wrap gap-2 pt-1">
                                @foreach($item->variants as $v)
                                <div class="size-pill"><div class="size-label">Size: {{ $v->size }}</div><div class="stock-count">Stock: {{ $v->stock }}</div></div>
                                @endforeach
                            </div>
                        </div>
                        <div class="mt-4 flex items-end justify-between border-t pt-4">
                            <div class="price-box flex flex-col">
                                @if($item->first_variant->discount_price)
                                <span class="text-[10px] text-slate-400 line-through">{{ number_format($item->first_variant->price, 2) }} ₺</span>
                                <span class="text-base font-black text-slate-900">{{ number_format($item->first_variant->discount_price, 2) }} ₺</span>
                                @else
                                <span class="text-base font-black text-slate-900">{{ number_format($item->first_variant->price, 2) }} ₺</span>
                                @endif
                            </div>
                            <button @click="openQuickView({{ json_encode($item) }}, variantId)" class="w-10 h-10 bg-indigo-600 text-white hover:bg-indigo-700 rounded-xl flex items-center justify-center shadow-lg transition-all active:scale-90">
                                <i class="bi bi-eye-fill text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Kalan ürünler + Show all butonu --}}
            @if($hasMore)
            <div x-show="expanded"
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                 x-cloak
                 class="flex gap-6 overflow-x-auto hide-scrollbar snap-x px-2 pt-2 pb-4">
                @foreach($remaining as $item)
                @php
                    $uniqueId       = $item->id . '-' . str_replace('#', '', $item->color_code ?? '');
                    $images         = $item->images;
                    $firstVariantId = $item->first_variant->id ?? null;
                    $favInGroup     = $item->variants->whereIn('id', $userFavorites ?? [])->first();
                    $cardVariantId  = $favInGroup ? $favInGroup->id : $firstVariantId;
                    $avgRating      = $item->reviews_avg_rating ?? 0;
                    $reviewCount    = $item->reviews_count ?? 0;
                @endphp
                <div class="product-card shadow-sm" id="p-card-{{ $uniqueId }}"
                     x-data="productCard({{ $cardVariantId }}, true, {{ $avgRating }}, {{ $reviewCount }}, 0, '{{ $categoryKey }}')"
                     @favorite-updated.window="handleFavoriteUpdate($event.detail)"
                     x-show="!isRemoved"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95 -translate-x-full">

                    <div class="carousel-container group/main relative">
                        <button @click="toggleFavoriteDebounced()"
                            :class="isFavorite ? 'bg-red-500 text-white border-red-500' : 'bg-white/90 backdrop-blur-sm text-slate-400 hover:text-red-500 border-slate-100'"
                            class="absolute top-2 right-2 sm:top-3 sm:right-3 z-30 w-8 h-8 sm:w-9 sm:h-9 rounded-full shadow-lg border flex items-center justify-center transition-all duration-300 hover:scale-110 active:scale-95">
                            <svg xmlns="http://www.w3.org/2000/svg" :fill="isFavorite ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 sm:w-5 sm:h-5 transition-transform duration-300" :class="isFavorite ? 'scale-110' : 'scale-100'">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                            </svg>
                        </button>
                        <div class="absolute top-2 left-2 z-20" x-show="reviewCount > 0">
                            <div class="review-badge"><i class="bi bi-star-fill"></i><span x-text="avgRating.toFixed(1)"></span><span x-text="'(' + reviewCount + ')'" class="opacity-75"></span></div>
                        </div>
                        <div class="image-scroll-container" id="scroll-{{ $uniqueId }}">
                            @foreach($images as $img)
                            <div class="scroll-slide"><img src="{{ $img->image_url }}" alt="{{ $item->name }}" loading="lazy"></div>
                            @endforeach
                        </div>
                        @if($images->count() > 1)
                        <button type="button" class="nav-btn left-[10px]" onclick="btnScroll('scroll-{{ $uniqueId }}', -1)"><i class="bi bi-chevron-left text-lg"></i></button>
                        <button type="button" class="nav-btn right-[10px]" onclick="btnScroll('scroll-{{ $uniqueId }}', 1)"><i class="bi bi-chevron-right text-lg"></i></button>
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
                            <h3 class="text-sm font-bold text-slate-800 leading-tight">{{ $item->name }} <span class="text-slate-400">({{ $item->color_name }})</span></h3>
                            <div class="text-xs text-slate-600 space-y-1">
                                @if(!empty($item->brand))<div><span class="text-slate-400">Brand:</span> <span class="font-medium text-slate-700">{{ $item->brand }}</span></div>@endif
                                @if(!empty($item->collection))<div><span class="text-slate-400">Collection:</span> <span class="font-medium text-slate-700">{{ $item->collection }}</span></div>@endif
                            </div>
                            <div class="flex flex-wrap gap-2 pt-1">
                                @foreach($item->variants as $v)
                                <div class="size-pill"><div class="size-label">Size: {{ $v->size }}</div><div class="stock-count">Stock: {{ $v->stock }}</div></div>
                                @endforeach
                            </div>
                        </div>
                        <div class="mt-4 flex items-end justify-between border-t pt-4">
                            <div class="price-box flex flex-col">
                                @if($item->first_variant->discount_price)
                                <span class="text-[10px] text-slate-400 line-through">{{ number_format($item->first_variant->price, 2) }} ₺</span>
                                <span class="text-base font-black text-slate-900">{{ number_format($item->first_variant->discount_price, 2) }} ₺</span>
                                @else
                                <span class="text-base font-black text-slate-900">{{ number_format($item->first_variant->price, 2) }} ₺</span>
                                @endif
                            </div>
                            <button @click="openQuickView({{ json_encode($item) }}, variantId)" class="w-10 h-10 bg-indigo-600 text-white hover:bg-indigo-700 rounded-xl flex items-center justify-center shadow-lg transition-all active:scale-90">
                                <i class="bi bi-eye-fill text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Show all / Show less -->
            <div class="flex justify-center mt-4 pb-2">
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

    <div class="mt-12">
        {{ $urunler->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Quick View Modal -->
<template x-if="quickView">
    <div class="quickview-overlay" x-cloak @click.self="quickView = false">
        <div class="bg-white w-full max-w-6xl max-h-[90vh] rounded-[40px] shadow-2xl overflow-hidden flex flex-col lg:flex-row relative"
             x-data="quickViewData()" x-init="initQuickView()">

            <button @click="quickView = false" class="absolute top-4 right-4 lg:top-6 lg:right-6 z-50 w-10 h-10 lg:w-12 lg:h-12 bg-white/90 shadow-xl rounded-full flex items-center justify-center text-slate-400 hover:text-red-500 border border-slate-100 transition-colors">
                <i class="bi bi-x-lg text-lg lg:text-xl"></i>
            </button>

            <button @click="toggleModalFavoriteDebounced()"
                :class="isModalFavorite ? 'bg-red-500 text-white border-red-500' : 'bg-white/90 backdrop-blur-sm text-slate-400 hover:text-red-500 border-slate-100'"
                class="absolute top-4 left-4 lg:top-6 lg:left-6 z-40 w-10 h-10 lg:w-12 lg:h-12 rounded-full shadow-xl border flex items-center justify-center transition-all duration-300 hover:scale-110 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" :fill="isModalFavorite ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 lg:w-6 lg:h-6 transition-transform duration-300" :class="isModalFavorite ? 'scale-110' : 'scale-100'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
            </button>

            <div class="lg:w-1/2 bg-slate-50 relative flex items-center justify-center p-4 lg:p-10">
                <div class="relative w-full aspect-[3/4] group/modal">
                    <div x-ref="modalTrack" class="modal-image-container hide-scrollbar shadow-2xl border-[6px] lg:border-[10px] border-white bg-white relative">
                        <template x-for="img in (activeVariant?.images?.length ? activeVariant.images : selectedItem?.images || [])" :key="img.image_url">
                            <div class="modal-image-slide"><img :src="img.image_url" class="w-full h-full object-cover"></div>
                        </template>
                    </div>
                    <template x-if="(activeVariant?.images?.length ? activeVariant.images : selectedItem?.images || []).length > 1">
                        <div>
                            <button @click="btnScrollModal(-1)" class="absolute left-2 lg:left-4 top-1/2 -translate-y-1/2 w-9 h-9 lg:w-11 lg:h-11 bg-white/90 rounded-xl lg:rounded-2xl shadow-xl flex items-center justify-center border border-slate-100 transition-all hover:bg-white"><i class="bi bi-chevron-left text-lg lg:text-xl font-bold text-slate-700"></i></button>
                            <button @click="btnScrollModal(1)" class="absolute right-2 lg:right-4 top-1/2 -translate-y-1/2 w-9 h-9 lg:w-11 lg:h-11 bg-white/90 rounded-xl lg:rounded-2xl shadow-xl flex items-center justify-center border border-slate-100 transition-all hover:bg-white"><i class="bi bi-chevron-right text-lg lg:text-xl font-bold text-slate-700"></i></button>
                        </div>
                    </template>
                </div>
            </div>

            <div class="lg:w-1/2 p-6 lg:p-12 overflow-y-auto flex flex-col gap-4 lg:gap-6">
                <div>
                    <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-full uppercase" x-text="selectedItem?.category_name || 'Product'"></span>
                    <h2 class="text-2xl lg:text-3xl font-black text-slate-900 mt-4 leading-tight uppercase" x-text="selectedItem?.name"></h2>
                    <p class="text-slate-400 font-bold mt-2 uppercase text-xs">
                        <span x-show="activeVariant?.brand" x-text="activeVariant?.brand"></span>
                        <span x-show="activeVariant?.brand && activeVariant?.collection"> • </span>
                        <span x-show="activeVariant?.collection" x-text="activeVariant?.collection"></span>
                    </p>
                </div>

                <div class="bg-slate-50 p-4 lg:p-6 rounded-[20px] lg:rounded-[24px] border border-slate-100">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Description</p>
                    <div :class="descOpen ? '' : 'line-clamp-2'" class="text-slate-600 text-sm leading-relaxed" x-text="selectedItem?.description || 'No description available.'"></div>
                    <button x-show="selectedItem?.description && selectedItem?.description.length > 50" @click="descOpen = !descOpen" class="text-indigo-600 text-[10px] font-black uppercase mt-2 hover:underline" x-text="descOpen ? 'Show Less' : 'Read More'"></button>
                </div>

                <div class="flex items-center gap-4 py-2" x-show="modalReviews.count > 0">
                    <div class="flex items-center gap-2">
                        <div class="star-rating">
                            <template x-for="i in 5" :key="i"><i class="bi" :class="i <= Math.round(modalReviews.avg) ? 'bi-star-fill' : 'bi-star'"></i></template>
                        </div>
                        <span class="text-sm font-bold text-slate-700" x-text="modalReviews.avg.toFixed(1)"></span>
                    </div>
                    <span class="text-slate-400">•</span>
                    <button @click="showReviewsModal = true; loadReviewsDetail()" class="text-sm text-indigo-600 font-bold hover:underline">
                        <span x-text="modalReviews.count"></span> Reviews
                    </button>
                </div>

                <div class="py-4 flex items-center justify-between border-b border-slate-100">
                    <div class="flex flex-col">
                        <template x-if="activeVariant?.discount_price">
                            <div class="flex items-baseline gap-3">
                                <span class="text-2xl lg:text-3xl font-black text-indigo-600" x-text="Number(activeVariant?.discount_price).toFixed(2) + ' ₺'"></span>
                                <span class="text-sm text-slate-300 line-through font-bold" x-text="Number(activeVariant?.price).toFixed(2) + ' ₺'"></span>
                            </div>
                        </template>
                        <template x-if="!activeVariant?.discount_price">
                            <span class="text-2xl lg:text-3xl font-black text-slate-900" x-text="Number(activeVariant?.price || 0).toFixed(2) + ' ₺'"></span>
                        </template>
                    </div>
                    <span class="text-[10px] font-black text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-100 uppercase">
                        <span x-text="activeVariant?.stock || 0"></span> in stock
                    </span>
                </div>

                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-4 lg:p-5 rounded-[20px] border border-indigo-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">Add to Cart</span>
                        <span class="text-xs text-slate-500" x-show="activeVariant?.stock > 0">Max: <span x-text="Math.min(activeVariant?.stock, 10)"></span></span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center bg-white rounded-xl border border-indigo-200 shadow-sm">
                            <button @click="cartQuantity = Math.max(1, cartQuantity - 1)" :disabled="cartQuantity <= 1" class="w-10 h-10 flex items-center justify-center text-indigo-600 hover:bg-indigo-50 rounded-l-xl transition disabled:opacity-30"><i class="bi bi-dash-lg"></i></button>
                            <span class="w-12 text-center font-bold text-slate-800" x-text="cartQuantity"></span>
                            <button @click="cartQuantity = Math.min(Math.min(activeVariant?.stock || 0, 10), cartQuantity + 1)" :disabled="cartQuantity >= Math.min(activeVariant?.stock || 0, 10)" class="w-10 h-10 flex items-center justify-center text-indigo-600 hover:bg-indigo-50 rounded-r-xl transition disabled:opacity-30"><i class="bi bi-plus-lg"></i></button>
                        </div>
                        <button @click="addToCart()" :disabled="cartLoading || (activeVariant?.stock || 0) < 1" class="flex-1 py-3 px-4 bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white rounded-xl font-bold text-sm shadow-lg shadow-indigo-200 transition-all active:scale-95 flex items-center justify-center gap-2">
                            <template x-if="!cartLoading"><span class="flex items-center gap-2"><i class="bi bi-bag-plus-fill text-lg"></i><span>Add to Cart</span></span></template>
                            <template x-if="cartLoading"><span class="flex items-center gap-2"><div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div><span>Adding...</span></span></template>
                        </button>
                    </div>
                    <p x-show="(activeVariant?.stock || 0) < 1" class="text-xs text-red-500 mt-2 font-medium flex items-center gap-1"><i class="bi bi-exclamation-circle"></i> This item is currently out of stock</p>
                </div>

                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Select Variant</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <template x-for="v in selectedItem?.all_variants || []" :key="v.id">
                            <button type="button" @click="changeVariant(v)" :class="activeVariant?.id === v.id ? 'qv-variant-btn active' : 'qv-variant-btn'">
                                <div class="flex items-center gap-2">
                                    <span class="w-3.5 h-3.5 rounded-full border border-slate-200 shrink-0" :style="'background:' + v.color_code"></span>
                                    <span class="text-[9px] font-black text-slate-400 uppercase truncate w-16" x-text="v.color"></span>
                                </div>
                                <span class="text-sm font-black text-slate-800 uppercase" x-text="v.size"></span>
                                <span class="text-[9px] text-slate-400" x-text="'Stock: ' + v.stock"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="bg-slate-900 text-white p-4 lg:p-6 rounded-[24px] lg:rounded-[28px] flex justify-between items-center mt-auto shadow-2xl">
                    <div>
                        <p class="text-[9px] font-black text-slate-500 uppercase mb-1">SKU</p>
                        <p class="font-mono text-sm font-bold text-indigo-400 uppercase" x-text="activeVariant?.sku"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Reviews Modal -->
<template x-if="showReviewsModal">
    <div class="fixed inset-0 z-[20001] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" x-cloak @click.self="showReviewsModal = false">
        <div class="bg-white w-full max-w-2xl max-h-[80vh] rounded-[24px] shadow-2xl overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-slate-900">Reviews & Ratings</h3>
                    <p class="text-sm text-slate-500 mt-1" x-text="selectedItem?.name"></p>
                </div>
                <button @click="showReviewsModal = false" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-slate-200 transition"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="p-6 overflow-y-auto review-section">
                <div x-show="reviewsLoading" class="flex items-center justify-center py-12">
                    <div class="w-8 h-8 border-2 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
                </div>
                <div x-show="!reviewsLoading && modalReviews.list.length === 0" class="text-center py-12">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4"><i class="bi bi-chat-square-text text-2xl text-slate-400"></i></div>
                    <p class="text-slate-500">No reviews yet for this product.</p>
                </div>
                <div x-show="!reviewsLoading && modalReviews.list.length > 0" class="space-y-4">
                    <template x-for="review in modalReviews.list" :key="review.id">
                        <div class="review-item rounded-2xl">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-sm" x-text="(review.user?.name || 'U').charAt(0).toUpperCase()"></div>
                                    <div>
                                        <p class="font-bold text-slate-900 text-sm" x-text="review.user?.name || 'Anonymous'"></p>
                                        <div class="flex items-center gap-2">
                                            <div class="star-rating">
                                                <template x-for="i in 5" :key="i"><i class="bi" :class="i <= review.rating ? 'bi-star-fill' : 'bi-star'"></i></template>
                                            </div>
                                            <span class="text-sm font-bold text-slate-700" x-text="review.rating + '.0'"></span>
                                        </div>
                                    </div>
                                </div>
                                <span class="text-xs text-slate-400" x-text="formatDate(review.created_at)"></span>
                            </div>
                            <p class="text-slate-600 text-sm leading-relaxed" x-text="review.comment"></p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
function appData() {
    return {
        isModalFavorite: false,
        quickView: false,
        selectedItem: null,
        activeVariant: null,
        descOpen: false,
        showFilters: false,
        showScrollTop: false,
        showReviewsModal: false,
        toasts: [],
        reviewsCache: {},
        modalReviews: { list: [], avg: 0, count: 0, likes: 0 },
        reviewsLoading: false,

        openQuickView(item, variantId) {
            this.selectedItem = item;
            const allVariants = item.all_variants || item.variants || [];
            this.activeVariant = allVariants.find(v => v.id == variantId) || allVariants[0];
            this.checkModalFavoriteOptimized(this.activeVariant.id);
            this.loadReviews(this.activeVariant.id);
            this.descOpen = false;
            this.quickView = true;
        },

        checkModalFavoriteOptimized(variantId) {
            const cached = sessionStorage.getItem(`fav_${variantId}`);
            if (cached !== null) this.isModalFavorite = cached === 'true';
            this.debouncedFavoriteCheck(variantId);
        },

        debouncedFavoriteCheck: debounce(function(variantId) {
            fetch(`/favorites/check/${variantId}`, { method: 'GET', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' } })
            .then(r => r.json()).then(data => { this.isModalFavorite = data.isFavorite; sessionStorage.setItem(`fav_${variantId}`, data.isFavorite); })
            .catch(e => console.error(e));
        }, 300),

        async loadReviews(variantId) {
            if (this.reviewsCache[variantId]) { this.modalReviews = this.reviewsCache[variantId]; return; }
            this.reviewsLoading = true;
            try {
                const response = await fetch(`/reviews/variant/${variantId}`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content } });
                const data = await response.json();
                this.modalReviews = { list: data.reviews || [], avg: data.avg_rating || 0, count: data.total_reviews || 0, likes: data.total_likes || 0 };
                this.reviewsCache[variantId] = this.modalReviews;
            } catch (e) {
                this.modalReviews = { list: [], avg: 0, count: 0, likes: 0 };
            } finally { this.reviewsLoading = false; }
        },

        formatDate(dateString) {
            if (!dateString) return '';
            return new Date(dateString).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        },

        btnScrollModal(dir) {
            const c = this.$refs.modalTrack;
            if (!c) return;
            const maxScroll = c.scrollWidth - c.offsetWidth;
            if (dir === 1 && c.scrollLeft >= maxScroll - 5) c.scrollLeft = 0;
            else if (dir === -1 && c.scrollLeft <= 5) c.scrollLeft = maxScroll;
            else c.scrollBy({ left: c.offsetWidth * dir, behavior: 'smooth' });
        },

        scrollToTop() { window.scrollTo({ top: 0, behavior: 'smooth' }); },

        initScrollWatcher() {
            window.addEventListener('scroll', () => { this.showScrollTop = window.pageYOffset > 500; });
        },

        addToast(type, title, message) {
            const id = Date.now();
            this.toasts.push({ id, type, title, message });
            setTimeout(() => { this.removeToast(id); }, 4000);
        },

        removeToast(id) {
            const index = this.toasts.findIndex(t => t.id === id);
            if (index > -1) this.toasts.splice(index, 1);
        },

        initApp() {
            this.initScrollWatcher();
            this.modalReviews = { list: [], avg: 0, count: 0, likes: 0 };
            this.reviewsLoading = false;
            sessionStorage.clear();
        }
    };
}

// ========== FAVORITES MANAGER ==========
function favoritesManager() {
    return {
        categories: {
            @foreach($urunler->groupBy('category_name') as $categoryName => $groupedItems)
            @php
                $catSlug = Str::slug($categoryName ?: 'uncategorized');
                $catKey  = 'cat_' . $catSlug . '_' . $loop->index;
            @endphp
            '{{ $catKey }}': { count: {{ $groupedItems->count() }}, visible: true, name: '{{ addslashes($categoryName ?: "Collection") }}' },
            @endforeach
        },

        get allCategoriesEmpty() {
            return Object.values(this.categories).every(cat => !cat.visible);
        },

        initFavoritesManager() {
            window.addEventListener('product-removed-from-category', (e) => {
                this.decrementCategory(e.detail.category);
            });
        },

        decrementCategory(categoryKey) {
            if (!this.categories[categoryKey]) return;
            this.categories[categoryKey].count--;
            if (this.categories[categoryKey].count <= 0) {
                // Son ürün kaldırıldı → sayfayı yenile
                setTimeout(() => window.location.reload(), 500);
            }
        }
    };
}

// ========== PRODUCT CARD ==========
function productCard(variantId, initialFavorite, avgRating, reviewCount, likeCount, categoryKey) {
    return {
        variantId: variantId,
        isFavorite: initialFavorite,
        isRemoved: false,
        avgRating: avgRating,
        reviewCount: reviewCount,
        likeCount: likeCount,
        categoryKey: categoryKey,
        favoriteLoading: false,

        toggleFavoriteDebounced() {
            if (this.favoriteLoading) return;
            this.isFavorite = !this.isFavorite;
            this.favoriteLoading = true;
            this.debouncedToggle();
        },

        debouncedToggle: debounce(function() {
            fetch(`/favorites/toggle/${this.variantId}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json', 'Content-Type': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                const isNowFavorite = (data.status === 'added');
                this.isFavorite = isNowFavorite;
                sessionStorage.setItem(`fav_${this.variantId}`, isNowFavorite);

                if (!isNowFavorite) {
                    this.isRemoved = true;
                    setTimeout(() => {
                        window.dispatchEvent(new CustomEvent('product-removed-from-category', { detail: { category: this.categoryKey } }));
                    }, 100);
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', title: 'Removed!', message: 'Product removed from favorites.' } }));
                } else {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', title: 'Added!', message: 'Product added to favorites.' } }));
                }

                window.dispatchEvent(new CustomEvent('favorite-updated', { detail: { variantId: this.variantId, isFavorite: isNowFavorite } }));
            })
            .catch(() => {
                this.isFavorite = !this.isFavorite;
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', title: 'Error!', message: 'Could not update favorites.' } }));
            })
            .finally(() => { this.favoriteLoading = false; });
        }, 200),

        handleFavoriteUpdate(detail) {
            if (detail.variantId !== this.variantId) return;
            if (detail.isFavorite === this.isFavorite) return;
            this.isFavorite = detail.isFavorite;
            if (!detail.isFavorite && !this.isRemoved) {
                this.isRemoved = true;
                setTimeout(() => {
                    window.dispatchEvent(new CustomEvent('product-removed-from-category', { detail: { category: this.categoryKey } }));
                }, 100);
            }
        }
    };
}

// ========== QUICK VIEW DATA ==========
function quickViewData() {
    return {
        cartLoading: false,
        cartQuantity: 1,
        isModalFavorite: false,
        modalReviews: { list: [], avg: 0, count: 0 },
        reviewsLoading: false,
        favoriteDebounceTimer: null,

        initQuickView() {
            if (this.selectedItem) {
                this.modalReviews = { list: [], avg: this.selectedItem.reviews_avg_rating || 0, count: this.selectedItem.reviews_count || 0 };
            }
            this.$watch('activeVariant', (newVariant) => {
                if (newVariant && newVariant.id) {
                    this.checkFavoriteStatus(newVariant.id);
                    this.modalReviews.avg = this.selectedItem?.reviews_avg_rating || 0;
                    this.modalReviews.count = this.selectedItem?.reviews_count || 0;
                    this.cartQuantity = 1;
                }
            });
            if (this.activeVariant && this.activeVariant.id) this.checkFavoriteStatus(this.activeVariant.id);
        },

        checkFavoriteStatus(variantId) {
            const cached = sessionStorage.getItem(`fav_${variantId}`);
            if (cached !== null) this.isModalFavorite = cached === 'true';
            clearTimeout(this.favoriteDebounceTimer);
            this.favoriteDebounceTimer = setTimeout(() => {
                fetch(`/favorites/check/${variantId}`, { headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' } })
                .then(r => r.json()).then(data => { this.isModalFavorite = data.isFavorite; sessionStorage.setItem(`fav_${variantId}`, data.isFavorite); })
                .catch(() => {});
            }, 100);
        },

        async loadReviewsDetail() {
            if (!this.activeVariant || this.modalReviews.list.length > 0) return;
            this.reviewsLoading = true;
            try {
                const response = await fetch(`/reviews/variant/${this.activeVariant.id}`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content } });
                const data = await response.json();
                this.modalReviews.list = data.reviews || [];
            } catch (e) { console.error(e); }
            finally { this.reviewsLoading = false; }
        },

        toggleModalFavoriteDebounced() {
            if (!this.activeVariant) return;
            this.isModalFavorite = !this.isModalFavorite;
            clearTimeout(this.favoriteDebounceTimer);
            this.favoriteDebounceTimer = setTimeout(() => {
                fetch(`/favorites/toggle/${this.activeVariant.id}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json', 'Content-Type': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    const isNowFavorite = (data.status === 'added');
                    this.isModalFavorite = isNowFavorite;
                    sessionStorage.setItem(`fav_${this.activeVariant.id}`, isNowFavorite);

                    if (isNowFavorite) {
                        // Yeni favori eklendi → hızlıca yenile
                        window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', title: 'Added!', message: 'Product added to favorites.' } }));
                        setTimeout(() => window.location.reload(), 800);
                    } else {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', title: 'Removed!', message: 'Product removed from favorites.' } }));
                    }

                    window.dispatchEvent(new CustomEvent('favorite-updated', { detail: { variantId: this.activeVariant.id, isFavorite: isNowFavorite } }));
                })
                .catch(() => { this.isModalFavorite = !this.isModalFavorite; });
            }, 200);
        },

        async addToCart() {
            if (this.cartLoading || !this.activeVariant) return;
            if (this.activeVariant.stock < 1) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', title: 'Out of Stock!', message: 'This product is currently unavailable.' } }));
                return;
            }
            if (this.cartQuantity > this.activeVariant.stock) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', title: 'Error!', message: `Maximum ${this.activeVariant.stock} items allowed.` } }));
                return;
            }
            this.cartLoading = true;
            try {
                const formData = new FormData();
                formData.append('quantity', this.cartQuantity);
                formData.append('_token', document.querySelector('meta[name=csrf-token]').content);
                const response = await fetch(`/mycart/add/${this.activeVariant.id}`, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
                const data = await response.json();
                if (response.ok && data.status !== 'error') {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', title: 'Added to Cart!', message: data.message || `${this.cartQuantity} item(s) added to cart.` } }));
                    if (data.cartCount !== undefined) window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cartCount } }));
                    this.cartQuantity = 1;
                } else { throw new Error(data.message || 'An error occurred.'); }
            } catch (error) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', title: 'Error!', message: error.message || 'Could not add to cart.' } }));
            } finally { this.cartLoading = false; }
        },

        changeVariant(variant) {
            this.activeVariant = variant;
            this.cartQuantity = 1;
            this.checkFavoriteStatus(variant.id);
        }
    };
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => { clearTimeout(timeout); func.apply(this, args); };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function btnScroll(id, dir) {
    const c = document.getElementById(id);
    if (!c) return;
    const scrollAmount = c.offsetWidth;
    const maxScroll = c.scrollWidth - scrollAmount;
    const currentScroll = c.scrollLeft;
    if (dir === 1 && currentScroll >= maxScroll - 10) { c.scrollTo({ left: 0, behavior: 'smooth' }); }
    else if (dir === -1 && currentScroll <= 10) { c.scrollTo({ left: maxScroll, behavior: 'smooth' }); }
    else { c.scrollBy({ left: scrollAmount * dir, behavior: 'smooth' }); }
}
</script>
</body>
</html>
