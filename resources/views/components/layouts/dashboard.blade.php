<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/icons/icon_64.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/icons/icon_128.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/icons/icon_256.png') }}">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased" x-data="{ sidebarOpen: false }">
    <x-toast />

    <div class="min-h-screen flex">
        
        <!-- Mobile Backdrop -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-slate-900/80 z-40 lg:hidden"
             style="display: none;"></div>

        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
             class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto flex flex-col shadow-xl">
            
            <!-- Logo area -->
            <div class="flex items-center justify-center h-20 border-b border-white/10 bg-teal-900/20">
                 <a href="{{ route('user.home') }}" wire:navigate>
                    <img src="{{ asset('images/logo-white.png') }}" class="h-8 w-auto" alt="Logo">
                 </a>
            </div>

            <!-- Nav Links -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <x-nav-link href="{{ route('user.home') }}" icon="home" :active="request()->routeIs('user.home')">
                    Dashboard
                </x-nav-link>

                <x-nav-link href="{{ route('user.settings') }}" icon="cog-6-tooth" :active="request()->routeIs('user.settings')">
                    Settings
                </x-nav-link>
            </nav>

            <!-- User Profile (Bottom) -->
            <div class="p-4 border-t border-white/10 bg-black/20">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-full bg-teal-500 flex items-center justify-center text-white font-bold text-sm shadow-inner ring-2 ring-white/10">
                        {{ auth()->user()->initials() ?? 'U' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">
                            {{ auth()->user()->name ?? 'User' }}
                        </p>
                        <p class="text-xs text-slate-400 truncate">
                            {{ auth()->user()->email }}
                        </p>
                    </div>
                     <a href="{{ route('logout') }}" class="text-slate-400 hover:text-white transition-colors p-1" title="Logout">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 2.062-5M18 12l-2.062 5" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 transition-all duration-300">
            
            <!-- Mobile Header -->
            <div class="lg:hidden flex items-center justify-between bg-white border-b border-slate-200 px-4 py-3 shadow-sm sticky top-0 z-30">
                <button @click="sidebarOpen = true" class="text-slate-500 hover:text-slate-700 focus:outline-none p-1 rounded-md hover:bg-slate-100">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <img src="{{ asset('images/logo.png') }}" class="h-6 w-auto" alt="Logo">
                <div class="w-8"></div> <!-- Spacer -->
            </div>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>
    
    @livewireScripts
</body>
</html>
