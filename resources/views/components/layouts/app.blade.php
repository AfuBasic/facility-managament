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
    <body>
        {{ $slot }}
        @livewireScripts
    </body>
</html>
