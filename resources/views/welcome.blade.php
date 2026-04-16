<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Miharbi Clothing</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        
        * { box-sizing: border-box; }
        
        body { 
            background-color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: #1e293b; 
            margin: 0; 
            overflow-x: hidden; 
        }
        
        html { overflow-y: scroll; scroll-behavior: smooth; }
        
        .hide-scrollbar::-webkit-scrollbar { display: none !important; }
        .hide-scrollbar { -ms-overflow-style: none !important; scrollbar-width: none !important; }
        
        /* NAVIGATION */
        .admin-nav { 
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e2e8f0; 
            position: sticky; 
            top: 0; 
            z-index: 9999; 
            width: 100%;
        }
        
        .nav-item { 
            display: flex; align-items: center; gap: 8px; padding: 12px 16px; border-radius: 12px; 
            font-size: 14px; font-weight: 600; color: #64748b; transition: 0.2s; text-decoration: none; 
            cursor: pointer;
        }
        .nav-item:hover { background: #eef2ff; color: #4f46e5; }
        .nav-item.active { background: #4f46e5; color: white; }

        /* SEARCH */
        .search-container { position: relative; max-width: 672px; margin: 0 auto 40px auto; }
        .search-input { 
            width: 100%; padding: 14px 100px 14px 52px; background: white; border: 1px solid #e2e8f0; 
            border-radius: 24px; font-size: 14px; transition: 0.3s; box-shadow: 0 4px 12px rgba(0,0,0,0.03); 
        }
        .search-input:focus { outline: none; border-color: #4f46e5; box-shadow: 0 8px 20px rgba(79, 70, 229, 0.1); }
        
        .search-clear-btn { 
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: #f1f5f9; 
            color: #64748b; padding: 6px 16px; border-radius: 16px; font-size: 11px; font-weight: 800; 
            text-transform: uppercase; transition: 0.2s; border: none; cursor: pointer; display: flex; 
            align-items: center; gap: 4px; 
        }
        .search-clear-btn:hover { background: #ef4444; color: white; }

        /* PRODUCT CARD — sabit width kaldırıldı, grid'e göre uzuyor */
        .product-card { 
            background: white; border: 1px solid #e2e8f0; border-radius: 20px; overflow: hidden; 
            transition: all 0.4s; height: 100%; display: flex; flex-direction: column; 
            position: relative; width: 100%;
        }
        
        .product-card:hover { transform: translateY(-6px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.08); }

        .carousel-container { 
            position: relative; overflow: hidden; aspect-ratio: 1/1.2; background: #f1f5f9; 
        }
        .image-scroll-container { 
            display: flex; overflow-x: auto; scroll-snap-type: x mandatory; height: 100%; 
            scrollbar-width: none; -ms-overflow-style: none; 
        }
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

        .color-swatch-container { 
            position: absolute; bottom: 12px; left: 12px; z-index: 40; 
            background: rgba(255,255,255,0.95); padding: 6px; border-radius: 20px; 
            display: flex; gap: 5px; border: 1px solid #f1f5f9; backdrop-filter: blur(4px); 
        }
        .color-circle { 
            width: 14px; height: 14px; border-radius: 50%; border: 1px solid rgba(0,0,0,0.1); 
            transition: 0.2s; 
        }
        .color-circle.active { outline: 2px solid #4f46e5; outline-offset: 1px; transform: scale(1.1); }

        .size-pill { 
            display: flex; align-items: center; border: 1px solid #e2e8f0; 
            border-radius: 10px; overflow: hidden; background: white; 
        }
        .size-label { 
            padding: 4px 8px; background: #f8fafc; border-right: 1px solid #e2e8f0; 
            font-size: 11px; font-weight: 800; color: #1e293b; 
        }
        .stock-count { padding: 4px 8px; font-size: 11px; font-weight: 700; color: #4f46e5; }

        /* MODAL STYLES */
        .modal-image-container { 
            display: flex; overflow-x: auto; scroll-snap-type: x mandatory; 
            height: 100%; border-radius: 24px; scroll-behavior: smooth; 
        }
        .modal-image-slide { min-width: 100%; scroll-snap-align: start; height: 100%; }
        .modal-image-slide img { width: 100%; height: 100%; object-fit: cover; }
        
        .qv-variant-btn { 
            display: flex; flex-direction: column; align-items: start; gap: 2px; 
            padding: 10px 16px; border-radius: 14px; border: 2px solid #f1f5f9; 
            background: white; transition: all 0.15s ease-out; cursor: pointer; 
        }
        .qv-variant-btn.active { 
            border-color: #4f46e5; background: #eef2ff; transform: translateY(-1px); 
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.1); 
        }
        
        .quickview-overlay { 
            position: fixed; inset: 0; z-index: 20000; display: flex; 
            align-items: center; justify-content: center; padding: 20px; 
            background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px); 
        }

        /* SCROLL TO TOP BUTTON */
        .scroll-to-top {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 50px;
            height: 50px;
            background: #4f46e5;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.4);
            transition: all 0.3s ease;
            z-index: 9998;
            opacity: 0;
            transform: translateY(100px);
            pointer-events: none;
        }
        
        .scroll-to-top.visible {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }
        
        .scroll-to-top:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(79, 70, 229, 0.5);
            background: #4338ca;
        }
        
        .scroll-to-top:active {
            transform: translateY(-2px);
        }

        /* ABOUT SECTION */
        .about-section {
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 50%, #f8fafc 100%);
            position: relative;
            overflow: hidden;
        }
        
        .about-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.03) 0%, transparent 70%);
            pointer-events: none;
        }

        .about-card {
            background: white;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 20px 60px -15px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .about-image-wrapper {
            position: relative;
            overflow: hidden;
        }

        .about-image-wrapper::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.3) 0%, transparent 50%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .about-card:hover .about-image-wrapper::after {
            opacity: 1;
        }

        .about-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #eef2ff;
            color: #4f46e5;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .about-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800;
            line-height: 1.1;
            color: #0f172a;
            margin-bottom: 24px;
        }

        .about-text {
            font-size: 1.125rem;
            line-height: 1.8;
            color: #475569;
        }

        /* CONTACT SECTION - MOBILE OPTIMIZED */
        .contact-section {
            background: #0f172a;
            position: relative;
            overflow: hidden;
        }

        .contact-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(79, 70, 229, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(99, 102, 241, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 32px;
        }

        @media (min-width: 1024px) {
            .contact-grid {
                grid-template-columns: 1fr 1.2fr;
                gap: 48px;
            }
        }

        .contact-info-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 24px;
        }

        @media (min-width: 640px) {
            .contact-info-card {
                padding: 32px;
            }
        }

        .contact-info-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        @media (min-width: 640px) {
            .contact-info-item {
                gap: 20px;
                padding: 20px 0;
            }
        }

        .contact-info-item:last-child {
            border-bottom: none;
        }

        .contact-info-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            flex-shrink: 0;
        }

        @media (min-width: 640px) {
            .contact-info-icon {
                width: 56px;
                height: 56px;
                border-radius: 16px;
                font-size: 24px;
            }
        }

        .contact-form-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        @media (min-width: 640px) {
            .contact-form-card {
                border-radius: 32px;
                padding: 32px;
            }
        }

        @media (min-width: 768px) {
            .contact-form-card {
                padding: 48px;
            }
        }

        .contact-input-modern {
            width: 100%;
            padding: 14px 18px;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 500;
            color: #0f172a;
            transition: all 0.3s ease;
        }

        @media (min-width: 640px) {
            .contact-input-modern {
                padding: 16px 20px;
                border-radius: 16px;
            }
        }

        .contact-input-modern:focus {
            background: white;
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            outline: none;
        }

        .contact-input-modern::placeholder {
            color: #94a3b8;
        }

        .contact-submit-btn {
            width: 100%;
            padding: 16px 24px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        @media (min-width: 640px) {
            .contact-submit-btn {
                padding: 18px 32px;
                border-radius: 16px;
                font-size: 16px;
                gap: 12px;
            }
        }

        .contact-submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .contact-submit-btn:hover::before {
            left: 100%;
        }

        .contact-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(79, 70, 229, 0.4);
        }

        .contact-submit-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .social-link-modern {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        @media (min-width: 640px) {
            .social-link-modern {
                width: 52px;
                height: 52px;
                border-radius: 16px;
                font-size: 20px;
            }
        }

        .social-link-modern:hover {
            background: #4f46e5;
            border-color: #4f46e5;
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(79, 70, 229, 0.3);
        }

        /* TOAST NOTIFICATION - MOBILE OPTIMIZED */
        .toast-container {
            position: fixed;
            top: 16px;
            right: 16px;
            left: 16px;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }

        @media (min-width: 640px) {
            .toast-container {
                top: 24px;
                right: 24px;
                left: auto;
            }
        }

        .toast {
            background: white;
            border-radius: 14px;
            padding: 14px 16px;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            transform: translateY(-100%);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            pointer-events: auto;
            border-left: 4px solid;
        }

        @media (min-width: 640px) {
            .toast {
                min-width: 320px;
                max-width: 400px;
                border-radius: 16px;
                padding: 16px 20px;
                transform: translateX(100%);
            }
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        @media (min-width: 640px) {
            .toast.show {
                transform: translateX(0);
            }
        }

        .toast.success { border-left-color: #10b981; }
        .toast.error { border-left-color: #ef4444; }

        .toast-icon {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
        }

        @media (min-width: 640px) {
            .toast-icon { width: 40px; height: 40px; border-radius: 12px; font-size: 20px; }
        }

        .toast.success .toast-icon { background: #d1fae5; color: #059669; }
        .toast.error .toast-icon { background: #fee2e2; color: #dc2626; }

        .toast-content { flex: 1; min-width: 0; }

        .toast-title { font-weight: 700; font-size: 13px; color: #0f172a; margin-bottom: 2px; }

        @media (min-width: 640px) { .toast-title { font-size: 14px; } }

        .toast-message { font-size: 12px; color: #64748b; line-height: 1.4; }

        @media (min-width: 640px) { .toast-message { font-size: 13px; } }

        .toast-close {
            width: 26px; height: 26px; border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            color: #94a3b8; cursor: pointer; transition: all 0.2s; flex-shrink: 0;
        }

        @media (min-width: 640px) { .toast-close { width: 28px; height: 28px; border-radius: 8px; } }

        .toast-close:hover { background: #f1f5f9; color: #64748b; }

        /* LOADING SPINNER */
        .spinner {
            width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white; border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @media (min-width: 640px) { .spinner { width: 20px; height: 20px; } }

        @keyframes spin { to { transform: rotate(360deg); } }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body x-data="{ 
    quickView: false, 
    selectedItem: null, 
    activeVariant: null, 
    descOpen: false,
    showFilters: false,
    mobileMenu: false,
    activeSection: 'products',
    showScrollTop: false,
    toasts: [],
 openQuickView(item) {
    this.selectedItem = item;
    // all_variants içinden mevcut color_code'a uyan ilk varyantı bul
    const matchingVariant = item.all_variants 
        ? item.all_variants.find(v => v.color_code === item.color_code) 
        : null;
    this.activeVariant = matchingVariant || (item.all_variants ? item.all_variants[0] : item.variants[0]);
    this.descOpen = false;
    this.quickView = true;
},
    
    btnScrollModal(dir) {
        const c = this.$refs.modalTrack;
        if(!c) return;
        const maxScroll = c.scrollWidth - c.offsetWidth;
        if (dir === 1 && c.scrollLeft >= maxScroll - 5) c.scrollLeft = 0;
        else if (dir === -1 && c.scrollLeft <= 5) c.scrollLeft = maxScroll;
        else c.scrollBy({ left: c.offsetWidth * dir, behavior: 'smooth' });
    },
    
    scrollToSection(section) {
        this.activeSection = section;
        const el = document.getElementById(section + '-section');
        if(el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    },
    
    scrollToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },
    
    initScrollWatcher() {
        window.addEventListener('scroll', () => {
            this.showScrollTop = window.pageYOffset > 500;
            
            const sections = ['products', 'about', 'contact'];
            const scrollPos = window.scrollY + 200;
            
            for (const section of sections) {
                const el = document.getElementById(section + '-section');
                if (el) {
                    const top = el.offsetTop;
                    const height = el.offsetHeight;
                    if (scrollPos >= top && scrollPos < top + height) {
                        this.activeSection = section;
                        break;
                    }
                }
            }
        });
    },
    
    addToast(type, title, message) {
        const id = Date.now();
        this.toasts.push({ id, type, title, message });
        setTimeout(() => {
            this.removeToast(id);
        }, 4000);
    },
    
    removeToast(id) {
        const index = this.toasts.findIndex(t => t.id === id);
        if (index > -1) {
            this.toasts.splice(index, 1);
        }
    }
}" x-init="initScrollWatcher()" @toast.window="addToast($event.detail.type, $event.detail.title, $event.detail.message)">

<!-- Toast Notifications -->
<div class="toast-container">
    <template x-for="toast in toasts" :key="toast.id">
        <div class="toast" 
             :class="toast.type + ' show'"
             x-show="true"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="toast-icon">
                <i class="bi" :class="toast.type === 'success' ? 'bi-check-lg' : 'bi-exclamation-triangle-fill'"></i>
            </div>
            <div class="toast-content">
                <div class="toast-title" x-text="toast.title"></div>
                <div class="toast-message" x-text="toast.message"></div>
            </div>
            <div class="toast-close" @click="removeToast(toast.id)">
                <i class="bi bi-x-lg"></i>
            </div>
        </div>
    </template>
</div>

<header class="admin-nav shadow-sm">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between h-20">
            <a href="{{ url('/') }}" class="flex items-center gap-2 hover:opacity-80 transition no-underline">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                    <i class="bi bi-shop text-xl"></i>
                </div>
                <span class="font-bold text-slate-900 tracking-tight text-base md:text-lg">Miharbi Clothing</span>
            </a>
            <button @click="mobileMenu = !mobileMenu" type="button" class="md:hidden p-2.5 rounded-xl text-slate-600 hover:bg-slate-100 transition-all">
                <i class="bi" :class="mobileMenu ? 'bi-x-lg' : 'bi-list'" style="font-size: 1.8rem;"></i>
            </button>
            <nav class="hidden md:flex items-center gap-2">
                <span @click="scrollToSection('products')" :class="activeSection === 'products' ? 'nav-item' : 'nav-item'" style="cursor: pointer;">
                    <i class="bi bi-grid-1x2"></i>Dashboard
                </span>
                <span @click="scrollToSection('about')" :class="activeSection === 'about' ? 'nav-item active' : 'nav-item'" style="cursor: pointer;">
                    <i class="bi bi-info-circle"></i>About Us
                </span>
                <span @click="scrollToSection('contact')" :class="activeSection === 'contact' ? 'nav-item active' : 'nav-item'" style="cursor: pointer;">
                    <i class="bi bi-envelope"></i>Contact
                </span>
                <a href="{{ url('userlogin') }}" class="nav-item"><i class="bi bi-person"></i>Member Login</a>
            </nav>
        </div>
        <nav x-show="mobileMenu" x-cloak @click.away="mobileMenu = false" x-transition class="md:hidden px-4 pb-4 space-y-1 bg-white border-t mt-2">
            <span @click="scrollToSection('products'); mobileMenu = false" :class="activeSection === 'products' ? 'nav-item mt-2' : 'nav-item mt-2'" style="cursor: pointer;">
                <i class="bi bi-grid-1x2"></i>Dashboard
            </span>
            <span @click="scrollToSection('about'); mobileMenu = false" :class="activeSection === 'about' ? 'nav-item active' : 'nav-item'" style="cursor: pointer;">
                <i class="bi bi-info-circle"></i>About Us
            </span>
            <span @click="scrollToSection('contact'); mobileMenu = false" :class="activeSection === 'contact' ? 'nav-item active' : 'nav-item'" style="cursor: pointer;">
                <i class="bi bi-envelope"></i>Contact
            </span>
            <a href="{{ url('userlogin') }}" class="nav-item"><i class="bi bi-person"></i>Member Login</a>
        </nav>
    </div>
</header>

<!-- Scroll to Top Button -->
<button @click="scrollToTop()" 
        :class="showScrollTop ? 'visible' : ''"
        class="scroll-to-top"
        aria-label="Scroll to top">
    <i class="bi bi-arrow-up"></i>
</button>

<!-- PRODUCTS SECTION -->
<div id="products-section" class="max-w-7xl mx-auto px-4 py-8">
    <!-- Search & Filter Bar -->
    <div class="flex flex-col md:flex-row gap-4 mb-10">
        <div class="search-container group flex-1 !mb-0">
            <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition text-lg"></i>
            <form action="{{ route('welcome') }}" method="GET" id="searchForm">
                <input type="text" name="q" id="searchInput" value="{{ request('q') }}" 
                    placeholder="Search name, category, brand or SKU..." class="search-input">
                @if(request('q') || request('cat') || request('size') || request('min') || request('max') || request('brand') || request('collection'))
                    <button type="button" onclick="window.location.href='{{ route('welcome') }}'" class="search-clear-btn">
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
                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    <i class="bi bi-sliders2 text-indigo-500"></i> Filters
                </h2>
                <button @click="showFilters = false" class="text-slate-400 hover:text-rose-500 transition text-2xl">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form action="{{ route('welcome') }}" method="GET" class="space-y-8">
                <input type="hidden" name="q" value="{{ request('q') }}">

                @if(isset($brands) && $brands->count() > 0)
                <div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-4">Brands</span>
                    <div class="space-y-2 max-h-40 overflow-y-auto pr-2 hide-scrollbar">
                        @foreach($brands as $brand)
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="brand[]" value="{{ $brand }}" 
                                {{ is_array(request('brand')) && in_array($brand, request('brand')) ? 'checked' : '' }} 
                                class="rounded text-indigo-600">
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
                            <input type="checkbox" name="collection[]" value="{{ $col }}" 
                                {{ is_array(request('collection')) && in_array($col, request('collection')) ? 'checked' : '' }} 
                                class="rounded text-indigo-600">
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
                            <input type="checkbox" name="cat[]" value="{{ $cat->id }}" 
                                {{ is_array(request('cat')) && in_array($cat->id, request('cat')) ? 'checked' : '' }} 
                                class="rounded text-indigo-600">
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
                            <input type="checkbox" name="size[]" value="{{ $size }}" 
                                {{ is_array(request('size')) && in_array($size, request('size')) ? 'checked' : '' }} 
                                class="hidden peer">
                            <div class="px-3 py-1.5 border rounded-lg text-xs font-bold peer-checked:bg-indigo-600 peer-checked:text-white transition">{{ $size }}</div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-4">Price Range</span>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" name="min" placeholder="Min" value="{{ request('min') }}" 
                            class="w-full p-3 border rounded-xl text-xs outline-none focus:border-indigo-500">
                        <input type="number" name="max" placeholder="Max" value="{{ request('max') }}" 
                            class="w-full p-3 border rounded-xl text-xs outline-none focus:border-indigo-500">
                    </div>
                </div>

                <button type="submit" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-bold text-sm shadow-xl hover:bg-black transition transform active:scale-95">
                    Apply Filters
                </button>
            </form>
        </div>
    </div>

    <!-- Empty State -->
    @if($urunler->isEmpty() && request()->anyFilled(['q', 'cat', 'brand', 'collection', 'size', 'min', 'max']))
        <div class="flex flex-col items-center justify-center py-20 bg-white rounded-3xl border-2 border-dashed border-slate-200">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                <i class="bi bi-search text-3xl text-slate-300"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800">Product not found</h3>
            <p class="text-slate-500 mt-2">No products match your selected filters.</p>
            <button onclick="window.location.href='{{ route('welcome') }}'" class="mt-6 px-6 py-2 bg-indigo-600 text-white font-bold rounded-xl">
                Clear Filters
            </button>
        </div>
    @elseif($urunler->isEmpty())
        <div class="text-center py-20">
            <p class="text-slate-400 italic">No products available at the moment.</p>
        </div>
    @else
        <!-- Product Grid by Category -->
        <main class="space-y-16">
            @foreach($urunler->groupBy('category_name') as $categoryName => $groupedItems)
            {{-- Her kategori kendi expanded state'ini tutuyor --}}
            <section class="relative" x-data="{ expanded: false }">

                <div class="flex items-center justify-between mb-6 px-2">
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                        <span class="w-2 h-8 bg-indigo-600 rounded-full"></span>
                        {{ $categoryName ?: 'Collection' }}
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
                        <div class="carousel-container group/main relative">
                            <div class="image-scroll-container" id="scroll-{{ $uniqueId }}">
                                @foreach($images as $img)
                                <div class="scroll-slide"
                                    data-variant-id="{{ $img->variant_id ?? $item->first_variant->id }}"
                                    data-price="{{ number_format($item->first_variant->price, 2) }}"
                                    data-sku="{{ $item->first_variant->sku ?? 'NO-CODE' }}"
                                    data-discount="{{ $item->first_variant->discount_price ? number_format($item->first_variant->discount_price, 2) : '' }}"
                                    data-all-sizes="{{ json_encode($item->variants->map(fn($v) => ['size' => $v->size, 'stock' => $v->stock, 'sku' => $v->sku])) }}">
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
                                    @if(!empty($item->brand))
                                        <div><span class="text-slate-400">Brand:</span> <span class="font-medium text-slate-700">{{ $item->brand }}</span></div>
                                    @endif
                                    @if(!empty($item->collection))
                                        <div><span class="text-slate-400">Collection:</span> <span class="font-medium text-slate-700">{{ $item->collection }}</span></div>
                                    @endif
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
                                <button @click="openQuickView({{ json_encode($item) }})"
                                    class="w-10 h-10 bg-indigo-600 text-white hover:bg-indigo-700 rounded-xl flex items-center justify-center shadow-lg transition-all active:scale-90">
                                    <i class="bi bi-eye-fill text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- KALAN ÜRÜNLER — sadece expanded true ise görünür, aksi halde DOM'dan tamamen kaldırılır --}}
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
                        <div class="carousel-container group/main relative">
                            <div class="image-scroll-container" id="scroll-{{ $uniqueId }}">
                                @foreach($images as $img)
                                <div class="scroll-slide"
                                    data-variant-id="{{ $img->variant_id ?? $item->first_variant->id }}"
                                    data-price="{{ number_format($item->first_variant->price, 2) }}"
                                    data-sku="{{ $item->first_variant->sku ?? 'NO-CODE' }}"
                                    data-discount="{{ $item->first_variant->discount_price ? number_format($item->first_variant->discount_price, 2) : '' }}"
                                    data-all-sizes="{{ json_encode($item->variants->map(fn($v) => ['size' => $v->size, 'stock' => $v->stock, 'sku' => $v->sku])) }}">
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
                                    @if(!empty($item->brand))
                                        <div><span class="text-slate-400">Brand:</span> <span class="font-medium text-slate-700">{{ $item->brand }}</span></div>
                                    @endif
                                    @if(!empty($item->collection))
                                        <div><span class="text-slate-400">Collection:</span> <span class="font-medium text-slate-700">{{ $item->collection }}</span></div>
                                    @endif
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
                                <button @click="openQuickView({{ json_encode($item) }})"
                                    class="w-10 h-10 bg-indigo-600 text-white hover:bg-indigo-700 rounded-xl flex items-center justify-center shadow-lg transition-all active:scale-90">
                                    <i class="bi bi-eye-fill text-lg"></i>
                                </button>
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

<!-- ABOUT SECTION - MODERN PROFESSIONAL -->
<section id="about-section" class="about-section py-24 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <span class="about-badge mb-6">
                <i class="bi bi-stars"></i>
                About Us
            </span>
            <h2 class="text-4xl md:text-6xl font-black text-slate-900 tracking-tight mt-6">
                Crafting <span class="text-indigo-600">Excellence</span>
            </h2>
            <p class="text-slate-500 text-lg max-w-2xl mx-auto mt-6 font-medium">
                Discover the story behind Miharbi Clothing and our commitment to quality.
            </p>
        </div>

        <div class="about-card">
            <div class="grid lg:grid-cols-2 gap-0">
                <!-- Image Side -->
                <div class="about-image-wrapper min-h-[400px] lg:min-h-[600px] bg-slate-100">
                    @if(isset($about) && $about->image)
                        <img src="{{ $about->image }}" class="w-full h-full object-cover transition-transform duration-700 hover:scale-105" alt="About Us">
                    @else
                        <div class="h-full flex flex-col items-center justify-center text-slate-400 gap-4 py-20">
                            <div class="w-24 h-24 bg-slate-200 rounded-full flex items-center justify-center">
                                <i class="bi bi-image text-4xl"></i>
                            </div>
                            <span class="font-medium text-sm uppercase tracking-widest">Image Not Available</span>
                        </div>
                    @endif
                </div>

                <!-- Content Side -->
                <div class="p-8 md:p-12 lg:p-16 flex flex-col justify-center">
                    <span class="about-badge mb-6">
                        <i class="bi bi-info-circle"></i>
                        ABOUT US
                    </span>

                    <h3 class="about-title">
                        {{ $about->title ?? 'Miharbi Clothing' }}
                    </h3>

                    <div class="space-y-6 about-text">
                        <p class="text-xl font-semibold text-slate-800 leading-relaxed">
                            {{ $about->description ?? 'Premium quality clothing brand dedicated to style and comfort.' }}
                        </p>
                        @if(isset($about) && $about->second_paragraph) 
                            <p>{{ $about->second_paragraph }}</p> 
                        @endif
                        @if(isset($about) && $about->last_paragraph) 
                            <p>{{ $about->last_paragraph }}</p> 
                        @endif
                    </div>

                    @if(isset($about) && ($about->phone || $about->email))
                    <div class="mt-10 pt-8 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @if($about->phone)
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 shrink-0">
                                <i class="bi bi-telephone-fill text-lg"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Phone</p>
                                <p class="text-base font-bold text-slate-900 leading-tight">{{ $about->phone }}</p>
                            </div>
                        </div>
                        @endif
                        @if($about->email)
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 shrink-0">
                                <i class="bi bi-envelope-fill text-lg"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Email</p>
                                <p class="text-base font-bold text-slate-900 leading-tight">{{ $about->email }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CONTACT SECTION - MOBILE OPTIMIZED PROFESSIONAL -->
<section id="contact-section" class="contact-section py-16 sm:py-24 px-4">
    <div class="max-w-7xl mx-auto relative">
        <div class="text-center mb-12 sm:mb-16 px-2">
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 text-white rounded-full text-xs font-bold uppercase tracking-widest mb-6 backdrop-blur-sm border border-white/10">
                <i class="bi bi-chat-dots"></i>
                Get In Touch
            </span>
            <h2 class="text-3xl sm:text-4xl md:text-6xl font-black text-white tracking-tight">
                Let's <span class="text-indigo-400">Connect</span>
            </h2>
            <p class="text-slate-400 text-base sm:text-lg max-w-2xl mx-auto mt-4 sm:mt-6 font-medium px-4">
                Have a question or need assistance? We're here to help you with anything you need.
            </p>
        </div>

        <div class="contact-grid">
            <!-- Contact Info Side -->
            <div class="space-y-4 sm:space-y-6">
                <div class="contact-info-card">
                    <h3 class="text-lg sm:text-xl font-bold text-white mb-4 sm:mb-6">Contact Information</h3>
                    
                    @if(isset($about) && $about->email)
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-slate-400 text-xs sm:text-sm mb-1">Email</p>
                            <a href="mailto:{{ $about->email }}" class="text-white font-bold text-base sm:text-lg hover:text-indigo-400 transition break-all">{{ $about->email }}</a>
                        </div>
                    </div>
                    @endif

                    @if(isset($about) && $about->phone)
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs sm:text-sm mb-1">Phone</p>
                            <a href="tel:{{ $about->phone }}" class="text-white font-bold text-base sm:text-lg hover:text-indigo-400 transition">{{ $about->phone }}</a>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Social Links -->
                @if(isset($about) && ($about->instagram_url || $about->twitter_url || $about->linkedin_url))
                <div class="flex gap-3 sm:gap-4 justify-center sm:justify-start">
                    @if(!empty($about->instagram_url)) 
                        <a href="{{ $about->instagram_url }}" target="_blank" class="social-link-modern">
                            <i class="bi bi-instagram"></i>
                        </a> 
                    @endif
                    @if(!empty($about->twitter_url)) 
                        <a href="{{ $about->twitter_url }}" target="_blank" class="social-link-modern">
                            <i class="bi bi-twitter-x"></i>
                        </a> 
                    @endif
                    @if(!empty($about->linkedin_url)) 
                        <a href="{{ $about->linkedin_url }}" target="_blank" class="social-link-modern">
                            <i class="bi bi-linkedin"></i>
                        </a> 
                    @endif
                    @if(!empty($about->github_url)) 
                        <a href="{{ $about->github_url }}" target="_blank" class="social-link-modern">
                            <i class="bi bi-github"></i>
                        </a> 
                    @endif
                </div>
                @endif
            </div>

            <!-- Contact Form Side -->
            <div class="contact-form-card" x-data="contactForm()">
                <div class="flex items-center gap-3 sm:gap-4 mb-6 sm:mb-8">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 shrink-0">
                        <i class="bi bi-send-fill text-xl sm:text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl sm:text-2xl font-bold text-slate-900">Send Message</h3>
                        <p class="text-slate-500 text-sm sm:text-base">We'll respond within 24 hours'</p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-4 sm:space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5 sm:mb-2">Full Name *</label>
                            <input type="text" x-model="form.name" required placeholder="John Doe" class="contact-input-modern">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5 sm:mb-2">Email Address *</label>
                            <input type="email" x-model="form.email" required placeholder="john@example.com" class="contact-input-modern">
                        </div>
                    </div>
<div>
    <label class="block text-sm font-bold text-slate-700 mb-1.5 sm:mb-2">Subject</label>
    <input type="text" x-model="form.subject" placeholder="Subject" class="contact-input-modern">
</div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5 sm:mb-2">Message *</label>
                        <textarea x-model="form.message" rows="4" required placeholder="Tell us about your inquiry..." class="contact-input-modern resize-none"></textarea>
                    </div>

                    <button type="submit" class="contact-submit-btn" :disabled="loading">
                        <template x-if="!loading">
                            <span class="flex items-center gap-2 sm:gap-3">
                                <span>Send Message</span>
                                <i class="bi bi-arrow-right text-lg sm:text-xl"></i>
                            </span>
                        </template>
                        <template x-if="loading">
                            <span class="flex items-center gap-2 sm:gap-3">
                                <div class="spinner"></div>
                                <span>Sending...</span>
                            </span>
                        </template>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Quick View Modal -->
<template x-if="quickView">
    <div class="quickview-overlay" x-cloak @click.self="quickView = false">
        <div class="bg-white w-full max-w-6xl max-h-[90vh] rounded-[40px] shadow-2xl overflow-hidden flex flex-col lg:flex-row relative">
            <button @click="quickView = false" class="absolute top-6 right-6 z-50 w-12 h-12 bg-white/90 shadow-xl rounded-full flex items-center justify-center text-slate-400 hover:text-red-500 border border-slate-100 transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>

            <div class="lg:w-1/2 bg-slate-50 relative flex items-center justify-center p-6 lg:p-10">
                <div class="relative w-full aspect-[3/4] group/modal">
                    <div x-ref="modalTrack" class="modal-image-container hide-scrollbar shadow-2xl border-[10px] border-white bg-white">
                        <template x-for="img in (activeVariant.images?.length ? activeVariant.images : selectedItem.images)">
                            <div class="modal-image-slide">
                                <img :src="img.image_url" class="w-full h-full object-cover">
                            </div>
                        </template>
                    </div>
                    
                    <template x-if="(activeVariant.images?.length ? activeVariant.images : selectedItem.images).length > 1">
                        <div>
                            <button @click="btnScrollModal(-1)" class="absolute left-4 top-1/2 -translate-y-1/2 w-11 h-11 bg-white/90 rounded-2xl shadow-xl flex items-center justify-center border border-slate-100 transition-all hover:bg-white">
                                <i class="bi bi-chevron-left text-xl font-bold text-slate-700"></i>
                            </button>
                            <button @click="btnScrollModal(1)" class="absolute right-4 top-1/2 -translate-y-1/2 w-11 h-11 bg-white/90 rounded-2xl shadow-xl flex items-center justify-center border border-slate-100 transition-all hover:bg-white">
                                <i class="bi bi-chevron-right text-xl font-bold text-slate-700"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <div class="lg:w-1/2 p-8 lg:p-12 overflow-y-auto flex flex-col gap-6">
                <div>
                    <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-full uppercase" x-text="selectedItem.category_name || 'Product'"></span>
                    <h2 class="text-3xl font-black text-slate-900 mt-4 leading-tight uppercase" x-text="selectedItem.name"></h2>
                    <p class="text-slate-400 font-bold mt-2 uppercase text-xs">
                        <span x-show="activeVariant.brand" x-text="activeVariant.brand"></span>
                        <span x-show="activeVariant.brand && activeVariant.collection"> • </span>
                        <span x-show="activeVariant.collection" x-text="activeVariant.collection"></span>
                    </p>
                </div>

                <div class="bg-slate-50 p-6 rounded-[24px] border border-slate-100">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Description</p>
                    <div :class="descOpen ? '' : 'line-clamp-2'" class="text-slate-600 text-sm leading-relaxed" x-text="selectedItem.description || 'No description available.'"></div>
                    <button x-show="selectedItem.description && selectedItem.description.length > 50" @click="descOpen = !descOpen" class="text-indigo-600 text-[10px] font-black uppercase mt-2 hover:underline" x-text="descOpen ? 'Show Less' : 'Read More'"></button>
                </div>

                <div class="py-4 flex items-center justify-between border-b border-slate-100">
                    <div class="flex flex-col">
                        <template x-if="activeVariant.discount_price">
                            <div class="flex items-baseline gap-3">
                                <span class="text-3xl font-black text-indigo-600" x-text="Number(activeVariant.discount_price).toFixed(2) + ' ₺'"></span>
                                <span class="text-sm text-slate-300 line-through font-bold" x-text="Number(activeVariant.price).toFixed(2) + ' ₺'"></span>
                            </div>
                        </template>
                        <template x-if="!activeVariant.discount_price">
                            <span class="text-3xl font-black text-slate-900" x-text="Number(activeVariant.price).toFixed(2) + ' ₺'"></span>
                        </template>
                    </div>
                    <span class="text-[10px] font-black text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-100 uppercase">
                        <span x-text="activeVariant.stock"></span> in stock
                    </span>
                </div>

                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Select Variant</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <template x-for="v in selectedItem.all_variants" :key="v.id">
                            <button type="button" @click="activeVariant = v" :class="activeVariant.id === v.id ? 'qv-variant-btn active' : 'qv-variant-btn'">
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

                <div class="bg-slate-900 text-white p-6 rounded-[28px] flex justify-between items-center mt-auto shadow-2xl">
                    <div>
                        <p class="text-[9px] font-black text-slate-500 uppercase mb-1">SKU</p>
                        <p class="font-mono text-sm font-bold text-indigo-400 uppercase" x-text="activeVariant.sku"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    // Contact Form Component
    function contactForm() {
        return {
            loading: false,
            form: {
                name: '',
                email: '',
                subject: '',
                message: ''
            },
            
            async submit() {
                if (this.loading) return;
                
                if (!this.form.name || !this.form.email || !this.form.message) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { type: 'error', title: 'Error!', message: 'Please fill in all required fields.' }
                    }));
                    return;
                }
                
                this.loading = true;
                
                try {
                    const formData = new FormData();
                    formData.append('name', this.form.name);
                    formData.append('email', this.form.email);
                    formData.append('subject', this.form.subject);
                    formData.append('message', this.form.message);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                    
                    const response = await fetch('{{ route('contact.send') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    
                    let data;
                    try {
                        data = await response.json();
                    } catch (e) {
                        const text = await response.text();
                        console.error('Server response:', text.substring(0, 500));
                        throw new Error('Server error. Please check logs.');
                    }
                    
                    if (response.ok && data.success) {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: { type: 'success', title: 'Success!', message: data.message || 'Your message has been sent successfully.' }
                        }));
                        this.form = { name: '', email: '', subject: '', message: '' };
                    } else {
                        throw new Error(data.message || 'An error occurred.');
                    }
                } catch (error) {
                    console.error('Form error:', error);
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { type: 'error', title: 'Error!', message: error.message || 'Failed to send message. Please try again.' }
                    }));
                } finally {
                    this.loading = false;
                }
            }
        }
    }
    
    // Product scroll function
    function btnScroll(id, dir) { 
        const c = document.getElementById(id); 
        if(!c) return;
        const scrollAmount = c.offsetWidth;
        const maxScroll = c.scrollWidth - scrollAmount;
        const currentScroll = c.scrollLeft;
        
        if (dir === 1 && currentScroll >= maxScroll - 10) {
            c.scrollTo({ left: 0, behavior: 'smooth' });
        } else if (dir === -1 && currentScroll <= 10) {
            c.scrollTo({ left: maxScroll, behavior: 'smooth' });
        } else {
            c.scrollBy({ left: scrollAmount * dir, behavior: 'smooth' }); 
        }
    }
</script>

</body>
</html>
