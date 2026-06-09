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

        <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        {{ Vite::fonts() }}
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-ink">

            {{-- ===== FLOATING LOGO (top-left) ===== --}}
            <a href="{{ route('dashboard') }}" wire:navigate class="fixed top-5 left-5 z-50 group">
                <img src="/logo/klaar-paper.svg" alt="Klaar" class="h-7 opacity-80 group-hover:opacity-100 transition" />
            </a>

            {{-- ===== FLOATING NAV (left-center) ===== --}}
            <nav class="fixed left-5 top-1/2 -translate-y-1/2 z-50 hidden lg:flex flex-col gap-2">
                @php
                    $navItems = [
                        ['route' => 'dashboard', 'icon' => 'fa-grid-2', 'label' => 'Dashboard', 'match' => 'dashboard'],
                        ['route' => 'invoeren.index', 'icon' => 'fa-microphone', 'label' => 'Invoeren', 'match' => 'invoeren.*'],
                        ['route' => 'werkbonnen.index', 'icon' => 'fa-clipboard-list', 'label' => 'Werkbonnen', 'match' => 'werkbonnen.*'],
                        ['route' => 'projects.index', 'icon' => 'fa-folder-open', 'label' => 'Projecten', 'match' => 'projects.*'],
                        ['route' => 'clients.index', 'icon' => 'fa-address-book', 'label' => 'Relaties', 'match' => 'clients.*'],
                        ['route' => 'invoices.index', 'icon' => 'fa-file-invoice', 'label' => 'Facturen', 'match' => 'invoices.*'],
                    ];
                @endphp

                @foreach($navItems as $item)
                    <a href="{{ route($item['route']) }}" wire:navigate
                       class="group/nav relative w-11 h-11 rounded-full flex items-center justify-center transition-all duration-200
                           {{ request()->routeIs($item['match']) ? 'bg-amber text-ink shadow-[0_0_20px_rgba(255,180,0,0.3)]' : 'bg-ink-90/80 backdrop-blur-sm text-paper/50 border border-ink-70/20 hover:text-paper hover:border-paper/20 hover:bg-ink-90' }}">
                        <i class="fa-solid {{ $item['icon'] }} text-sm"></i>
                        {{-- Tooltip --}}
                        <span class="absolute left-full ml-3 px-2.5 py-1 rounded-md bg-ink-90 border border-ink-70/20 text-paper text-xs font-heading font-semibold whitespace-nowrap opacity-0 -translate-x-1 pointer-events-none group-hover/nav:opacity-100 group-hover/nav:translate-x-0 transition-all duration-150">
                            {{ $item['label'] }}
                        </span>
                    </a>
                @endforeach

                {{-- Divider --}}
                <div class="w-5 h-px bg-paper/10 mx-auto my-1"></div>

                {{-- Nieuwe invoer --}}
                <a href="{{ route('invoeren.index') }}" wire:navigate
                   class="group/nav relative w-11 h-11 rounded-full flex items-center justify-center bg-ink-90/80 backdrop-blur-sm text-amber border border-amber/20 hover:bg-amber hover:text-ink hover:border-amber transition-all duration-200">
                    <i class="fa-solid fa-plus text-sm"></i>
                    <span class="absolute left-full ml-3 px-2.5 py-1 rounded-md bg-ink-90 border border-ink-70/20 text-paper text-xs font-heading font-semibold whitespace-nowrap opacity-0 -translate-x-1 pointer-events-none group-hover/nav:opacity-100 group-hover/nav:translate-x-0 transition-all duration-150">
                        Nieuwe invoer
                    </span>
                </a>
            </nav>

            {{-- ===== FLOATING PROFILE (top-right) ===== --}}
            <div class="fixed top-5 right-5 z-50 hidden lg:block" x-data="{ profileOpen: false }">
                <div class="relative" @mouseenter="profileOpen = true" @mouseleave="profileOpen = false">
                    {{-- Avatar button --}}
                    <button class="w-10 h-10 rounded-full bg-amber/20 border border-amber/20 flex items-center justify-center cursor-pointer hover:border-amber/40 transition">
                        <span class="text-sm font-bold text-amber font-heading">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    </button>

                    {{-- Dropdown (pt-2 bridges the gap so hover stays active) --}}
                    <div x-show="profileOpen"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="absolute right-0 top-full pt-2 w-56"
                         style="display: none;">
                      <div class="bg-ink-90 border border-ink-70/20 rounded-lg shadow-xl overflow-hidden">

                        {{-- User info --}}
                        <div class="px-4 py-3 border-b border-ink-70/15">
                            <p class="text-sm font-semibold text-paper font-heading truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-ink-50 truncate">{{ auth()->user()->email }}</p>
                        </div>

                        {{-- Links --}}
                        <div class="py-1.5">
                            <a href="{{ route('profile') }}" wire:navigate class="flex items-center gap-3 px-4 py-2.5 text-sm text-paper/60 hover:text-paper hover:bg-paper/5 transition">
                                <i class="fa-solid fa-user w-4 text-center text-xs"></i>
                                Profiel
                            </a>
                            <a href="{{ route('settings') }}" wire:navigate class="flex items-center gap-3 px-4 py-2.5 text-sm text-paper/60 hover:text-paper hover:bg-paper/5 transition">
                                <i class="fa-solid fa-building w-4 text-center text-xs"></i>
                                Bedrijf
                            </a>
                        </div>

                        {{-- Logout --}}
                        <div class="border-t border-ink-70/15 py-1.5">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-400/70 hover:text-red-400 hover:bg-red-500/5 transition cursor-pointer">
                                    <i class="fa-solid fa-right-from-bracket w-4 text-center text-xs"></i>
                                    Uitloggen
                                </button>
                            </form>
                        </div>
                      </div>
                    </div>
                </div>
            </div>

            {{-- ===== MOBILE NAV (bottom bar) ===== --}}
            <nav class="fixed bottom-0 inset-x-0 z-50 lg:hidden bg-ink-90/95 backdrop-blur-sm border-t border-ink-70/20">
                @php
                    $mobileNavItems = collect($navItems)->filter(fn($i) => $i['route'] !== 'projects.index')->values();
                @endphp
                <div class="flex items-center justify-around px-2 py-2">
                    @foreach($mobileNavItems as $item)
                        <a href="{{ route($item['route']) }}" wire:navigate
                           class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-lg transition
                               {{ request()->routeIs($item['match']) ? 'text-amber' : 'text-paper/40 hover:text-paper/70' }}">
                            <i class="fa-solid {{ $item['icon'] }} text-base"></i>
                            <span class="text-[9px] font-heading font-semibold uppercase tracking-wider">{{ $item['label'] }}</span>
                        </a>
                    @endforeach

                    {{-- Mobile profile --}}
                    <a href="{{ route('profile') }}" wire:navigate
                       class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-lg transition
                           {{ request()->routeIs('profile') || request()->routeIs('settings') ? 'text-amber' : 'text-paper/40 hover:text-paper/70' }}">
                        <i class="fa-solid fa-user text-base"></i>
                        <span class="text-[9px] font-heading font-semibold uppercase tracking-wider">Account</span>
                    </a>
                </div>
            </nav>

            {{-- ===== MAIN CONTENT ===== --}}
            <main class="min-h-screen lg:pl-20 pb-20 lg:pb-0">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
