<x-layouts.public>
    @push('styles')
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
    @endpush

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

                            <!-- Decorative stars -->
                            <path d="M50 80 L60 80 L65 70 L70 80 L80 80 L72 90 L75 100 L65 95 L55 100 L58 90 Z" fill="#fbbf24" class="animate-bounce" style="animation-duration: 2s"/>
                            <path d="M320 50 L325 50 L327 45 L330 50 L335 50 L331 55 L332 60 L327 57 L322 60 L323 55 Z" fill="#fbbf24" class="animate-bounce" style="animation-duration: 3s"/>

                            <!-- Gradients -->
                            <defs>
                                <radialGradient id="bgGradient" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(200 200) rotate(90) scale(180)">
                                    <stop stop-color="#14b8a6"/>
                                    <stop offset="1" stop-color="#14b8a6" stop-opacity="0"/>
                                </radialGradient>
                                <linearGradient id="boxGradient" x1="200" y1="180" x2="200" y2="320" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#ccfbf1"/>
                                    <stop offset="1" stop-color="#99f6e4"/>
                                </linearGradient>
                                <linearGradient id="lidGradient" x1="200" y1="150" x2="200" y2="190" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#99f6e4"/>
                                    <stop offset="1" stop-color="#5eead4"/>
                                </linearGradient>
                            </defs>
                        </svg>
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
</x-layouts.public>
