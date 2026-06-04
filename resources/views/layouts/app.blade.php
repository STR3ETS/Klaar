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
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-paper">
            <livewire:layout.navigation />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-snow shadow-sm">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
