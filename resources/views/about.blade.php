<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About us - Miharbi Clothing</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700" rel="stylesheet" />

    <style>
        :root {
            --brand: #4f46e5;
            --brand-light: #eef2ff;
        }
        /* Kaydırma çubuğu zıplamasını engellemek için her zaman göster */
        html { overflow-y: scroll; }

        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; overflow-x: hidden; }
        
        .admin-nav { background: white; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 9999; }
        
        .nav-item {
            display: flex; align-items: center; gap: 8px; padding: 12px 16px; border-radius: 12px;
            font-size: 14px; font-weight: 600; color: #64748b; transition: all 0.2s; text-decoration: none;
        }
        .nav-item:hover { background: var(--brand-light); color: var(--brand); }
        .nav-item.active { background: var(--brand); color: white; }

        .glass-card { background: white; border: 1px solid #e2e8f0; border-radius: 32px; overflow: hidden; }
        .social-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: #f1f5f9; color: #64748b; transition: 0.3s; text-decoration: none; }
        .social-icon:hover { background: var(--brand); color: white; transform: translateY(-3px); }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen" x-data="{ mobileMenu: false }">

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

<div class="fixed top-24 right-5 z-50 flex flex-col gap-3">
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="bg-emerald-500 text-white px-6 py-3 rounded-2xl shadow-xl flex items-center gap-3">
        <i class="bi bi-check-circle-fill"></i>
        <span class="font-semibold text-sm">{{ session('success') }}</span>
    </div>
    @endif
</div>

<main class="max-w-7xl mx-auto px-4 py-10 pb-24">
    <div class="glass-card shadow-sm flex flex-col lg:flex-row items-stretch">
        <div class="w-full lg:flex-1 min-h-[300px] md:min-h-[400px] bg-slate-100">
            @if($about && $about->image)
                <img src="{{ $about->image }}" class="w-full h-full object-cover">
            @else
                <div class="h-full flex flex-col items-center justify-center text-slate-400 gap-3 py-20">
                    <i class="bi bi-image text-5xl"></i>
                    <span class="font-medium text-sm uppercase tracking-widest">Image Not Available</span>
                </div>
            @endif
        </div>

        <div class="flex-1 p-6 md:p-10 lg:p-16 flex flex-col justify-center">
            <div class="flex items-center justify-between mb-8">
                <span class="px-4 py-1.5 bg-indigo-50 text-indigo-600 rounded-full text-xs font-bold uppercase tracking-widest">About Us Details</span>
                @if($about)
                    <a href="{{ route('editAbout', $about->id) }}" class="w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition shadow-sm border border-slate-100 no-underline">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                @endif
            </div>

            <h2 class="text-2xl md:text-4xl font-black text-slate-900 mb-6 leading-tight">{{ $about->title ?? 'Sayfa Başlığı' }}</h2>

            <div class="space-y-4 text-slate-600 leading-relaxed text-sm md:text-base">
                <p class="font-medium text-slate-800">{{ $about->description }}</p>
                @if($about->second_paragraph) <p>{{ $about->second_paragraph }}</p> @endif
                @if($about->last_paragraph) <p>{{ $about->last_paragraph }}</p> @endif
            </div>

            <div class="mt-10 pt-8 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="flex items-center gap-4">
                    <div class="social-icon bg-indigo-50 text-indigo-600 pointer-events-none"><i class="bi bi-telephone-fill"></i></div>
                    <div class="overflow-hidden">
                        <p class="text-xs md:text-sm font-bold text-slate-700 m-0 truncate">
                            {{ preg_replace('/(\d{3})(\d{3})(\d{2})(\d{2})/', '$1 $2 $3 $4', $about->phone) }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="social-icon bg-indigo-50 text-indigo-600 pointer-events-none"><i class="bi bi-envelope-at-fill"></i></div>
                    <div class="overflow-hidden">
                        <p class="text-xs md:text-sm font-bold text-slate-700 m-0 truncate">{{ $about->email }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-10 flex flex-wrap gap-3">
                @if(!empty($about->instagram_url)) <a href="{{ $about->instagram_url }}" target="_blank" class="social-icon"><i class="bi bi-instagram"></i></a> @endif
                @if(!empty($about->twitter_url)) <a href="{{ $about->twitter_url }}" target="_blank" class="social-icon"><i class="bi bi-twitter-x"></i></a> @endif
                @if(!empty($about->github_url)) <a href="{{ $about->github_url }}" target="_blank" class="social-icon"><i class="bi bi-github"></i></a> @endif
                @if(!empty($about->linkedin_url)) <a href="{{ $about->linkedin_url }}" target="_blank" class="social-icon"><i class="bi bi-linkedin"></i></a> @endif
            </div>
        </div>
    </div>
</main>
</body>
</html>