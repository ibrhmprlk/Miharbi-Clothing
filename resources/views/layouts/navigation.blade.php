<nav x-data="{ open: false }" class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- Logo + Links -->
            <div class="flex items-center">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                </a>

                <!-- Desktop Links -->
                <div class="hidden sm:flex sm:ml-10 space-x-8">
                    <a href="{{ route('dashboard') }}"
                       class="text-gray-700 hover:text-black font-medium border-b-2 {{ request()->routeIs('dashboard') ? 'border-black' : 'border-transparent' }} px-1 pt-1">
                        Dashboard
                    </a>

                    <a href="{{ route('profile.edit') }}"
                       class="text-gray-700 hover:text-black font-medium border-b-2 {{ request()->routeIs('profile.edit') ? 'border-black' : 'border-transparent' }} px-1 pt-1">
                        Profile
                    </a>
                </div>
            </div>

            <!-- Right Side -->
           <div class="hidden sm:flex sm:items-center space-x-4">
    @auth
        <span class="text-gray-600 text-sm">
            {{ Auth::user()->name }}
        </span>
        <form method="POST" action="{{ route('welcome') }}">
            @csrf
            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Log Out</button>
        </form>
    @else
        <span class="text-gray-600 text-sm font-medium">Visitor Mode</span>
        <a href="{{ route('login') }}" class="text-indigo-600 text-sm font-medium">Login</a>
    @endauth
</div>
            <!-- Mobile Button -->
            <div class="sm:hidden flex items-center">
                <button @click="open = !open"
                    class="p-2 rounded-md text-gray-600 hover:bg-gray-100">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="open" class="sm:hidden px-4 pb-4 space-y-2">
        <a href="{{ route('dashboard') }}"
           class="block text-gray-700 hover:text-black">
            Dashboard
        </a>
        

        <a href="{{ route('profile.edit') }}"
           class="block text-gray-700 hover:text-black">
            Profile
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="block text-red-600 hover:text-red-800">
                Log Out
            </button>
        </form>
    </div>
</nav>
