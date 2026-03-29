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

    <!-- Forgot Password Başlık - Ortada -->
    <div class="mb-6 text-center pt-4">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Forgot Password</h1>
        <div class="mt-2 w-12 h-1 bg-black dark:bg-white mx-auto rounded-full"></div>
    </div>

    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400 text-center">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label
                for="email"
                :value="__('Email')"
                class="text-gray-700 dark:text-gray-300"
            />

            <x-text-input
                id="email"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
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

            <x-input-error
                :messages="$errors->get('email')"
                class="mt-2 text-red-500"
            />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button
                class="rounded-xl px-6 py-3
                       bg-black text-white
                       dark:bg-white dark:text-black
                       hover:opacity-90 transition">
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
