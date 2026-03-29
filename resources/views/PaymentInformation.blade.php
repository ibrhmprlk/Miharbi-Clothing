<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Checkout - Miharbi Clothing</title>
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <script src="https://cdn.tailwindcss.com"></script>
            <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
            <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
            <style>
                body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
                [x-cloak] { display: none !important; }
                
                /* Nav Item Styles */
                .nav-item { display: flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 12px; font-size: 14px; font-weight: 600; color: #64748b; transition: all 0.2s; text-decoration: none; cursor: pointer; }
                .nav-item:hover { background: #eef2ff; color: #4f46e5; }
                .nav-item.active { background: #4f46e5; color: white; box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3); }
                
                /* Admin Nav Background */
                .admin-nav { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-bottom: 1px solid #e2e8f0; }
                
                /* Toast Notification */
                .toast-container { position: fixed; top: 24px; right: 24px; z-index: 1000; display: flex; flex-direction: column; gap: 12px; }
                .toast { background: white; border-radius: 16px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); border-left: 4px solid; padding: 16px 20px; min-width: 320px; max-width: 400px; transform: translateX(450px); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); opacity: 0; }
                .toast.show { transform: translateX(0); opacity: 1; }
                .toast.success { border-left-color: #10b981; }
                .toast.error { border-left-color: #ef4444; }
                
                /* Scroll to Top Button */
                .scroll-to-top { position: fixed; bottom: 20px; right: 20px; width: 48px; height: 48px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3); transition: all 0.3s; opacity: 0; visibility: hidden; transform: translateY(20px); z-index: 999; border: none; }
                .scroll-to-top.show { opacity: 1; visibility: visible; transform: translateY(0); }
                .scroll-to-top:hover { transform: translateY(-3px) scale(1.05); }
                
                /* Glass Card */
                .glass-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border: 1px solid rgba(226, 232, 240, 0.8); border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); }
                
                /* Address Card Compact */
                .address-card { background: white; border: 2px solid #e2e8f0; border-radius: 12px; padding: 16px; transition: all 0.2s; cursor: pointer; position: relative; }
                .address-card:hover { border-color: #c7d2fe; }
                .address-card.selected { border-color: #4f46e5; background: #eef2ff; }
                
                /* Payment Option Compact */
                .payment-card { background: white; border: 2px solid #e2e8f0; border-radius: 12px; padding: 16px; transition: all 0.2s; cursor: pointer; }
                .payment-card:hover { border-color: #c7d2fe; }
                .payment-card.selected { border-color: #4f46e5; background: #eef2ff; }
                
                /* Credit Card Form */
                .credit-card-form { background: #1e293b; border-radius: 16px; padding: 20px; color: white; margin-top: 16px; }
                .credit-input { background: #334155; border: 1px solid #475569; border-radius: 8px; padding: 12px 16px; color: white; width: 100%; font-size: 14px; transition: all 0.2s; }
                .credit-input:focus { outline: none; background: #475569; border-color: #6366f1; }
                .credit-input::placeholder { color: #94a3b8; }
                
                /* Buttons */
                .btn-primary { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border: none; border-radius: 12px; padding: 14px 24px; color: white; font-weight: 600; font-size: 16px; transition: all 0.2s; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3); }
                .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4); }
                .btn-secondary { background: white; border: 2px solid #e2e8f0; border-radius: 12px; padding: 14px 24px; color: #475569; font-weight: 600; font-size: 16px; transition: all 0.2s; }
                .btn-secondary:hover { background: #f8fafc; border-color: #cbd5e1; }
                
                /* Form Elements */
                .form-input { width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; transition: all 0.2s; background: #f8fafc; }
                .form-input:focus { outline: none; border-color: #4f46e5; background: white; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
                
                /* Product Item */
                .product-item { display: flex; gap: 12px; padding: 12px; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0; }
                
                /* Section Title */
                .section-title { font-size: 16px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px; margin-bottom: 16px; }
                
                /* Badge */
                .badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
                .badge-indigo { background: #eef2ff; color: #4f46e5; }
                .badge-emerald { background: #d1fae5; color: #059669; }
                .badge-amber { background: #fef3c7; color: #d97706; }
                
                /* Radio Circle */
                .radio-circle { width: 20px; height: 20px; border: 2px solid #cbd5e1; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s; flex-shrink: 0; }
                .selected .radio-circle { border-color: #4f46e5; background: #4f46e5; }
                .radio-inner { width: 8px; height: 8px; background: white; border-radius: 50%; transform: scale(0); transition: transform 0.2s; }
                .selected .radio-inner { transform: scale(1); }
                
                /* Spinner */
                .spinner { border: 2px solid rgba(255,255,255,0.3); border-radius: 50%; border-top: 2px solid white; width: 18px; height: 18px; animation: spin 1s linear infinite; }
                @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
                
                /* Delete Button */
                .delete-btn-container { position: absolute; top: 12px; right: 12px; z-index: 10; }
                .delete-btn { 
                    width: 32px; 
                    height: 32px; 
                    display: flex; 
                    align-items: center; 
                    justify-content: center; 
                    border-radius: 8px; 
                    background: white;
                    border: 1px solid #e2e8f0;
                    color: #64748b; 
                    transition: all 0.2s; 
                    cursor: pointer;
                }
                .delete-btn:hover { 
                    background: #fef2f2; 
                    color: #ef4444; 
                    border-color: #fecaca;
                    transform: scale(1.05);
                }
            </style>
        </head>
       
        <body class="bg-slate-50" x-data="pageApp()" @scroll.window="handleScroll()">

            <!-- Scroll to Top Button -->
            <button class="scroll-to-top" :class="{ 'show': showScrollTop }" @click="window.scrollTo({top: 0, behavior: 'smooth'})">
                <i class="bi bi-arrow-up text-lg"></i>
            </button>

            <!-- Toast Notifications -->
            <div class="toast-container" x-data="{ toasts: [] }" @add-toast.window="toasts.push($event.detail); setTimeout(() => toasts.shift(), 4000)">
                <template x-for="(toast, index) in toasts" :key="index">
                    <div class="toast show" :class="toast.type">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center" :class="toast.type === 'success' ? 'bg-emerald-100' : 'bg-red-100'">
                                <i class="bi text-sm" :class="toast.type === 'success' ? 'bi-check-lg text-emerald-600' : 'bi-x-lg text-red-600'"></i>
                            </div>
                            <div class="flex-1 pt-1">
                                <p class="text-sm font-semibold text-slate-800" x-text="toast.message"></p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            @if(session('success'))
                <div x-data="{}" x-init="$dispatch('add-toast', { message: '{{ session('success') }}', type: 'success' })"></div>
            @endif
            @if(session('error'))
                <div x-data="{}" x-init="$dispatch('add-toast', { message: '{{ session('error') }}', type: 'error' })"></div>
            @endif

            <!-- Header -->
            <header class="admin-nav shadow-sm sticky top-0 z-50">
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
                            <span @click="scrollToSection('products')" class="nav-item">
                                <i class="bi bi-grid-1x2"></i>Dashboard
                            </span>

                            <a href="{{ url('favorites') }}" class="nav-item"><i class="bi bi-heart"></i> Favorites</a>
                            <a href="{{ url('mycart') }}" class="nav-item "><i class="bi bi-bag"></i> My Cart</a>
                            <a href="{{ url('myorders') }}" class="nav-item"><i class="bi bi-bag"></i> My Orders</a>

                            <!-- Profile Dropdown -->
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
                        
                        <span @click="scrollToSection('products'); mobileMenu = false" class="nav-item mt-2">
                            <i class="bi bi-grid-1x2"></i>Dashboard
                        </span>

                        <a href="{{ url('favorites') }}" class="nav-item"><i class="bi bi-heart"></i> Favorites</a>
                        <a href="{{ url('mycart') }}" class="nav-item"><i class="bi bi-bag"></i> My Cart</a>
                        <a href="{{ url('myorders') }}" class="nav-item " @click="mobileMenu = false">
                            <i class="bi bi-bag"></i> My Orders
                        </a>

                        <!-- Mobile Profile Section -->
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
                                <a href="{{ route('profile.edit') }}" class="nav-item">
                                    <i class="bi bi-person-gear"></i>Profile Settings
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                                    @csrf
                                    <button type="submit" class="nav-item w-full text-left text-red-600">
                                        <i class="bi bi-box-arrow-right"></i>Log Out
                                    </button>
                                </form>
                            </div>
                        @endauth
                    </nav>
                </div>
            </header>

            <div class="h-16 lg:h-20"></div>

            <!-- Main Content -->
            <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                
                {{--
                    FIX (SyntaxError kesin çözüm):
                    Blade {{ }} çıktıları artık JS string içinde değil, data-* attribute'larda.
                    Alpine parser bu attribute'ları JS olarak yorumlamaz → hata imkansız.
                --}}
                <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form"
                      data-has-addresses="{{ (isset($addresses) && $addresses->count() > 0) ? '1' : '0' }}"
                      data-first-address="{{ optional($addresses->first())->id ?? '' }}"
                      @submit.prevent="submitForm">
                    @csrf
               
                    <input type="hidden" name="shipping_address_id" :value="showNewAddress ? '' : selectedAddress">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        
                        <!-- Left Column -->
                        <div class="lg:col-span-8 space-y-4">
                            
                            <!-- Shipping Address -->
                            <div class="glass-card p-5">
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="section-title mb-0">
                                        <i class="bi bi-geo-alt-fill text-indigo-600"></i>
                                        Shipping Address
                                    </h2>
                                    <button type="button" @click="toggleNewAddress()" 
                                            class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1 bg-indigo-50 px-3 py-1.5 rounded-lg transition-all hover:bg-indigo-100">
                                        <i class="bi bi-plus-lg"></i>
                                        <span x-text="showNewAddress ? 'Saved' : 'New'"></span>
                                    </button>
                                </div>

                                <!-- Saved Addresses -->
                                <div x-show="!showNewAddress" x-cloak>
                                    @if(isset($addresses) && $addresses->count() > 0)
                                        <div class="grid grid-cols-1 gap-3">
                                            @foreach($addresses as $address)
                                            <div class="address-card" :class="{ 'selected': selectedAddress == '{{ $address->id }}' }" @click="selectAddress('{{ $address->id }}')">
                                                
                                                <!-- Silme Butonu -->
                                                <div class="delete-btn-container" @click.stop>
                                                    <button type="button" 
                                                            @click="if(confirm('Bu adresi silmek istediğinize emin misiniz?')) { $refs.deleteAddressForm.action = '{{ route('address.delete', $address->id) }}'; $refs.deleteAddressForm.submit(); }" 
                                                            class="delete-btn" 
                                                            title="Adresi Sil">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </div>

                                                <div class="flex items-start gap-3 pr-10">
                                                    <div class="radio-circle mt-0.5">
                                                        <div class="radio-inner"></div>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                                                            <span class="font-semibold text-slate-900 text-sm">{{ $address->title }}</span>
                                                            @if($address->is_default)
                                                                <span class="badge badge-indigo">Default</span>
                                                            @endif
                                                        </div>
                                                        <p class="font-medium text-slate-900 text-sm">{{ $address->full_name }}</p>
                                                        <p class="text-slate-600 text-sm mt-0.5 leading-relaxed">{{ $address->address }}</p>
                                                        <p class="text-slate-500 text-sm">{{ $address->district }}, {{ $address->city }}</p>
                                                        <p class="text-slate-600 text-sm mt-1 font-medium">{{ $address->phone }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-6 text-slate-500 bg-slate-50 rounded-lg border-2 border-dashed border-slate-200">
                                            <i class="bi bi-geo-alt text-2xl mb-2 block"></i>
                                            <p class="text-sm font-medium">No saved addresses</p>
                                        </div>
                                        <div x-init="showNewAddress = true"></div>
                                    @endif
                                </div>

                                <!-- New Address Form -->
                                <div x-show="showNewAddress" x-cloak class="mt-4 pt-4 border-t border-slate-100">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                                            {{-- FIX: x-model önce, name sonra — Alpine parser sıraya duyarlı --}}
                                            <input x-model="newAddress.fullName" type="text" name="shipping_full_name" class="form-input" placeholder="John Doe">
                                        </div>

                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Phone <span class="text-red-500">*</span></label>
                                            <input 
                                                x-model="newAddress.phone"
                                                @input="formatPhone"
                                                type="tel" 
                                                name="shipping_phone" 
                                                maxlength="11"
                                                inputmode="numeric"
                                                pattern="[0-9]*"
                                                class="form-input" 
                                                placeholder="05XXXXXXXXX">
                                            <p class="text-xs mt-1" style="color: #ef4444;" x-show="newAddress.phone.length > 0 && newAddress.phone.length < 10">Min 10 digits</p>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">City <span class="text-red-500">*</span></label>
                                            <select x-model="newAddress.city" name="shipping_city" class="form-input">
                                                <option value="">Select City</option>
                                                <option value="Adana">Adana</option>
                                                <option value="Adıyaman">Adıyaman</option>
                                                <option value="Afyonkarahisar">Afyonkarahisar</option>
                                                <option value="Ağrı">Ağrı</option>
                                                <option value="Aksaray">Aksaray</option>
                                                <option value="Amasya">Amasya</option>
                                                <option value="Ankara">Ankara</option>
                                                <option value="Antalya">Antalya</option>
                                                <option value="Ardahan">Ardahan</option>
                                                <option value="Artvin">Artvin</option>
                                                <option value="Aydın">Aydın</option>
                                                <option value="Balıkesir">Balıkesir</option>
                                                <option value="Bartın">Bartın</option>
                                                <option value="Batman">Batman</option>
                                                <option value="Bayburt">Bayburt</option>
                                                <option value="Bilecik">Bilecik</option>
                                                <option value="Bingöl">Bingöl</option>
                                                <option value="Bitlis">Bitlis</option>
                                                <option value="Bolu">Bolu</option>
                                                <option value="Burdur">Burdur</option>
                                                <option value="Bursa">Bursa</option>
                                                <option value="Çanakkale">Çanakkale</option>
                                                <option value="Çankırı">Çankırı</option>
                                                <option value="Çorum">Çorum</option>
                                                <option value="Denizli">Denizli</option>
                                                <option value="Diyarbakır">Diyarbakır</option>
                                                <option value="Düzce">Düzce</option>
                                                <option value="Edirne">Edirne</option>
                                                <option value="Elazığ">Elazığ</option>
                                                <option value="Erzincan">Erzincan</option>
                                                <option value="Erzurum">Erzurum</option>
                                                <option value="Eskişehir">Eskişehir</option>
                                                <option value="Gaziantep">Gaziantep</option>
                                                <option value="Giresun">Giresun</option>
                                                <option value="Gümüşhane">Gümüşhane</option>
                                                <option value="Hakkari">Hakkari</option>
                                                <option value="Hatay">Hatay</option>
                                                <option value="Iğdır">Iğdır</option>
                                                <option value="Isparta">Isparta</option>
                                                <option value="Istanbul">Istanbul</option>
                                                <option value="Izmir">Izmir</option>
                                                <option value="Kahramanmaraş">Kahramanmaraş</option>
                                                <option value="Karabük">Karabük</option>
                                                <option value="Karaman">Karaman</option>
                                                <option value="Kars">Kars</option>
                                                <option value="Kastamonu">Kastamonu</option>
                                                <option value="Kayseri">Kayseri</option>
                                                <option value="Kilis">Kilis</option>
                                                <option value="Kırıkkale">Kırıkkale</option>
                                                <option value="Kırklareli">Kırklareli</option>
                                                <option value="Kırşehir">Kırşehir</option>
                                                <option value="Kocaeli">Kocaeli</option>
                                                <option value="Konya">Konya</option>
                                                <option value="Kütahya">Kütahya</option>
                                                <option value="Malatya">Malatya</option>
                                                <option value="Manisa">Manisa</option>
                                                <option value="Mardin">Mardin</option>
                                                <option value="Mersin">Mersin</option>
                                                <option value="Muğla">Muğla</option>
                                                <option value="Muş">Muş</option>
                                                <option value="Nevşehir">Nevşehir</option>
                                                <option value="Niğde">Niğde</option>
                                                <option value="Ordu">Ordu</option>
                                                <option value="Osmaniye">Osmaniye</option>
                                                <option value="Rize">Rize</option>
                                                <option value="Sakarya">Sakarya</option>
                                                <option value="Samsun">Samsun</option>
                                                <option value="Siirt">Siirt</option>
                                                <option value="Sinop">Sinop</option>
                                                <option value="Sivas">Sivas</option>
                                                <option value="Şanlıurfa">Şanlıurfa</option>
                                                <option value="Şırnak">Şırnak</option>
                                                <option value="Tekirdağ">Tekirdağ</option>
                                                <option value="Tokat">Tokat</option>
                                                <option value="Trabzon">Trabzon</option>
                                                <option value="Tunceli">Tunceli</option>
                                                <option value="Uşak">Uşak</option>
                                                <option value="Van">Van</option>
                                                <option value="Yalova">Yalova</option>
                                                <option value="Yozgat">Yozgat</option>
                                                <option value="Zonguldak">Zonguldak</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">District <span class="text-red-500">*</span></label>
                                            <input x-model="newAddress.district" type="text" name="shipping_district" class="form-input" placeholder="Kadikoy">
                                        </div>

                                        <div class="sm:col-span-2">
                                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Full Address <span class="text-red-500">*</span></label>
                                            <textarea x-model="newAddress.address" name="shipping_address" rows="2" class="form-input resize-none" placeholder="Street, building no, apartment, floor..."></textarea>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Postal Code</label>
                                           <input x-model="newAddress.zip" 
       @input="newAddress.zip = newAddress.zip.replace(/\D/g, '').slice(0,5)"
       type="text" 
       name="shipping_zip" 
       class="form-input" 
       placeholder="34000" 
       maxlength="5"
       inputmode="numeric">
                                        </div>
                                    </div>
                                    
                                    <input type="hidden" name="save_address" value="1">
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="glass-card p-5">
                                <h2 class="section-title">
                                    <i class="bi bi-credit-card-fill text-indigo-600"></i>
                                    Payment Method
                                </h2>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <!-- Credit Card -->
                                    <div class="payment-card" :class="{ 'selected': paymentMethod === 'credit_card' }" @click="paymentMethod = 'credit_card'">
                                        <input type="radio" name="payment_method" value="credit_card" class="sr-only" x-model="paymentMethod">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center text-white flex-shrink-0">
                                                <i class="bi bi-credit-card text-lg"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <h3 class="font-semibold text-slate-900 text-sm">Credit Card</h3>
                                                    <div class="radio-circle">
                                                        <div class="radio-inner"></div>
                                                    </div>
                                                </div>
                                                <p class="text-xs text-slate-500 mt-0.5">3D Secure</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Cash on Delivery -->
                                    <div class="payment-card" :class="{ 'selected': paymentMethod === 'cash_on_delivery' }" @click="paymentMethod = 'cash_on_delivery'">
                                        <input type="radio" name="payment_method" value="cash_on_delivery" class="sr-only" x-model="paymentMethod">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-emerald-600 rounded-lg flex items-center justify-center text-white flex-shrink-0">
                                                <i class="bi bi-cash-stack text-lg"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <h3 class="font-semibold text-slate-900 text-sm">Cash on Delivery</h3>
                                                    <div class="radio-circle">
                                                        <div class="radio-inner"></div>
                                                    </div>
                                                </div>
                                                <p class="text-xs text-slate-500 mt-0.5">Pay at door</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            
                            </div>

                            <!-- Order Note -->
                            <div class="glass-card p-5">
                                <h2 class="section-title">
                                    <i class="bi bi-chat-square-text text-indigo-600"></i>
                                    Order Note
                                </h2>
                                <textarea x-model="customerNote" name="customer_note" rows="2" maxlength="500" class="form-input resize-none" placeholder="Delivery instructions..."></textarea>
                                <p class="text-xs text-slate-400 mt-1.5 text-right"><span x-text="customerNote.length"></span>/500</p>
                            </div>
                        </div>

                        <!-- Right Column: Order Summary -->
                        <div class="lg:col-span-4">
                            <div class="sticky top-40 space-y-4">
                                
                                <!-- Products -->
                                <div class="glass-card p-5">
                                    <h3 class="font-bold text-slate-900 mb-3 flex items-center gap-2 text-sm">
                                        <i class="bi bi-bag-check text-indigo-600"></i>
                                        Order Summary
                                        <span class="badge badge-indigo ml-auto">{{ $cartItems->count() }}</span>
                                    </h3>

                                    <div class="space-y-3 max-h-64 overflow-y-auto pr-1 mb-4">
                                        @forelse($cartItems as $item)
                                        @php
                                            $variant = $item->urunVariant;
                                            $urun = $variant->urun;
                                            $firstImage = $variant->images->first();
                                            $imageUrl = null;
                                            if ($firstImage && $firstImage->image_url) {
                                                $rawUrl = $firstImage->image_url;
                                                if (str_starts_with($rawUrl, 'http://') || str_starts_with($rawUrl, 'https://')) {
                                                    $imageUrl = $rawUrl;
                                                } else {
                                                    $imageUrl = asset('storage/' . $rawUrl);
                                                }
                                            }
                                            $price = $variant->discount_price ?? $variant->price;
                                            $totalPrice = $price * $item->quantity;
                                            $stockWarning = $variant->stock <= 5 && $variant->stock >= $item->quantity;
                                            $stockError = $variant->stock < $item->quantity;
                                        @endphp
                                        
                                        <div class="product-item {{ $stockError ? 'opacity-60' : '' }}">
                                            <div class="w-16 h-16 bg-white rounded-lg overflow-hidden flex-shrink-0 flex items-center justify-center border border-slate-200 relative">
                                                @if($imageUrl)
                                                    <img src="{{ $imageUrl }}" alt="{{ $urun->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'bi bi-image text-slate-400 text-xs\'></i>';">
                                                @else
                                                    <i class="bi bi-image text-slate-400 text-xs"></i>
                                                @endif
                                                
                                                @if($stockWarning)
                                                    <div class="absolute -top-1 -right-1 w-4 h-4 bg-amber-500 rounded-full flex items-center justify-center text-white text-xs font-bold">!</div>
                                                @endif
                                            </div>
                                            
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-slate-900 line-clamp-2 leading-tight">{{ $urun->name }}</p>
                                                <p class="text-xs text-slate-500 mt-0.5">{{ $variant->color }} {{ $variant->size ? '• ' . $variant->size : '' }}</p>
                                                
                                                @if($stockError)
                                                    <p class="text-xs text-red-600 font-medium mt-0.5">Stock: {{ $variant->stock }}</p>
                                                @elseif($stockWarning)
                                                    <p class="text-xs text-amber-600 font-medium mt-0.5">Only {{ $variant->stock }} left</p>
                                                @endif
                                                
                                                <div class="flex items-center justify-between mt-1.5">
                                                    <span class="text-xs text-slate-500 bg-white px-1.5 py-0.5 rounded border border-slate-200">x{{ $item->quantity }}</span>
                                                    <span class="font-semibold text-indigo-600 text-sm">{{ number_format($totalPrice, 2, ',', '.') }} ₺</span>
                                                </div>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="text-center py-6 text-slate-500">
                                            <i class="bi bi-bag-x text-xl mb-2 block"></i>
                                            <p class="text-sm">Cart is empty</p>
                                        </div>
                                        @endforelse
                                    </div>

                                    <div class="space-y-2 border-t border-slate-200 pt-3 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Subtotal</span>
                                            <span class="font-medium text-slate-900">{{ number_format($subtotal, 2, ',', '.') }} ₺</span>
                                        </div>
                                        
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Shipping</span>
                                            <span class="font-medium text-emerald-600">Free</span>
                                        </div>
                                        
                                        @if($discountAmount > 0)
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Discount</span>
                                            <span class="font-medium text-emerald-600">-{{ number_format($discountAmount, 2, ',', '.') }} ₺</span>
                                        </div>
                                        @endif
                                        
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">VAT (18%)</span>
                                            <span class="font-medium text-slate-900">{{ number_format($taxAmount, 2, ',', '.') }} ₺</span>
                                        </div>
                                        
                                        <div class="flex justify-between items-center pt-3 border-t-2 border-slate-200">
                                            <span class="font-bold text-slate-900">Total</span>
                                            <span class="text-lg font-bold text-indigo-600">{{ number_format($total, 2, ',', '.') }} ₺</span>
                                        </div>
                                    </div>

                                    <div class="mt-3 flex items-center justify-center gap-3 text-slate-400 text-xs">
                                        <span class="flex items-center gap-1"><i class="bi bi-shield-lock"></i> SSL</span>
                                        <span class="flex items-center gap-1"><i class="bi bi-shield-check"></i> 3D</span>
                                    </div>
                                </div>

                                <!-- Payment Button -->
                                <button type="submit" id="pay-button" 
                                        class="btn-primary w-full flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                        :disabled="isSubmitting">
                                    <span x-show="!isSubmitting" class="flex items-center gap-2">
                                        <i class="bi bi-lock-fill"></i>
                                        Pay {{ number_format($total, 2, ',', '.') }} ₺
                                    </span>
                                    <span x-show="isSubmitting" class="flex items-center gap-2">
                                        <div class="spinner"></div>
                                        Processing...
                                    </span>
                                </button>

                                <p class="text-xs text-center text-slate-500 leading-relaxed px-2">
                                    By paying, you agree to our <a href="#" class="text-indigo-600 hover:underline">terms</a>.
                                </p>
                            </div>
                        </div>
                    </div>
                </form>
            </main>

            <!-- Scripts -->
            <script>
                /*
                 * FIX ÖZET:
                 * 1. body x-data="checkoutPageData()" + form x-data="checkoutData()" iç içe scope'u
                 *    TEK bir pageApp() fonksiyonunda birleştirildi.
                 * 2. newAddress.full_name  →  newAddress.fullName  (alt çizgi Alpine parser'ını
                 *    bazı tarayıcılarda yanıltıyordu; camelCase güvenlidir)
                 * 3. Tüm input'larda x-model/@ direktifleri en başa alındı,
                 *    name/type attribute'ları sona taşındı.
                 */
                function pageApp() {
                    return {
                        /* ── Sayfa geneli ── */
                        showScrollTop: false,
                        mobileMenu: false,
                        activeSection: 'products',

                        handleScroll() {
                            this.showScrollTop = window.pageYOffset > 300;
                            const sections = ['products', 'about', 'contact'];
                            let current = 'products';
                            sections.forEach(section => {
                                const element = document.getElementById(section);
                                if (element) {
                                    const rect = element.getBoundingClientRect();
                                    if (rect.top <= 100 && rect.bottom >= 100) {
                                        current = section;
                                    }
                                }
                            });
                            this.activeSection = current;
                        },

                        scrollToSection(sectionId) {
                            const element = document.getElementById(sectionId);
                            if (element) {
                                const headerOffset = 80;
                                const offsetPosition = element.getBoundingClientRect().top + window.pageYOffset - headerOffset;
                                window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
                            } else {
                                if (sectionId === 'products') {
                                    window.location.href = '{{ url("/dashboard") }}';
                                }
                            }
                        },

                       
                        showNewAddress: (document.getElementById('checkout-form')?.dataset.hasAddresses !== '1'),
                        selectedAddress: (document.getElementById('checkout-form')?.dataset.firstAddress ?? ''),
                        paymentMethod: 'credit_card',
                        customerNote: '',
                        isSubmitting: false,

                        newAddress: {
                            fullName: '',   /* full_name → fullName (camelCase) */
                            phone: '',
                            city: '',
                            district: '',
                            address: '',
                            zip: ''
                        },

                    
                        formatPhone() {
                            this.newAddress.phone = this.newAddress.phone.replace(/\D/g, '').slice(0, 11);
                        },

                        toggleNewAddress() {
                            this.showNewAddress = !this.showNewAddress;
                            if (this.showNewAddress) {
                                this.selectedAddress = '';
                            }
                        },

                        selectAddress(id) {
                            this.selectedAddress = id;
                            this.showNewAddress = false;
                        },

                        formatCardNumber() {
                            let value = this.cardDetails.number.replace(/\D/g, '');
                            this.cardDetails.number = value.match(/.{1,4}/g)?.join(' ').substr(0, 19) || value;
                        },

                        formatExpiry() {
                            let value = this.cardDetails.expiry.replace(/\D/g, '');
                            if (value.length >= 2) {
                                value = value.substr(0, 2) + '/' + value.substr(2, 2);
                            }
                            this.cardDetails.expiry = value.substr(0, 5);
                        },

                        validateForm() {
                            if (!this.showNewAddress) {
                                if (!this.selectedAddress) {
                                    alert('Please select a shipping address.');
                                    return false;
                                }
                            } else {
                                if (!this.newAddress.fullName.trim()) {
                                    alert('Please enter your full name.');
                                    return false;
                                }
                                if (!this.newAddress.phone.trim()) {
                                    alert('Please enter your phone number.');
                                    return false;
                                }
                                if (!this.newAddress.city) {
                                    alert('Please select a city.');
                                    return false;
                                }
                                if (!this.newAddress.district.trim()) {
                                    alert('Please enter your district.');
                                    return false;
                                }
                                if (!this.newAddress.address.trim()) {
                                    alert('Please enter your full address.');
                                    return false;
                                }
                            }

                            return true;
                        },

                        submitForm() {
                            if (this.isSubmitting) return;
                            if (!this.validateForm()) return;
                            this.isSubmitting = true;
                            document.getElementById('checkout-form').submit();
                        }
                    }
                }
            </script>

            <form x-ref="deleteAddressForm" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </body>
        </html>
