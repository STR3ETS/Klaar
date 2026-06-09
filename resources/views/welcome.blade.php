<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Klaar. - Administratie-assistent voor de vakman</title>
    <meta name="description" content="Spreek in wat je gedaan hebt, maak een foto van de bon. Klaar regelt de rest. Werkbonnen en facturen voor ZZP'ers en kleine aannemers.">

    <link rel="icon" href="/favicon/favicon.ico" sizes="32x32">
    <link rel="icon" href="/favicon/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/favicon/apple-touch-icon.png">

    <link rel="preload" href="{{ asset('fontawesome/css/all.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}"></noscript>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{ Vite::fonts() }}
</head>
<body>
    <nav class="w-full fixed z-100 left-0 right-0 top-0 transition-colors duration-300" id="main-nav">
        <div class="max-w-7xl mx-auto py-4 px-6 flex items-center justify-between">
            <a href="/" class="inline-flex shrink-0"><img class="h-14 w-auto -ml-8" src="/logo/klaar-paper.svg" alt="Klaar"></a>

            {{-- Desktop navigatie --}}
            <div class="hidden lg:flex items-center gap-7">

                {{-- Product dropdown --}}
                <div class="relative group">
                    <button class="font-sans text-paper text-xs font-light opacity-80 hover:opacity-100 transition flex items-center gap-1.5 py-2">
                        Product
                        <i class="fa-solid fa-chevron-down text-[7px] mt-px transition-transform duration-200 group-hover:rotate-180"></i>
                    </button>
                    <div class="absolute top-full -left-4 pt-3 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                        <div class="bg-ink border border-ink-70/20 rounded-sm shadow-lg p-2 w-[480px]">
                            <div class="grid grid-cols-2 gap-1">
                                <a href="/hoe-het-werkt" class="flex items-start gap-3 p-3 rounded-sm hover:bg-ink-90 transition">
                                    <div class="w-8 h-8 rounded-md bg-amber/10 flex items-center justify-center shrink-0 mt-0.5">
                                        <i class="fa-solid fa-wand-magic-sparkles text-amber text-xs"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs font-heading font-semibold text-paper block">Hoe het werkt</span>
                                        <span class="text-[11px] text-ink-50 leading-snug">Drie stappen, nul formulieren</span>
                                    </div>
                                </a>
                                <a href="/spraakherkenning" class="flex items-start gap-3 p-3 rounded-sm hover:bg-ink-90 transition">
                                    <div class="w-8 h-8 rounded-md bg-amber/10 flex items-center justify-center shrink-0 mt-0.5">
                                        <i class="fa-solid fa-microphone text-amber text-xs"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs font-heading font-semibold text-paper block">Spraakherkenning</span>
                                        <span class="text-[11px] text-ink-50 leading-snug">Spreek in, Klaar schrijft mee</span>
                                    </div>
                                </a>
                                <a href="/facturen-werkbonnen" class="flex items-start gap-3 p-3 rounded-sm hover:bg-ink-90 transition">
                                    <div class="w-8 h-8 rounded-md bg-amber/10 flex items-center justify-center shrink-0 mt-0.5">
                                        <i class="fa-solid fa-file-invoice text-amber text-xs"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs font-heading font-semibold text-paper block">Facturen & werkbonnen</span>
                                        <span class="text-[11px] text-ink-50 leading-snug">Van inspraak naar PDF</span>
                                    </div>
                                </a>
                                <a href="/fotoherkenning" class="flex items-start gap-3 p-3 rounded-sm hover:bg-ink-90 transition">
                                    <div class="w-8 h-8 rounded-md bg-amber/10 flex items-center justify-center shrink-0 mt-0.5">
                                        <i class="fa-solid fa-camera text-amber text-xs"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs font-heading font-semibold text-paper block">Fotoherkenning</span>
                                        <span class="text-[11px] text-ink-50 leading-snug">Bonnen en pakbonnen scannen</span>
                                    </div>
                                </a>
                                <a href="/integraties" class="flex items-start gap-3 p-3 rounded-sm hover:bg-ink-90 transition">
                                    <div class="w-8 h-8 rounded-md bg-amber/10 flex items-center justify-center shrink-0 mt-0.5">
                                        <i class="fa-solid fa-puzzle-piece text-amber text-xs"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs font-heading font-semibold text-paper block">Integraties</span>
                                        <span class="text-[11px] text-ink-50 leading-snug">Moneybird, e-mail en meer</span>
                                    </div>
                                </a>
                                <a href="/beveiliging" class="flex items-start gap-3 p-3 rounded-sm hover:bg-ink-90 transition">
                                    <div class="w-8 h-8 rounded-md bg-amber/10 flex items-center justify-center shrink-0 mt-0.5">
                                        <i class="fa-solid fa-shield-halved text-amber text-xs"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs font-heading font-semibold text-paper block">Beveiliging & AVG</span>
                                        <span class="text-[11px] text-ink-50 leading-snug">EU-servers, jouw eigendom</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Voor wie dropdown --}}
                <div class="relative group">
                    <button class="font-sans text-paper text-xs font-light opacity-80 hover:opacity-100 transition flex items-center gap-1.5 py-2">
                        Voor wie
                        <i class="fa-solid fa-chevron-down text-[7px] mt-px transition-transform duration-200 group-hover:rotate-180"></i>
                    </button>
                    <div class="absolute top-full -left-4 pt-3 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                        <div class="bg-ink border border-ink-70/20 rounded-sm shadow-lg p-2 w-[220px]">
                            <a href="/zzp-bouw" class="flex items-center gap-3 px-3 py-2.5 rounded-sm hover:bg-ink-90 transition">
                                <i class="fa-solid fa-helmet-safety text-amber text-xs w-4 text-center"></i>
                                <span class="text-xs text-paper">ZZP'ers in de bouw</span>
                            </a>
                            <a href="/kleine-aannemers" class="flex items-center gap-3 px-3 py-2.5 rounded-sm hover:bg-ink-90 transition">
                                <i class="fa-solid fa-people-group text-amber text-xs w-4 text-center"></i>
                                <span class="text-xs text-paper">Kleine aannemers</span>
                            </a>
                            <a href="/installateurs" class="flex items-center gap-3 px-3 py-2.5 rounded-sm hover:bg-ink-90 transition">
                                <i class="fa-solid fa-wrench text-amber text-xs w-4 text-center"></i>
                                <span class="text-xs text-paper">Installateurs</span>
                            </a>
                            <a href="/schilders-stukadoors" class="flex items-center gap-3 px-3 py-2.5 rounded-sm hover:bg-ink-90 transition">
                                <i class="fa-solid fa-paint-roller text-amber text-xs w-4 text-center"></i>
                                <span class="text-xs text-paper">Schilders & stukadoors</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Prijzen --}}
                <a href="/prijzen" class="font-sans text-paper text-xs font-light opacity-80 hover:opacity-100 transition py-2">Prijzen</a>

                {{-- Hulp dropdown --}}
                <div class="relative group">
                    <button class="font-sans text-paper text-xs font-light opacity-80 hover:opacity-100 transition flex items-center gap-1.5 py-2">
                        Hulp
                        <i class="fa-solid fa-chevron-down text-[7px] mt-px transition-transform duration-200 group-hover:rotate-180"></i>
                    </button>
                    <div class="absolute top-full right-0 pt-3 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                        <div class="bg-ink border border-ink-70/20 rounded-sm shadow-lg p-2 w-[220px]">
                            <a href="/faq" class="flex items-center gap-3 px-3 py-2.5 rounded-sm hover:bg-ink-90 transition">
                                <i class="fa-solid fa-circle-question text-amber text-xs w-4 text-center"></i>
                                <span class="text-xs text-paper">Veelgestelde vragen</span>
                            </a>
                            <a href="/blog" class="flex items-center gap-3 px-3 py-2.5 rounded-sm hover:bg-ink-90 transition">
                                <i class="fa-solid fa-book text-amber text-xs w-4 text-center"></i>
                                <span class="text-xs text-paper">Blog & tips</span>
                            </a>
                            <a href="/contact" class="flex items-center gap-3 px-3 py-2.5 rounded-sm hover:bg-ink-90 transition">
                                <i class="fa-solid fa-envelope text-amber text-xs w-4 text-center"></i>
                                <span class="text-xs text-paper">Contact</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Scheiding --}}
                <div class="w-px h-5 bg-paper/15"></div>

                {{-- Inloggen --}}
                <a href="/login" class="font-sans text-paper text-xs font-light opacity-80 hover:opacity-100 transition">Inloggen</a>

                {{-- CTA --}}
                <a href="/register" class="bg-amber px-4 py-2.5 text-ink font-semibold text-xs font-heading transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)]">Gratis proberen</a>
            </div>

            {{-- Mobile hamburger --}}
            <button id="mobile-menu-btn" class="lg:hidden w-10 h-10 flex items-center justify-center text-paper" aria-label="Menu openen">
                <i class="fa-solid fa-bars text-lg" id="mobile-menu-icon"></i>
            </button>
        </div>

        {{-- Mobile menu --}}
        <div id="mobile-menu" class="hidden lg:hidden bg-ink border-t border-ink-70/20 px-6 pb-8 max-h-[calc(100vh-72px)] overflow-y-auto">

            {{-- Product accordion --}}
            <div class="border-b border-ink-70/15">
                <button class="mobile-accordion-btn w-full flex items-center justify-between py-4 text-sm text-paper font-heading font-semibold">
                    Product
                    <i class="fa-solid fa-chevron-down text-[10px] text-ink-50 transition-transform duration-200"></i>
                </button>
                <div class="mobile-accordion-panel hidden pb-4 space-y-1">
                    <a href="/hoe-het-werkt" class="flex items-center gap-3 px-3 py-2.5 rounded-sm hover:bg-ink-90 transition">
                        <i class="fa-solid fa-wand-magic-sparkles text-amber text-xs w-5 text-center"></i>
                        <span class="text-sm text-ink-30">Hoe het werkt</span>
                    </a>
                    <a href="/spraakherkenning" class="flex items-center gap-3 px-3 py-2.5 rounded-sm hover:bg-ink-90 transition">
                        <i class="fa-solid fa-microphone text-amber text-xs w-5 text-center"></i>
                        <span class="text-sm text-ink-30">Spraakherkenning</span>
                    </a>
                    <a href="/facturen-werkbonnen" class="flex items-center gap-3 px-3 py-2.5 rounded-sm hover:bg-ink-90 transition">
                        <i class="fa-solid fa-file-invoice text-amber text-xs w-5 text-center"></i>
                        <span class="text-sm text-ink-30">Facturen & werkbonnen</span>
                    </a>
                    <a href="/fotoherkenning" class="flex items-center gap-3 px-3 py-2.5 rounded-sm hover:bg-ink-90 transition">
                        <i class="fa-solid fa-camera text-amber text-xs w-5 text-center"></i>
                        <span class="text-sm text-ink-30">Fotoherkenning</span>
                    </a>
                    <a href="/integraties" class="flex items-center gap-3 px-3 py-2.5 rounded-sm hover:bg-ink-90 transition">
                        <i class="fa-solid fa-puzzle-piece text-amber text-xs w-5 text-center"></i>
                        <span class="text-sm text-ink-30">Integraties</span>
                    </a>
                    <a href="/beveiliging" class="flex items-center gap-3 px-3 py-2.5 rounded-sm hover:bg-ink-90 transition">
                        <i class="fa-solid fa-shield-halved text-amber text-xs w-5 text-center"></i>
                        <span class="text-sm text-ink-30">Beveiliging & AVG</span>
                    </a>
                </div>
            </div>

            {{-- Voor wie accordion --}}
            <div class="border-b border-ink-70/15">
                <button class="mobile-accordion-btn w-full flex items-center justify-between py-4 text-sm text-paper font-heading font-semibold">
                    Voor wie
                    <i class="fa-solid fa-chevron-down text-[10px] text-ink-50 transition-transform duration-200"></i>
                </button>
                <div class="mobile-accordion-panel hidden pb-4 space-y-1">
                    <a href="/zzp-bouw" class="flex items-center gap-3 px-3 py-2.5 rounded-sm hover:bg-ink-90 transition">
                        <i class="fa-solid fa-helmet-safety text-amber text-xs w-5 text-center"></i>
                        <span class="text-sm text-ink-30">ZZP'ers in de bouw</span>
                    </a>
                    <a href="/kleine-aannemers" class="flex items-center gap-3 px-3 py-2.5 rounded-sm hover:bg-ink-90 transition">
                        <i class="fa-solid fa-people-group text-amber text-xs w-5 text-center"></i>
                        <span class="text-sm text-ink-30">Kleine aannemers</span>
                    </a>
                    <a href="/installateurs" class="flex items-center gap-3 px-3 py-2.5 rounded-sm hover:bg-ink-90 transition">
                        <i class="fa-solid fa-wrench text-amber text-xs w-5 text-center"></i>
                        <span class="text-sm text-ink-30">Installateurs</span>
                    </a>
                    <a href="/schilders-stukadoors" class="flex items-center gap-3 px-3 py-2.5 rounded-sm hover:bg-ink-90 transition">
                        <i class="fa-solid fa-paint-roller text-amber text-xs w-5 text-center"></i>
                        <span class="text-sm text-ink-30">Schilders & stukadoors</span>
                    </a>
                </div>
            </div>

            {{-- Prijzen --}}
            <a href="/prijzen" class="block py-4 text-sm text-paper font-heading font-semibold border-b border-ink-70/15">Prijzen</a>

            {{-- Hulp accordion --}}
            <div class="border-b border-ink-70/15">
                <button class="mobile-accordion-btn w-full flex items-center justify-between py-4 text-sm text-paper font-heading font-semibold">
                    Hulp
                    <i class="fa-solid fa-chevron-down text-[10px] text-ink-50 transition-transform duration-200"></i>
                </button>
                <div class="mobile-accordion-panel hidden pb-4 space-y-1">
                    <a href="/faq" class="flex items-center gap-3 px-3 py-2.5 rounded-sm hover:bg-ink-90 transition">
                        <i class="fa-solid fa-circle-question text-amber text-xs w-5 text-center"></i>
                        <span class="text-sm text-ink-30">Veelgestelde vragen</span>
                    </a>
                    <a href="/blog" class="flex items-center gap-3 px-3 py-2.5 rounded-sm hover:bg-ink-90 transition">
                        <i class="fa-solid fa-book text-amber text-xs w-5 text-center"></i>
                        <span class="text-sm text-ink-30">Blog & tips</span>
                    </a>
                    <a href="/contact" class="flex items-center gap-3 px-3 py-2.5 rounded-sm hover:bg-ink-90 transition">
                        <i class="fa-solid fa-envelope text-amber text-xs w-5 text-center"></i>
                        <span class="text-sm text-ink-30">Contact</span>
                    </a>
                </div>
            </div>

            {{-- Mobile CTA's --}}
            <div class="flex flex-col gap-3 mt-6">
                <a href="/register" class="text-center bg-amber px-4 py-3 text-ink font-semibold text-sm font-heading transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)]">Gratis proberen</a>
                <a href="/login" class="text-center border border-paper/20 px-4 py-3 text-paper font-semibold text-sm font-heading transition hover:border-paper/40 hover:bg-paper/10">Inloggen</a>
            </div>
        </div>
    </nav>

    <style>
        .wave-bar {
            display: inline-block;
            width: 3px;
            margin: 0 1.5px;
            background: #FFB400;
            border-radius: 2px;
            animation: wave 1.2s ease-in-out infinite;
        }
        .wave-bar:nth-child(1)  { height: 10px; animation-delay: 0s; }
        .wave-bar:nth-child(2)  { height: 20px; animation-delay: 0.1s; }
        .wave-bar:nth-child(3)  { height: 14px; animation-delay: 0.15s; }
        .wave-bar:nth-child(4)  { height: 28px; animation-delay: 0.25s; }
        .wave-bar:nth-child(5)  { height: 18px; animation-delay: 0.35s; }
        .wave-bar:nth-child(6)  { height: 24px; animation-delay: 0.45s; }
        .wave-bar:nth-child(7)  { height: 12px; animation-delay: 0.55s; }
        .wave-bar:nth-child(8)  { height: 22px; animation-delay: 0.65s; }
        .wave-bar:nth-child(9)  { height: 16px; animation-delay: 0.75s; }
        .wave-bar:nth-child(10) { height: 8px;  animation-delay: 0.85s; }
        @keyframes wave {
            0%, 100% { transform: scaleY(0.4); }
            50%      { transform: scaleY(1); }
        }

        /* Bento: typewriter */
        .bento-typewriter span {
            opacity: 0;
            animation: bento-fade-in 0.3s ease forwards;
        }
        @keyframes bento-fade-in {
            to { opacity: 1; }
        }

        /* Bento: scanning line */
        .bento-scan-line {
            position: absolute;
            left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #FFB400, transparent);
            animation: bento-scan 2.5s ease-in-out infinite;
        }
        @keyframes bento-scan {
            0%   { top: 10%; opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 1; }
            100% { top: 85%; opacity: 0; }
        }

        /* Bento: form field fill */
        .bento-fill {
            animation: bento-fill-width 1.8s ease forwards;
        }
        @keyframes bento-fill-width {
            from { width: 0; }
        }

        /* Bento: vertical bar grow */
        .bento-bar {
            transform-origin: bottom;
            animation: bento-fill-height 1.8s ease forwards;
        }
        @keyframes bento-fill-height {
            from { transform: scaleY(0); }
            to   { transform: scaleY(1); }
        }

        /* Bento: slide-up rows */
        .bento-row { animation: bento-slide-up 0.5s ease both; }
        @keyframes bento-slide-up {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Bento: pulse dot */
        .bento-pulse {
            animation: bento-pulse 2s ease-in-out infinite;
        }
        @keyframes bento-pulse {
            0%, 100% { transform: scale(1); opacity: 0.7; }
            50%      { transform: scale(1.3); opacity: 1; }
        }

        /* Bento: counter */
        .bento-tick span {
            display: inline-block;
            animation: bento-tick-roll 1s ease both;
        }
        @keyframes bento-tick-roll {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Bento: send fly */
        .bento-send {
            animation: bento-fly 2.5s ease-in-out infinite;
        }
        @keyframes bento-fly {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 1; }
            40%      { transform: translate(12px, -8px) scale(0.95); opacity: 0.8; }
            50%      { transform: translate(50px, -30px) scale(0.6); opacity: 0; }
            51%      { transform: translate(0, 0) scale(0.6); opacity: 0; }
            70%      { transform: translate(0, 0) scale(1); opacity: 1; }
        }

        /* Bento: sync spin */
        .bento-sync {
            animation: bento-sync-spin 3s ease-in-out infinite;
        }
        @keyframes bento-sync-spin {
            0%, 70%, 100% { transform: rotate(0deg); }
            85%           { transform: rotate(360deg); }
        }

        /* Marquee */
        .animate-marquee {
            animation: marquee 35s linear infinite;
        }
        .animate-marquee-reverse {
            animation: marquee-reverse 35s linear infinite;
        }
        @keyframes marquee {
            0%   { transform: translateX(0); }
            100% { transform: translateX(calc(-100% - 1rem)); }
        }
        @keyframes marquee-reverse {
            0%   { transform: translateX(calc(-100% - 1rem)); }
            100% { transform: translateX(0); }
        }
        .marquee-mask {
            mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
            -webkit-mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
        }
    </style>

    <div class="w-full min-h-[900px] flex items-center bg-ink" style="background-image: radial-gradient(circle, rgba(255,180,0,0.07) 1px, transparent 1px); background-size: 24px 24px;">
        <div class="max-w-7xl w-full h-full mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center py-32">
            {{-- Linker kolom: tekst --}}
            <div>
                <p class="text-sm uppercase font-heading tracking-widest text-amber ml-1 mb-6 flex items-center gap-4">
                    <span class="w-[35px] h-[2px] bg-amber"></span><span>Administratie-assistent voor de vakman</span>
                </p>
                <h1 class="font-display text-paper text-9xl font-medium uppercase">
                    <span>Inspreken.<br><span class="flex items-end">Klaar <div class="w-18 h-18 bg-amber mb-2.5 ml-3"></div></span></span>
                </h1>
                <p class="font-sans text-paper text-md max-w-[500px] font-light opacity-80 mt-8 mb-10 leading-6">
                    De klus klaar, de administratie ook. Zonder één veld in te tikken. Spreek na de klus twintig seconden in. Klaar maakt de werkbon, prikt de uren, herkent het materiaal en zet de factuur klaar.
                </p>
                <div class="flex items-center gap-3">
                    <a href="/register" class="border border-amber bg-amber px-4 py-2.5 text-ink font-semibold text-sm font-heading transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)]">Probeer 14 dagen gratis</a>
                    <a href="/hoe-het-werkt" class="border border-paper px-4 py-2.5 text-paper font-semibold text-sm font-heading transition hover:bg-paper/10">Bekijk hoe het werkt</a>
                </div>
                <p class="font-sans text-paper text-sm max-w-[500px] font-light opacity-80 mt-4 leading-6">
                    Geen creditcard · Maandelijks opzegbaar · <span class="text-amber font-bold">Data in de EU</span>
                </p>
            </div>
            <div class="hidden lg:flex justify-center">
                <div class="relative">
                    {{-- Zwevende USPs --}}
                    <div class="absolute -left-55 top-12 flex items-center gap-2.5 bg-ink-90 border border-ink-70/20 rounded-sm px-4 py-2.5 shadow-lg">
                        <div class="w-7 h-7 rounded-md bg-amber/10 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-microphone text-amber text-[10px]"></i>
                        </div>
                        <div>
                            <span class="text-[11px] font-heading font-semibold text-paper block">Spraakherkenning</span>
                            <span class="text-[9px] text-ink-50">Nederlands, dialect & vakjargon</span>
                        </div>
                    </div>

                    <div class="absolute -right-50 top-14 flex items-center gap-2.5 bg-ink-90 border border-ink-70/20 rounded-sm px-4 py-2.5 shadow-lg">
                        <div class="w-7 h-7 rounded-md bg-amber/10 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-bolt text-amber text-[10px]"></i>
                        </div>
                        <div>
                            <span class="text-[11px] font-heading font-semibold text-paper block">20 seconden</span>
                            <span class="text-[9px] text-ink-50">Gemiddelde inspraaktijd</span>
                        </div>
                    </div>

                    <div class="absolute -left-60 top-1/2 -translate-y-1/2 flex items-center gap-2.5 bg-ink-90 border border-ink-70/20 rounded-sm px-4 py-2.5 shadow-lg">
                        <div class="w-7 h-7 rounded-md bg-amber/10 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-camera text-amber text-[10px]"></i>
                        </div>
                        <div>
                            <span class="text-[11px] font-heading font-semibold text-paper block">Fotoherkenning</span>
                            <span class="text-[9px] text-ink-50">Bonnen & pakbonnen scannen</span>
                        </div>
                    </div>

                    <div class="absolute -right-55 bottom-2/4 flex items-center gap-2.5 bg-ink-90 border border-ink-70/20 rounded-sm px-4 py-2.5 shadow-lg">
                        <div class="w-7 h-7 rounded-md bg-amber/10 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-file-invoice text-amber text-[10px]"></i>
                        </div>
                        <div>
                            <span class="text-[11px] font-heading font-semibold text-paper block">Factuur in &eacute;&eacute;n klik</span>
                            <span class="text-[9px] text-ink-50">Van inspraak naar PDF</span>
                        </div>
                    </div>

                    <div class="absolute -left-45 bottom-20 flex items-center gap-2.5 bg-ink-90 border border-ink-70/20 rounded-sm px-4 py-2.5 shadow-lg">
                        <div class="w-7 h-7 rounded-md bg-amber/10 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-shield-halved text-amber text-[10px]"></i>
                        </div>
                        <div>
                            <span class="text-[11px] font-heading font-semibold text-paper block">AVG-compliant</span>
                            <span class="text-[9px] text-ink-50">EU-servers, versleuteld</span>
                        </div>
                    </div>

                    <div class="absolute -right-50 bottom-12 flex items-center gap-2.5 bg-ink-90 border border-ink-70/20 rounded-sm px-4 py-2.5 shadow-lg">
                        <div class="w-7 h-7 rounded-md bg-amber/10 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-clock text-amber text-[10px]"></i>
                        </div>
                        <div>
                            <span class="text-[11px] font-heading font-semibold text-paper block">Urenregistratie</span>
                            <span class="text-[9px] text-ink-50">Automatisch uit je inspraak</span>
                        </div>
                    </div>

                <div class="relative" style="width: 280px; height: 570px; background: #17130E; border-radius: 40px; border: 3px solid #2E2A23; overflow: hidden;">
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[100px] h-[28px] bg-[#17130E] rounded-b-2xl z-10"></div>
                    <div class="absolute inset-[8px] bg-paper rounded-[34px] overflow-hidden px-3.5 pt-11 pb-3.5 flex flex-col">
                        <div class="flex items-center justify-between mb-4 px-1">
                            <img src="/logo/klaar-ink.svg" alt="" class="h-6 -ml-1">
                            <div class="w-6 h-6 rounded-full bg-ink-10 flex items-center justify-center">
                                <span class="text-[8px] font-bold text-ink">JV</span>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col items-center justify-center">
                            <div class="w-16 h-16 rounded-full bg-amber flex items-center justify-center" style="box-shadow: 0 4px 20px rgba(255,180,0,0.4);">
                                <div class="w-5 h-5 bg-ink rounded-full"></div>
                            </div>
                            <div class="mt-3 flex items-center h-6">
                                <span class="wave-bar"></span>
                                <span class="wave-bar"></span>
                                <span class="wave-bar"></span>
                                <span class="wave-bar"></span>
                                <span class="wave-bar"></span>
                                <span class="wave-bar"></span>
                                <span class="wave-bar"></span>
                                <span class="wave-bar"></span>
                                <span class="wave-bar"></span>
                                <span class="wave-bar"></span>
                            </div>
                            <div class="mt-1 font-mono text-[0.7rem] text-ink-50">00:09</div>
                            <div class="mt-4 w-full bg-amber/5 border-l-2 border-amber rounded-r-md p-3">
                                <p class="text-[0.6rem] text-ink-70 leading-relaxed italic">"Bij mevrouw Jansen cv-ketel vervangen, Remeha Tzerra, drie uur, plus een expansievat en wat koppelingen"</p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 mt-3">
                            <div class="text-center py-1.5 bg-amber rounded-md text-[0.55rem] font-semibold text-ink">Ja, klopt</div>
                            <div class="text-center py-1.5 border border-ink-10 rounded-md text-[0.55rem] font-medium text-ink-50">Nee, opnieuw</div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════ BENTO FUNCTIES ═══════════ --}}
    <div class="py-32 bg-paper">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-6xl mx-auto mb-16">
                <p class="text-sm uppercase font-heading tracking-widest text-amber mb-6 flex items-center justify-center gap-4">
                    <span class="w-[35px] h-[2px] bg-amber"></span><span>Functies</span><span class="w-[35px] h-[2px] bg-amber"></span>
                </p>
                <h2 class="font-display text-ink text-6xl uppercase text-center">Klaar<span class="text-amber">.</span> verandert twintig seconden inspreken<br>in een <span class="text-amber">complete administratie</span>.</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 auto-rows-[280px] lg:grid-rows-[280px_360px]">

                {{-- 1. Spraakherkenning (breed, 4 kolommen) --}}
                <div class="lg:col-span-4 bg-ink rounded-sm p-8 flex flex-col justify-between overflow-hidden relative" style="background-image: radial-gradient(circle, rgba(255,180,0,0.05) 1px, transparent 1px); background-size: 20px 20px;">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-8 rounded-md bg-amber/10 flex items-center justify-center">
                                <i class="fa-solid fa-microphone text-amber text-sm"></i>
                            </div>
                            <h3 class="font-heading font-semibold text-paper text-sm">Spraakherkenning</h3>
                        </div>
                        <p class="text-xs text-ink-50 leading-relaxed max-w-sm">Spreek in het Nederlands,<br>inclusief vakjargon en dialect. Klaar begrijpt je.</p>
                    </div>
                    <div class="mt-auto">
                        {{-- Live transcriptie-demo --}}
                        <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-6 h-6 rounded-full bg-amber/20 flex items-center justify-center">
                                    <div class="w-2 h-2 rounded-full bg-amber bento-pulse"></div>
                                </div>
                                <div class="flex items-center gap-[2px] h-5">
                                    <span class="wave-bar"></span><span class="wave-bar"></span><span class="wave-bar"></span>
                                    <span class="wave-bar"></span><span class="wave-bar"></span><span class="wave-bar"></span>
                                    <span class="wave-bar"></span><span class="wave-bar"></span>
                                </div>
                                <span class="text-[10px] font-mono text-ink-50 ml-auto">00:14</span>
                            </div>
                            <div class="bento-typewriter text-sm text-ink-30 leading-relaxed italic">
                                <span style="animation-delay:0s">"Bij </span>
                                <span style="animation-delay:0.3s">mevrouw </span>
                                <span style="animation-delay:0.5s">Jansen </span>
                                <span style="animation-delay:0.8s">cv-ketel </span>
                                <span style="animation-delay:1.0s">vervangen, </span>
                                <span style="animation-delay:1.3s">Remeha </span>
                                <span style="animation-delay:1.5s">Tzerra, </span>
                                <span style="animation-delay:1.8s">drie </span>
                                <span style="animation-delay:1.9s">uur, </span>
                                <span style="animation-delay:2.2s">plus </span>
                                <span style="animation-delay:2.4s">een </span>
                                <span style="animation-delay:2.5s">expansie&shy;vat"</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Fotoherkenning (2 kolommen) --}}
                <div class="lg:col-span-2 bg-ink rounded-sm p-6 flex flex-col overflow-hidden relative" style="background-image: radial-gradient(circle, rgba(255,180,0,0.05) 1px, transparent 1px); background-size: 20px 20px;">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 rounded-md bg-amber/10 flex items-center justify-center">
                            <i class="fa-solid fa-camera text-amber text-sm"></i>
                        </div>
                        <h3 class="font-heading font-semibold text-paper text-sm">Fotoherkenning</h3>
                    </div>
                    <p class="text-xs text-ink-50 leading-relaxed mb-4">Maak een foto van een bon. AI leest alles uit.</p>
                    {{-- Bon-mockup met scanlijn --}}
                    <div class="flex-1 bg-paper rounded-sm p-4 relative overflow-hidden">
                        <div class="bento-scan-line"></div>
                        <div class="space-y-2.5">
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-heading font-bold text-ink">BOUWMARKT PRAXIS</span>
                                <span class="text-[9px] text-ink-50">04-06-2026</span>
                            </div>
                            <div class="border-t border-dashed border-ink-10"></div>
                            <div class="flex justify-between bento-row" style="animation-delay:0.5s">
                                <span class="text-[10px] text-ink-70">Expansievat 18L</span>
                                <span class="text-[10px] font-semibold text-ink bg-amber/15 px-1 rounded">&euro; 34,95</span>
                            </div>
                            <div class="flex justify-between bento-row" style="animation-delay:0.8s">
                                <span class="text-[10px] text-ink-70">Koppelingen 15mm 3x</span>
                                <span class="text-[10px] font-semibold text-ink bg-amber/15 px-1 rounded">&euro; 12,45</span>
                            </div>
                            <div class="flex justify-between bento-row" style="animation-delay:1.1s">
                                <span class="text-[10px] text-ink-70">Teflon tape</span>
                                <span class="text-[10px] font-semibold text-ink bg-amber/15 px-1 rounded">&euro; 3,49</span>
                            </div>
                            <div class="border-t border-ink-10 pt-1.5 flex justify-between bento-row" style="animation-delay:1.5s">
                                <span class="text-[10px] font-bold text-ink">Totaal</span>
                                <span class="text-[10px] font-bold text-ink">&euro; 50,89</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Werkbon generatie (2 kolommen) --}}
                <div class="lg:col-span-2 bg-ink rounded-sm p-6 flex flex-col overflow-hidden relative" style="background-image: radial-gradient(circle, rgba(255,180,0,0.05) 1px, transparent 1px); background-size: 20px 20px;">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 rounded-md bg-amber/10 flex items-center justify-center">
                            <i class="fa-solid fa-clipboard-list text-amber text-sm"></i>
                        </div>
                        <h3 class="font-heading font-semibold text-paper text-sm">Werkbonnen</h3>
                    </div>
                    <p class="text-xs text-ink-50 leading-relaxed mb-4">Automatisch ingevuld vanuit je inspraak.</p>
                    {{-- Werkbon mockup --}}
                    <div class="flex-1 bg-ink-90 rounded-sm p-4 border border-ink-70/20 space-y-3">
                        <div class="bento-row" style="animation-delay:0.3s">
                            <span class="text-[9px] text-ink-50 uppercase tracking-wider">Klant</span>
                            <div class="mt-1 h-5 bg-ink-70/20 rounded-sm overflow-hidden">
                                <div class="bento-fill h-full bg-amber/15 flex items-center px-2" style="width:75%; animation-delay:0.5s">
                                    <span class="text-[10px] text-paper whitespace-nowrap">Mevr. Jansen</span>
                                </div>
                            </div>
                        </div>
                        <div class="bento-row" style="animation-delay:0.6s">
                            <span class="text-[9px] text-ink-50 uppercase tracking-wider">Werkzaamheden</span>
                            <div class="mt-1 h-5 bg-ink-70/20 rounded-sm overflow-hidden">
                                <div class="bento-fill h-full bg-amber/15 flex items-center px-2" style="width:90%; animation-delay:0.9s">
                                    <span class="text-[10px] text-paper whitespace-nowrap">CV-ketel vervangen (Remeha Tzerra)</span>
                                </div>
                            </div>
                        </div>
                        <div class="bento-row" style="animation-delay:0.9s">
                            <span class="text-[9px] text-ink-50 uppercase tracking-wider">Uren</span>
                            <div class="mt-1 h-5 bg-ink-70/20 rounded-sm overflow-hidden">
                                <div class="bento-fill h-full bg-amber/15 flex items-center px-2" style="width:25%; animation-delay:1.3s">
                                    <span class="text-[10px] text-paper">3:00</span>
                                </div>
                            </div>
                        </div>
                        <div class="bento-row" style="animation-delay:1.2s">
                            <span class="text-[9px] text-ink-50 uppercase tracking-wider">Materiaal</span>
                            <div class="mt-1 h-5 bg-ink-70/20 rounded-sm overflow-hidden">
                                <div class="bento-fill h-full bg-amber/15 flex items-center px-2" style="width:85%; animation-delay:1.6s">
                                    <span class="text-[10px] text-paper whitespace-nowrap">Expansievat, koppelingen, tape</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. Factuur versturen (2 kolommen) --}}
                <div class="lg:col-span-2 bg-ink rounded-sm p-6 flex flex-col overflow-hidden relative" style="background-image: radial-gradient(circle, rgba(255,180,0,0.05) 1px, transparent 1px); background-size: 20px 20px;">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 rounded-md bg-amber/10 flex items-center justify-center">
                            <i class="fa-solid fa-file-invoice text-amber text-sm"></i>
                        </div>
                        <h3 class="font-heading font-semibold text-paper text-sm">Facturen versturen</h3>
                    </div>
                    <p class="text-xs text-ink-50 leading-relaxed mb-4">Professionele PDF, direct per e-mail.</p>
                    {{-- Factuur mini-preview --}}
                    <div class="flex-1 flex flex-col items-center justify-center">
                        <div class="w-full max-w-[180px] bg-paper rounded-sm p-4 shadow-lg relative">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <div class="w-12 h-2 bg-ink rounded-sm mb-1"></div>
                                    <div class="w-8 h-1.5 bg-ink-10 rounded-sm"></div>
                                </div>
                                <span class="text-[8px] font-mono text-ink-50">F-2026-047</span>
                            </div>
                            <div class="space-y-1 mb-3">
                                <div class="flex justify-between">
                                    <div class="w-16 h-1.5 bg-ink-10 rounded-sm"></div>
                                    <div class="w-8 h-1.5 bg-ink-10 rounded-sm"></div>
                                </div>
                                <div class="flex justify-between">
                                    <div class="w-20 h-1.5 bg-ink-10 rounded-sm"></div>
                                    <div class="w-6 h-1.5 bg-ink-10 rounded-sm"></div>
                                </div>
                            </div>
                            <div class="border-t border-ink-10 pt-2 flex justify-between items-center">
                                <span class="text-[8px] font-bold text-ink">Totaal</span>
                                <span class="text-[9px] font-bold text-ink">&euro; 487,50</span>
                            </div>
                            {{-- Verstuur-animatie --}}
                            <div class="absolute -top-2 -right-2 w-8 h-8 bg-amber rounded-full flex items-center justify-center shadow-md">
                                <i class="fa-solid fa-paper-plane text-ink text-[10px] bento-send"></i>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center gap-2 bento-row" style="animation-delay:1.5s">
                            <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                            <span class="text-[10px] text-ink-30">Verstuurd naar j.jansen@email.nl</span>
                        </div>
                    </div>
                </div>

                {{-- 5. Urenregistratie (2 kolommen) --}}
                <div class="lg:col-span-2 bg-ink rounded-sm p-6 flex flex-col overflow-hidden relative" style="background-image: radial-gradient(circle, rgba(255,180,0,0.05) 1px, transparent 1px); background-size: 20px 20px;">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 rounded-md bg-amber/10 flex items-center justify-center">
                            <i class="fa-solid fa-clock text-amber text-sm"></i>
                        </div>
                        <h3 class="font-heading font-semibold text-paper text-sm">Urenregistratie</h3>
                    </div>
                    <p class="text-xs text-ink-50 leading-relaxed mb-4">Uren worden automatisch uit je inspraak gehaald.</p>
                    {{-- Week-overzicht --}}
                    <div class="flex-1 flex flex-col justify-end gap-2">
                        <div class="flex gap-2 h-full">
                            <div class="flex-1 flex flex-col items-center justify-end gap-1">
                                <div class="w-full bg-amber/20 rounded-sm bento-bar" style="height:60%; animation-delay:0.2s"></div>
                                <span class="text-[9px] text-ink-50">Ma</span>
                            </div>
                            <div class="flex-1 flex flex-col items-center justify-end gap-1">
                                <div class="w-full bg-amber/30 rounded-sm bento-bar" style="height:85%; animation-delay:0.4s"></div>
                                <span class="text-[9px] text-ink-50">Di</span>
                            </div>
                            <div class="flex-1 flex flex-col items-center justify-end gap-1">
                                <div class="w-full bg-amber/40 rounded-sm bento-bar" style="height:70%; animation-delay:0.6s"></div>
                                <span class="text-[9px] text-ink-50">Wo</span>
                            </div>
                            <div class="flex-1 flex flex-col items-center justify-end gap-1">
                                <div class="w-full bg-amber rounded-sm bento-bar" style="height:90%; animation-delay:0.8s"></div>
                                <span class="text-[9px] text-ink-50">Do</span>
                            </div>
                            <div class="flex-1 flex flex-col items-center justify-end gap-1">
                                <div class="w-full bg-amber/25 rounded-sm bento-bar" style="height:45%; animation-delay:1.0s"></div>
                                <span class="text-[9px] text-ink-50">Vr</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-ink-70/20">
                            <span class="text-[10px] text-ink-50">Deze week</span>
                            <div class="bento-tick">
                                <span class="text-sm font-heading font-bold text-amber" style="animation-delay:1.2s">38,5</span>
                                <span class="text-[10px] text-ink-50 ml-1" style="animation-delay:1.4s">uur</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="flex items-center justify-center gap-3 mt-14">
                <a href="/register" class="border border-amber bg-amber px-4 py-2.5 text-ink font-semibold text-sm font-heading transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)]">Start gratis</a>
                <a href="/prijzen" class="border border-ink px-4 py-2.5 text-ink font-semibold text-sm font-heading transition hover:bg-ink hover:text-paper">Bekijk onze prijzen</a>
            </div>
        </div>
    </div>

    {{-- ═══════════ PROBLEEM / OPLOSSING &ndash; BEFORE / AFTER ═══════════ --}}
    <div class="py-32 bg-paper">
        <div class="max-w-7xl mx-auto px-6">
            <div class="mb-20">
                <p class="text-sm uppercase font-heading tracking-widest text-amber mb-6 flex items-center gap-4">
                    <span class="w-[35px] h-[2px] bg-amber"></span><span>Het verschil</span></span>
                </p>
                <h2 class="font-display text-ink text-4xl lg:text-6xl uppercase">De oude manier<br>vs. <span class="text-amber">de Klaar manier</span><span class="text-amber">.</span></h2>
            </div>

            <div class="grid md:grid-cols-2 gap-0 items-stretch">
                {{-- Before: the old way --}}
                <div class="bg-snow border border-ink-10 rounded-l-sm p-10 relative">
                    <div class="absolute top-6 right-6">
                        <span class="text-xs font-heading font-semibold text-ink-50 bg-ink-10 px-3 py-1 rounded-sm uppercase tracking-wider">Vroeger</span>
                    </div>
                    <div class="w-14 h-14 rounded-lg bg-ink-10 flex items-center justify-center mb-6">
                        <i class="fa-solid fa-keyboard text-ink-50 text-xl"></i>
                    </div>
                    <h3 class="font-display text-ink text-2xl uppercase mb-6">Typen op de bouwplaats werkt niet<span class="text-ink-30">.</span></h3>
                    <p class="text-sm text-ink-70 leading-relaxed mb-4">Je staat op een steiger, je handen zitten onder de kit of je bent net klaar met een lange dag tegels zetten. Het laatste waar je zin in hebt is achter een laptop kruipen om uren in te vullen, materialen bij te houden en werkbonnen te maken.</p>
                    <p class="text-sm text-ink-70 leading-relaxed mb-4">Toch moet het. Want zonder administratie geen factuur, zonder factuur geen betaling. En dus schuif je het op &ndash; tot het weekend, tot 's avonds, tot het te laat is. Of je vergeet de helft.</p>
                    <p class="text-sm text-ink-70 leading-relaxed mb-8">Uit onderzoek blijkt dat vakmensen gemiddeld 5 tot 8 uur per week kwijt zijn aan administratie. Tijd die je niet kunt factureren. Tijd die je liever aan je gezin, je hobby of gewoon aan rust besteedt.</p>

                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-xmark text-red-500 text-[10px]"></i>
                            </div>
                            <span class="text-sm text-ink-70">Urenlang typen na een lange werkdag</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-xmark text-red-500 text-[10px]"></i>
                            </div>
                            <span class="text-sm text-ink-70">Vergeten wat je precies gedaan hebt</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-xmark text-red-500 text-[10px]"></i>
                            </div>
                            <span class="text-sm text-ink-70">Ingewikkelde formulieren invullen</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-xmark text-red-500 text-[10px]"></i>
                            </div>
                            <span class="text-sm text-ink-70">5&ndash;8 uur per week kwijt aan administratie</span>
                        </div>
                    </div>
                </div>

                {{-- After: the Klaar way --}}
                <div class="bg-ink rounded-r-sm p-10 relative" style="background-image: radial-gradient(circle, rgba(255,180,0,0.05) 1px, transparent 1px); background-size: 20px 20px;">
                    <div class="absolute top-6 right-6">
                        <span class="text-xs font-heading font-semibold text-ink bg-amber px-3 py-1 rounded-sm uppercase tracking-wider">Met Klaar</span>
                    </div>
                    <div class="w-14 h-14 rounded-lg bg-amber/10 flex items-center justify-center mb-6">
                        <i class="fa-solid fa-microphone text-amber text-xl"></i>
                    </div>
                    <h3 class="font-display text-paper text-2xl uppercase mb-6">Gewoon vertellen wat je gedaan hebt<span class="text-amber">.</span></h3>
                    <p class="text-sm text-ink-30 leading-relaxed mb-4">Met Klaar spreek je gewoon in wat je gedaan hebt &ndash; in je eigen woorden, zonder moeilijke formulieren. Klaar luistert, begrijpt wat je bedoelt en maakt er automatisch een werkbon of factuur van.</p>
                    <p class="text-sm text-ink-30 leading-relaxed mb-4">De spraakherkenning van Klaar is speciaal getraind voor de bouw. Vakjargon, klantnamen, materiaalnamen en veelgebruikte afkortingen worden foutloos herkend &ndash; ook als je spreekt met een accent of dialect.</p>
                    <p class="text-sm text-ink-30 leading-relaxed mb-8">In gemiddeld 20 seconden is je inspraak verwerkt. Klaar herkent automatisch de klant, de gewerkte uren, de gebruikte materialen en de uitgevoerde werkzaamheden. Jij hoeft alleen nog te bevestigen.</p>

                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-amber/20 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-check text-amber text-[10px]"></i>
                            </div>
                            <span class="text-sm text-ink-30">20 seconden inspreken, klaar</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-amber/20 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-check text-amber text-[10px]"></i>
                            </div>
                            <span class="text-sm text-ink-30">Direct na de klus, alles nog vers</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-amber/20 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-check text-amber text-[10px]"></i>
                            </div>
                            <span class="text-sm text-ink-30">Gewoon praten, geen formulieren</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-amber/20 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-check text-amber text-[10px]"></i>
                            </div>
                            <span class="text-sm text-ink-30">Bespaar 5+ uur per week</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════ HOE HET WERKT ═══════════ --}}
    <div id="hoe-het-werkt" class="pb-20 pt-32 bg-ink" style="background-image: radial-gradient(circle, rgba(255,180,0,0.07) 1px, transparent 1px); background-size: 24px 24px;">
        <div class="max-w-7xl mx-auto px-6">
            <div class="mb-20">
                <p class="text-sm uppercase font-heading tracking-widest text-amber mb-6 flex items-center gap-4">
                    <span class="w-[35px] h-[2px] bg-amber"></span><span>Hoe het werkt</span></span>
                </p>
                <h2 class="font-display text-paper text-6xl uppercase">Drie stappen.<br><span class="text-amber">Nul formulieren.</span></h2>
                <p class="text-sm text-ink-30 leading-relaxed mt-6">Van bouwplaats tot boekhouding, zonder een veld in te vullen. Zo werkt het.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6 relative">

                {{-- Stap 1: Inspreken --}}
                <div class="bg-ink-90/40 border border-ink-70/20 rounded-sm p-8 relative">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-lg bg-amber/10 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-microphone text-amber text-xl"></i>
                        </div>
                        <div>
                            <span class="text-xs font-heading font-semibold text-amber tracking-wider uppercase">Stap 01</span>
                            <h3 class="font-display text-2xl text-paper uppercase">Inspreken</h3>
                        </div>
                    </div>
                    <p class="text-sm text-ink-30 leading-relaxed mb-5">
                        Vertel na je werkdag wat je gedaan hebt. In je eigen woorden, op de bouwplaats, in de bus naar huis.
                    </p>
                    <ul class="space-y-2.5">
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-30">Spreek in via de app, 20 seconden is genoeg</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-30">Of maak een foto van een bon of pakbon</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-30">Vakjargon, accenten en dialect worden herkend</span>
                        </li>
                    </ul>
                </div>

                {{-- Stap 2: Ontrafelen --}}
                <div class="bg-ink-90/40 border border-ink-70/20 rounded-sm p-8 relative">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-lg bg-amber/10 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-wand-magic-sparkles text-amber text-xl"></i>
                        </div>
                        <div>
                            <span class="text-xs font-heading font-semibold text-amber tracking-wider uppercase">Stap 02</span>
                            <h3 class="font-display text-2xl text-paper uppercase">Ontrafelen</h3>
                        </div>
                    </div>
                    <p class="text-sm text-ink-30 leading-relaxed mb-5">
                        Klaar luistert, begrijpt en maakt er een complete werkbon van. Automatisch, in seconden.
                    </p>
                    <ul class="space-y-2.5">
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-30">Uren, materialen en bedragen worden herkend</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-30">Klantgegevens automatisch gekoppeld</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-30">Bonnen en pakbonnen worden uitgelezen via AI</span>
                        </li>
                    </ul>
                </div>

                {{-- Stap 3: Klaar. --}}
                <div class="bg-ink-90/40 border border-ink-70/20 rounded-sm p-8 relative">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-lg bg-amber/10 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-paper-plane text-amber text-xl"></i>
                        </div>
                        <div>
                            <span class="text-xs font-heading font-semibold text-amber tracking-wider uppercase">Stap 03</span>
                            <h3 class="font-display text-2xl text-paper uppercase">Klaar<span class="text-amber">.</span></h3>
                        </div>
                    </div>
                    <p class="text-sm text-ink-30 leading-relaxed mb-5">
                        Controleer, pas aan als nodig, en verstuur als factuur. Je boekhouding loopt automatisch mee.
                    </p>
                    <ul class="space-y-2.5">
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-30">Professionele PDF-factuur in één klik</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-30">Direct versturen per e-mail naar je klant</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-30">Koppel met Moneybird voor je boekhouding</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="flex items-center justify-center gap-3 mt-14">
                <a href="/register" class="border border-amber bg-amber px-4 py-2.5 text-ink font-semibold text-sm font-heading transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)]">Probeer 14 dagen gratis</a>
                <a href="/hoe-het-werkt" class="border border-paper/20 px-4 py-2.5 text-paper font-semibold text-sm font-heading transition hover:border-paper/40 hover:bg-paper/10">Bekijk alle functies</a>
            </div>
        </div>
    </div>

    {{-- CTA-balk --}}
    <div class="flex items-center justify-center py-10 bg-ink">
        <div class="max-w-7xl w-full mx-auto px-6">
            <div class="bg-amber rounded-sm p-10">
                <h3 class="font-display text-ink text-2xl lg:text-3xl uppercase leading-tight mb-4">Geen implementatiekosten<span class="text-ink/30">.</span> Geen consultant<span class="text-ink/30">.</span><br>Je verdient het terug op dag &eacute;&eacute;n<span class="text-ink/30">.</span></h3>
                <p class="text-sm text-ink/70 leading-relaxed max-w-2xl">Bespaar je 3&ndash;5 uur administratie per week, dan is &euro;49 per maand op de eerste dag al terugverdiend. Bij ERP-trajecten kost de implementatie 1&ndash;3&times; de licentie &ndash; bij Klaar nul.</p>
            </div>
        </div>
    </div>

    {{-- ═══════════ REVIEWS MARQUEE ═══════════ --}}
    <div class="pt-20 pb-32 bg-ink overflow-hidden" style="background-image: radial-gradient(circle, rgba(255,180,0,0.04) 1px, transparent 1px); background-size: 24px 24px;">
        <div class="max-w-7xl mx-auto px-6 mb-12">
            <p class="text-sm uppercase font-heading tracking-widest text-amber mb-6 flex items-center gap-4">
                <span class="w-[35px] h-[2px] bg-amber"></span><span>Reviews</span>
            </p>
            <h2 class="font-display text-paper text-5xl lg:text-6xl uppercase">Vakmannen over Klaar<span class="text-amber">.</span></h2>
        </div>

        {{-- Marquee rij 1 (links naar rechts) --}}
        <div class="relative flex gap-4 overflow-hidden mb-4 marquee-mask">
            <div class="flex gap-4 animate-marquee shrink-0">
                <div class="w-[380px] shrink-0 bg-ink-90 border border-ink-70/20 rounded-sm p-6">
                    <div class="flex items-center gap-1 mb-3">
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                    </div>
                    <p class="text-sm text-ink-30 leading-relaxed mb-4">"Ik deed altijd 's avonds nog een uur administratie. Nu spreek ik het in op de terugweg en het staat klaar. Scheelt me makkelijk 4 uur per week."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber/15 flex items-center justify-center"><span class="text-[10px] font-bold text-amber">JV</span></div>
                        <div>
                            <span class="text-xs font-heading font-semibold text-paper">Jan Vermeer</span>
                            <span class="text-[10px] text-ink-50 block">ZZP'er &middot; Loodgieter</span>
                        </div>
                    </div>
                </div>
                <div class="w-[380px] shrink-0 bg-ink-90 border border-ink-70/20 rounded-sm p-6">
                    <div class="flex items-center gap-1 mb-3">
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                    </div>
                    <p class="text-sm text-ink-30 leading-relaxed mb-4">"Mijn boekhouder is blij. Alles klopt, facturen gaan sneller de deur uit en ik hoef niks meer over te tikken. Werkt echt zoals beloofd."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber/15 flex items-center justify-center"><span class="text-[10px] font-bold text-amber">PB</span></div>
                        <div>
                            <span class="text-xs font-heading font-semibold text-paper">Pieter de Boer</span>
                            <span class="text-[10px] text-ink-50 block">Kleine aannemer &middot; 3 man</span>
                        </div>
                    </div>
                </div>
                <div class="w-[380px] shrink-0 bg-ink-90 border border-ink-70/20 rounded-sm p-6">
                    <div class="flex items-center gap-1 mb-3">
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                    </div>
                    <p class="text-sm text-ink-30 leading-relaxed mb-4">"Die fotoherkenning is geniaal. Bon van de bouwmarkt fotograferen, en alles staat al in de werkbon. Zelfs het handgeschreven bonnetje van de groothandel."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber/15 flex items-center justify-center"><span class="text-[10px] font-bold text-amber">RK</span></div>
                        <div>
                            <span class="text-xs font-heading font-semibold text-paper">Rick Kuijpers</span>
                            <span class="text-[10px] text-ink-50 block">Installateur &middot; CV & airco</span>
                        </div>
                    </div>
                </div>
                <div class="w-[380px] shrink-0 bg-ink-90 border border-ink-70/20 rounded-sm p-6">
                    <div class="flex items-center gap-1 mb-3">
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star-half-stroke text-amber text-xs"></i>
                    </div>
                    <p class="text-sm text-ink-30 leading-relaxed mb-4">"Eindelijk een app die snapt hoe wij werken. Geen ingewikkelde menu's, gewoon inspreken en klaar. Precies wat ik nodig had."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber/15 flex items-center justify-center"><span class="text-[10px] font-bold text-amber">MH</span></div>
                        <div>
                            <span class="text-xs font-heading font-semibold text-paper">Marco Hendriks</span>
                            <span class="text-[10px] text-ink-50 block">Schilder &middot; ZZP'er</span>
                        </div>
                    </div>
                </div>
                <div class="w-[380px] shrink-0 bg-ink-90 border border-ink-70/20 rounded-sm p-6">
                    <div class="flex items-center gap-1 mb-3">
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                    </div>
                    <p class="text-sm text-ink-30 leading-relaxed mb-4">"Wij werken met drie man. Iedereen spreekt z'n eigen werk in en ik heb aan het eind van de week alles bij elkaar. Top voor de planning."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber/15 flex items-center justify-center"><span class="text-[10px] font-bold text-amber">DV</span></div>
                        <div>
                            <span class="text-xs font-heading font-semibold text-paper">Dennis Visser</span>
                            <span class="text-[10px] text-ink-50 block">Stukadoor &middot; Kleine aannemer</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex gap-4 animate-marquee shrink-0" aria-hidden="true">
                <div class="w-[380px] shrink-0 bg-ink-90 border border-ink-70/20 rounded-sm p-6">
                    <div class="flex items-center gap-1 mb-3">
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                    </div>
                    <p class="text-sm text-ink-30 leading-relaxed mb-4">"Ik deed altijd 's avonds nog een uur administratie. Nu spreek ik het in op de terugweg en het staat klaar. Scheelt me makkelijk 4 uur per week."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber/15 flex items-center justify-center"><span class="text-[10px] font-bold text-amber">JV</span></div>
                        <div>
                            <span class="text-xs font-heading font-semibold text-paper">Jan Vermeer</span>
                            <span class="text-[10px] text-ink-50 block">ZZP'er &middot; Loodgieter</span>
                        </div>
                    </div>
                </div>
                <div class="w-[380px] shrink-0 bg-ink-90 border border-ink-70/20 rounded-sm p-6">
                    <div class="flex items-center gap-1 mb-3">
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                    </div>
                    <p class="text-sm text-ink-30 leading-relaxed mb-4">"Mijn boekhouder is blij. Alles klopt, facturen gaan sneller de deur uit en ik hoef niks meer over te tikken. Werkt echt zoals beloofd."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber/15 flex items-center justify-center"><span class="text-[10px] font-bold text-amber">PB</span></div>
                        <div>
                            <span class="text-xs font-heading font-semibold text-paper">Pieter de Boer</span>
                            <span class="text-[10px] text-ink-50 block">Kleine aannemer &middot; 3 man</span>
                        </div>
                    </div>
                </div>
                <div class="w-[380px] shrink-0 bg-ink-90 border border-ink-70/20 rounded-sm p-6">
                    <div class="flex items-center gap-1 mb-3">
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                    </div>
                    <p class="text-sm text-ink-30 leading-relaxed mb-4">"Die fotoherkenning is geniaal. Bon van de bouwmarkt fotograferen, en alles staat al in de werkbon. Zelfs het handgeschreven bonnetje van de groothandel."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber/15 flex items-center justify-center"><span class="text-[10px] font-bold text-amber">RK</span></div>
                        <div>
                            <span class="text-xs font-heading font-semibold text-paper">Rick Kuijpers</span>
                            <span class="text-[10px] text-ink-50 block">Installateur &middot; CV & airco</span>
                        </div>
                    </div>
                </div>
                <div class="w-[380px] shrink-0 bg-ink-90 border border-ink-70/20 rounded-sm p-6">
                    <div class="flex items-center gap-1 mb-3">
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star-half-stroke text-amber text-xs"></i>
                    </div>
                    <p class="text-sm text-ink-30 leading-relaxed mb-4">"Eindelijk een app die snapt hoe wij werken. Geen ingewikkelde menu's, gewoon inspreken en klaar. Precies wat ik nodig had."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber/15 flex items-center justify-center"><span class="text-[10px] font-bold text-amber">MH</span></div>
                        <div>
                            <span class="text-xs font-heading font-semibold text-paper">Marco Hendriks</span>
                            <span class="text-[10px] text-ink-50 block">Schilder &middot; ZZP'er</span>
                        </div>
                    </div>
                </div>
                <div class="w-[380px] shrink-0 bg-ink-90 border border-ink-70/20 rounded-sm p-6">
                    <div class="flex items-center gap-1 mb-3">
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                    </div>
                    <p class="text-sm text-ink-30 leading-relaxed mb-4">"Wij werken met drie man. Iedereen spreekt z'n eigen werk in en ik heb aan het eind van de week alles bij elkaar. Top voor de planning."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber/15 flex items-center justify-center"><span class="text-[10px] font-bold text-amber">DV</span></div>
                        <div>
                            <span class="text-xs font-heading font-semibold text-paper">Dennis Visser</span>
                            <span class="text-[10px] text-ink-50 block">Stukadoor &middot; Kleine aannemer</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Marquee rij 2 (rechts naar links) --}}
        <div class="relative flex gap-4 overflow-hidden marquee-mask">
            <div class="flex gap-4 animate-marquee-reverse shrink-0">
                <div class="w-[380px] shrink-0 bg-ink-90 border border-ink-70/20 rounded-sm p-6">
                    <div class="flex items-center gap-1 mb-3">
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                    </div>
                    <p class="text-sm text-ink-30 leading-relaxed mb-4">"Ik ben niet zo technisch, maar dit kan iedereen. Gewoon praten tegen je telefoon. Mijn vrouw doet nu de facturen in twee klikken."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber/15 flex items-center justify-center"><span class="text-[10px] font-bold text-amber">HJ</span></div>
                        <div>
                            <span class="text-xs font-heading font-semibold text-paper">Henk Jansen</span>
                            <span class="text-[10px] text-ink-50 block">Timmerman &middot; ZZP'er</span>
                        </div>
                    </div>
                </div>
                <div class="w-[380px] shrink-0 bg-ink-90 border border-ink-70/20 rounded-sm p-6">
                    <div class="flex items-center gap-1 mb-3">
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                    </div>
                    <p class="text-sm text-ink-30 leading-relaxed mb-4">"De koppeling met Moneybird bespaart me dubbel werk. Factuur versturen, en het staat meteen goed in de boekhouding. Geen exports meer."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber/15 flex items-center justify-center"><span class="text-[10px] font-bold text-amber">SB</span></div>
                        <div>
                            <span class="text-xs font-heading font-semibold text-paper">Stefan Bakker</span>
                            <span class="text-[10px] text-ink-50 block">Elektricien &middot; ZZP'er</span>
                        </div>
                    </div>
                </div>
                <div class="w-[380px] shrink-0 bg-ink-90 border border-ink-70/20 rounded-sm p-6">
                    <div class="flex items-center gap-1 mb-3">
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star-half-stroke text-amber text-xs"></i>
                    </div>
                    <p class="text-sm text-ink-30 leading-relaxed mb-4">"Binnen een week had ik het terugverdiend. Geen avonden meer achter de laptop. Nu drink ik 's avonds gewoon een biertje met een gerust hart."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber/15 flex items-center justify-center"><span class="text-[10px] font-bold text-amber">TM</span></div>
                        <div>
                            <span class="text-xs font-heading font-semibold text-paper">Tom Mulder</span>
                            <span class="text-[10px] text-ink-50 block">Dakdekker &middot; Kleine aannemer</span>
                        </div>
                    </div>
                </div>
                <div class="w-[380px] shrink-0 bg-ink-90 border border-ink-70/20 rounded-sm p-6">
                    <div class="flex items-center gap-1 mb-3">
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                    </div>
                    <p class="text-sm text-ink-30 leading-relaxed mb-4">"Ik heb van die dikke vingers, typen op een telefoon is niks voor mij. Met Klaar hoeft dat niet meer. Gewoon praten, hij begrijpt me."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber/15 flex items-center justify-center"><span class="text-[10px] font-bold text-amber">WD</span></div>
                        <div>
                            <span class="text-xs font-heading font-semibold text-paper">Willem de Groot</span>
                            <span class="text-[10px] text-ink-50 block">Metselaar &middot; ZZP'er</span>
                        </div>
                    </div>
                </div>
                <div class="w-[380px] shrink-0 bg-ink-90 border border-ink-70/20 rounded-sm p-6">
                    <div class="flex items-center gap-1 mb-3">
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                    </div>
                    <p class="text-sm text-ink-30 leading-relaxed mb-4">"Klant belt, klus gedaan, werkbon ingesproken, factuur verstuurd. Alles op dezelfde dag. Dat lukte me vroeger nooit."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber/15 flex items-center justify-center"><span class="text-[10px] font-bold text-amber">KZ</span></div>
                        <div>
                            <span class="text-xs font-heading font-semibold text-paper">Koen Zwart</span>
                            <span class="text-[10px] text-ink-50 block">Glaszetter &middot; ZZP'er</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex gap-4 animate-marquee-reverse shrink-0" aria-hidden="true">
                <div class="w-[380px] shrink-0 bg-ink-90 border border-ink-70/20 rounded-sm p-6">
                    <div class="flex items-center gap-1 mb-3">
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                    </div>
                    <p class="text-sm text-ink-30 leading-relaxed mb-4">"Ik ben niet zo technisch, maar dit kan iedereen. Gewoon praten tegen je telefoon. Mijn vrouw doet nu de facturen in twee klikken."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber/15 flex items-center justify-center"><span class="text-[10px] font-bold text-amber">HJ</span></div>
                        <div>
                            <span class="text-xs font-heading font-semibold text-paper">Henk Jansen</span>
                            <span class="text-[10px] text-ink-50 block">Timmerman &middot; ZZP'er</span>
                        </div>
                    </div>
                </div>
                <div class="w-[380px] shrink-0 bg-ink-90 border border-ink-70/20 rounded-sm p-6">
                    <div class="flex items-center gap-1 mb-3">
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                    </div>
                    <p class="text-sm text-ink-30 leading-relaxed mb-4">"De koppeling met Moneybird bespaart me dubbel werk. Factuur versturen, en het staat meteen goed in de boekhouding. Geen exports meer."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber/15 flex items-center justify-center"><span class="text-[10px] font-bold text-amber">SB</span></div>
                        <div>
                            <span class="text-xs font-heading font-semibold text-paper">Stefan Bakker</span>
                            <span class="text-[10px] text-ink-50 block">Elektricien &middot; ZZP'er</span>
                        </div>
                    </div>
                </div>
                <div class="w-[380px] shrink-0 bg-ink-90 border border-ink-70/20 rounded-sm p-6">
                    <div class="flex items-center gap-1 mb-3">
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star-half-stroke text-amber text-xs"></i>
                    </div>
                    <p class="text-sm text-ink-30 leading-relaxed mb-4">"Binnen een week had ik het terugverdiend. Geen avonden meer achter de laptop. Nu drink ik 's avonds gewoon een biertje met een gerust hart."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber/15 flex items-center justify-center"><span class="text-[10px] font-bold text-amber">TM</span></div>
                        <div>
                            <span class="text-xs font-heading font-semibold text-paper">Tom Mulder</span>
                            <span class="text-[10px] text-ink-50 block">Dakdekker &middot; Kleine aannemer</span>
                        </div>
                    </div>
                </div>
                <div class="w-[380px] shrink-0 bg-ink-90 border border-ink-70/20 rounded-sm p-6">
                    <div class="flex items-center gap-1 mb-3">
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                    </div>
                    <p class="text-sm text-ink-30 leading-relaxed mb-4">"Ik heb van die dikke vingers, typen op een telefoon is niks voor mij. Met Klaar hoeft dat niet meer. Gewoon praten, hij begrijpt me."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber/15 flex items-center justify-center"><span class="text-[10px] font-bold text-amber">WD</span></div>
                        <div>
                            <span class="text-xs font-heading font-semibold text-paper">Willem de Groot</span>
                            <span class="text-[10px] text-ink-50 block">Metselaar &middot; ZZP'er</span>
                        </div>
                    </div>
                </div>
                <div class="w-[380px] shrink-0 bg-ink-90 border border-ink-70/20 rounded-sm p-6">
                    <div class="flex items-center gap-1 mb-3">
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                        <i class="fa-solid fa-star text-amber text-xs"></i>
                    </div>
                    <p class="text-sm text-ink-30 leading-relaxed mb-4">"Klant belt, klus gedaan, werkbon ingesproken, factuur verstuurd. Alles op dezelfde dag. Dat lukte me vroeger nooit."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber/15 flex items-center justify-center"><span class="text-[10px] font-bold text-amber">KZ</span></div>
                        <div>
                            <span class="text-xs font-heading font-semibold text-paper">Koen Zwart</span>
                            <span class="text-[10px] text-ink-50 block">Glaszetter &middot; ZZP'er</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════ PRIJZEN ═══════════ --}}
    <div class="py-32 bg-paper">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <p class="text-sm uppercase font-heading tracking-widest text-amber mb-6 flex items-center justify-center gap-4">
                    <span class="w-[35px] h-[2px] bg-amber"></span><span>Prijzen</span><span class="w-[35px] h-[2px] bg-amber"></span>
                </p>
                <h2 class="font-display text-ink text-5xl lg:text-6xl uppercase">Eerlijk geprijsd.<br><span class="text-amber">Geen verrassingen.</span></h2>
                <p class="text-sm text-ink-50 leading-relaxed mt-6">Geen opstartkosten, geen contracten. Maandelijks opzegbaar.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto items-start">

                {{-- ZZP --}}
                <div class="pricing-card bg-snow border border-ink-10 rounded-sm p-8 flex flex-col relative overflow-hidden border-t-4 border-t-ink-10">
                    <div class="mb-6">
                        <h3 class="font-heading font-bold text-ink text-xl mb-1">ZZP</h3>
                        <p class="text-xs text-ink-50">Voor de zelfstandige vakman.</p>
                    </div>
                    <div class="flex items-baseline gap-1 mb-2">
                        <span class="font-display text-5xl text-ink">&euro;29</span>
                        <span class="text-sm text-ink-50">/ mnd</span>
                    </div>
                    <p class="text-[11px] text-ink-50 mb-8">Excl. BTW</p>

                    <ul class="space-y-3 mb-8 flex-1">
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-70">Werkbonnen & facturen</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-70">Fotoherkenning</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-70">Urenregistratie</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-70">50 inspraken per maand</span>
                        </li>
                        <li class="flex items-start gap-2.5 opacity-40">
                            <i class="fa-solid fa-xmark text-ink-30 text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-30">Moneybird-koppeling</span>
                        </li>
                        <li class="flex items-start gap-2.5 opacity-40">
                            <i class="fa-solid fa-xmark text-ink-30 text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-30">Prioriteit support</span>
                        </li>
                        <li class="flex items-start gap-2.5 opacity-40">
                            <i class="fa-solid fa-xmark text-ink-30 text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-30">Teamoverzicht & planning</span>
                        </li>
                        <li class="flex items-start gap-2.5 opacity-40">
                            <i class="fa-solid fa-xmark text-ink-30 text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-30">Klant- & projectbeheer</span>
                        </li>
                    </ul>
                    <a href="/register" class="block text-center border border-ink px-4 py-3 text-ink font-semibold text-sm font-heading transition cursor-pointer hover:bg-ink hover:text-paper">Start gratis proefperiode</a>
                </div>

                {{-- Vakman (featured) --}}
                <div class="pricing-card pricing-card-featured price-highlight bg-ink rounded-sm p-8 flex flex-col relative overflow-hidden border-t-4 border-t-amber md:-mt-4 md:pb-12" style="background-image: radial-gradient(circle, rgba(255,180,0,0.05) 1px, transparent 1px); background-size: 20px 20px;">
                    <div class="absolute top-4 right-4 bg-amber px-3 py-1 text-[10px] font-heading font-bold text-ink uppercase tracking-wider">Populair</div>
                    <div class="mb-6">
                        <h3 class="font-heading font-bold text-paper text-xl mb-1">Vakman</h3>
                        <p class="text-xs text-ink-50">Voor de vakman die alles uit Klaar haalt.</p>
                    </div>
                    <div class="flex items-baseline gap-1 mb-2">
                        <span class="font-display text-5xl text-paper">&euro;49</span>
                        <span class="text-sm text-ink-50">/ mnd</span>
                    </div>
                    <p class="text-[11px] text-ink-50 mb-8">Excl. BTW</p>

                    <ul class="space-y-3 mb-8 flex-1">
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-30">Werkbonnen & facturen</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-30">Fotoherkenning</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-30">Urenregistratie</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-30">Onbeperkt inspraken</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-30">Moneybird-koppeling</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-30">Prioriteit support</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-30">Klant- & projectbeheer</span>
                        </li>
                        <li class="flex items-start gap-2.5 opacity-40">
                            <i class="fa-solid fa-xmark text-ink-70 text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-70">Teamoverzicht & planning</span>
                        </li>
                    </ul>
                    <a href="/register" class="block text-center border border-amber bg-amber px-4 py-3 text-ink font-semibold text-sm font-heading transition cursor-pointer hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)]">Start gratis proefperiode</a>
                </div>

                {{-- Ploeg --}}
                <div class="pricing-card bg-snow border border-ink-10 rounded-sm p-8 flex flex-col relative overflow-hidden border-t-4 border-t-clay">
                    <div class="mb-6">
                        <h3 class="font-heading font-bold text-ink text-xl mb-1">Ploeg</h3>
                        <p class="text-xs text-ink-50">Voor teams vanaf 3 gebruikers.</p>
                    </div>
                    <div class="flex items-baseline gap-1 mb-2">
                        <span class="font-display text-5xl text-ink">&euro;44</span>
                        <span class="text-sm text-ink-50">/ gebruiker / mnd</span>
                    </div>
                    <p class="text-[11px] text-ink-50 mb-8">Min. 3 gebruikers &middot; Excl. BTW</p>

                    <ul class="space-y-3 mb-8 flex-1">
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-70">Werkbonnen & facturen</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-70">Fotoherkenning</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-70">Urenregistratie</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-70">Onbeperkt inspraken</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-70">Moneybird-koppeling</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-70">Prioriteit support</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-70">Klant- & projectbeheer</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-amber text-[10px] mt-1.5 shrink-0"></i>
                            <span class="text-sm text-ink-70">Teamoverzicht & planning</span>
                        </li>
                    </ul>
                    <a href="/register" class="block text-center border border-ink px-4 py-3 text-ink font-semibold text-sm font-heading transition cursor-pointer hover:bg-ink hover:text-paper">Start gratis proefperiode</a>
                </div>
            </div>

            {{-- Pakketten vergelijken --}}
            <div class="mt-16 max-w-5xl mx-auto bg-snow border border-ink-10 rounded-sm p-8 overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead>
                        <tr class="border-b border-ink-10">
                            <th class="py-4 pr-4 font-heading font-semibold text-ink text-base">Pakketten vergelijken</th>
                            <th class="py-4 px-4 text-center font-heading font-semibold text-ink">ZZP</th>
                            <th class="py-4 px-4 text-center font-heading font-semibold text-ink">Vakman</th>
                            <th class="py-4 px-4 text-center font-heading font-semibold text-ink">Ploeg</th>
                        </tr>
                    </thead>
                    <tbody class="text-ink-70">
                        <tr class="border-b border-ink-10/60">
                            <td class="py-3 pr-4">Werkbonnen & facturen</td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-check text-amber"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-check text-amber"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-check text-amber"></i></td>
                        </tr>
                        <tr class="border-b border-ink-10/60">
                            <td class="py-3 pr-4">Fotoherkenning</td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-check text-amber"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-check text-amber"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-check text-amber"></i></td>
                        </tr>
                        <tr class="border-b border-ink-10/60">
                            <td class="py-3 pr-4">Urenregistratie</td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-check text-amber"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-check text-amber"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-check text-amber"></i></td>
                        </tr>
                        <tr class="border-b border-ink-10/60">
                            <td class="py-3 pr-4">Inspraken</td>
                            <td class="py-3 px-4 text-center text-xs text-ink-50">50 / mnd</td>
                            <td class="py-3 px-4 text-center text-xs text-ink-50">Onbeperkt</td>
                            <td class="py-3 px-4 text-center text-xs text-ink-50">Onbeperkt</td>
                        </tr>
                        <tr class="border-b border-ink-10/60">
                            <td class="py-3 pr-4">Moneybird-koppeling</td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-xmark text-ink-30"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-check text-amber"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-check text-amber"></i></td>
                        </tr>
                        <tr class="border-b border-ink-10/60">
                            <td class="py-3 pr-4">Prioriteit support</td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-xmark text-ink-30"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-check text-amber"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-check text-amber"></i></td>
                        </tr>
                        <tr class="border-b border-ink-10/60">
                            <td class="py-3 pr-4">Teamoverzicht & planning</td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-xmark text-ink-30"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-xmark text-ink-30"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-check text-amber"></i></td>
                        </tr>
                        <tr class="border-b border-ink-10/60">
                            <td class="py-3 pr-4">Klant- & projectbeheer</td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-xmark text-ink-30"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-xmark text-ink-30"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-check text-amber"></i></td>
                        </tr>
                        <tr>
                            <td class="py-3 pr-4">Rapportages & export</td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-xmark text-ink-30"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-xmark text-ink-30"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-check text-amber"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="text-center text-xs text-ink-50 mt-8">Alle prijzen zijn excl. BTW &middot; 14 dagen gratis proberen &middot; Geen creditcard nodig</p>
        </div>
    </div>

    {{-- ═══════════ AVG ═══════════ --}}
    <div class="py-20 bg-paper">
        <div class="max-w-3xl mx-auto px-6">
            <div class="flex items-start gap-5 bg-snow rounded-sm p-8 border border-ink-10">
                <div class="w-12 h-12 bg-amber/10 rounded-lg flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-lock text-amber text-lg"></i>
                </div>
                <div>
                    <h3 class="font-heading font-bold text-ink mb-1">Jouw data, jouw eigendom</h3>
                    <p class="text-sm text-ink-50 leading-relaxed">
                        Alle gegevens worden versleuteld opgeslagen op EU-servers. Klaar is volledig AVG-compliant. Exporteer of verwijder je data op elk moment &ndash; geen lock-in, geen gedoe.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════ CTA ═══════════ --}}
    <div class="py-32 bg-amber">
        <div class="max-w-5xl mx-auto px-6 text-center">
            <h2 class="font-display text-6xl lg:text-8xl text-ink uppercase mb-10">
                Klus gedaan<span class="text-ink/30">?</span><br>Spreek &rsquo;m in<span class="text-ink/30">.</span>
            </h2>
            <a href="/register" class="border border-ink bg-ink px-4 py-2.5 text-amber font-semibold text-sm font-heading transition hover:brightness-125 hover:shadow-lg">Probeer 14 dagen gratis</a>
            <p class="mt-5 text-sm text-ink/50">
                Geen creditcard &middot; Maandelijks opzegbaar
            </p>
        </div>
    </div>

    {{-- ═══════════ FOOTER ═══════════ --}}
    <footer class="bg-ink pt-20 pb-10" style="background-image: radial-gradient(circle, rgba(255,180,0,0.04) 1px, transparent 1px); background-size: 24px 24px;">
        <div class="max-w-7xl mx-auto px-6">

            {{-- Top: logo + kolommen --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-12 gap-10 lg:gap-8">

                {{-- Branding --}}
                <div class="col-span-2 md:col-span-3 lg:col-span-4">
                    <a href="/"><img src="/logo/klaar-paper.svg" alt="Klaar" class="h-10 mb-5 -ml-2.5" /></a>
                    <p class="font-display text-lg text-ink-30 uppercase leading-tight">
                        De klus klaar<span class="text-amber">.</span><br>De administratie ook<span class="text-amber">.</span>
                    </p>
                    <p class="text-sm text-ink-50 mt-4 leading-relaxed max-w-xs">
                        Administratie-assistent voor ZZP'ers en kleine aannemers. Spreek in, maak een foto &ndash; Klaar regelt de rest.
                    </p>
                    <div class="flex items-center gap-3 mt-6">
                        <a href="#" class="w-8 h-8 rounded-full border border-ink-70/30 flex items-center justify-center text-ink-50 hover:text-amber hover:border-amber transition" aria-label="LinkedIn">
                            <i class="fa-brands fa-linkedin-in text-xs"></i>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-full border border-ink-70/30 flex items-center justify-center text-ink-50 hover:text-amber hover:border-amber transition" aria-label="Instagram">
                            <i class="fa-brands fa-instagram text-xs"></i>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-full border border-ink-70/30 flex items-center justify-center text-ink-50 hover:text-amber hover:border-amber transition" aria-label="Facebook">
                            <i class="fa-brands fa-facebook-f text-xs"></i>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-full border border-ink-70/30 flex items-center justify-center text-ink-50 hover:text-amber hover:border-amber transition" aria-label="X / Twitter">
                            <i class="fa-brands fa-x-twitter text-xs"></i>
                        </a>
                    </div>
                </div>

                {{-- Product --}}
                <div class="lg:col-span-2">
                    <h4 class="font-heading font-semibold text-sm text-paper mb-4">Product</h4>
                    <ul class="space-y-2.5 text-sm text-ink-50">
                        <li><a href="/hoe-het-werkt" class="hover:text-paper transition">Hoe het werkt</a></li>
                        <li><a href="/spraakherkenning" class="hover:text-paper transition">Spraakherkenning</a></li>
                        <li><a href="/facturen-werkbonnen" class="hover:text-paper transition">Facturen & werkbonnen</a></li>
                        <li><a href="/fotoherkenning" class="hover:text-paper transition">Fotoherkenning</a></li>
                        <li><a href="/integraties" class="hover:text-paper transition">Integraties</a></li>
                        <li><a href="/beveiliging" class="hover:text-paper transition">Beveiliging & AVG</a></li>
                        <li><a href="/prijzen" class="hover:text-paper transition">Prijzen</a></li>
                    </ul>
                </div>

                {{-- Voor wie --}}
                <div class="lg:col-span-2">
                    <h4 class="font-heading font-semibold text-sm text-paper mb-4">Voor wie</h4>
                    <ul class="space-y-2.5 text-sm text-ink-50">
                        <li><a href="/zzp-bouw" class="hover:text-paper transition">ZZP'ers in de bouw</a></li>
                        <li><a href="/kleine-aannemers" class="hover:text-paper transition">Kleine aannemers</a></li>
                        <li><a href="/installateurs" class="hover:text-paper transition">Installateurs</a></li>
                        <li><a href="/schilders-stukadoors" class="hover:text-paper transition">Schilders & stukadoors</a></li>
                    </ul>
                </div>

                {{-- Hulp --}}
                <div class="lg:col-span-2">
                    <h4 class="font-heading font-semibold text-sm text-paper mb-4">Hulp</h4>
                    <ul class="space-y-2.5 text-sm text-ink-50">
                        <li><a href="/faq" class="hover:text-paper transition">Veelgestelde vragen</a></li>
                        <li><a href="/blog" class="hover:text-paper transition">Blog & tips</a></li>
                        <li><a href="/contact" class="hover:text-paper transition">Contact</a></li>
                        <li><a href="/over-ons" class="hover:text-paper transition">Over ons</a></li>
                    </ul>
                </div>

                {{-- Juridisch --}}
                <div class="lg:col-span-2">
                    <h4 class="font-heading font-semibold text-sm text-paper mb-4">Juridisch</h4>
                    <ul class="space-y-2.5 text-sm text-ink-50">
                        <li><a href="/privacybeleid" class="hover:text-paper transition">Privacybeleid</a></li>
                        <li><a href="/algemene-voorwaarden" class="hover:text-paper transition">Algemene voorwaarden</a></li>
                        <li><a href="/cookiebeleid" class="hover:text-paper transition">Cookiebeleid</a></li>
                    </ul>
                </div>
            </div>

            {{-- Divider --}}
            <div class="mt-14 pt-8 border-t border-ink-70/15">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-xs text-ink-50">&copy; {{ date('Y') }} Klaar. Alle rechten voorbehouden.</p>
                    <p class="text-xs text-ink-50">KVK: 12345678 &middot; BTW: NL123456789B01</p>
                </div>
            </div>
        </div>
    </footer>

    {{-- ═══════════ COOKIE CONSENT ═══════════ --}}
    <div id="cookie-consent" class="hidden fixed bottom-4 left-4 z-100 w-[340px]">
        <div class="bg-ink border border-ink-70/20 rounded-sm shadow-2xl p-5">
            <div class="flex items-start gap-3 mb-4">
                <div class="w-8 h-8 rounded-md bg-amber/10 flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fa-solid fa-cookie-bite text-amber text-sm"></i>
                </div>
                <div>
                    <h4 class="font-heading font-semibold text-paper text-sm mb-1">Cookies</h4>
                    <p class="text-[11px] text-ink-50 leading-relaxed">
                        Wij gebruiken cookies om de website te verbeteren. Lees ons <a href="/cookiebeleid" class="text-amber hover:underline">cookiebeleid</a>.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button id="cookie-decline" class="flex-1 border border-paper/20 px-3 py-2 text-paper font-semibold text-xs font-heading transition cursor-pointer hover:border-paper/40 hover:bg-paper/10">Weigeren</button>
                <button id="cookie-accept" class="flex-1 bg-amber px-3 py-2 text-ink font-semibold text-xs font-heading transition cursor-pointer hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)]">Accepteren</button>
            </div>
        </div>
    </div>
</body>
</html>
