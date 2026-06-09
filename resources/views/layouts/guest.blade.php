<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Klaar') }}</title>

        <!-- Favicons -->
        <link rel="icon" href="/favicon/favicon.ico" sizes="32x32">
        <link rel="icon" href="/favicon/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/favicon/apple-touch-icon.png">

        <link rel="preload" href="{{ asset('fontawesome/css/all.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
        <noscript><link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}"></noscript>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        {{ Vite::fonts() }}
    </head>
    <body class="font-sans text-ink antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center bg-ink relative overflow-hidden" style="background-image: radial-gradient(circle, rgba(255,180,0,0.04) 1px, transparent 1px); background-size: 24px 24px;">

            {{-- Decoratieve elementen --}}
            <div class="absolute top-20 left-[10%] w-20 h-20 rounded-full bg-amber/5 blur-2xl"></div>
            <div class="absolute bottom-32 right-[15%] w-32 h-32 rounded-full bg-amber/5 blur-3xl"></div>

            {{-- Logo --}}
            <div class="mb-8">
                <a href="/" wire:navigate>
                    <img src="/logo/klaar-paper.svg" alt="Klaar" class="h-14" />
                </a>
            </div>

            {{-- Content --}}
            <div class="w-full max-w-4xl mx-auto px-6">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            <p class="mt-8 text-xs text-ink-50">
                &copy; {{ date('Y') }} Klaar. &middot; <a href="/beveiliging" class="text-amber hover:underline" wire:navigate>Beveiliging</a> &middot; <a href="/faq" class="text-amber hover:underline" wire:navigate>FAQ</a>
            </p>
        </div>
    </body>
</html>
