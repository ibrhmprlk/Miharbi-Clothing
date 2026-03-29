<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Miharbi Clothing | Collections</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
        
        body { 
            background-color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: #1e293b; 
            margin: 0; 
            overflow-x: hidden; 
        }
        
        html { overflow-y: scroll; }
        
        .hide-scrollbar::-webkit-scrollbar { display: none !important; }
        .hide-scrollbar { -ms-overflow-style: none !important; scrollbar-width: none !important; }
        
        /* NAVIGATION - DOKUNULMADI */
        .admin-nav { 
            background: white; 
            border-bottom: 1px solid #e2e8f0; 
            position: sticky; 
            top: 0; 
            z-index: 9999; 
            width: 100%;
        }
        
        .nav-item { 
            display: flex; align-items: center; gap: 8px; padding: 12px 16px; border-radius: 12px; 
            font-size: 14px; font-weight: 600; color: #64748b; transition: 0.2s; text-decoration: none; 
        }
        .nav-item:hover { background: #eef2ff; color: #4f46e5; }
        .nav-item.active { background: #4f46e5; color: white; }

        /* AUTH CARD STYLES */
        .auth-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .btn-indigo {
            background-color: #4f46e5;
            color: white;
            transition: all 0.2s ease;
        }
        .btn-indigo:hover {
            background-color: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
    </style>
</head>
<body x-data="{ mobileMenu: false }">

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

    <a href="{{ url('/') }}" class="nav-item">
        <i class="bi bi-grid-1x2 text-sm"></i>
        Dashboard
    </a>
    
  <a href="{{ url('/#about-section') }}" class="nav-item">
                <i class="bi bi-info-circle"></i>About Us
            </a>
    
          <a href="{{ url('/#contact-section') }}" class="nav-item">
                <i class="bi bi-envelope"></i>Contact
            </a>
    
    <a href="{{ url('adminpanel') }}" class="nav-item">
        <i class="bi bi-shield-lock text-sm"></i>
        Miharbi Clothing Admin
    </a>

</nav>
        </div>
        <nav x-show="mobileMenu" 
     x-cloak 
     @click.away="mobileMenu = false" 
     x-transition 
     class="md:hidden px-4 pb-4 space-y-1 bg-white border-t mt-2">

    <a href="{{ url('/') }}" class="nav-item mt-2">
        <i class="bi bi-grid-1x2"></i>
        Dashboard
    </a>

    <a href="{{ url('/login') }}" class="nav-item">
        <i class="bi bi-box-arrow-in-right"></i>
        Login
    </a>

    <a href="{{ url('/register') }}" class="nav-item">
        <i class="bi bi-person-plus"></i>
        Register
    </a>

    <a href="{{ url('/adminpanel') }}" class="nav-item">
        <i class="bi bi-shield-lock"></i>
        Miharbi Clothing Admin
    </a>

</nav>
    </div>
</header>

<main class="min-h-[80vh] flex items-center justify-center p-6">
    <div class="w-full max-w-md auth-card rounded-[2.5rem] p-10 border border-gray-200 shadow-xl">
        
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl mb-4 border border-indigo-100">
                <i class="bi bi-person text-3xl"></i>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900">Miharbi Clothing</h1>
            <p class="mt-2 text-sm font-medium text-slate-500 tracking-wide uppercase">
                
Modern · Minimal · Powerful
            </p>
        </div>

        <div class="space-y-4">
            <a href="{{ route('login') }}"
               class="block w-full text-center py-4 rounded-2xl btn-indigo font-bold tracking-wide transition shadow-lg">
                LOG IN
            </a>

            <a href="{{ route('register') }}"
               class="block w-full text-center py-4 rounded-2xl border-2 border-slate-100 hover:border-indigo-100 hover:bg-indigo-50/50 transition-all font-bold text-slate-700">
                REGISTER
            </a>

            <div class="relative py-4">
                <div class="absolute inset-0 flex items-center"><span class="w-full border-t border-slate-100"></span></div>
                <div class="relative flex justify-center text-xs uppercase"><span class="bg-white px-4 text-slate-400 font-bold">ADMINISTRATION PANEL</span></div>
            </div>

            <a href="{{ route('adminpanel') }}"
               class="flex items-center justify-center gap-2 w-full text-center py-3 rounded-2xl bg-slate-900 text-white font-bold hover:bg-black transition shadow-md text-sm">
                <i class="bi bi-cpu"></i> Miharbi Clothing Admin
            </a>
        </div>

        <div class="mt-10 text-center text-xs font-bold text-slate-400 tracking-widest">
            © {{ date('Y') }} MIHARBI CLOTHING
        </div>
    </div>
</main>

</body>
</html>