<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Optima FM - Powerful Facility Management. Completely Free.' }}</title>
    <meta name="description" content="Streamline operations, track assets, and manage work orders without the enterprise price tag. The free facility management platform for everyone.">
    <link rel="canonical" href="https://optimafm.org">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://optimafm.org/">
    <meta property="og:title" content="Optima FM - Powerful Facility Management. Completely Free.">
    <meta property="og:description" content="Streamline operations, track assets, and manage work orders without the enterprise price tag. The free facility management platform for everyone.">
    <meta property="og:image" content="{{ asset('images/logo-white.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://optimafm.org/">
    <meta property="twitter:title" content="Optima FM - Powerful Facility Management. Completely Free.">
    <meta property="twitter:description" content="Streamline operations, track assets, and manage work orders without the enterprise price tag. The free facility management platform for everyone.">
    <meta property="twitter:image" content="{{ asset('images/logo-white.png') }}">

    <!-- Structured Data -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "SoftwareApplication",
      "name": "Optima FM",
      "applicationCategory": "BusinessApplication",
      "operatingSystem": "Web",
      "offers": {
        "@@type": "Offer",
        "price": "0",
        "priceCurrency": "USD"
      },
      "description": "Powerful facility management software that is completely free. Track assets, manage work orders, and streamline operations.",
      "image": "{{ asset('images/logo-white.png') }}",
      "featureList": "Work Order Management, Asset Tracking, Preventive Maintenance, Multi-Tenancy"
    }
    </script>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('images/icons/rsz_icon_64.png') }}">
    <link rel="icon" type="image/png" sizes="128x128" href="{{ asset('images/icons/icon_128.png') }}">
    <link rel="icon" type="image/png" sizes="256x256" href="{{ asset('images/icons/icon_256.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon_256.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <x-google-analytics />
    
    @stack('styles')
</head>
<body class="font-sans antialiased text-slate-600 bg-white" x-data="{ mobileMenuOpen: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">

    <!-- Floating CTA (appears on scroll) -->
    <div
        x-show="scrolled"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-6 right-6 z-50 md:hidden"
    >
        <a href="{{ route('signup') }}" class="flex items-center gap-2 px-5 py-3 text-sm font-bold text-white bg-gradient-to-r from-teal-500 to-emerald-500 rounded-full shadow-lg shadow-teal-500/30">
            Get Started
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="fixed w-full z-50 transition-all duration-500" :class="scrolled ? 'bg-slate-900/95 backdrop-blur-md shadow-lg shadow-slate-900/10' : 'bg-slate-900/80 backdrop-blur-md'" >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-3">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('images/logo-white.png') }}" alt="Optima FM" class="h-10 w-auto">
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}#features" class="text-sm font-medium text-slate-300 hover:text-white transition-colors relative group">
                        Features
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-teal-400 transition-all group-hover:w-full"></span>
                    </a>
                    <a href="{{ route('home') }}#solutions" class="text-sm font-medium text-slate-300 hover:text-white transition-colors relative group">
                        Solutions
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-teal-400 transition-all group-hover:w-full"></span>
                    </a>
                    <a href="{{ route('home') }}#contact" class="text-sm font-medium text-slate-300 hover:text-white transition-colors relative group">
                        Contact
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-teal-400 transition-all group-hover:w-full"></span>
                    </a>
                </div>

                <!-- Auth Buttons -->
                <div class="hidden md:flex items-center gap-4">
                    @auth
                        <a href="{{ route('user.home') }}" class="text-sm font-semibold text-white hover:text-teal-400 transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" wire:navigate class="text-sm font-semibold text-slate-300 hover:text-white transition-colors">Log in</a>
                        <a href="{{ route('signup') }}" wire:navigate class="px-5 py-2.5 text-sm font-semibold text-white bg-teal-500 hover:bg-teal-400 rounded-full transition-all hover:scale-105 shadow-lg shadow-teal-900/20 btn-shine">
                            Get Started Free
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-300 hover:text-white p-2">
                        <svg x-show="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="mobileMenuOpen" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="md:hidden bg-slate-900/98 backdrop-blur-lg border-t border-slate-800">
            <div class="px-4 py-6 space-y-4">
                <a href="{{ route('home') }}#features" @click="mobileMenuOpen = false" class="block text-base font-medium text-slate-300 hover:text-white transition-colors py-2">Features</a>
                <a href="{{ route('home') }}#solutions" @click="mobileMenuOpen = false" class="block text-base font-medium text-slate-300 hover:text-white transition-colors py-2">Solutions</a>
                <a href="{{ route('home') }}#contact" @click="mobileMenuOpen = false" class="block text-base font-medium text-slate-300 hover:text-white transition-colors py-2">Contact</a>
                <div class="pt-4 border-t border-slate-800 space-y-3">
                    @auth
                        <a href="{{ route('user.home') }}" class="block w-full text-center px-5 py-3 text-base font-semibold text-white bg-teal-500 rounded-xl">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="block w-full text-center px-5 py-3 text-base font-semibold text-slate-300 border border-slate-700 rounded-xl hover:bg-slate-800 transition-colors">Log in</a>
                        <a href="{{ route('signup') }}" class="block w-full text-center px-5 py-3 text-base font-semibold text-white bg-gradient-to-r from-teal-500 to-emerald-500 rounded-xl">Get Started Free</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{ $slot }}

    <!-- Footer -->
    <footer class="bg-slate-900 border-t border-slate-800 text-slate-400 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center justify-center mb-12 text-center">
                <div class="flex items-center gap-2 mb-4">
                    <img src="{{ asset('images/logo-white.png') }}" alt="Optima FM" class="h-8 w-auto">
                </div>
                <p class="text-sm mb-6 max-w-sm">Empowering facility teams with modern, free tools.</p>

                <!-- Contact Emails -->
                <div class="flex flex-col sm:flex-row items-center gap-4 mb-6 text-sm">
                    <a href="mailto:support@optimafm.org" class="flex items-center gap-2 text-slate-400 hover:text-teal-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        support@optimafm.org
                    </a>
                    <span class="hidden sm:inline text-slate-700">|</span>
                    <a href="mailto:features@optimafm.org" class="flex items-center gap-2 text-slate-400 hover:text-teal-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        features@optimafm.org
                    </a>
                </div>

                <div class="flex gap-3">
                    <a href="https://x.com/OptimaFM_NG" target="_blank" class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center hover:bg-teal-500 hover:text-white transition-all">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" /></svg>
                    </a>
                </div>
            </div>
            <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm"> {{ date('Y') }} Optima FM. All rights reserved.</p>
                <div class="flex gap-6 text-sm">
                    <a href="{{ route('privacy-policy') }}" class="hover:text-white transition-colors">Privacy Policy</a>
                    <a href="{{ route('terms-of-use') }}" class="hover:text-white transition-colors">Terms of Use</a>
                </div>
                <p class="text-sm text-slate-500">Made with <span class="text-red-400">&hearts;</span> for facility teams everywhere</p>
            </div>
        </div>
    </footer>
</body>
</html>
