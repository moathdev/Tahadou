<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Tahadou — Eid Gift Exchange')</title>

    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎁</text></svg>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Tailwind CDN (replace with Vite build in production) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary:  { DEFAULT: '#7c3aed', light: '#a78bfa', dark: '#5b21b6' },
                        gold:     { DEFAULT: '#d97706', light: '#fbbf24' },
                    }
                }
            }
        }
    </script>
    @stack('styles')
</head>
<body class="min-h-screen bg-gradient-to-br from-violet-50 via-white to-amber-50 font-sans text-gray-800">

    <nav class="bg-white/80 backdrop-blur border-b border-violet-100 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-bold text-violet-700">
                🎁 <span>Tahadou</span>
            </a>
            <span class="text-sm text-gray-400">Eid Gift Exchange</span>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 py-10">
        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="mt-16 py-6 text-center text-xs text-gray-400 border-t border-gray-100">
        Tahadou © {{ date('Y') }} — Built with ❤️ by <a href="https://nit.sa" class="text-violet-500 hover:underline">NIT</a>
    </footer>

    @stack('scripts')
</body>
</html>
