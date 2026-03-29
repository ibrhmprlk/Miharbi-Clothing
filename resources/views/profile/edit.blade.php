<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Miharbi Clothing</title>
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

        /* PROFILE PAGE STYLES */
        .profile-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 24px;
        }

        @media (min-width: 640px) {
            .profile-container {
                padding: 40px 24px;
            }
        }

        .profile-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .profile-header-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 32px 24px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .profile-header-gradient::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            pointer-events: none;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 32px;
            color: #4f46e5;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }

        .profile-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 4px;
            position: relative;
            z-index: 1;
        }

        .profile-subtitle {
            font-size: 14px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .section-header {
            padding: 24px 24px 0 24px;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 24px;
        }

        @media (min-width: 640px) {
            .section-header {
                padding: 32px 32px 0 32px;
            }
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-description {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 24px;
        }

        .section-body {
            padding: 0 24px 32px 24px;
        }

        @media (min-width: 640px) {
            .section-body {
                padding: 0 32px 40px 32px;
            }
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 500;
            color: #111827;
            transition: all 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #4f46e5;
            background: white;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-input::placeholder {
            color: #9ca3af;
        }

        .form-error {
            color: #ef4444;
            font-size: 13px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-danger {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(239, 68, 68, 0.3);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #f1f5f9;
            color: #475569;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
        }

        .alert-success {
            background: #d1fae5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .verification-box {
            background: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 12px;
            padding: 16px;
            margin-top: 16px;
        }

        .verification-box p {
            color: #92400e;
            font-size: 14px;
            line-height: 1.5;
        }

        .verification-box button {
            background: transparent;
            color: #d97706;
            font-weight: 600;
            text-decoration: underline;
            border: none;
            cursor: pointer;
            font-size: 14px;
            margin-top: 8px;
        }

        .verification-box button:hover {
            color: #b45309;
        }

        .danger-zone {
            border: 2px solid #fee2e2;
            background: #fef2f2;
        }

        .danger-zone .section-header {
            border-bottom-color: #fecaca;
        }

        .danger-zone .section-title {
            color: #dc2626;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-content {
            background: white;
            border-radius: 24px;
            max-width: 480px;
            width: 100%;
            padding: 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .modal-text {
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .password-confirm-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 15px;
        }

        .password-confirm-input:focus {
            outline: none;
            border-color: #4f46e5;
        }

        /* SCROLL TO TOP BUTTON - HEM MOBIL HEM PC */
        .scroll-to-top {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 9998;
            opacity: 0;
            transform: translateY(100px);
            pointer-events: none;
            border: none;
            outline: none;
        }

        .scroll-to-top.visible {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .scroll-to-top:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(79, 70, 229, 0.5);
        }

        .scroll-to-top:active {
            transform: translateY(-2px);
        }

        /* PC'de biraz daha büyük */
        @media (min-width: 768px) {
            .scroll-to-top {
                width: 56px;
                height: 56px;
                font-size: 24px;
                bottom: 32px;
                right: 32px;
            }
        }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body x-data="{ 
    mobileMenu: false,
    showDeleteModal: false,
    password: '',
    showScrollTop: false,
    lastScrollY: 0,
    scrollThreshold: 300,
    
    init() {
        @if (session('status') === 'profile-updated')
            this.$dispatch('toast', { type: 'success', title: 'Success', message: 'Profile updated successfully!' });
        @endif
        
        @if (session('status') === 'password-updated')
            this.$dispatch('toast', { type: 'success', title: 'Success', message: 'Password updated successfully!' });
        @endif
        
        @if (session('status') === 'verification-link-sent')
            this.$dispatch('toast', { type: 'success', title: 'Email Sent', message: 'Verification link sent to your email!' });
        @endif
        
        this.setupScrollListener();
    },
    
    setupScrollListener() {
        let ticking = false;
        
        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    this.handleScroll();
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });
    },
    
    handleScroll() {
        const currentScrollY = window.pageYOffset;
        
        if (currentScrollY > this.scrollThreshold && currentScrollY > this.lastScrollY) {
            this.showScrollTop = true;
        }
        else if (currentScrollY < this.lastScrollY || currentScrollY < this.scrollThreshold) {
            this.showScrollTop = false;
        }
        
        this.lastScrollY = currentScrollY;
    },
    
    scrollToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        this.showScrollTop = false;
    }
}" x-init="init()">

<!-- Toast Notifications -->
<div class="fixed top-4 right-4 left-4 sm:left-auto z-50 flex flex-col gap-2 pointer-events-none">
    <template x-for="toast in $store.toasts.items" :key="toast.id">
        <div class="pointer-events-auto bg-white rounded-2xl shadow-lg border-l-4 p-4 flex items-center gap-3 animate-slide-in"
             :class="toast.type === 'success' ? 'border-green-500' : 'border-red-500'"
             x-show="true"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="w-10 h-10 rounded-full flex items-center justify-center"
                 :class="toast.type === 'success' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'">
                <i class="bi" :class="toast.type === 'success' ? 'bi-check-lg' : 'bi-exclamation-triangle-fill'"></i>
            </div>
            <div class="flex-1">
                <div class="font-semibold text-gray-900" x-text="toast.title"></div>
                <div class="text-sm text-gray-500" x-text="toast.message"></div>
            </div>
            <button @click="$store.toasts.remove(toast.id)" class="text-gray-400 hover:text-gray-600">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </template>
</div>

<!-- SCROLL TO TOP BUTTON -->
<button 
    @click="scrollToTop()"
    class="scroll-to-top"
    :class="showScrollTop ? 'visible' : ''"
    aria-label="Scroll to top"
    x-cloak>
    <i class="bi bi-arrow-up"></i>
</button>

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

<main class="profile-container">
    <div class="profile-card">
        <div class="profile-header-gradient">
            <div class="profile-avatar">
                <i class="bi bi-person-fill"></i>
            </div>
            <h1 class="profile-title">Profile Information</h1>
            <p class="profile-subtitle">Update your account details and email address</p>
        </div>

        <div class="section-body">
            <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                @csrf
            </form>

            <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                @csrf
                @method('patch')

                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="bi bi-person mr-1 text-indigo-500"></i>Full Name
                    </label>
                    <input 
                        id="name" 
                        name="name" 
                        type="text" 
                        class="form-input" 
                        value="{{ old('name', $user->name) }}" 
                        required 
                        autofocus 
                        autocomplete="name"
                        placeholder="Enter your full name"
                    >
                    @if($errors->get('name'))
                        <div class="form-error">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope mr-1 text-indigo-500"></i>Email Address
                    </label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        class="form-input" 
                        value="{{ old('email', $user->email) }}" 
                        required 
                        autocomplete="username"
                        placeholder="Enter your email address"
                    >
                    @if($errors->get('email'))
                        <div class="form-error">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ $errors->first('email') }}
                        </div>
                    @endif

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="verification-box">
                            <p>
                                <i class="bi bi-exclamation-triangle-fill mr-1"></i>
                                Your email address is not verified.
                            </p>
                            <button form="send-verification" type="submit">
                                Click here to resend verification email
                            </button>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-check-lg"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="profile-card">
        <div class="section-header">
            <h2 class="section-title">
                <i class="bi bi-shield-lock text-indigo-500"></i>
                Update Password
            </h2>
            <p class="section-description">
                Ensure your account is using a long, random password to stay secure.
            </p>
        </div>

        <div class="section-body">
            <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                @csrf
                @method('put')

                <div class="form-group">
                    <label for="update_password_current_password" class="form-label">
                        <i class="bi bi-key mr-1 text-indigo-500"></i>Current Password
                    </label>
                    <input 
                        id="update_password_current_password" 
                        name="current_password" 
                        type="password" 
                        class="form-input" 
                        autocomplete="current-password"
                        placeholder="Enter your current password"
                    >
                    @if($errors->updatePassword->get('current_password'))
                        <div class="form-error">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ $errors->updatePassword->first('current_password') }}
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="update_password_password" class="form-label">
                        <i class="bi bi-lock mr-1 text-indigo-500"></i>New Password
                    </label>
                    <input 
                        id="update_password_password" 
                        name="password" 
                        type="password" 
                        class="form-input" 
                        autocomplete="new-password"
                        placeholder="Enter new password"
                    >
                    @if($errors->updatePassword->get('password'))
                        <div class="form-error">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ $errors->updatePassword->first('password') }}
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="update_password_password_confirmation" class="form-label">
                        <i class="bi bi-lock-fill mr-1 text-indigo-500"></i>Confirm Password
                    </label>
                    <input 
                        id="update_password_password_confirmation" 
                        name="password_confirmation" 
                        type="password" 
                        class="form-input" 
                        autocomplete="new-password"
                        placeholder="Confirm new password"
                    >
                    @if($errors->updatePassword->get('password_confirmation'))
                        <div class="form-error">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ $errors->updatePassword->first('password_confirmation') }}
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-check-lg"></i>
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="profile-card danger-zone">
        <div class="section-header">
            <h2 class="section-title">
                <i class="bi bi-trash"></i>
                Delete Account
            </h2>
            <p class="section-description">
                Once your account is deleted, all of its resources and data will be permanently deleted. 
                Please download any data or information that you wish to retain.
            </p>
        </div>

        <div class="section-body">
            <button @click="showDeleteModal = true" class="btn-danger">
                <i class="bi bi-trash"></i>
                Delete Account
            </button>
        </div>
    </div>
