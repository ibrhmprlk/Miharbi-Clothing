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

        /* NAVİGASYON */
        .admin-nav { background: white; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 1000; }
        .nav-item { display: flex; align-items: center; gap: 8px; padding: 12px 16px; border-radius: 12px; font-size: 14px; font-weight: 600; color: #64748b; transition: all 0.2s; text-decoration: none; }
        .nav-item:hover { background: var(--brand-light); color: var(--brand); }
        .nav-item.active { background: var(--brand); color: white; }

        /* BİLDİRİM SİSTEMİ */
        #toast-container { position: fixed; top: 24px; right: 24px; z-index: 99999; display: flex; flex-direction: column; gap: 12px; }
        .toast-card { min-width: 320px; background: white; padding: 16px 20px; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 12px; transform: translateX(120%); transition: 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55); border-left: 6px solid #ccc; }
        .toast-card.show { transform: translateX(0); }
        .toast-card.success { border-left-color: var(--success); }
        .toast-card.error { border-left-color: var(--danger); }

        .glass-card { background: white; border: 1px solid #e2e8f0; border-radius: 24px; padding: 24px; }
        .input-field { width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 14px; transition: all 0.2s; background-color: #fcfdfe; }
        .input-field:focus { outline: none; border-color: var(--brand); box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }

        /* CAROUSEL */
        .carousel-item { min-width: 100%; aspect-ratio: 1/1.2; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #f1f5f9; border-radius: 16px; }
        .carousel-item img { width: 100%; height: 100%; object-fit: cover; }
        
        #main-track-container::-webkit-scrollbar { display: none; }
        #main-track-container { -ms-overflow-style: none; scrollbar-width: none; }

        /* VARYANT BUTONLARI GÜNCELLEME */
        .variant-btn { 
            display: flex; align-items: center; gap: 8px; padding: 8px 12px; 
            border-radius: 12px; border: 2px solid #e2e8f0; background: white;
            transition: all 0.2s; cursor: pointer; height: auto; min-width: 90px;
        }
        .variant-btn.active { 
            border-color: var(--brand); background: var(--brand-light); 
            transform: translateY(-2px); box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15); 
        }
        .variant-color-dot { width: 18px; height: 18px; border-radius: 6px; border: 1px solid rgba(0,0,0,0.1); shrink: 0; }

        .btn-update { background: var(--brand); color: white; padding: 14px 28px; border-radius: 14px; font-weight: 700; transition: 0.3s; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; }
        .btn-update:hover { transform: translateY(-2px); box-shadow: 0 10px 15px rgba(79, 70, 229, 0.2); }
        
        [x-cloak] { display: none !important; }

        .carousel-nav-btn {
            position: absolute; top: 50%; transform: translateY(-50%); z-index: 50; 
            background: rgba(255,255,255,0.9); width: 40px; height: 40px; border-radius: 12px; 
            display: flex; align-items: center; justify-content: center; 
            transition: all 0.3s; border: 1px solid #e2e8f0; cursor: pointer; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); color: #1e293b;
        }
        
        @media (min-width: 769px) {
            .carousel-nav-btn { opacity: 0; }
            .group\/carousel:hover .carousel-nav-btn { opacity: 1; }
        }
        @media (max-width: 768px) {
            .carousel-nav-btn { width: 34px; height: 34px; }
        }
    </style>
</head>

<body x-data="{ mobileMenu: false }">

<!-- Header -->
    <header class="admin-nav shadow-sm">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-20">
            <a href="{{ route('adminpanel') }}" class="flex items-center gap-2 hover:opacity-80 transition no-underline">
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
                <a href="{{ route('create') }}" class="nav-item {{ request()->routeIs('create') ? 'active' : '' }}">
                    <i class="bi bi-plus-lg"></i> Add Product
                </a>
                <a href="{{ route('categories') }}" class="nav-item {{ request()->routeIs('categories') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i> Categories
                </a>
                <a href="{{ route('urunler') }}" class="nav-item {{ request()->routeIs('urunler') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i> Products
                </a>
                
                <!-- Orders Link -->
                <a href="{{ route('admin.orders.index') }}" class="nav-item relative {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="bi bi-cart-check"></i> Orders
                    @php $pendingOrders = \App\Models\MyOrder::where('status', 'pending')->count(); @endphp
                    @if($pendingOrders > 0)
                        <span class="badge">{{ $pendingOrders }}</span>
                    @endif
                </a>
                
                <!-- Reviews Link -->
                <a href="{{ route('admin.reviews.index') }}" class="nav-item relative {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <i class="bi bi-star"></i> Reviews
                    @php $pendingReviews = \App\Models\Review::where('is_approved', false)->count(); @endphp
                    @if($pendingReviews > 0)
                        <span class="badge">{{ $pendingReviews }}</span>
                    @endif
                </a>
                
                <a href="{{ route('about') }}" class="nav-item {{ request()->routeIs('about') ? 'active' : '' }}">
                    <i class="bi bi-info-circle"></i> About
                </a>
                
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="nav-item group transition-all duration-300 border border-transparent hover:text-rose-600 hover:bg-rose-50 ml-4">
                        <i class="bi bi-box-arrow-right text-lg group-hover:translate-x-1 transition-transform"></i>
                        <span class="font-bold">Log Out</span>
                    </button>
                </form>
            </nav>
        </div>

        <!-- Mobile Navigation -->
        <nav x-show="mobileMenu" x-cloak x-transition class="md:hidden absolute left-0 right-0 top-20 bg-white border-b border-slate-200 shadow-2xl p-4 space-y-2 z-50">
            <a href="{{ route('create') }}" class="nav-item {{ request()->routeIs('create') ? 'active' : '' }}">
                <i class="bi bi-plus-lg"></i> Add Product
            </a>
            <a href="{{ route('categories') }}" class="nav-item {{ request()->routeIs('categories') ? 'active' : '' }}">
                <i class="bi bi-tags"></i> Categories
            </a>
            <a href="{{ route('urunler') }}" class="nav-item {{ request()->routeIs('urunler') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Products
            </a>
            
            <!-- Orders Link Mobile -->
            <a href="{{ route('admin.orders.index') }}" class="nav-item relative justify-between {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <span class="flex items-center gap-2"><i class="bi bi-cart-check"></i> Orders</span>
                @if($pendingOrders > 0)
                    <span class="badge">{{ $pendingOrders }}</span>
                @endif
            </a>
            
            <!-- Reviews Link Mobile -->
            <a href="{{ route('admin.reviews.index') }}" class="nav-item relative justify-between {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                <span class="flex items-center gap-2"><i class="bi bi-star"></i> Reviews</span>
                @if($pendingReviews > 0)
                    <span class="badge">{{ $pendingReviews }}</span>
                @endif
            </a>
            
            <a href="{{ route('about') }}" class="nav-item {{ request()->routeIs('about') ? 'active' : '' }}">
                <i class="bi bi-info-circle"></i> About
            </a>
            
            <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 pt-2 mt-2">
                @csrf
                <button type="submit" class="nav-item text-rose-600 font-bold w-full text-left">
                    <i class="bi bi-box-arrow-right"></i> Log Out
                </button>
            </form>
        </nav>
    </header>

<div id="toast-container"></div>

<div class="max-w-7xl mx-auto px-4 pb-20 mt-8">
    <form action="{{ route('variant.update') }}" method="POST" id="updateForm">
        @csrf
        @method('PUT')

        <input type="hidden" name="urun_id" value="{{ $urun->id }}">
        <input type="hidden" name="variant_id" id="selected-variant-id" value="{{ $varsayilanVariant->id }}">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10 bg-white p-6 rounded-[28px] border border-slate-200 shadow-sm">
            <div class="flex items-center gap-4">
                <a href="{{ route('urunler') }}" class="w-12 h-12 flex items-center justify-center bg-slate-50 border border-slate-200 rounded-2xl hover:bg-indigo-50 hover:text-indigo-600 transition shadow-sm"><i class="bi bi-arrow-left text-xl"></i></a>
                <div class="flex flex-col">
                    <span id="header-product-name" class="text-lg font-black text-slate-900 leading-tight uppercase">{{ $urun->name }}</span>
                    <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest bg-indigo-50 px-2 py-0.5 rounded-lg w-fit mt-1">{{ $urun->category->name ?? 'Product Detail' }}</span>
                </div>
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <button type="button" onclick="confirmDelete()" class="flex-1 md:flex-none px-5 py-3 text-sm font-bold text-rose-500 hover:bg-rose-50 rounded-2xl border-none bg-transparent cursor-pointer"><i class="bi bi-trash3 mr-1"></i> Delete Variant</button>
                <button type="submit" class="btn-update shadow-indigo-100 shadow-xl flex-1 md:flex-none"><i class="bi bi-cloud-check-fill mr-2 text-lg"></i> Save Changes</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-5 space-y-6">
                <div class="glass-card shadow-sm p-4 relative group/carousel text-center">
                    <div id="main-track-container" class="overflow-hidden rounded-2xl">
                        <div id="main-track" class="flex transition-transform duration-500 ease-out">
                            @forelse($varsayilanVariant->images as $img)
                                <div class="carousel-item"><img src="{{ $img->image_url }}"></div>
                            @empty
                                <div class="carousel-item"><img src="https://placehold.co/400x500?text=Gorsel+Yok"></div>
                            @endforelse
                        </div>
                    </div>
                    @if($varsayilanVariant->images->count() > 1)
                        <button type="button" onclick="btnScrollDetail(-1)" class="carousel-nav-btn nav-arrow absolute left-6 top-1/2 -translate-y-1/2"><i class="bi bi-chevron-left text-lg"></i></button>
                        <button type="button" onclick="btnScrollDetail(1)" class="carousel-nav-btn nav-arrow absolute right-6 top-1/2 -translate-y-1/2"><i class="bi bi-chevron-right text-lg"></i></button>
                    @endif
                </div>

                <div class="glass-card shadow-sm border-l-4 border-indigo-500">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Variant Selection (Color & Size)</h3>
                    <div class="flex flex-wrap gap-3">
                        @foreach($urun->variants as $v)
                            <button type="button" onclick="updateAdminPanel({{ $v->id }}, this)" class="variant-btn transition-all {{ $v->id == $varsayilanVariant->id ? 'active' : '' }}">
                                <span class="variant-color-dot" style="background-color: {{ $v->color_code }};"></span>
                                <span class="text-xs font-bold text-slate-700 uppercase">{{ $v->size }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7 space-y-6">
                <div class="glass-card shadow-sm border-l-4 border-slate-900">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ml-1">Product Title</label>
                    <input type="text" name="name" id="name-input" value="{{ $urun->name }}" required class="w-full text-xl font-black text-slate-900 bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 focus:outline-none focus:border-indigo-500 transition shadow-inner">
                </div>

                <div class="glass-card shadow-sm grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ml-1">Category</label>
                        <select name="category_id" class="input-field border-slate-100 font-bold">
                            @foreach($categories as $category) <option value="{{ $category->id }}" {{ $urun->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ml-1">Sale Status</label>
                       <select name="is_active" class="input-field border-slate-100 font-bold">
    <option value="1" {{ $varsayilanVariant->is_active == 1 ? 'selected' : '' }}>Active</option>
    <option value="0" {{ $varsayilanVariant->is_active == 0 ? 'selected' : '' }}>Inactive</option>
</select>
                    </div>
                    <div>
    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ml-1">Brand</label>
    <input type="text" name="brand" value="{{ $varsayilanVariant->brand }}" placeholder="e.g. Miharbi" 
           oninput="this.value = this.value.replace(/[^a-zA-ZçÇğĞıİöÖşŞüÜ\s]/g, '')"
           class="input-field border-slate-100 font-bold">
</div>

<div>
    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ml-1">Collection</label>
    <input type="text" name="collection" value="{{ $varsayilanVariant->collection }}" placeholder="e.g. 2026 Winter" 
           oninput="this.value = this.value.replace(/[^a-zA-ZçÇğĞıİöÖşŞüÜ\s]/g, '')"
           class="input-field border-slate-100 font-bold">
</div>

                    <div class="col-span-1 md:col-span-2">
                        <label class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-2 block ml-1">Variant Image URLs (One per line)</label>
                        <textarea name="variant_images" id="admin-image-urls" rows="4" placeholder="Paste each URL on a new line..." class="input-field font-mono text-xs border-slate-100 leading-relaxed bg-indigo-50/20">{{ $varsayilanVariant->images->pluck('image_url')->implode("\n") }}</textarea>
                    </div>
                </div>

                <div class="glass-card shadow-sm">
                    <h3 class="text-sm font-bold mb-6 flex items-center gap-2 text-slate-800"><i class="bi bi-tag-fill text-indigo-500"></i> Price & Stock Management</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Price</label>
                            <div class="flex items-center text-xl font-black text-slate-900 leading-none">
                                <span class="mr-1 text-slate-400">₺</span>
                                <input type="number" step="0.01" name="price" id="admin-price" value="{{ $varsayilanVariant->price }}" class="w-full bg-transparent focus:outline-none">
                            </div>
                        </div>
                        <div class="p-5 bg-rose-50 rounded-2xl border border-rose-100" id="discount-container">
                            <label class="text-[9px] font-black text-rose-500 uppercase tracking-widest block mb-1">Discount</label>
                            <div class="flex items-center text-xl font-black text-rose-900 leading-none">
                                <span class="mr-1 text-rose-300">₺</span>
                                <input type="number" step="0.01" name="discount_price" id="admin-discount-price" value="{{ $varsayilanVariant->discount_price }}" class="w-full bg-transparent focus:outline-none" placeholder="0.00">
                            </div>
                        </div>
                        <div class="p-5 bg-emerald-50 rounded-2xl border border-emerald-100">
                            <label class="text-[9px] font-black text-emerald-500 uppercase tracking-widest block mb-1">Stock</label>
                            <input type="number" name="stock" id="admin-stock" value="{{ $varsayilanVariant->stock }}" class="w-full bg-transparent text-xl font-black text-emerald-900 mt-1 focus:outline-none">
                        </div>
                    </div>
                </div>

                <div class="glass-card shadow-sm space-y-6">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ml-1 text-indigo-600">SKU (Product Code)</label>
                        <input type="text" name="sku" id="admin-sku" value="{{ $varsayilanVariant->sku }}" class="input-field font-mono font-bold uppercase border-indigo-100 bg-indigo-50/30" placeholder="MHRB-101">
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ml-1">Size</label>
                            <input type="text" name="size" id="admin-size" value="{{ $varsayilanVariant->size }}" class="input-field font-bold border-slate-100">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ml-1">Color Name</label>
                            <input type="text" name="color" id="admin-color-name" value="{{ $varsayilanVariant->color }}" class="input-field font-bold border-slate-100">
                        </div>
                        <div class="col-span-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ml-1">Hex Code</label>
                            <div class="flex gap-4">
                                <input type="color" id="color-picker" value="{{ $varsayilanVariant->color_code }}" class="w-14 h-14 rounded-2xl border-2 border-white shadow-sm cursor-pointer" oninput="document.getElementById('admin-color-code').value = this.value">
                                <input type="text" name="color_code" id="admin-color-code" value="{{ $varsayilanVariant->color_code }}" class="flex-1 input-field font-mono uppercase border-slate-100">
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ml-1">Description</label>
                        <textarea name="description" rows="5" class="input-field resize-none leading-relaxed text-slate-600 border-slate-100">{{ $urun->description }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form id="delete-form" action="{{ route('variant.delete') }}" method="POST">
        @csrf @method('DELETE')
        <input type="hidden" name="variant_id" id="delete-variant-id" value="{{ $varsayilanVariant->id }}">
    </form>
</div>

<script>
    const variants = @json($urun->variants->load('images'));
    let currentSlide = 0;
    
    // Ürün Adı Senkronizasyonu
    document.getElementById('name-input').addEventListener('input', function(e) {
        document.getElementById('header-product-name').innerText = e.target.value;
    });

    function showInstantToast(msg, type) {
        const container = document.getElementById('toast-container');
        const id = 'toast-' + Math.random().toString(36).substr(2, 9);
        const html = `<div id="${id}" class="toast-card ${type} show">
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}-fill text-${type === 'success' ? 'emerald' : 'rose'}-500 text-xl"></i>
                <div><strong class="block text-sm">System</strong><span class="text-xs text-slate-500">${msg}</span></div>
            </div>`;
        container.insertAdjacentHTML('beforeend', html);
        const el = document.getElementById(id);
        setTimeout(() => { if(el) { el.classList.remove('show'); setTimeout(() => el.remove(), 500); } }, 3500);
    }

    function btnScrollDetail(dir) {
        const track = document.getElementById('main-track');
        const items = track.querySelectorAll('.carousel-item');
        if (items.length <= 1) return;
        currentSlide += dir;
        if (currentSlide < 0) currentSlide = items.length - 1;
        if (currentSlide >= items.length) currentSlide = 0;
        track.style.transform = `translateX(-${currentSlide * 100}%)`;
    }

   function updateAdminPanel(variantId, el) {
    // 1. Veri Kaynağından Seçili Varyantı Bul
    const selected = variants.find(v => v.id == variantId);
    if(!selected) return;

    // --- KRİTİK NOKTA: URL GÜNCELLEME ---
    // Sayfayı yenilemeden URL'yi 'urundetay/7' gibi günceller
    const newUrl = window.location.protocol + "//" + window.location.host + "/urundetay/" + variantId;
    window.history.pushState({path: newUrl}, '', newUrl);

    // 2. Carousel Resetleme
    currentSlide = 0;
    const track = document.getElementById('main-track');
    track.style.transform = `translateX(0%)`;

    // 3. Form Gizli Inputlarını Güncelle (Kaydet ve Sil butonları için)
    document.getElementById('selected-variant-id').value = variantId;
    document.getElementById('delete-variant-id').value = variantId;
document.querySelector('input[name="brand"]').value = selected.brand || '';
    document.querySelector('input[name="collection"]').value = selected.collection || '';
    document.querySelector('select[name="is_active"]').value = selected.is_active;
    // 4. Input Alanlarını Doldur
    document.getElementById('admin-sku').value = selected.sku || '';
    document.getElementById('admin-price').value = selected.price;
    document.getElementById('admin-discount-price').value = selected.discount_price || '';
    document.getElementById('admin-stock').value = selected.stock;
    document.getElementById('admin-size').value = selected.size;
    document.getElementById('admin-color-name').value = selected.color;
    document.getElementById('admin-color-code').value = selected.color_code;
    document.getElementById('color-picker').value = selected.color_code;

    // 5. Görsel URL'lerini Textarea'ya Yaz
    const imageUrls = selected.images ? selected.images.map(img => img.image_url).join('\n') : '';
    document.getElementById('admin-image-urls').value = imageUrls;

    // 6. Görsel Carousel ve Okları Güncelle
    const existingArrows = document.querySelectorAll('.nav-arrow');
    
    if (selected.images && selected.images.length > 0) {
        track.innerHTML = selected.images.map(img => `<div class="carousel-item"><img src="${img.image_url}" onerror="this.src='https://placehold.co/400x500?text=Resim+Yuklenemedi'"></div>`).join('');
        
        if (selected.images.length > 1 && existingArrows.length === 0) {
            const btnPrev = `<button type="button" onclick="btnScrollDetail(-1)" class="carousel-nav-btn nav-arrow absolute left-6 top-1/2 -translate-y-1/2"><i class="bi bi-chevron-left text-lg"></i></button>`;
            const btnNext = `<button type="button" onclick="btnScrollDetail(1)" class="carousel-nav-btn nav-arrow absolute right-6 top-1/2 -translate-y-1/2"><i class="bi bi-chevron-right text-lg"></i></button>`;
            document.querySelector('.group\\/carousel').insertAdjacentHTML('beforeend', btnPrev);
            document.querySelector('.group\\/carousel').insertAdjacentHTML('beforeend', btnNext);
        } 
        else if (selected.images.length <= 1 && existingArrows.length > 0) {
            existingArrows.forEach(arrow => arrow.remove());
        }
    } else {
        track.innerHTML = '<div class="carousel-item"><img src="https://placehold.co/400x500?text=Gorsel+Yok"></div>';
        existingArrows.forEach(arrow => arrow.remove());
    }
    
    // 7. Buton Aktiflik Durumu (UI)
    document.querySelectorAll('.variant-btn').forEach(btn => btn.classList.remove('active'));
    el.classList.add('active');
document.querySelector('select[name="is_active"]').value = selected.is_active;
    // Konsolda takip etmek istersen
    console.log("Varyant Seçildi ve URL güncellendi: " + variantId);
}
    const pInp = document.getElementById('admin-price');
    const dInp = document.getElementById('admin-discount-price');
    const dCont = document.getElementById('discount-container');

    if(dInp && pInp) {
        dInp.addEventListener('input', function() {
            if (this.value && parseFloat(this.value) >= parseFloat(pInp.value)) {
                dCont.classList.replace('bg-rose-50', 'bg-red-200');
                showInstantToast('Discount must be lower than price!', 'error');
            } else {
                dCont.classList.replace('bg-red-200', 'bg-rose-50');
            }
        });
    }

    function confirmDelete() { if(confirm('Delete this variant? This cannot be undone.')) { document.getElementById('delete-form').submit(); } }

    @if(session('success')) showInstantToast("{{ session('success') }}", 'success'); @endif
    @if(session('error')) showInstantToast("{{ session('error') }}", 'error'); @endif
</script>
</body>
</html>