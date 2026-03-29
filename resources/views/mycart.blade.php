<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart - Miharbi Clothing</title>
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
        }
        
        .cart-item {
            transition: all 0.3s ease;
        }
        
        .cart-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08);
        }
        
        .quantity-btn {
            transition: all 0.2s;
        }
        
        .quantity-btn:hover:not(:disabled) {
            background: #eef2ff;
            color: #4f46e5;
        }
        
        .quantity-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }
        
        .remove-btn {
            transition: all 0.2s;
        }
        
        .remove-btn:hover {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .checkout-btn {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            transition: all 0.3s ease;
        }
        
        .checkout-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(79, 70, 229, 0.4);
        }
        
        .checkout-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .empty-cart {
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
        }
        
        /* TOAST NOTIFICATION */
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

        .toast.success {
            border-left-color: #10b981;
        }

        .toast.error {
            border-left-color: #ef4444;
        }

        .toast-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .toast.success .toast-icon {
            background: #d1fae5;
            color: #059669;
        }

        .toast.error .toast-icon {
            background: #fee2e2;
            color: #dc2626;
        }

        .toast-content {
            flex: 1;
            min-width: 0;
        }

        .toast-title {
            font-weight: 700;
            font-size: 13px;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .toast-message {
            font-size: 12px;
            color: #64748b;
            line-height: 1.4;
        }

        .toast-close {
            width: 26px;
            height: 26px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .toast-close:hover {
            background: #f1f5f9;
            color: #64748b;
        }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body x-data="cartPage()">

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
<style>
    .nav-item { 
        display: flex; align-items: center; gap: 8px; padding: 12px 16px; border-radius: 12px; 
        font-size: 14px; font-weight: 600; color: #64748b; transition: 0.2s; text-decoration: none; 
        cursor: pointer;
    }
    .nav-item:hover { background: #eef2ff; color: #4f46e5; }
    .nav-item.active { background: #4f46e5; color: white; }
</style>
<!-- Header - Dashboard ile Aynı -->
<header class="admin-nav shadow-sm" x-data="{ 
    mobileMenu: false
}">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <a href="{{ url('/dashboard') }}" class="flex items-center gap-2 hover:opacity-80 transition no-underline">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                    <i class="bi bi-shop text-xl"></i>
                </div>
                <span class="font-bold text-slate-900 tracking-tight text-base md:text-lg">Miharbi Clothing</span>
            </a>

            <!-- Mobile Menu Button -->
            <button @click="mobileMenu = !mobileMenu" type="button" class="md:hidden p-2.5 rounded-xl text-slate-600 hover:bg-slate-100 transition-all">
                <i class="bi" :class="mobileMenu ? 'bi-x-lg' : 'bi-list'" style="font-size: 1.8rem;"></i>
            </button>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center gap-2">
                <a href="{{ url('/dashboard') }}" class="nav-item">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
                <a href="{{ url('favorites') }}" class="nav-item"><i class="bi bi-heart"></i> Favorites</a>
                <a href="{{ url('mycart') }}" class="nav-item"><i class="bi bi-cart3"></i> My Cart</a>
                <a href="{{ url('myorders') }}" class="nav-item"><i class="bi bi-bag"></i> My Orders</a>
                
                @auth
                    <div class="relative ml-2" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-100 transition-all duration-200">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <span class="text-sm font-medium text-slate-700">{{ Auth::user()->name }}</span>
                            <i class="bi bi-chevron-down text-xs text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        
                        <div x-show="open" 
                             x-cloak 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-1 z-50">
                            
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="text-sm font-medium text-slate-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            
                            <a href="{{ route('profile.edit') }}" 
                               class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                                <i class="bi bi-person-gear mr-2"></i>Profile Settings
                            </a>
                            
                            <div class="border-t border-slate-100 mt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="bi bi-box-arrow-right mr-2"></i>Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endauth
            </nav>
        </div>

        <!-- Mobile Navigation -->
        <nav x-show="mobileMenu" 
             x-cloak 
             @click.away="mobileMenu = false" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="transform opacity-0 -translate-y-2"
             x-transition:enter-end="transform opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="transform opacity-100 translate-y-0"
             x-transition:leave-end="transform opacity-0 -translate-y-2"
             class="md:hidden px-4 pb-4 space-y-1 bg-white border-t mt-2">
            
            <a href="{{ url('/dashboard') }}" class="nav-item block mt-2" @click="mobileMenu = false">
                <i class="bi bi-grid-1x2"></i>Dashboard
            </a>
            <a href="{{ url('favorites') }}" class="nav-item block" @click="mobileMenu = false">
                <i class="bi bi-heart"></i> Favorites
            </a>
            <a href="{{ url('mycart') }}" class="nav-item block" @click="mobileMenu = false">
                <i class="bi bi-cart3"></i> My Cart
            </a>
            <a href="{{ url('myorders') }}" class="nav-item block" @click="mobileMenu = false">
                <i class="bi bi-bag"></i> My Orders
            </a>

            @auth
                <div class="border-t pt-2 mt-2">
                    <div class="flex items-center gap-3 px-3 py-2">
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600">
                            <i class="bi bi-person-fill text-lg"></i>
                        </div>
                        <div>
                            <p class="font-medium text-slate-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate-500">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="nav-item block" @click="mobileMenu = false">
                        <i class="bi bi-person-gear"></i>Profile Settings
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-1">
                        @csrf
                        <button type="submit" class="nav-item w-full text-left text-red-600 bg-transparent border-0" @click="mobileMenu = false">
                            <i class="bi bi-box-arrow-right"></i>Log Out
                        </button>
                    </form>
                </div>
            @endauth
        </nav>
    </div>
</header>

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 py-8">
    
    <!-- Title -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-900">My Cart</h1>
            <p class="text-slate-500 text-sm mt-1">
                <span x-text="cartItems.length"></span> items
            </p>
        </div>
        
        <button x-show="cartItems.length > 0" @click="clearCart()" :disabled="loading" class="text-sm text-red-500 hover:text-red-700 font-semibold flex items-center gap-1 transition">
            <i class="bi bi-trash3"></i>
            Clear Cart
        </button>
    </div>

    <!-- Empty Cart -->
    <div x-show="cartItems.length === 0" x-cloak class="empty-cart rounded-3xl p-12 text-center">
        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
            <i class="bi bi-bag-x text-4xl text-slate-300"></i>
        </div>
        <h2 class="text-xl font-bold text-slate-800 mb-2">Your Cart is Empty</h2>
        <p class="text-slate-500 mb-6">Discover products and add them to your cart.</p>
        <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition">
            <i class="bi bi-arrow-left"></i>
            Continue Shopping
        </a>
    </div>

    <!-- Cart Content -->
    <div x-show="cartItems.length > 0" class="grid lg:grid-cols-4 gap-6">
        
        <!-- Product List - 3 Columns -->
        <div class="lg:col-span-3">
            <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
                <template x-for="item in cartItems" :key="item.id">
                    <div class="cart-item bg-white rounded-2xl p-4 border border-slate-200 flex flex-col">
                        <!-- Product Image -->
                        <div class="relative w-full aspect-square bg-slate-100 rounded-xl overflow-hidden mb-3">
                            <img :src="item.urun_variant.images[0]?.image_url || '/placeholder.jpg'" class="w-full h-full object-cover">
                            
                            <!-- Remove Button (X) - Top Right -->
                            <button @click="removeItem(item.urun_variant.id)" :disabled="loading" 
                                class="absolute top-2 right-2 w-7 h-7 bg-white/90 backdrop-blur rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 border border-slate-200 transition shadow-sm">
                                <i class="bi bi-x-lg text-sm"></i>
                            </button>
                        </div>
                        
                        <!-- Product Details -->
                        <div class="flex-1 flex flex-col">
                            <h3 class="font-bold text-slate-900 text-sm truncate mb-1" x-text="item.urun_variant.urun.name"></h3>
                            <p class="text-xs text-slate-500 mb-1">
                                <span x-text="item.urun_variant.brand"></span> • 
                                <span x-text="item.urun_variant.color"></span>
                            </p>
                            <p class="text-xs font-mono text-slate-400 mb-3" x-text="'SKU: ' + item.urun_variant.sku"></p>
                            
<!-- Size ve Stock - Yan Yana -->
<div class="mb-3 flex items-center gap-2">
    <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 rounded-lg text-xs font-bold text-slate-700">
        Size: <span x-text="item.urun_variant.size" class="ml-1"></span>
    </span>
    <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 rounded-lg text-xs font-bold text-slate-600">
        Stock: <span x-text="item.urun_variant.stock" class="ml-1 text-slate-900"></span>
    </span>
</div>
                            <!-- Bottom Row - Quantity and Price -->
                            <div class="mt-auto flex items-center justify-between">
                                <!-- Quantity Control -->
                                <div class="flex items-center bg-slate-50 rounded-lg border border-slate-200">
                                    <button @click="decreaseQuantity(item)" 
                                        :disabled="item.quantity <= 1 || loading"
                                        class="quantity-btn w-7 h-7 flex items-center justify-center text-slate-600 rounded-l-lg">
                                        <i class="bi bi-dash text-sm"></i>
                                    </button>
                                    <span x-text="item.quantity" class="w-8 text-center font-bold text-sm"></span>
                                    <button @click="increaseQuantity(item)" 
                                        :disabled="item.quantity >= Math.min(item.urun_variant.stock, 10) || loading"
                                        class="quantity-btn w-7 h-7 flex items-center justify-center text-slate-600 rounded-r-lg">
                                        <i class="bi bi-plus text-sm"></i>
                                    </button>
                                </div>
                                
                                <!-- Price -->
                                <div class="text-right">
                                    <template x-if="item.urun_variant.discount_price">
                                        <div>
                                            <span class="text-[10px] text-slate-400 line-through block" x-text="formatPrice(item.urun_variant.price * item.quantity)"></span>
                                            <span class="font-black text-indigo-600 text-sm" x-text="formatPrice(item.urun_variant.discount_price * item.quantity)"></span>
                                        </div>
                                    </template>
                                    <template x-if="!item.urun_variant.discount_price">
                                        <span class="font-black text-slate-900 text-sm" x-text="formatPrice(item.urun_variant.price * item.quantity)"></span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

     <!-- Order Summary - 1 Column -->
<div class="lg:col-span-1">
    <div class="bg-white rounded-2xl p-5 border border-slate-200 sticky top-24">
        <h2 class="font-bold text-slate-900 mb-4 text-lg">Order Summary</h2>
        
        <div class="space-y-2 text-sm mb-4">
            <div class="flex justify-between text-slate-600">
                <span>Subtotal</span>
                <span x-text="formatPrice(subtotal)"></span>
            </div>
          <div class="flex justify-between text-emerald-600">
    <span>Shipping</span>
    <span>Free</span>
</div>

            <div class="flex justify-between text-slate-600">
                <span>VAT (18%)</span>
                <span x-text="formatPrice(tax)"></span>
            </div>

            <div x-show="discount > 0" class="flex justify-between text-emerald-600">
                <span>Discount</span>
                <span x-text="'-' + formatPrice(discount)"></span>
            </div>
        </div>
        
        <div class="border-t border-slate-200 my-3 pt-3">
            <div class="flex justify-between items-center">
                <span class="font-bold text-slate-900">Total</span>
                <span class="text-xl font-black text-indigo-600" x-text="formatPrice(total)"></span>
            </div>
        </div>
        
        <!-- Confirm Button -->
        <button 
            @click="checkout()" 
            :disabled="loading || cartItems.length === 0" 
            class="checkout-btn w-full py-3 rounded-xl text-white font-bold flex items-center justify-center gap-2 text-sm"
            x-data="{ loading: false }"
        >
            <span x-show="!loading" class="flex items-center gap-2">
                <span>Confirm Order</span>
                <i class="bi bi-arrow-right"></i>
            </span>
            <span x-show="loading" class="flex items-center gap-2" style="display: none;">
                <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                <span>Processing...</span>
            </span>
        </button>
        
        <p class="text-xs text-slate-400 text-center mt-3">
            <i class="bi bi-shield-check"></i> Secure Payment
        </p>
    </div>
</div>
    </div>
</main>
<script>
function cartPage() {
    return {
        cartItems: @json($cartItems ?? []),
        loading: false,
        mobileMenu: false,
        toasts: [],
        
        get cartCount() {
            return this.cartItems.reduce((sum, item) => sum + item.quantity, 0);
        },
        
        get subtotal() {
            return this.cartItems.reduce((sum, item) => {
                const price = item.urun_variant.discount_price || item.urun_variant.price;
                return sum + (price * item.quantity);
            }, 0);
        },
        
      get shipping() {
    return 0;
},
        
        get discount() {
            return 0;
        },
        
      get total() {
    return this.subtotal - this.discount + this.tax;
},
        get tax() {
    return (this.subtotal - this.discount) * 0.18;
},

get total() {
    return this.subtotal + this.shipping - this.discount + this.tax;
},
        
        formatPrice(price) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD'
            }).format(price);
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
        },
        
        async increaseQuantity(item) {
            const newQuantity = item.quantity + 1;
            if (newQuantity > Math.min(item.urun_variant.stock, 10)) {
                this.addToast('error', 'Error', 'Maximum stock reached');
                return;
            }
            await this.updateQuantity(item.urun_variant.id, newQuantity);
        },
        
        async decreaseQuantity(item) {
            const newQuantity = item.quantity - 1;
            if (newQuantity < 1) {
                this.addToast('error', 'Error', 'Minimum quantity is 1');
                return;
            }
            await this.updateQuantity(item.urun_variant.id, newQuantity);
        },
        
        async updateQuantity(variantId, newQuantity) {
            if (this.loading) return;
            this.loading = true;
            
            try {
                const formData = new FormData();
                formData.append('quantity', newQuantity);
                formData.append('_token', document.querySelector('meta[name=csrf-token]').content);
                
                const response = await fetch(`/mycart/update/${variantId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok && data.status !== 'error') {
                    const item = this.cartItems.find(i => i.urun_variant.id === variantId);
                    if (item) item.quantity = newQuantity;
                    this.addToast('success', 'Updated', 'Quantity updated successfully');
                } else {
                    this.addToast('error', 'Error', data.message || 'Failed to update quantity');
                }
            } catch (e) {
                console.error('Update error:', e);
                this.addToast('error', 'Error', 'Failed to update quantity');
            } finally {
                this.loading = false;
            }
        },
        
        async removeItem(variantId) {
            if (this.loading) return;
            
            const item = this.cartItems.find(i => i.urun_variant.id === variantId);
            const itemName = item ? item.urun_variant.urun.name : 'Item';
            
            this.loading = true;
            
            try {
                const response = await fetch(`/mycart/remove/${variantId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.cartItems = this.cartItems.filter(item => item.urun_variant.id !== variantId);
                    this.addToast('success', 'Removed', `${itemName} removed from cart`);
                } else {
                    this.addToast('error', 'Error', data.message || 'Failed to remove item');
                }
            } catch (e) {
                console.error('Remove error:', e);
                this.addToast('error', 'Error', 'Failed to remove item');
            } finally {
                this.loading = false;
            }
        },
        
        async clearCart() {
            if (this.loading) return;
            if (!confirm('Are you sure you want to clear your cart?')) return;
            
            this.loading = true;
            
            try {
                const response = await fetch('/mycart/clear', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.cartItems = [];
                    this.addToast('success', 'Cleared', 'Cart cleared successfully');
                } else {
                    this.addToast('error', 'Error', data.message || 'Failed to clear cart');
                }
            } catch (e) {
                console.error('Clear error:', e);
                this.addToast('error', 'Error', 'Failed to clear cart');
            } finally {
                this.loading = false;
            }
        },
        
        checkout() {
            if (this.cartItems.length === 0) {
                this.addToast('error', 'Error', 'Your cart is empty');
                return;
            }
            
            // Kullanıcıyı checkout sayfasına yönlendir
            window.location.href = '/checkout';
        }
    }
}
</script>
</body>
</html>