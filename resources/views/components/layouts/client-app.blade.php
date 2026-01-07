<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('images/icons/icon_64.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/icons/icon_128.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/icons/icon_256.png') }}">
    <title>{{ $title ?? 'Optima FM' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans antialiased text-slate-600" x-data="{ sidebarOpen: false }">

    <!-- Mobile sidebar -->
    <div x-show="sidebarOpen" class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/80"></div>

        <div class="fixed inset-0 flex">
            <div x-show="sidebarOpen" 
                 x-transition:enter="transition ease-in-out duration-300 transform"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in-out duration-300 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="relative mr-16 flex w-full max-w-xs flex-1">
                
                <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                    <button type="button" @click="sidebarOpen = false" class="-m-2.5 p-2.5">
                        <span class="sr-only">Close sidebar</span>
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Mobile Sidebar Content -->
                <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-slate-900 px-6 pb-4 ring-1 ring-white/10">
                    <div class="flex h-16 shrink-0 items-center">
                         <img class="h-auto w-30" src="{{ asset('images/logo-white.png') }}" alt="Optima FM">
                    </div>
                    @include('components.layouts.partials.sidebar-nav')
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop Sidebar -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
        <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-slate-900 px-6 pb-4">
            <div class="flex h-16 my-2 shrink-0 items-center">
                 <img class="h-auto w-36" src="{{ asset('images/logo-white.png') }}" alt="Optima FM">
            </div>
            @include('components.layouts.partials.sidebar-nav')
        </div>
    </div>

    <!-- Main Content -->
    <div class="lg:pl-72">
        <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-slate-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
            <button type="button" @click="sidebarOpen = true" class="-m-2.5 p-2.5 text-slate-700 lg:hidden">
                <span class="sr-only">Open sidebar</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            <!-- Separator -->
            <div class="h-6 w-px bg-slate-200 lg:hidden" aria-hidden="true"></div>

            <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6 justify-between">
                <!-- Current Client Info -->
                <div class="flex items-center gap-x-3">
                     @if(app()->bound('currentClient')) 
                        <div class="flex items-center gap-x-2 px-3 py-1.5 bg-slate-50 rounded-full border border-slate-200">
                             <div class="h-2 w-2 rounded-full bg-green-500"></div>
                             <span class="text-sm font-medium text-slate-700">{{ app('currentClient')->name }}</span>
                        </div>
                     @endif
                </div>

                <div class="flex items-center gap-x-4 lg:gap-x-6">
                    <!-- User Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" type="button" class="-m-1.5 flex items-center p-1.5" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                            <span class="sr-only">Open user menu</span>
                            <div class="h-8 w-8 rounded-full bg-teal-600 flex items-center justify-center text-white font-bold text-xs ring-2 ring-white">
                                {{ Auth::user()->initials() }}
                            </div>
                            <span class="hidden lg:flex lg:items-center">
                                <span class="ml-2 text-sm font-semibold leading-6 text-slate-900" aria-hidden="true">{{ Auth::user()->name }}</span>
                                <svg class="ml-2 h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>
                        
                        <div x-show="open" @click.outside="open = false" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-slate-900/5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                            <a href="{{ route('user.home') }}" class="block px-3 py-1 text-sm leading-6 text-slate-900 hover:bg-slate-50" role="menuitem">Switch Client</a>
                            <a href="{{ route('logout') }}" class="block px-3 py-1 text-sm leading-6 text-slate-900 hover:bg-slate-50" role="menuitem">Sign out</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <main class="py-10">
            <div class="px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>

    <x-toast />
    
    <x-ui.confirm-modal />
    
    @livewireScripts
</body>
</html>
