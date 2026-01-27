<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/icons/icon_64.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/icons/icon_128.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/icons/icon_256.png') }}">
    <title>{{ $title ?? 'Admin Portal | ' . config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased">
    <x-toast />
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 overflow-hidden relative font-sans text-gray-900">

        <!-- Header -->
        <div class="w-full max-w-lg relative z-10 mb-8 flex flex-col items-center">
            <div class="text-white text-sm uppercase tracking-widest mb-2 opacity-60">Admin Portal</div>
            <a href="{{ url('/') }}">
                <img src="{{ asset('images/logo-white.png') }}" class="w-32" alt="Logo" />
            </a>
        </div>

        <!-- Background Decor -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
            <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-slate-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute top-[-10%] right-[-10%] w-96 h-96 bg-slate-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-[-20%] left-[20%] w-96 h-96 bg-slate-700 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
        </div>

        <!-- Content Slot -->
        {{ $slot }}

        <!-- Custom Keyframe for Blobs -->
        <style>
            @keyframes blob {
                0% { transform: translate(0px, 0px) scale(1); }
                33% { transform: translate(30px, -50px) scale(1.1); }
                66% { transform: translate(-20px, 20px) scale(0.9); }
                100% { transform: translate(0px, 0px) scale(1); }
            }
            .animate-blob {
                animation: blob 7s infinite;
            }
            .animation-delay-2000 {
                animation-delay: 2s;
            }
            .animation-delay-4000 {
                animation-delay: 4s;
            }
        </style>
    </div>
    @livewireScripts
</body>
</html>