</main>

<div x-show="showDeleteModal" 
     x-cloak 
     class="modal-overlay"
     @keydown.escape.window="showDeleteModal = false">
    <div class="modal-content" @click.away="showDeleteModal = false">
        <h3 class="modal-title text-red-600">
            <i class="bi bi-exclamation-triangle-fill mr-2"></i>
            Are you sure?
        </h3>
        <p class="modal-text">
            Once your account is deleted, all of its resources and data will be permanently deleted. 
            Please enter your password to confirm you would like to permanently delete your account.
        </p>

        <form method="post" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')

            <input 
                type="password" 
                name="password" 
                x-model="password"
                class="password-confirm-input" 
                placeholder="Enter your password"
                required
            >

            @if($errors->userDeletion->get('password'))
                <div class="form-error mb-4">
                    <i class="bi bi-exclamation-circle"></i>
                    {{ $errors->userDeletion->first('password') }}
                </div>
            @endif

            <div class="flex gap-3 justify-end">
                <button type="button" @click="showDeleteModal = false" class="btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn-danger" :disabled="!password">
                    Delete Account
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('toasts', {
            items: [],
            add(type, title, message) {
                const id = Date.now();
                this.items.push({ id, type, title, message });
                setTimeout(() => this.remove(id), 4000);
            },
            remove(id) {
                this.items = this.items.filter(item => item.id !== id);
            }
        });
    });
</script>

</body>
</html>