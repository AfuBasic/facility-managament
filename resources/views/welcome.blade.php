<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Optima FM - Powerful Facility Management. Completely Free.</title>
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

    <style>
        /* Hero perspective effect */
        .dashboard-perspective {
            transform: perspective(1000px) rotateX(2deg) rotateY(-2deg) rotateZ(1deg);
            box-shadow: 20px 20px 50px -5px rgba(0, 0, 0, 0.3);
        }

        /* Glow effects */
        .hero-glow {
            background: radial-gradient(ellipse at 50% 0%, rgba(20, 184, 166, 0.15), transparent 50%);
        }
        .hero-glow-2 {
            background: radial-gradient(ellipse at 80% 50%, rgba(52, 211, 153, 0.1), transparent 40%);
        }

        /* Floating animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        @keyframes float-slow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(2deg); }
        }
        @keyframes float-reverse {
            0%, 100% { transform: translateY(-10px); }
            50% { transform: translateY(0px); }
        }
        .animate-float { animation: float 4s ease-in-out infinite; }
        .animate-float-slow { animation: float-slow 6s ease-in-out infinite; }
        .animate-float-reverse { animation: float-reverse 5s ease-in-out infinite; }

        /* Gradient text animation */
        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient-shift 3s ease infinite;
        }

        /* Fade in up animation */
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.8s ease-out forwards;
        }
        .animation-delay-200 { animation-delay: 0.2s; opacity: 0; }
        .animation-delay-400 { animation-delay: 0.4s; opacity: 0; }
        .animation-delay-600 { animation-delay: 0.6s; opacity: 0; }

        /* Pulse ring */
        @keyframes pulse-ring {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(1.5); opacity: 0; }
        }
        .pulse-ring::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 9999px;
            border: 2px solid currentColor;
            animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Grid pattern */
        .grid-pattern {
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 50px 50px;
        }

        /* Shine effect on buttons */
        .btn-shine {
            position: relative;
            overflow: hidden;
        }
        .btn-shine::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to right,
                transparent,
                rgba(255,255,255,0.1),
                transparent
            );
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }
        @keyframes shine {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }

        /* Counter animation */
        .counter-value {
            display: inline-block;
            font-variant-numeric: tabular-nums;
        }

        /* Alpine cloak - hide elements until Alpine loads */
        [x-cloak] {
            display: none !important;
        }
    </style>

    <!-- Alpine.js (loaded before body for immediate availability) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <x-google-analytics />
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
                    <img src="{{ asset('images/logo-white.png') }}" alt="Optima FM" class="h-10 w-auto">
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-sm font-medium text-slate-300 hover:text-white transition-colors relative group">
                        Features
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-teal-400 transition-all group-hover:w-full"></span>
                    </a>
                    <a href="#solutions" class="text-sm font-medium text-slate-300 hover:text-white transition-colors relative group">
                        Solutions
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-teal-400 transition-all group-hover:w-full"></span>
                    </a>
                    <a href="#contact" class="text-sm font-medium text-slate-300 hover:text-white transition-colors relative group">
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
                <a href="#features" @click="mobileMenuOpen = false" class="block text-base font-medium text-slate-300 hover:text-white transition-colors py-2">Features</a>
                <a href="#solutions" @click="mobileMenuOpen = false" class="block text-base font-medium text-slate-300 hover:text-white transition-colors py-2">Solutions</a>
                <a href="#contact" @click="mobileMenuOpen = false" class="block text-base font-medium text-slate-300 hover:text-white transition-colors py-2">Contact</a>
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

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden bg-slate-900 grid-pattern">
        <!-- Glow Effects -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full hero-glow pointer-events-none"></div>
        <div class="absolute top-0 right-0 w-full h-full hero-glow-2 pointer-events-none"></div>

        <!-- Floating Decorations -->
        <div class="absolute top-32 left-10 w-20 h-20 bg-teal-500/10 rounded-full blur-2xl animate-float"></div>
        <div class="absolute top-64 right-20 w-32 h-32 bg-emerald-500/10 rounded-full blur-3xl animate-float-slow"></div>
        <div class="absolute bottom-32 left-1/4 w-16 h-16 bg-cyan-500/10 rounded-full blur-2xl animate-float-reverse"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-teal-500/10 border border-teal-500/20 text-teal-300 text-xs font-semibold uppercase tracking-wide mb-8 animate-fade-in-up">
                    <span class="relative w-2 h-2 rounded-full bg-teal-400 pulse-ring"></span>
                    Now available for everyone
                </div>

                <!-- Headline -->
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold text-white tracking-tight mb-6 leading-[1.1] animate-fade-in-up animation-delay-200">
                    Powerful FM Software. <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 via-emerald-400 to-cyan-400 animate-gradient">Completely Free.</span>
                </h1>

                <!-- Subheadline -->
                <p class="text-lg md:text-xl text-slate-400 mb-10 leading-relaxed max-w-2xl mx-auto animate-fade-in-up animation-delay-400">
                    Streamline operations, track assets, and manage work orders without the enterprise price tag.
                    The professional facility management platform built for <span class="text-white font-medium">everyone</span>.
                </p>

                <!-- CTAs -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in-up animation-delay-600">
                    <a href="{{ route('signup') }}" wire:navigate class="w-full sm:w-auto px-8 py-4 text-base font-bold text-white bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 rounded-full transition-all hover:scale-105 shadow-xl shadow-teal-500/25 flex items-center justify-center gap-2 btn-shine">
                        Start Using for Free
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    <a href="#features" class="w-full sm:w-auto px-8 py-4 text-base font-bold text-slate-300 hover:text-white border border-slate-700 hover:border-slate-500 rounded-full transition-all hover:bg-slate-800/50 flex items-center justify-center gap-2 group">
                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        See How It Works
                    </a>
                </div>

                <!-- Trust indicators -->
                <div class="mt-12 flex flex-wrap items-center justify-center gap-x-8 gap-y-4 text-sm text-slate-500">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        No credit card required
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Unlimited users
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Setup in minutes
                    </div>
                </div>
            </div>

            <!-- Dashboard Mockup -->
            <div class="relative max-w-5xl mx-auto mt-12 dashboard-perspective transform transition-transform hover:scale-[1.02] duration-700 animate-float-slow">
                <!-- Glow behind dashboard -->
                <div class="absolute -inset-4 bg-gradient-to-r from-teal-500/20 via-emerald-500/20 to-cyan-500/20 rounded-2xl blur-2xl opacity-50"></div>

                <!-- Browser Frame -->
                <div class="relative bg-slate-800 rounded-xl shadow-2xl border border-slate-700 overflow-hidden">
                    <!-- Browser Header -->
                    <div class="h-10 bg-slate-900 flex items-center px-4 gap-2 border-b border-slate-700">
                        <div class="flex gap-1.5">
                            <div class="w-3 h-3 rounded-full bg-red-500/80 hover:bg-red-500 transition-colors cursor-pointer"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-500/80 hover:bg-amber-500 transition-colors cursor-pointer"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-500/80 hover:bg-emerald-500 transition-colors cursor-pointer"></div>
                        </div>
                        <div class="mx-auto w-1/2 h-6 bg-slate-800 rounded-md border border-slate-700 flex items-center justify-center px-3">
                            <svg class="w-3 h-3 text-slate-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span class="text-[10px] text-slate-500 font-medium">optimafm.org/dashboard</span>
                        </div>
                    </div>

                    <!-- Dashboard Content -->
                    <div class="bg-slate-50 p-6 min-h-[500px]">
                        <div class="grid grid-cols-12 gap-6">
                            <!-- Sidebar Mockup -->
                            <div class="hidden md:block col-span-2 space-y-4">
                                <div class="h-8 w-24 bg-slate-200 rounded-md"></div>
                                <div class="space-y-2 mt-8">
                                    <div class="h-8 w-full bg-teal-100 rounded-md border-l-4 border-teal-500"></div>
                                    <div class="h-8 w-full bg-white rounded-md hover:bg-slate-50 transition-colors cursor-pointer"></div>
                                    <div class="h-8 w-full bg-white rounded-md hover:bg-slate-50 transition-colors cursor-pointer"></div>
                                    <div class="h-8 w-full bg-white rounded-md hover:bg-slate-50 transition-colors cursor-pointer"></div>
                                </div>
                            </div>

                            <!-- Main Area -->
                            <div class="col-span-12 md:col-span-10 space-y-6">
                                <!-- Top Stats -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 hover:shadow-md hover:border-slate-300 transition-all cursor-pointer group">
                                        <div class="flex items-center gap-3 mb-2">
                                            <div class="p-2 bg-rose-100 rounded-lg text-rose-600 group-hover:scale-110 transition-transform">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            </div>
                                            <span class="text-sm font-medium text-slate-500">Overdue Tasks</span>
                                        </div>
                                        <div class="text-2xl font-bold text-slate-900">12</div>
                                    </div>
                                    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 hover:shadow-md hover:border-slate-300 transition-all cursor-pointer group">
                                        <div class="flex items-center gap-3 mb-2">
                                            <div class="p-2 bg-amber-100 rounded-lg text-amber-600 group-hover:scale-110 transition-transform">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                            </div>
                                            <span class="text-sm font-medium text-slate-500">In Progress</span>
                                        </div>
                                        <div class="text-2xl font-bold text-slate-900">45</div>
                                    </div>
                                    <div class="bg-gradient-to-br from-teal-500 to-emerald-600 p-4 rounded-xl shadow-md text-white hover:shadow-lg hover:scale-[1.02] transition-all cursor-pointer">
                                        <div class="flex items-center gap-3 mb-2">
                                            <div class="p-2 bg-white/20 rounded-lg text-white">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            </div>
                                            <span class="text-sm font-medium text-teal-100">Completed</span>
                                        </div>
                                        <div class="text-2xl font-bold">128</div>
                                    </div>
                                </div>

                                <!-- Chart & List -->
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <!-- Chart Mockup -->
                                    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 h-64 flex flex-col">
                                        <div class="flex justify-between items-center mb-6">
                                            <div class="h-4 w-32 bg-slate-200 rounded"></div>
                                        </div>
                                        <div class="flex-1 flex items-end justify-between px-2 gap-2" x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 500)">
                                            <div class="w-full bg-teal-50 rounded-t-sm relative overflow-hidden" :class="loaded ? 'h-[40%]' : 'h-0'" style="transition: height 1s ease-out 0.1s">
                                                <div class="absolute inset-0 bg-gradient-to-t from-teal-500 to-teal-400"></div>
                                            </div>
                                            <div class="w-full bg-teal-50 rounded-t-sm relative overflow-hidden" :class="loaded ? 'h-[65%]' : 'h-0'" style="transition: height 1s ease-out 0.2s">
                                                <div class="absolute inset-0 bg-gradient-to-t from-teal-500 to-teal-400"></div>
                                            </div>
                                            <div class="w-full bg-teal-50 rounded-t-sm relative overflow-hidden" :class="loaded ? 'h-[45%]' : 'h-0'" style="transition: height 1s ease-out 0.3s">
                                                <div class="absolute inset-0 bg-gradient-to-t from-teal-500 to-teal-400"></div>
                                            </div>
                                            <div class="w-full bg-teal-50 rounded-t-sm relative overflow-hidden" :class="loaded ? 'h-[80%]' : 'h-0'" style="transition: height 1s ease-out 0.4s">
                                                <div class="absolute inset-0 bg-gradient-to-t from-emerald-500 to-emerald-400"></div>
                                            </div>
                                            <div class="w-full bg-teal-50 rounded-t-sm relative overflow-hidden" :class="loaded ? 'h-[55%]' : 'h-0'" style="transition: height 1s ease-out 0.5s">
                                                <div class="absolute inset-0 bg-gradient-to-t from-teal-500 to-teal-400"></div>
                                            </div>
                                            <div class="w-full bg-teal-50 rounded-t-sm relative overflow-hidden" :class="loaded ? 'h-[70%]' : 'h-0'" style="transition: height 1s ease-out 0.6s">
                                                <div class="absolute inset-0 bg-gradient-to-t from-teal-500 to-teal-400"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Recent List -->
                                    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 h-64 overflow-hidden">
                                        <div class="h-4 w-24 bg-slate-200 rounded mb-6"></div>
                                        <div class="space-y-4">
                                            <div class="flex items-center gap-3 hover:bg-slate-50 p-2 -mx-2 rounded-lg transition-colors cursor-pointer">
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600"></div>
                                                <div class="flex-1 space-y-1">
                                                    <div class="h-3 w-32 bg-slate-200 rounded"></div>
                                                    <div class="h-2 w-20 bg-slate-100 rounded"></div>
                                                </div>
                                                <div class="w-2 h-2 rounded-full bg-teal-500"></div>
                                            </div>
                                            <div class="flex items-center gap-3 hover:bg-slate-50 p-2 -mx-2 rounded-lg transition-colors cursor-pointer">
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-400 to-purple-600"></div>
                                                <div class="flex-1 space-y-1">
                                                    <div class="h-3 w-40 bg-slate-200 rounded"></div>
                                                    <div class="h-2 w-24 bg-slate-100 rounded"></div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3 hover:bg-slate-50 p-2 -mx-2 rounded-lg transition-colors cursor-pointer">
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-orange-600"></div>
                                                <div class="flex-1 space-y-1">
                                                    <div class="h-3 w-28 bg-slate-200 rounded"></div>
                                                    <div class="h-2 w-16 bg-slate-100 rounded"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-white border-b border-slate-100" x-data="{
        shown: false,
        workOrders: 0,
        facilities: 0,
        uptime: 0,
        init() {
            // Use IntersectionObserver for scroll-triggered animation
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !this.shown) {
                        this.animateCounters();
                    }
                });
            }, { threshold: 0.3 });
            observer.observe(this.$el);
        },
        animateCounters() {
            if (this.shown) return;
            this.shown = true;
            const duration = 2000;
            const steps = 60;
            const interval = duration / steps;

            let step = 0;
            const timer = setInterval(() => {
                step++;
                const progress = step / steps;
                const eased = 1 - Math.pow(1 - progress, 3);

                this.workOrders = Math.floor(2500 * eased);
                this.facilities = Math.floor(85 * eased);
                this.uptime = Math.floor(99.9 * eased * 10) / 10;

                if (step >= steps) clearInterval(timer);
            }, interval);
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-4xl md:text-5xl font-extrabold text-slate-900">
                        <span class="counter-value" x-text="workOrders.toLocaleString()">0</span>+
                    </div>
                    <div class="mt-2 text-sm font-medium text-slate-500 uppercase tracking-wide">Work Orders Completed</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-extrabold text-slate-900">
                        <span class="counter-value" x-text="facilities.toLocaleString()">0</span>+
                    </div>
                    <div class="mt-2 text-sm font-medium text-slate-500 uppercase tracking-wide">Facilities Managed</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-teal-500 to-emerald-500">
                        <span class="counter-value" x-text="uptime">0</span>%
                    </div>
                    <div class="mt-2 text-sm font-medium text-slate-500 uppercase tracking-wide">Uptime SLA</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-extrabold text-slate-900">$0</div>
                    <div class="mt-2 text-sm font-medium text-slate-500 uppercase tracking-wide">Forever Free</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-white relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-50 text-teal-600 text-xs font-semibold uppercase tracking-wide mb-4">
                    Features
                </div>
                <h2 class="text-3xl font-extrabold text-slate-900 md:text-5xl">Everything you need. Nothing you don't.</h2>
                <p class="mt-4 text-lg text-slate-500">Optima FM gives you enterprise-grade tools without the complexity or cost.</p>
            </div>

            <!-- Bento Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Work Order Flow -->
                <div class="col-span-1 md:col-span-2 bg-gradient-to-br from-slate-50 to-slate-100/50 rounded-3xl p-8 border border-slate-200/50 hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-teal-100/50 to-transparent rounded-full -mr-32 -mt-32 group-hover:scale-110 transition-transform duration-500"></div>

                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-3 bg-blue-100 text-blue-600 rounded-xl">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900">Visual Work Order Pipeline</h3>
                        </div>
                        <p class="text-slate-500 mb-8 max-w-md">Track every job from request to resolution with our intuitive status flow. Never lose track of a task again.</p>

                        <!-- Pipeline Widget -->
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="flex items-center gap-2 opacity-50">
                                <span class="w-3 h-3 rounded-full bg-slate-300"></span>
                                <span class="text-sm font-semibold text-slate-500">Request</span>
                            </div>
                            <div class="h-0.5 w-8 bg-slate-200 hidden sm:block"></div>
                            <div class="flex flex-col items-center gap-2">
                                <div class="px-4 py-1.5 bg-amber-100 text-amber-700 text-xs font-bold uppercase rounded-full border border-amber-200 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    In Progress
                                </div>
                                <div class="h-1.5 w-20 bg-amber-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-amber-500 w-2/3 rounded-full"></div>
                                </div>
                            </div>
                            <div class="h-0.5 w-8 bg-slate-200 hidden sm:block"></div>
                            <div class="flex items-center gap-2">
                                <span class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shadow-sm">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </span>
                                <span class="text-sm font-semibold text-slate-900">Complete</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SLA Tracking -->
                <div class="col-span-1 bg-slate-900 rounded-3xl p-8 border border-slate-800 hover:shadow-2xl hover:shadow-slate-900/50 transition-all duration-300 relative overflow-hidden text-white flex flex-col justify-between group">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-teal-500/20 rounded-full blur-3xl -mr-20 -mt-20 group-hover:bg-teal-500/30 transition-colors"></div>
                    <div class="absolute bottom-0 left-0 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl -ml-16 -mb-16"></div>

                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-3 bg-teal-500/20 text-teal-300 rounded-xl">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <h3 class="text-xl font-bold">SLA Tracking</h3>
                        </div>
                        <p class="text-slate-400 text-sm">Never miss a deadline with automated alerts.</p>
                    </div>

                    <!-- Gauge -->
                    <div class="relative flex items-center justify-center py-8" x-data="{
                        value: 0,
                        init() {
                            const observer = new IntersectionObserver((entries) => {
                                entries.forEach(entry => {
                                    if (entry.isIntersecting && this.value === 0) {
                                        setTimeout(() => this.value = 98, 300);
                                    }
                                });
                            }, { threshold: 0.5 });
                            observer.observe(this.$el);
                        }
                    }">
                        <svg class="w-40 h-20 overflow-visible" viewBox="0 0 100 50">
                            <path d="M 10 50 A 40 40 0 0 1 90 50" fill="none" stroke="#334155" stroke-width="8" stroke-linecap="round" />
                            <path d="M 10 50 A 40 40 0 0 1 75 22" fill="none" stroke="url(#gaugeGradient)" stroke-width="8" stroke-linecap="round"
                                  :stroke-dasharray="value > 0 ? '110 110' : '0 110'"
                                  style="transition: stroke-dasharray 1.5s ease-out" />
                            <defs>
                                <linearGradient id="gaugeGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" stop-color="#14b8a6" />
                                    <stop offset="100%" stop-color="#10b981" />
                                </linearGradient>
                            </defs>
                        </svg>
                        <div class="absolute bottom-2 text-center">
                            <div class="text-4xl font-bold tracking-tighter" x-text="value + '%'">0%</div>
                            <div class="text-[10px] text-teal-400 uppercase tracking-widest mt-1">Compliance</div>
                        </div>
                    </div>
                </div>

                <!-- Real-time Updates -->
                <div class="col-span-1 bg-white rounded-3xl p-8 border border-slate-200 hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 relative overflow-hidden">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-3 bg-rose-100 text-rose-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">Real-time Updates</h3>
                    </div>
                    <p class="text-slate-500 text-sm mb-6">Instant notifications keep your team in sync.</p>

                    <!-- Notification Animation -->
                    <div class="space-y-3">
                        <div class="flex gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100 animate-pulse">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex-shrink-0"></div>
                            <div>
                                <div class="text-xs font-semibold text-slate-900">Work Order #1024</div>
                                <div class="text-[11px] text-slate-500">Status changed to "In Progress"</div>
                            </div>
                        </div>
                        <div class="flex gap-3 flex-row-reverse">
                            <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center text-[10px] font-bold text-teal-600 flex-shrink-0">You</div>
                            <div class="bg-teal-500 text-white px-4 py-2 rounded-2xl rounded-tr-sm text-xs shadow-lg shadow-teal-500/20">
                                On my way! ETA 15 mins
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Asset Management -->
                <div class="col-span-1 md:col-span-2 bg-gradient-to-br from-indigo-50 to-slate-50 rounded-3xl p-8 border border-slate-200/50 hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300">
                    <div class="flex flex-col md:flex-row items-start md:items-center gap-8">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-3 bg-indigo-100 text-indigo-600 rounded-xl">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                </div>
                                <h3 class="text-xl font-bold text-slate-900">Complete Asset Lifecycle</h3>
                            </div>
                            <p class="text-slate-500">From procurement to disposal, maintain a complete audit trail of all your facility equipment with QR codes, maintenance history, and depreciation tracking.</p>
                        </div>

                        <!-- Asset Lifecycle Visual -->
                        <div class="flex items-center gap-2 flex-wrap justify-center">
                            <div class="flex flex-col items-center gap-1">
                                <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                </div>
                                <span class="text-[10px] font-medium text-slate-500">Add</span>
                            </div>
                            <svg class="w-4 h-4 text-slate-300 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            <div class="flex flex-col items-center gap-1">
                                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                </div>
                                <span class="text-[10px] font-medium text-slate-500">QR Tag</span>
                            </div>
                            <svg class="w-4 h-4 text-slate-300 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            <div class="flex flex-col items-center gap-1">
                                <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                </div>
                                <span class="text-[10px] font-medium text-slate-500">Maintain</span>
                            </div>
                            <svg class="w-4 h-4 text-slate-300 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            <div class="flex flex-col items-center gap-1">
                                <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                                </div>
                                <span class="text-[10px] font-medium text-slate-500">Track</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Free Section -->
    <section class="py-24 bg-slate-900 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 -mr-32 -mt-32 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-32 -mb-32 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 border border-teal-500/20 text-teal-300 text-xs font-semibold uppercase tracking-wide mb-6">
                        Our Philosophy
                    </div>
                    <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl lg:text-5xl mb-6">
                        Why give it away <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-emerald-400">for free?</span>
                    </h2>
                    <p class="text-lg text-slate-300 mb-8 leading-relaxed">
                        We believe efficient facility management shouldn't be a luxury. By democratizing access to professional tools, we help teams of all sizes operate safer, cleaner, and more productive spaces.
                    </p>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-teal-500/20 flex items-center justify-center">
                                <svg class="h-4 w-4 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-slate-300">No hidden fees or credit card required</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-teal-500/20 flex items-center justify-center">
                                <svg class="h-4 w-4 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-slate-300">Unlimited users, facilities, and assets</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-teal-500/20 flex items-center justify-center">
                                <svg class="h-4 w-4 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-slate-300">Community-driven feature roadmap</span>
                        </li>
                    </ul>
                </div>

                <!-- Free Illustration -->
                <div class="relative flex items-center justify-center">
                    <!-- Animated background glow -->
                    <div class="absolute w-80 h-80 bg-gradient-to-r from-teal-500/20 to-emerald-500/20 rounded-full blur-3xl animate-pulse"></div>

                    <!-- SVG Illustration -->
                    <div class="relative">
                        <svg class="w-72 h-72 md:w-96 md:h-96" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Background circle -->
                            <circle cx="200" cy="200" r="180" fill="url(#bgGradient)" opacity="0.1"/>

                            <!-- Gift box base -->
                            <rect x="100" y="180" width="200" height="140" rx="12" fill="url(#boxGradient)" />
                            <rect x="100" y="180" width="200" height="140" rx="12" stroke="#0d9488" stroke-width="3" opacity="0.3"/>

                            <!-- Gift box lid -->
                            <rect x="85" y="150" width="230" height="40" rx="8" fill="url(#lidGradient)" />
                            <rect x="85" y="150" width="230" height="40" rx="8" stroke="#0d9488" stroke-width="3" opacity="0.3"/>

                            <!-- Ribbon vertical -->
                            <rect x="185" y="150" width="30" height="170" fill="#f0fdfa" opacity="0.9"/>
                            <rect x="185" y="150" width="30" height="170" stroke="#5eead4" stroke-width="2"/>

                            <!-- Ribbon horizontal -->
                            <rect x="85" y="160" width="230" height="20" fill="#f0fdfa" opacity="0.9"/>
                            <rect x="85" y="160" width="230" height="20" stroke="#5eead4" stroke-width="2"/>

                            <!-- Bow left loop -->
                            <ellipse cx="165" cy="130" rx="35" ry="25" fill="#f0fdfa" stroke="#5eead4" stroke-width="2"/>

                            <!-- Bow right loop -->
                            <ellipse cx="235" cy="130" rx="35" ry="25" fill="#f0fdfa" stroke="#5eead4" stroke-width="2"/>

                            <!-- Bow center -->
                            <circle cx="200" cy="140" r="15" fill="url(#bowGradient)" stroke="#5eead4" stroke-width="2"/>

                            <!-- Sparkles -->
                            <g class="animate-pulse">
                                <path d="M80 100 L85 110 L80 120 L75 110 Z" fill="#5eead4"/>
                                <path d="M320 90 L325 100 L320 110 L315 100 Z" fill="#5eead4"/>
                                <path d="M340 200 L345 210 L340 220 L335 210 Z" fill="#2dd4bf"/>
                                <path d="M60 220 L65 230 L60 240 L55 230 Z" fill="#2dd4bf"/>
                                <path d="M100 80 L103 87 L100 94 L97 87 Z" fill="#99f6e4"/>
                                <path d="M300 280 L303 287 L300 294 L297 287 Z" fill="#99f6e4"/>
                            </g>

                            <!-- Floating circles -->
                            <circle cx="70" cy="160" r="6" fill="#14b8a6" opacity="0.6"/>
                            <circle cx="330" cy="140" r="8" fill="#10b981" opacity="0.6"/>
                            <circle cx="350" cy="260" r="5" fill="#5eead4" opacity="0.6"/>
                            <circle cx="50" cy="280" r="7" fill="#2dd4bf" opacity="0.6"/>

                            <!-- Gradients -->
                            <defs>
                                <linearGradient id="bgGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#14b8a6"/>
                                    <stop offset="100%" stop-color="#10b981"/>
                                </linearGradient>
                                <linearGradient id="boxGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#0f766e"/>
                                    <stop offset="100%" stop-color="#047857"/>
                                </linearGradient>
                                <linearGradient id="lidGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#14b8a6"/>
                                    <stop offset="100%" stop-color="#10b981"/>
                                </linearGradient>
                                <linearGradient id="bowGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#2dd4bf"/>
                                    <stop offset="100%" stop-color="#34d399"/>
                                </linearGradient>
                            </defs>
                        </svg>

                        <!-- Floating badge -->
                        <div class="absolute -top-4 -right-4 px-4 py-2 bg-gradient-to-r from-amber-400 to-orange-500 rounded-full shadow-lg transform rotate-12 animate-bounce">
                            <span class="text-white font-bold text-sm">100% Free!</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Multi-Tenancy Section -->
    <section id="solutions" class="py-24 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-center">
                <!-- Visual -->
                <div class="relative mb-12 lg:mb-0">
                    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 relative z-10 max-w-md mx-auto lg:mx-0 hover:shadow-2xl transition-shadow">
                        <div class="flex items-center gap-4 mb-6 border-b border-slate-100 pb-4">
                            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-teal-500 to-emerald-600 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-teal-500/20">AS</div>
                            <div>
                                <h4 class="font-bold text-slate-900">Alex Smith</h4>
                                <p class="text-xs text-slate-500">alex@example.com</p>
                            </div>
                            <div class="ml-auto px-2.5 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">Active</div>
                        </div>

                        <p class="text-xs font-semibold text-slate-400 uppercase mb-3">Your Organizations</p>

                        <div class="space-y-3">
                            <div class="flex items-center gap-3 p-3 bg-teal-50 rounded-xl border border-teal-100 cursor-pointer hover:bg-teal-100/50 transition-colors">
                                <div class="h-10 w-10 rounded-lg bg-white flex items-center justify-center shadow-sm">
                                    <svg class="w-6 h-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                </div>
                                <div class="flex-1">
                                    <h5 class="font-bold text-slate-900 text-sm">Downtown Office Plaza</h5>
                                    <p class="text-xs text-slate-500">Admin</p>
                                </div>
                                <div class="h-2 w-2 rounded-full bg-teal-500"></div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-slate-100 hover:border-slate-200 cursor-pointer transition-colors opacity-70 hover:opacity-100">
                                <div class="h-10 w-10 rounded-lg bg-slate-50 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                                </div>
                                <div class="flex-1">
                                    <h5 class="font-bold text-slate-900 text-sm">Westside Apartments</h5>
                                    <p class="text-xs text-slate-500">Manager</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                            <button class="text-sm font-semibold text-teal-600 hover:text-teal-700 flex items-center justify-center gap-2 mx-auto group">
                                <svg class="w-4 h-4 group-hover:rotate-90 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                Create New Organization
                            </button>
                        </div>
                    </div>

                    <!-- Invitation Popup -->
                    <div class="absolute -right-8 bottom-12 bg-white rounded-xl shadow-lg border border-slate-100 p-4 w-56 hidden sm:block animate-float z-20">
                        <div class="flex items-start gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 ring-4 ring-white flex-shrink-0">
                                <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </span>
                            <div>
                                <p class="text-sm font-medium text-slate-900">New Member</p>
                                <p class="text-xs text-slate-500">Sarah joined the team</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-50 text-teal-600 text-xs font-semibold uppercase tracking-wide mb-4">
                        Multi-Tenancy
                    </div>
                    <h2 class="text-3xl font-extrabold text-slate-900 md:text-4xl lg:text-5xl mb-6">One Account. <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-500 to-emerald-500">Unlimited Possibilities.</span></h2>
                    <p class="text-lg text-slate-500 mb-10">
                        Whether you manage a single building or a portfolio of properties, Optima FM handles it all from a single dashboard.
                    </p>

                    <div class="space-y-8">
                        <div class="flex gap-5">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 text-white shadow-lg shadow-teal-500/20">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-slate-900 mb-2">Role-Based Access Control</h4>
                                <p class="text-base text-slate-500">
                                    Invite technicians, managers, and tenants with precise permissions. Control exactly who sees what.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-5">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 text-white shadow-lg shadow-teal-500/20">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-slate-900 mb-2">Switch Context Instantly</h4>
                                <p class="text-base text-slate-500">
                                    Manage multiple independent workspaces with a single login. Switch between clients in one click.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Request / Contact Section -->
    <section id="contact" class="py-20 bg-white relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>
        <div class="absolute bottom-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative">
                <!-- Decorative elements -->
                <div class="absolute -top-10 -left-10 w-32 h-32 bg-gradient-to-br from-teal-100 to-emerald-100 rounded-full blur-3xl opacity-60"></div>
                <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full blur-3xl opacity-60"></div>

                <!-- Card -->
                <div class="relative bg-gradient-to-br from-slate-50 to-white rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/50 p-8 md:p-12 text-center">
                    <!-- Icon -->
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-lg shadow-indigo-500/30 mb-6">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>

                    <h3 class="text-2xl md:text-3xl font-extrabold text-slate-900 mb-4">
                        Missing a Feature?
                    </h3>

                    <p class="text-lg text-slate-500 mb-8 max-w-xl mx-auto leading-relaxed">
                        We're constantly improving Optima FM based on your feedback. If there's something you'd love to see, we'd love to hear about it!
                    </p>

                    <!-- Email CTAs -->
                    <div class="flex flex-col gap-4 max-w-xl mx-auto w-full">
                        <!-- Feature Request -->
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-3 bg-slate-100 rounded-2xl">
                            <div class="flex items-center gap-3 px-4 py-2 bg-white rounded-xl shadow-sm border border-slate-200">
                                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-teal-500 to-emerald-500 text-white">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                    </svg>
                                </div>
                                <span class="text-slate-700 font-medium select-all text-sm">features@optimafm.org</span>
                            </div>

                            <a href="mailto:features@optimafm.org?subject=Feature%20Request%20for%20Optima%20FM" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 transition-all hover:scale-105 hover:shadow-xl text-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Send Feature Request
                            </a>
                        </div>

                        <!-- General Support -->
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-3 bg-slate-100 rounded-2xl">
                            <div class="flex items-center gap-3 px-4 py-2 bg-white rounded-xl shadow-sm border border-slate-200">
                                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-500 text-white">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <span class="text-slate-700 font-medium select-all text-sm">support@optimafm.org</span>
                            </div>

                            <a href="mailto:support@optimafm.org?subject=Support%20Request%20-%20Optima%20FM" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/25 transition-all hover:scale-105 hover:shadow-xl text-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Contact Support
                            </a>
                        </div>
                    </div>

                    <!-- Additional note -->
                    <p class="mt-6 text-sm text-slate-400">
                        We read every suggestion and prioritize features based on community demand.
                    </p>

                    <!-- Decorative dots -->
                    <div class="absolute top-6 right-6 flex gap-1.5 opacity-30">
                        <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="py-24 bg-gradient-to-br from-teal-600 via-teal-500 to-emerald-500 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIj48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIyIi8+PC9nPjwvZz48L3N2Zz4=')] opacity-30"></div>

        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-6">Ready to streamline your facilities?</h2>
            <p class="text-teal-100 text-lg md:text-xl mb-10 max-w-2xl mx-auto">Join facility managers who have switched to a smarter, simpler way to work. Get started in under 5 minutes.</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('signup') }}" wire:navigate class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-10 py-4 text-lg font-bold text-teal-600 bg-white rounded-full hover:bg-teal-50 shadow-xl shadow-teal-700/20 transition-all hover:scale-105">
                    Create Your Free Account
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>
            <p class="mt-6 text-sm text-teal-200">No credit card required. Free forever.</p>
        </div>
    </section>

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
                <p class="text-sm text-slate-500">Made with <span class="text-red-400">&hearts;</span> for facility teams everywhere</p>
            </div>
        </div>
    </footer>
</body>
</html>
