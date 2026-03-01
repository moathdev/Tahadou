<!DOCTYPE html>
@php
    $isAr  = app()->getLocale() === 'ar';
    $dir   = $isAr ? 'rtl' : 'ltr';
    $lang  = $isAr ? 'ar' : 'en';
    $font  = $isAr ? 'Noto Sans Arabic' : 'Inter';
    $otherLocale = $isAr ? 'en' : 'ar';
    $otherLabel  = $isAr ? 'English' : 'عربي';
@endphp
<html lang="{{ $lang }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', __('app.app_name') . ' — ' . __('app.tagline'))</title>

    @php
        $metaDesc = $isAr
            ? 'تهادوا — نظام مفتوح المصدر لتنظيم تبادل الهدايا بين الأهل والأصدقاء. أنشئ مجموعة، شارك الرابط، واسحب القرعة بضغطة واحدة.'
            : 'Tahadou — open-source gift exchange platform. Create a group, share a link, run the draw, and send WhatsApp assignments in minutes.';
        $metaTitle  = __('app.app_name') . ' — ' . __('app.tagline');
        $metaUrl    = config('app.url');
        $metaLocale = $isAr ? 'ar_SA' : 'en_US';
    @endphp

    <!-- SEO -->
    <meta name="description" content="@yield('meta_description', $metaDesc)" />
    <meta name="robots" content="index, follow" />
    <link rel="canonical" href="{{ $metaUrl }}" />

    <!-- Open Graph -->
    <meta property="og:type"        content="website" />
    <meta property="og:site_name"   content="{{ __('app.app_name') }}" />
    <meta property="og:title"       content="@yield('meta_title', $metaTitle)" />
    <meta property="og:description" content="@yield('meta_description', $metaDesc)" />
    <meta property="og:url"         content="{{ $metaUrl }}" />
    <meta property="og:locale"      content="{{ $metaLocale }}" />

    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary" />
    <meta name="twitter:site"        content="@moathdev" />
    <meta name="twitter:title"       content="@yield('meta_title', $metaTitle)" />
    <meta name="twitter:description" content="@yield('meta_description', $metaDesc)" />

    @stack('meta')

    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎁</text></svg>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" />
    @if($isAr)
        <link href="https://fonts.bunny.net/css?family=noto-sans-arabic:400,500,600,700" rel="stylesheet" />
    @else
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    @endif

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['{{ $font }}', 'sans-serif'] },
                    colors: {
                        primary: { DEFAULT: '#7c3aed', light: '#a78bfa', dark: '#5b21b6' },
                        gold:    { DEFAULT: '#d97706', light: '#fbbf24' },
                    }
                }
            }
        }
    </script>
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col bg-gradient-to-br from-violet-50 via-white to-amber-50 font-sans text-gray-800">

    <nav class="bg-white/80 backdrop-blur border-b border-violet-100 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-bold text-violet-700">
                🎁 <span>{{ __('app.app_name') }}</span>
            </a>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-400 hidden sm:inline">{{ __('app.tagline') }}</span>
                <a
                    href="{{ route('lang.switch', $otherLocale) }}"
                    class="text-xs px-2 py-1 rounded-lg bg-violet-100 text-violet-600 hover:bg-violet-200 transition font-medium"
                >
                    {{ $otherLabel }}
                </a>
                <a
                    href="{{ route('admin.find') }}"
                    class="text-xs px-2 py-1 rounded-lg bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-700 transition font-medium"
                    title="{{ __('app.admin_login') }}"
                >
                    🔐 {{ __('app.admin_login') }}
                </a>
            </div>
        </div>
    </nav>

    <main class="flex-1 max-w-4xl w-full mx-auto px-4 py-10">
        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                <ul class="{{ $isAr ? 'list-none' : 'list-disc list-inside' }} space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="py-6 text-center text-xs text-gray-400 border-t border-gray-100 mt-auto">
        {{ __('app.app_name') }} © {{ date('Y') }} — {{ __('app.footer') }}
        <a href="{{ __('app.footer_url') }}" class="text-violet-500 hover:underline">{{ __('app.footer_author') }}</a>
    </footer>

    @stack('scripts')
</body>
</html>
