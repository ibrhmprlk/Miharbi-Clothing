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

    <!-- Register Başlık - Ortada -->
    <div class="mb-8 text-center pt-4">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Register</h1>
        <div class="mt-2 w-12 h-1 bg-black dark:bg-white mx-auto rounded-full"></div>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label value="Name Surname" />
            <x-text-input
                name="name"
                type="text"
                required
                class="w-full mt-1 px-4 py-3 rounded-xl
                       bg-gray-50 dark:bg-[#1a1a1a]
                       border border-gray-300 dark:border-[#333]
                       focus:ring-2 focus:ring-black dark:focus:ring-white"
            />
        </div>

        <div>
            <x-input-label value="Email" />
            <x-text-input
                name="email"
                type="email"
                required
                class="w-full mt-1 px-4 py-3 rounded-xl
                       bg-gray-50 dark:bg-[#1a1a1a]
                       border border-gray-300 dark:border-[#333]
                       focus:ring-2 focus:ring-black dark:focus:ring-white"
            />
        </div>

        <div>
            <x-input-label value="Password" />
            <x-text-input
                name="password"
                type="password"
                required
                class="w-full mt-1 px-4 py-3 rounded-xl
                       bg-gray-50 dark:bg-[#1a1a1a]
                       border border-gray-300 dark:border-[#333]
                       focus:ring-2 focus:ring-black dark:focus:ring-white"
            />
        </div>

        <div>
            <x-input-label value="Password Confirmation" />
            <x-text-input
                name="password_confirmation"
                type="password"
                required
                class="w-full mt-1 px-4 py-3 rounded-xl
                       bg-gray-50 dark:bg-[#1a1a1a]
                       border border-gray-300 dark:border-[#333]
                       focus:ring-2 focus:ring-black dark:focus:ring-white"
            />
        </div>

        <div class="flex justify-between items-center pt-2">
            <a href="{{ route('login') }}"
               class="text-sm text-gray-500 hover:text-black dark:hover:text-white">
                I already have an account!
            </a>

            <button
                class="px-6 py-3 rounded-xl
                       bg-black text-white
                       dark:bg-white dark:text-black">
                Register
            </button>
        </div>
    </form>
</x-guest-layout>
