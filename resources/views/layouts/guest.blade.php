<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Miharbi Shop') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    boxShadow: {
                        soft: '0 20px 50px rgba(0,0,0,0.08)',
                        glow: '0 0 0 1px rgba(255,255,255,0.06), 0 30px 60px rgba(0,0,0,0.85)'
                    }
                }
            }
        }
    </script>

    <!-- GLOBAL THEME INIT -->
   
</head>

<body class="
    min-h-screen flex items-center justify-center px-4
    bg-gradient-to-br
        from-[#f7f7f5] to-[#ececea]
    dark:from-[#0a0b0d] dark:to-[#050507]
    text-[#1b1b18] dark:text-[#f1f1f1]
">

    <!-- THEME TOGGLE -->

    <!-- CARD -->
    <div class="
        relative w-full max-w-md
        rounded-3xl p-10
        bg-white/85
        dark:bg-[#0f1115]/95
        backdrop-blur-xl
        border border-black/5
        dark:border-white/10
        shadow-soft dark:shadow-glow
    ">

      

        <!-- SLOT -->
        <div class="space-y-6">
            {{ $slot }}
        </div>

        <!-- FOOTER -->
        <div class="mt-10 text-center text-xs text-gray-400 dark:text-gray-500">
            © {{ date('Y') }} Miharbi
        </div>
    </div>


</body>
</html>
