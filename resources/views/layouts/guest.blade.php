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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        {{ Vite::fonts() }}
    </head>
    <body class="font-sans text-ink antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-paper">
            <div>
                <a href="/" wire:navigate>
                    <img src="/logo/klaar-ink.svg" alt="Klaar" class="h-12" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-snow shadow-md overflow-hidden rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
