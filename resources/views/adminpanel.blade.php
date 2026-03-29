<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Miharbi Admin</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

        :root {
            --brand: #4f46e5;
            --brand-light: #eef2ff;
            --bg-main: #f8fafc;
        }

        html { overflow-y: scroll; }

        body {
            background-color: var(--bg-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
            margin: 0;
            overflow-x: hidden;
        }

        .admin-nav {
            background: white; 
            border-bottom: 1px solid #e2e8f0; 
            position: sticky; 
            top: 0; 
            z-index: 9999;
        }

        .nav-item {
            display: flex; 
            align-items: center; 
            gap: 8px; 
            padding: 10px 16px; 
            border-radius: 12px;
            font-size: 14px; 
            font-weight: 600; 
            color: #64748b; 
            transition: all 0.2s; 
            text-decoration: none;
        }
        .nav-item:hover { background: var(--brand-light); color: var(--brand); }
        .nav-item.active { background: var(--brand); color: white; }

        .main-content {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 20px;
        }
        @media (min-width: 768px) { .main-content { padding: 40px 20px; } }

        .dashboard-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 24px;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            gap: 16px;
            text-decoration: none;
            color: inherit;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
            border-color: var(--brand);
        }

        .icon-box {
            width: 54px; 
            height: 54px; 
            border-radius: 16px;
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 24px;
        }

        .bg-indigo-soft { background: #eef2ff; color: #4f46e5; }
        .bg-emerald-soft { background: #ecfdf5; color: #10b981; }
        .bg-amber-soft { background: #fffbeb; color: #f59e0b; }
        .bg-rose-soft { background: #fff1f2; color: #f43f5e; }
        .bg-purple-soft { background: #f3e8ff; color: #7c3aed; }
        .bg-orange-soft { background: #fff7ed; color: #ea580c; }
        .bg-blue-soft { background: #eff6ff; color: #2563eb; }
        .bg-pink-soft { background: #fdf2f8; color: #db2777; }

        .stat-card {
            background: #fdfdfd;
            border: 1px solid #f1f5f9;
            padding: 20px;
            border-radius: 20px;
            transition: all 0.3s;
        }
        .stat-card:hover { border-color: var(--brand); background: white; }

        [x-cloak] { display: none !important; }
        
        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            background: #ef4444;
            color: white;
            font-size: 11px;
            font-weight: 700;
            border-radius: 10px;
            margin-left: 6px;
        }
    </style>
</head>

<body x-data="{ mobileMenu: false }">

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
            
            <!-- NEW: Orders Link -->
            <a href="{{ route('admin.orders.index') }}" class="nav-item relative">
                <i class="bi bi-cart-check"></i> Orders
                @php $pendingOrders = \App\Models\Order::where('status', 'pending')->count(); @endphp
                @if($pendingOrders > 0)
                    <span class="badge">{{ $pendingOrders }}</span>
                @endif
            </a>
            
            <!-- NEW: Reviews Link -->
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
        
        <!-- NEW: Orders Link Mobile -->
        <a href="{{ route('admin.orders.index') }}" class="nav-item relative justify-between">
            <span class="flex items-center gap-2"><i class="bi bi-cart-check"></i> Orders</span>
            @if($pendingOrders > 0)
                <span class="badge">{{ $pendingOrders }}</span>
            @endif
        </a>
        
        <!-- NEW: Reviews Link Mobile -->
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

<main class="main-content">
    <header class="mb-8 md:mb-10 text-center md:text-left">
        <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">Control Center</h1>
        <p class="text-slate-500 mt-1 text-xs md:text-sm font-medium">Manage your store, orders, and customer reviews.</p>
    </header>

    <!-- Quick Actions Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-10">
        <!-- Product Management -->
        <a href="{{ route('create') }}" class="dashboard-card shadow-sm">
            <div class="icon-box bg-indigo-soft"><i class="bi bi-plus-circle-fill"></i></div>
            <div>
                <h3 class="font-bold text-slate-900">Add Product</h3>
                <p class="text-xs text-slate-500">Create new product</p>
            </div>
        </a>
        
        <a href="{{ route('categories') }}" class="dashboard-card shadow-sm">
            <div class="icon-box bg-emerald-soft"><i class="bi bi-collection-fill"></i></div>
            <div>
                <h3 class="font-bold text-slate-900">Categories</h3>
                <p class="text-xs text-slate-500">Manage groups</p>
            </div>
        </a>
        
        <a href="{{ route('urunler') }}" class="dashboard-card shadow-sm">
            <div class="icon-box bg-amber-soft"><i class="bi bi-list-stars"></i></div>
            <div>
                <h3 class="font-bold text-slate-900">Product List</h3>
                <p class="text-xs text-slate-500">Edit products</p>
            </div>
        </a>
        
        <a href="{{ route('about') }}" class="dashboard-card shadow-sm">
            <div class="icon-box bg-rose-soft"><i class="bi bi-pencil-square"></i></div>
            <div>
                <h3 class="font-bold text-slate-900">About Us</h3>
                <p class="text-xs text-slate-500">Update content</p>
            </div>
        </a>
    </div>

    <!-- NEW: Orders & Reviews Section -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6 mb-10">
        <!-- Orders Card -->
        <a href="{{ route('admin.orders.index') }}" class="dashboard-card shadow-sm border-l-4 border-l-indigo-500">
            <div class="flex items-start justify-between">
                <div class="icon-box bg-blue-soft"><i class="bi bi-cart-check"></i></div>
                @if($pendingOrders > 0)
                    <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-bold">{{ $pendingOrders }} Pending</span>
                @endif
            </div>
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Orders</h3>
                <p class="text-xs text-slate-500">Manage customer orders and shipments</p>
            </div>
            <div class="flex items-center gap-4 pt-2 border-t border-slate-100">
                <div class="text-center">
                    <div class="text-xl font-bold text-slate-900">{{ \App\Models\Order::count() }}</div>
                    <div class="text-[10px] text-slate-400 uppercase">Total</div>
                </div>
                <div class="text-center">
                    <div class="text-xl font-bold text-amber-600">{{ \App\Models\Order::where('status', 'pending')->count() }}</div>
                    <div class="text-[10px] text-slate-400 uppercase">Pending</div>
                </div>
                <div class="text-center">
                    <div class="text-xl font-bold text-green-600">{{ \App\Models\Order::where('status', 'delivered')->count() }}</div>
                    <div class="text-[10px] text-slate-400 uppercase">Delivered</div>
                </div>
            </div>
        </a>

        <!-- Reviews Card -->
        <a href="{{ route('admin.reviews.index') }}" class="dashboard-card shadow-sm border-l-4 border-l-amber-500">
            <div class="flex items-start justify-between">
                <div class="icon-box bg-orange-soft"><i class="bi bi-star-fill"></i></div>
                @if($pendingReviews > 0)
                    <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold">{{ $pendingReviews }} Pending</span>
                @endif
            </div>
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Reviews</h3>
                <p class="text-xs text-slate-500">Moderate customer reviews</p>
            </div>
            <div class="flex items-center gap-4 pt-2 border-t border-slate-100">
                <div class="text-center">
                    <div class="text-xl font-bold text-slate-900">{{ \App\Models\Review::count() }}</div>
                    <div class="text-[10px] text-slate-400 uppercase">Total</div>
                </div>
                <div class="text-center">
                    <div class="text-xl font-bold text-amber-600">{{ $pendingReviews }}</div>
                    <div class="text-[10px] text-slate-400 uppercase">Pending</div>
                </div>
                <div class="text-center">
                    <div class="text-xl font-bold text-yellow-500">
                        {{ number_format(\App\Models\Review::where('is_approved', true)->avg('rating') ?? 0, 1) }}
                    </div>
                    <div class="text-[10px] text-slate-400 uppercase">Avg Rating</div>
                </div>
            </div>
        </a>
    </div>

    <!-- Store Stats -->
    <div class="bg-white border border-slate-200 rounded-[24px] md:rounded-[32px] p-5 md:p-10 shadow-sm overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 border-b border-slate-50 pb-6 gap-4">
            <h2 class="text-lg md:text-xl font-bold text-slate-900 flex items-center gap-3">
                <div class="w-1.5 h-6 bg-indigo-500 rounded-full"></div>
                Store Summary
            </h2>
            <span class="w-fit text-[10px] font-black bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full uppercase tracking-widest">Live Data</span>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            <div class="stat-card">
                <span class="text-slate-400 text-[10px] font-bold uppercase tracking-widest block mb-1">Products</span>
                <div class="flex items-end gap-2">
                    <span class="text-3xl md:text-4xl font-black text-slate-900">{{ $totalProducts ?? 0 }}</span>
                    <span class="text-slate-400 text-xs mb-1.5">Models</span>
                </div>
            </div>

            <div class="stat-card">
                <span class="text-slate-400 text-[10px] font-bold uppercase tracking-widest block mb-1">Variants</span>
                <div class="flex items-end gap-2">
                    <span class="text-3xl md:text-4xl font-black text-slate-900">{{ $totalVariants ?? 0 }}</span>
                    <span class="text-slate-400 text-xs mb-1.5">SKUs</span>
                </div>
            </div>
            
            <div class="stat-card">
                <span class="text-slate-400 text-[10px] font-bold uppercase tracking-widest block mb-1">Total Stock</span>
                <div class="flex items-end gap-2">
                    <span class="text-3xl md:text-4xl font-black text-slate-900">{{ $totalStock ?? 0 }}</span>
                    <span class="text-slate-400 text-xs mb-1.5">Units</span>
                </div>
            </div>
            
            <div class="stat-card">
                <span class="text-slate-400 text-[10px] font-bold uppercase tracking-widest block mb-1">Categories</span>
                <div class="flex items-end gap-2">
                    <span class="text-3xl md:text-4xl font-black text-slate-900">{{ $totalCategories ?? 0 }}</span>
                    <span class="text-slate-400 text-xs mb-1.5">Groups</span>
                </div>
            </div>
        </div>
    </div>
</main>

</body>
</html>