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

        html { overflow-y: scroll; }
        body { background-color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; color: #1e293b; margin: 0; overflow-x: hidden; }

        .admin-nav { background: white; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 9999; }
        
        .nav-item {
            display: flex; align-items: center; gap: 8px; padding: 12px 16px; border-radius: 12px;
            font-size: 14px; font-weight: 600; color: #64748b; transition: all 0.2s; text-decoration: none;
        }
        .nav-item:hover { background: var(--brand-light); color: var(--brand); }
        .nav-item.active { background: var(--brand); color: white; }

        #toast-container { position: fixed; top: 24px; right: 24px; z-index: 99999; display: flex; flex-direction: column; gap: 12px; }
        .toast-card { min-width: 320px; background: white; padding: 16px 20px; border-radius: 16px; box-shadow: 0 15px 30px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 12px; transform: translateX(120%); transition: 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55); border-left: 6px solid #ccc; }
        .toast-card.show { transform: translateX(0); }
        .toast-card.success { border-left-color: var(--success); }
        .toast-card.error { border-left-color: var(--danger); }

        .glass-card { background: white; border: 1px solid #e2e8f0; border-radius: 24px; padding: 24px; }
        .input-field { width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 12px; transition: all 0.2s; font-size: 14px; background: white; }
        .input-field:focus { outline: none; border-color: var(--brand); box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
        
        .variant-card { background: white; border: 1px solid #f1f5f9; border-radius: 24px; padding: 24px; margin-bottom: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        
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

<main class="max-w-5xl mx-auto px-4 py-10 pb-20">
    <div class="mb-10 text-center md:text-left">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Create New Product</h1>
        <p class="text-slate-500 font-medium mt-2">Add a new item with multiple variants and poses.</p>
    </div>

    <form method="POST" action="{{ route('uruns.store') }}" id="productForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="glass-card shadow-sm">
                    <h2 class="text-lg font-bold mb-6 flex items-center gap-2"><i class="bi bi-pencil-square text-indigo-500"></i> Basic Information</h2>
                    <div class="space-y-5">
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-2">Product Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="input-field shadow-sm" placeholder="e.g. Silk Evening Dress">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-2">Description</label>
                            <textarea name="description" rows="4" class="input-field shadow-sm resize-none" placeholder="Fabric, cut, and style...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="glass-card shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold flex items-center gap-2"><i class="bi bi-layers text-indigo-500"></i> Variants & Details</h2>
                        <button type="button" id="add-variant-btn" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl text-sm font-bold hover:bg-indigo-600 hover:text-white transition-all"><i class="bi bi-plus-circle"></i> Add Variant</button>
                    </div>
                    <div id="variants-container" class="space-y-6"></div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="glass-card shadow-sm lg:sticky lg:top-28 space-y-6">
                    <h2 class="text-lg font-bold mb-4">Global Settings</h2>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-2">Category</label>
                        <select name="category_id" required class="input-field">
                            <option value="">Choose category</option>
                            @foreach($categories as $category) <option value="{{ $category->id }}">{{ $category->name }}</option> @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-bold text-lg shadow-lg hover:bg-indigo-700 hover:-translate-y-1 transition-all">Save All Products</button>
                    <p class="text-[10px] text-slate-400 text-center italic">Brand, collection and status settings are now managed per variant.</p>
                </div>
            </div>
        </div>
    </form>
</main>

<script>
    function createToast(msg, type) {
        const container = document.getElementById('toast-container');
        if(!container || !msg) return;

        const id = 'toast-' + Math.random().toString(36).substr(2, 9);
        const html = `
            <div id="${id}" class="toast-card ${type}">
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}-fill text-${type === 'success' ? 'emerald' : 'rose'}-500 text-xl"></i>
                <div>
                    <strong class="block text-sm">${type === 'success' ? 'Başarılı' : 'Hata'}</strong>
                    <span class="text-xs text-slate-500 font-medium">${msg}</span>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        const el = document.getElementById(id);
        
        setTimeout(() => { if(el) el.classList.add('show'); }, 100);
        setTimeout(() => { if(el) { el.classList.remove('show'); setTimeout(() => el.remove(), 500); } }, 4000);
    }

    window.onload = function() {
        @if(session('success')) createToast({!! json_encode(session('success')) !!}, 'success'); @endif
        @if(session('error')) createToast({!! json_encode(session('error')) !!}, 'error'); @endif
    };

    document.addEventListener('DOMContentLoaded', function(){
        const container = document.getElementById('variants-container');
        const addBtn = document.getElementById('add-variant-btn');

        function addVariant() {
            const index = container.children.length;
            const div = document.createElement('div');
            div.className = 'variant-card border-l-8 border-l-indigo-500 shadow-md';
            div.innerHTML = `
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <div class="col-span-2 md:col-span-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Color</label>
                        <div class="flex gap-2">
                            <input type="color" name="variants[${index}][color_code]" value="#6366f1" class="w-10 h-10 p-1 rounded-lg border cursor-pointer">
                            <input type="text" name="variants[${index}][color]" placeholder="Color Name" class="input-field py-2" required>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">SKU (Code)</label>
                        <input type="text" name="variants[${index}][sku]" placeholder="MHRB-101" class="input-field uppercase font-mono" required>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Brand</label>
                        <input type="text" name="variants[${index}][brand]" placeholder="e.g. Tudors" class="input-field">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Collection</label>
                        <input type="text" name="variants[${index}][collection]" placeholder="e.g. Winter 26" class="input-field">
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Size</label>
                        <input type="text" name="variants[${index}][size]" placeholder="S, M..." class="input-field" required>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Price</label>
                        <input type="number" step="0.01" name="variants[${index}][price]" class="input-field price-input font-bold" required>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-rose-500 uppercase tracking-widest block mb-1">Discount</label>
                        <input type="number" step="0.01" name="variants[${index}][discount_price]" class="input-field discount-input">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-indigo-500 uppercase tracking-widest block mb-1">Status</label>
                        <select name="variants[${index}][is_active]" class="input-field">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] font-black text-indigo-500 uppercase tracking-widest block mb-1">
                            <i class="bi bi-images"></i> Variant Image URLs (One per line)
                        </label>
                        <textarea name="variants[${index}][variant_images]" rows="3" 
                            placeholder="https://image-pose-1.jpg&#10;https://image-pose-2.jpg" 
                            class="input-field font-mono text-xs leading-relaxed border-indigo-100 bg-indigo-50/20"></textarea>
                    </div>
                    <div class="flex items-center justify-between bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <div class="flex items-center gap-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase">Stock:</label>
                            <input type="number" name="variants[${index}][stock]" value="0" class="input-field w-24 py-1.5 font-bold" required>
                        </div>
                        <button type="button" class="text-rose-500 hover:bg-rose-50 px-4 py-2 rounded-xl font-bold text-xs transition-all flex items-center gap-2 border border-rose-100" onclick="this.closest('.variant-card').remove()">
                            <i class="bi bi-trash3-fill"></i> Remove Variant
                        </button>
                    </div>
                </div>`;
            container.appendChild(div);

            const pInp = div.querySelector('.price-input');
            const dInp = div.querySelector('.discount-input');
            dInp.addEventListener('input', function() {
                if (this.value && parseFloat(this.value) >= parseFloat(pInp.value)) {
                    this.classList.add('border-rose-500', 'bg-rose-50');
                    createToast('Discounted price must be lower than normal price!', 'error');
                } else { 
                    this.classList.remove('border-rose-500', 'bg-rose-50'); 
                }
            });
        }
        addBtn.addEventListener('click', addVariant);
        if(container.children.length === 0) addVariant();
    });
</script>
</body>
</html>
