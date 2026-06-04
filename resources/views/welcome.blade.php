<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Klaar &mdash; Administratie voor de bouw</title>
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
    <nav class="w-full fixed z-100 left-0 right-0 top-0">
        <div class="max-w-7xl mx-auto py-4 flex items-center justify-between">
            <img class="max-h-14 -ml-3" src="/logo/klaar-paper.svg" alt="Klaar">
            <div class="flex items-center gap-8">
                <a href="#" class="font-sans text-paper text-xs max-w-[500px] font-light opacity-80">Hoe het werkt</a>
                <a href="#" class="font-sans text-paper text-xs max-w-[500px] font-light opacity-80">Voor wie</a>
                <a href="#" class="font-sans text-paper text-xs max-w-[500px] font-light opacity-80">Prijzen</a>
                <a href="#" class="font-sans text-paper text-xs max-w-[500px] font-light opacity-80">Inloggen</a>
                <a href="#" class="bg-amber px-4 py-2.5 text-ink font-semibold text-xs font-heading">Gratis proberen</a>
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
    </style>

    <div class="w-full min-h-[800px] bg-ink" style="background-image: radial-gradient(circle, rgba(255,180,0,0.07) 1px, transparent 1px); background-size: 24px 24px;">
        <div class="max-w-7xl h-full mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center py-32">
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
                    <a href="#" class="border border-amber bg-amber px-4 py-2.5 text-ink font-semibold text-sm font-heading">Probeer 14 dagen gratis</a>
                    <a href="#" class="border border-paper px-4 py-2.5 text-paper font-semibold text-sm font-heading">Bekijk hoe het werkt</a>
                </div>
                <p class="font-sans text-paper text-sm max-w-[500px] font-light opacity-80 mt-4 leading-6">
                    Geen creditcard · Maandelijks opzegbaar · <span class="text-amber font-bold">Data in de EU</span>
                </p>
            </div>
            <div class="hidden lg:flex justify-center">
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
    <div class="py-32 flex flex-col items-center justify-center">
        <h2 class="font-display text-ink text-5xl uppercase text-center mb-12">Klaar<span class="text-amber">.</span> verandert twintig seconden inspreken<br>in een <span class="text-amber">complete administratie</span>.</h2>
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="flex items-start gap-4 bg-white/30 p-6 rounded-sm">
                <div class="w-10 h-10 rounded-lg bg-amber/10 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-microphone text-amber"></i>
                </div>
                <div>
                    <h3 class="font-heading font-bold text-ink text-sm mb-1">Spraakherkenning</h3>
                    <p class="text-ink-50 text-sm leading-relaxed">Spreek in het Nederlands. Klaar begrijpt vakjargon, accenten en rekent automatisch.</p>
                </div>
            </div>
            <div class="flex items-start gap-4 bg-white/30 p-6 rounded-sm">
                <div class="w-10 h-10 rounded-lg bg-amber/10 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-file-invoice text-amber"></i>
                </div>
                <div>
                    <h3 class="font-heading font-bold text-ink text-sm mb-1">Facturen in één klik</h3>
                    <p class="text-ink-50 text-sm leading-relaxed">Van ingesproken werkbon naar professionele PDF-factuur. Verstuur direct per e-mail.</p>
                </div>
            </div>
            <div class="flex items-start gap-4 bg-white/30 p-6 rounded-sm">
                <div class="w-10 h-10 rounded-lg bg-amber/10 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-camera text-amber"></i>
                </div>
                <div>
                    <h3 class="font-heading font-bold text-ink text-sm mb-1">Fotoherkenning</h3>
                    <p class="text-ink-50 text-sm leading-relaxed">Maak een foto van een bon of materiaallijst. AI leest alles uit, ook slordig handschrift.</p>
                </div>
            </div>
            <div class="flex items-start gap-4 bg-white/30 p-6 rounded-sm">
                <div class="w-10 h-10 rounded-lg bg-amber/10 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-shield-halved text-amber"></i>
                </div>
                <div>
                    <h3 class="font-heading font-bold text-ink text-sm mb-1">AVG-compliant</h3>
                    <p class="text-ink-50 text-sm leading-relaxed">Data versleuteld op EU-servers. Exporteer of verwijder je gegevens op elk moment.</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3 mt-12">
            <a href="#" class="border border-amber bg-amber px-4 py-2.5 text-ink font-semibold text-sm font-heading">Start gratis</a>
            <a href="#" class="border border-ink px-4 py-2.5 text-ink font-semibold text-sm font-heading">Bekijk onze prijzen</a>
        </div>
    </div>

    {{-- ═══════════ HOE HET WERKT ═══════════ --}}
    <div id="hoe-het-werkt" class="py-32 bg-ink" style="background-image: radial-gradient(circle, rgba(255,180,0,0.07) 1px, transparent 1px); background-size: 24px 24px;">
        <div class="max-w-7xl mx-auto px-6">
            <p class="text-sm uppercase font-heading tracking-widest text-amber ml-1 mb-6 flex items-center gap-4">
                <span class="w-[35px] h-[2px] bg-amber"></span><span>Hoe het werkt</span>
            </p>
            <h2 class="font-display text-paper text-6xl uppercase">Drie stappen<span class="text-amber">.</span><br>Nul formulieren<span class="text-amber">.</span></h2>

            <div class="mt-16 grid md:grid-cols-3 gap-8">
                <div class="border border-ink-70/20 rounded-sm p-8">
                    <div class="font-display text-5xl text-amber/20 mb-4">01</div>
                    <h3 class="font-display text-2xl text-paper uppercase mb-3">Inspreken</h3>
                    <p class="text-sm text-ink-30 leading-relaxed">
                        Vertel na je werkdag wat je gedaan hebt. Of maak een foto van een bon, pakbon of materiaallijst. In je eigen woorden, op de bouwplaats.
                    </p>
                </div>
                <div class="border border-ink-70/20 rounded-sm p-8">
                    <div class="font-display text-5xl text-amber/20 mb-4">02</div>
                    <h3 class="font-display text-2xl text-paper uppercase mb-3">Ontrafelen</h3>
                    <p class="text-sm text-ink-30 leading-relaxed">
                        Klaar luistert, begrijpt en maakt er een werkbon van. Uren, materialen, bedragen &mdash; alles wordt automatisch herkend en netjes ingevuld.
                    </p>
                </div>
                <div class="border border-ink-70/20 rounded-sm p-8">
                    <div class="font-display text-5xl text-amber/20 mb-4">03</div>
                    <h3 class="font-display text-2xl text-paper uppercase mb-3">Klaar<span class="text-amber">.</span></h3>
                    <p class="text-sm text-ink-30 leading-relaxed">
                        Controleer, pas aan, verstuur als factuur. Koppel met Moneybird en je boekhouding loopt automatisch mee.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════ AVG ═══════════ --}}
    <div class="py-16 bg-paper">
        <div class="max-w-3xl mx-auto px-6">
            <div class="flex items-start gap-5 bg-snow rounded-sm p-8 border border-ink-10">
                <div class="w-12 h-12 bg-amber/10 rounded-lg flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-lock text-amber text-lg"></i>
                </div>
                <div>
                    <h3 class="font-heading font-bold text-ink mb-1">Jouw data, jouw eigendom</h3>
                    <p class="text-sm text-ink-50 leading-relaxed">
                        Alle gegevens worden versleuteld opgeslagen op EU-servers. Klaar is volledig AVG-compliant. Exporteer of verwijder je data op elk moment &mdash; geen lock-in, geen gedoe.
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
            <a href="#" class="border border-ink bg-ink px-4 py-2.5 text-amber font-semibold text-sm font-heading">Probeer 14 dagen gratis</a>
            <p class="mt-5 text-sm text-ink/50">
                Geen creditcard &middot; Maandelijks opzegbaar
            </p>
        </div>
    </div>

    {{-- ═══════════ FOOTER ═══════════ --}}
    <footer class="bg-ink py-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-8">
                <div>
                    <img src="/logo/klaar-paper.svg" alt="Klaar" class="h-8 mb-4" />
                    <p class="font-display text-lg text-ink-30 uppercase">
                        De klus klaar<span class="text-amber">.</span><br>De administratie ook<span class="text-amber">.</span>
                    </p>
                </div>
                <div class="flex flex-wrap gap-x-8 gap-y-2 text-sm text-ink-50">
                    <a href="#" class="hover:text-paper transition">Over ons</a>
                    <a href="#" class="hover:text-paper transition">Roadmap</a>
                    <a href="#" class="hover:text-paper transition">Contact</a>
                    <a href="#" class="hover:text-paper transition">Privacy</a>
                    <a href="#" class="hover:text-paper transition">Voorwaarden</a>
                </div>
            </div>
            <div class="mt-8 pt-6 border-t border-ink-70/15 text-xs text-ink-50">
                &copy; {{ date('Y') }} Klaar. Alle rechten voorbehouden.
            </div>
        </div>
    </footer>
</body>
</html>
