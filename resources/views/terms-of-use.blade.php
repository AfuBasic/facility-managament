<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terms of Use - Optima FM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <x-google-analytics />
</head>
<body class="antialiased font-sans text-slate-600 bg-slate-50" x-data="{ mobileMenuOpen: false }">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-slate-900 border-b border-slate-800">
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
                    <a href="{{ route('home') }}#features" class="text-sm font-medium text-slate-300 hover:text-white transition-colors">Features</a>
                    <a href="{{ route('home') }}#solutions" class="text-sm font-medium text-slate-300 hover:text-white transition-colors">Solutions</a>
                    <a href="{{ route('home') }}#contact" class="text-sm font-medium text-slate-300 hover:text-white transition-colors">Contact</a>
                </div>

                <!-- Auth Buttons -->
                <div class="hidden md:flex items-center gap-4">
                    @auth
                        <a href="{{ route('user.home') }}" class="text-sm font-semibold text-white hover:text-teal-400 transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-300 hover:text-white transition-colors">Log in</a>
                        <a href="{{ route('signup') }}" class="px-5 py-2.5 text-sm font-semibold text-white bg-teal-500 hover:bg-teal-400 rounded-full transition-all hover:scale-105 shadow-lg shadow-teal-900/20">
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
        <div x-show="mobileMenuOpen" x-cloak class="md:hidden bg-slate-900 border-t border-slate-800">
            <div class="px-4 py-6 space-y-4">
                <a href="{{ route('home') }}#features" class="block text-base font-medium text-slate-300 hover:text-white transition-colors py-2">Features</a>
                <a href="{{ route('home') }}#solutions" class="block text-base font-medium text-slate-300 hover:text-white transition-colors py-2">Solutions</a>
                <a href="{{ route('home') }}#contact" class="block text-base font-medium text-slate-300 hover:text-white transition-colors py-2">Contact</a>
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

    <!-- Header -->
    <header class="bg-slate-900 pt-32 pb-12 lg:pt-48 lg:pb-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-6">Terms of Use</h1>
            <p class="text-slate-400 text-lg">Last updated: {{ date('F d, Y') }}</p>
        </div>
    </header>

    <!-- Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
        <div class="prose prose-lg prose-slate max-w-none">
            <h2>1. Acceptance of Terms</h2>
            <p>By accessing and using Optima FM ("the Service"), you accept and agree to be bound by the terms and provision of this agreement. In addition, when using these particular services, you shall be subject to any posted guidelines or rules applicable to such services.</p>

            <h2>2. Provision of Services</h2>
            <p>You agree and acknowledge that Optima FM is entitled to modify, improve or discontinue any of its services at its sole discretion and without notice to you even if it may result in you being prevented from accessing any information contained in it.</p>

            <h2>3. Proprietary Rights</h2>
            <p>You acknowledge and agree that Optima FM may contain proprietary and confidential information including trademarks, service marks and patents protected by intellectual property laws and international intellectual property treaties. Optima FM authorizes you to view and make a single copy of portions of its content for offline, personal, non-commercial use. Our content may not be sold, reproduced, or distributed without our written permission.</p>

            <h2>4. Submitted Content</h2>
            <p>When you submit content to Optima FM you simultaneously grant Optima FM an irrevocable, worldwide, royalty free license to publish, display, modify, distribute and syndicate your content worldwide. You confirm and warrant that you have the required authority to grant the above license to Optima FM.</p>

            <h2>5. Termination of Agreement</h2>
            <p>The Terms of this agreement will continue to apply in perpetuity until terminated by either party without notice at any time for any reason. Terms that are to continue in perpetuity shall be unaffected by the termination of this agreement.</p>

            <h2>6. Disclaimer of Warranties</h2>
            <p>You understand and agree that your use of Optima FM is entirely at your own risk and that our services are provided "As Is" and "As Available". Optima FM does not make any express or implied warranties, endorsements or representations whatsoever as to the operation of the Otipma FM website, information, content, materials, or products.</p>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 border-t border-slate-800 text-slate-400 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center justify-center mb-12 text-center">
                <div class="flex items-center gap-2 mb-4">
                    <img src="{{ asset('images/logo-white.png') }}" alt="Optima FM" class="h-8 w-auto">
                </div>
                <p class="text-sm mb-6 max-w-sm">Empowering facility teams with modern, free tools.</p>
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
            </div>
        </div>
    </footer>
</body>
</html>
