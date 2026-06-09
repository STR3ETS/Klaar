<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Klaar. - Administratie-assistent voor de vakman')</title>
    <meta name="description" content="@yield('meta_description', 'Spreek in wat je gedaan hebt, maak een foto van de bon. Klaar regelt de rest. Werkbonnen en facturen voor ZZP\'ers en kleine aannemers.')">

    <link rel="icon" href="/favicon/favicon.ico" sizes="32x32">
    <link rel="icon" href="/favicon/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/favicon/apple-touch-icon.png">

    <link rel="preload" href="{{ asset('fontawesome/css/all.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}"></noscript>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{ Vite::fonts() }}

    @stack('styles')
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

    @yield('content')

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
