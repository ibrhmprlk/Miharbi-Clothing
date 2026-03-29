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

        :root {
            --brand: #4f46e5;
            --brand-light: #eef2ff;
            --success: #10b981;
            --danger: #ef4444;
        }

        /* Kaydırma çubuğu zıplamasını engellemek için her zaman göster */
        html { overflow-y: scroll; }

        body { 
            background-color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: #1e293b; 
            margin: 0;
            overflow-x: hidden;
        }

        .admin-nav {
            background: white; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 9999;
        }
        
        .nav-item {
            display: flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 12px;
            font-size: 14px; font-weight: 600; color: #64748b; transition: all 0.2s; text-decoration: none;
        }
        .nav-item:hover { background: var(--brand-light); color: var(--brand); }
        .nav-item.active { background: var(--brand); color: white; }

        #toast-container { position: fixed; top: 24px; right: 24px; z-index: 99999; display: flex; flex-direction: column; gap: 12px; }
        .toast-card { min-width: 320px; background: white; padding: 16px 20px; border-radius: 16px; box-shadow: 0 15px 30px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 12px; transform: translateX(120%); transition: 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55); border-left: 6px solid #ccc; }
        .toast-card.show { transform: translateX(0); }
        .toast-card.success { border-left-color: var(--success); }
        .toast-card.error { border-left-color: var(--danger); }

        .glass-card { background: white; border: 1px solid #e2e8f0; border-radius: 20px; padding: 24px; }
        .input-field { width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; transition: 0.2s; background-color: white; }
        .input-field:focus { outline: none; border-color: var(--brand); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }

        [x-cloak] { display: none !important; }
    </style>
</head>

<body x-data="{ mobileOpen: false }">

<!-- Header -->
<header class="admin-nav shadow-sm" x-data="{ mobileMenu: false }" @click.away="mobileMenu = false">
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
    <nav x-show="mobileMenu" 
         x-cloak 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden absolute left-0 right-0 top-20 bg-white border-b border-slate-200 shadow-2xl p-4 space-y-2 z-50">
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
<div id="toast-container"></div>

<div class="max-w-5xl mx-auto py-10 px-4">
    <div class="mb-10 text-center md:text-left">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Category Management</h1>
        <p class="text-slate-500 text-sm font-medium">Define categories to group your products.</p>
    </div>

    <div class="glass-card shadow-sm mb-8 border-l-4 border-indigo-500">
        <form method="POST" action="{{ route('categories.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf
            <div class="md:col-span-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block ml-1">Category Title</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="input-field" placeholder="
Example: Satin Dresses">
            </div>

            <div class="md:col-span-1">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block ml-1">Situation</label>
                <select name="is_active" class="input-field">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            <div class="md:col-span-1">
               <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-indigo-200 transition-all duration-200 transform active:scale-95 flex items-center justify-center gap-2 border-none cursor-pointer">
                    <i class="bi bi-plus-circle-fill text-lg"></i>
                    <span>Add Category</span>
                </button>
            </div>
        </form>
    </div>

    <div class="glass-card shadow-sm overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Category Name</th>
                        <th class="py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                        <th class="py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Date</th>
                        <th class="py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $category)
                    <tr class="hover:bg-indigo-50/30 transition">
                        <td class="py-4 px-6">
                            <span class="font-bold text-slate-700">{{ $category->name }}</span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            @if($category->is_active)
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-black uppercase tracking-tighter">Active</span>
                            @else
                                <span class="px-3 py-1 bg-slate-200 text-slate-600 rounded-full text-[10px] font-black uppercase tracking-tighter">Inactive</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-center text-xs text-slate-400 font-bold">
                            {{ $category->created_at->format('d/m/Y') }}
                        </td>
                        <td class="py-4 px-6 text-right">
                            <form action="{{ route('category.delete', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete the category?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-slate-300 hover:text-rose-500 transition rounded-lg hover:bg-rose-50 border-none bg-transparent cursor-pointer">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-16 text-center">
                            <div class="text-slate-300 text-3xl mb-2"><i class="bi bi-inbox"></i></div>
                            <p class="text-slate-400 text-xs italic font-medium">No categories defined yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function createToast(msg, type) {
        const container = document.getElementById('toast-container');
        const id = 'toast-' + Math.random().toString(36).substr(2, 9);
        const html = `
            <div id="${id}" class="toast-card ${type}">
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}-fill text-${type === 'success' ? 'emerald' : 'rose'}-500 text-xl"></i>
                <div>
                    <strong class="block text-sm">${type === 'success' ? 'Sistem Başarılı' : 'Hata Oluştu'}</strong>
                    <span class="text-[11px] text-slate-500 font-medium">${msg}</span>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        const el = document.getElementById(id);
        setTimeout(() => el.classList.add('show'), 50);
        setTimeout(() => {
            if(el) {
                el.classList.remove('show');
                setTimeout(() => el.remove(), 500);
            }
        }, 3500);
    }

    @if(session('success')) createToast("{{ session('success') }}", 'success'); @endif
    @if(session('error')) createToast("{{ session('error') }}", 'error'); @endif
</script>

</body>
</html>
