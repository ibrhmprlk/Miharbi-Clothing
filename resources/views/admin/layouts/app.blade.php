<!DOCTYPE html>
<html lang="en" x-data="{ mobileMenu: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Miharbi Clothing</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

        :root {
            --brand: #4f46e5;
            --brand-light: #eef2ff;
            --bg-main: #f8fafc;
            --ink: #0f172a;
            --paper: #f8fafc;
            --accent: #4f46e5;
            --accent2: #6366f1;
            --muted: #64748b;
            --border: rgba(226,232,240,0.8);
            --danger: #dc2626;
            --success: #059669;
            --warning: #d97706;
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
        
        .stat-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.08);
        }
        
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent2) 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(79,70,229,0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79,70,229,0.4);
        }
        
        .btn-danger {
            background: var(--danger);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-danger:hover {
            background: #b91c1c;
        }
        
        .btn-success {
            background: var(--success);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-success:hover {
            background: #047857;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-pending { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .status-approved { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .status-shipped { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .status-delivered { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .status-cancelled { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .status-processing { background: #f3e8ff; color: #7c3aed; border: 1px solid #ddd6fe; }
        
        .table-row {
            transition: all 0.2s;
        }
        
        .table-row:hover {
            background: #f8fafc;
        }
        
        .search-input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border: 2px solid var(--border);
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }
        
        .filter-select {
            padding: 10px 16px;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .filter-select:focus {
            outline: none;
            border-color: var(--accent);
        }
        
        .timeline-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--border);
            border: 3px solid white;
            box-shadow: 0 0 0 2px var(--border);
        }
        
        .timeline-dot.active {
            background: var(--accent);
            box-shadow: 0 0 0 4px #e0e7ff;
        }
        
        .timeline-dot.completed {
            background: var(--success);
            box-shadow: 0 0 0 4px #d1fae5;
        }
        
        .review-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            transition: all 0.2s;
        }
        
        .review-card:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        }
        
        .star-rating {
            color: #fbbf24;
            font-size: 16px;
        }
        
        .star-rating.empty {
            color: #e2e8f0;
        }
        
        .checkbox-wrapper {
            position: relative;
            display: inline-block;
        }
        
        .checkbox-wrapper input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--accent);
        }

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
        
        @media (max-width: 768px) {
            .stat-grid { grid-template-columns: 1fr !important; }
            .action-bar { flex-direction: column; gap: 12px; }
        }

        [x-cloak] { display: none !important; }
    </style>
    @yield('styles')
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
            
                  <form method="POST" action="{{ route('logout') }}" class="inline">
    @csrf
    <button type="submit" class="nav-item group transition-all duration-300 border border-transparent hover:text-rose-600 hover:bg-rose-50 ml-4">
        <i class="bi bi-box-arrow-right text-lg group-hover:translate-x-1 transition-transform"></i>
        <span class="font-bold">Log Out</span>
    </button>
</form>
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

    <!-- Main Content -->
    <main class="main-content">
        @if(session('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)"
                 class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="bi bi-check-circle-fill text-green-600"></i>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-green-600 hover:text-green-800">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    @yield('scripts')
</body>
</html>
