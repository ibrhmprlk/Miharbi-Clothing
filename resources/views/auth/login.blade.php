<x-guest-layout>
    <!-- Geri Dönüş Butonu - Sol Üstte -->
    <div class="absolute top-6 left-6">
        <a href="{{ url('userlogin') }}"
        class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-indigo-600 transition group">
            <svg xmlns="http://www.w3.org/2000/svg"
            class="w-5 h-5 group-hover:-translate-x-1 transition-transform"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Back</span>
        </a>
    </div>

    <!-- Login Başlık - Ortada -->
    <div class="mb-8 text-center pt-4">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Login</h1>
        <div class="mt-2 w-12 h-1 bg-black dark:bg-white mx-auto rounded-full"></div>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                class="block mt-1 w-full rounded-2xl
                       px-4 py-3
                       bg-white dark:bg-[#0f0f0f]
                       border-2 border-gray-300 dark:border-gray-700
                       text-base
                       text-gray-900 dark:text-gray-100
                       placeholder-gray-400 dark:placeholder-gray-500
                       focus:border-black dark:focus:border-white
                       focus:ring-0
                       transition"
            />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                class="block mt-1 w-full rounded-2xl
                       px-4 py-3
                       bg-white dark:bg-[#0f0f0f]
                       border-2 border-gray-300 dark:border-gray-700
                       text-base
                       text-gray-900 dark:text-gray-100
                       placeholder-gray-400 dark:placeholder-gray-500
                       focus:border-black dark:focus:border-white
                       focus:ring-0
                       transition"
            />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
