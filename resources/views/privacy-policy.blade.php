<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Privacy Policy - Optima FM</title>
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
            <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-6">Privacy Policy</h1>
            <p class="text-slate-400 text-lg">Last updated: {{ date('F d, Y') }}</p>
        </div>
    </header>

    <!-- Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
        <div class="prose prose-lg prose-slate max-w-none">
            <h2>1. Introduction</h2>
            <p>Welcome to Optima FM. We respect your privacy and are committed to protecting your personal data. This privacy policy will inform you as to how we look after your personal data when you visit our website and tell you about your privacy rights and how the law protects you.</p>

            <h2>2. Data We Collect</h2>
            <p>We may collect, use, store and transfer different kinds of personal data about you which we have grouped together follows:</p>
            <ul>
                <li><strong>Identity Data</strong> includes first name, last name, username or similar identifier.</li>
                <li><strong>Contact Data</strong> includes email address and telephone numbers.</li>
            </ul>

            <h2>3. How We Use Your Data</h2>
            <p>We will only use your personal data when the law allows us to. Most commonly, we will use your personal data in the following circumstances:</p>
            <ul>
                <li>Where we need to perform the contract we are about to enter into or have entered into with you.</li>
                <li>Where it is necessary for our legitimate interests (or those of a third party) and your interests and fundamental rights do not override those interests.</li>
                <li>Where we need to comply with a legal or regulatory obligation.</li>
            </ul>

            <h2>4. Data Security</h2>
            <p>We have put in place appropriate security measures to prevent your personal data from being accidentally lost, used or accessed in an unauthorized way, altered or disclosed. In addition, we limit access to your personal data to those employees, agents, contractors and other third parties who have a business need to know.</p>

            <h2>5. Contact Us</h2>
            <p>If you have any questions about this privacy policy or our privacy practices, please contact us at: <a href="mailto:support@optimafm.org">support@optimafm.org</a></p>
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
