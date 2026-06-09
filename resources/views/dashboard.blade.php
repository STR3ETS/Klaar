<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 pt-8 pb-12 lg:pt-12 lg:pb-16">

        {{-- Begroeting --}}
        @php
            $hour = now()->hour;
            $greeting = $hour < 6 ? 'Goedenacht' : ($hour < 12 ? 'Goedemorgen' : ($hour < 18 ? 'Goedemiddag' : 'Goedenavond'));
            $workspace = auth()->user()->currentWorkspace();

            // Werkbonnen
            $entriesThisMonth = $workspace->entries()->whereMonth('created_at', now()->month)->count();
            $draftEntries = $workspace->entries()->where('status', 'draft')->count();
            $draftTotal = $workspace->entries()->where('status', 'draft')->sum('total_amount');
            $finalEntries = $workspace->entries()->where('status', 'final')->count();
            $finalTotal = $workspace->entries()->where('status', 'final')->sum('total_amount');
            $processingEntries = $workspace->entries()->where('status', 'processing')->count();

            // Facturen
            $openInvoices = $workspace->invoices()->whereIn('status', ['sent', 'draft'])->count();
            $openAmount = $workspace->invoices()->whereIn('status', ['sent', 'draft'])->sum('total');
            $paidThisMonth = $workspace->invoices()->where('status', 'paid')->whereMonth('paid_at', now()->month)->sum('total');
            $overdueInvoices = $workspace->invoices()->where('status', 'sent')->where('due_date', '<', now())->get();
            $recentInvoices = $workspace->invoices()->with('client')->whereIn('status', ['sent', 'draft'])->orderBy('due_date')->take(5)->get();
        @endphp

        <div class="mb-8 pt-2 lg:pt-0">
            <p class="text-xs font-heading uppercase tracking-wider text-ink-50">{{ now()->translatedFormat('l j F') }}</p>
            <h1 class="font-display text-paper text-2xl lg:text-3xl uppercase mt-1">{{ $greeting }}, {{ auth()->user()->name }}<span class="text-amber">.</span></h1>
        </div>

        {{-- Onboarding checklist --}}
        @php
            $user = auth()->user();
            $hasCompany = !empty($user->company_name);
            $hasAddress = !empty($user->address_street) && !empty($user->address_city);
            $hasFiscal = !empty($user->kvk_number) || !empty($user->btw_number);
            $hasClient = $workspace->clients()->exists();
            $hasEntry = $workspace->entries()->exists();
            $setupComplete = $hasCompany && $hasAddress && $hasFiscal && $hasClient && $hasEntry;
            $stepsCompleted = collect([$hasCompany, $hasAddress, $hasFiscal, $hasClient, $hasEntry])->filter()->count();
        @endphp

        @unless($setupComplete)
            <div class="bg-ink-90 rounded-sm border border-amber/20 p-5 mb-8" x-data="{ open: true }">
                <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-amber/15 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-rocket text-amber text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-heading font-semibold text-paper text-sm">Klaar voor de start</h3>
                            <p class="text-[11px] text-ink-50">{{ $stepsCompleted }}/5 stappen voltooid</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        {{-- Progress dots --}}
                        <div class="flex items-center gap-1">
                            @foreach([$hasCompany, $hasAddress, $hasFiscal, $hasClient, $hasEntry] as $done)
                                <div class="w-2 h-2 rounded-full {{ $done ? 'bg-green-500' : 'bg-ink-70/30' }}"></div>
                            @endforeach
                        </div>
                        <i class="fa-solid fa-chevron-down text-ink-50 text-[10px] transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </div>
                </div>

                <div x-show="open" x-transition class="mt-4 space-y-2">
                    {{-- Bedrijfsnaam --}}
                    <a href="{{ route('settings') }}" wire:navigate class="flex items-center gap-3 px-3 py-2.5 rounded-sm {{ $hasCompany ? 'opacity-60' : 'hover:bg-ink-70/10' }} transition group">
                        <div class="w-6 h-6 rounded-full {{ $hasCompany ? 'bg-green-500/15' : 'bg-ink-70/15' }} flex items-center justify-center shrink-0">
                            @if($hasCompany)
                                <i class="fa-solid fa-check text-green-400 text-[9px]"></i>
                            @else
                                <i class="fa-solid fa-building text-ink-50 text-[9px]"></i>
                            @endif
                        </div>
                        <span class="text-sm font-heading {{ $hasCompany ? 'text-ink-50 line-through' : 'text-paper' }}">Bedrijfsnaam invullen</span>
                        @unless($hasCompany)
                            <i class="fa-solid fa-arrow-right text-ink-70 text-[9px] ml-auto group-hover:text-amber transition"></i>
                        @endunless
                    </a>

                    {{-- Adres --}}
                    <a href="{{ route('settings') }}" wire:navigate class="flex items-center gap-3 px-3 py-2.5 rounded-sm {{ $hasAddress ? 'opacity-60' : 'hover:bg-ink-70/10' }} transition group">
                        <div class="w-6 h-6 rounded-full {{ $hasAddress ? 'bg-green-500/15' : 'bg-ink-70/15' }} flex items-center justify-center shrink-0">
                            @if($hasAddress)
                                <i class="fa-solid fa-check text-green-400 text-[9px]"></i>
                            @else
                                <i class="fa-solid fa-location-dot text-ink-50 text-[9px]"></i>
                            @endif
                        </div>
                        <span class="text-sm font-heading {{ $hasAddress ? 'text-ink-50 line-through' : 'text-paper' }}">Adresgegevens toevoegen</span>
                        @unless($hasAddress)
                            <i class="fa-solid fa-arrow-right text-ink-70 text-[9px] ml-auto group-hover:text-amber transition"></i>
                        @endunless
                    </a>

                    {{-- KVK/BTW --}}
                    <a href="{{ route('settings') }}" wire:navigate class="flex items-center gap-3 px-3 py-2.5 rounded-sm {{ $hasFiscal ? 'opacity-60' : 'hover:bg-ink-70/10' }} transition group">
                        <div class="w-6 h-6 rounded-full {{ $hasFiscal ? 'bg-green-500/15' : 'bg-ink-70/15' }} flex items-center justify-center shrink-0">
                            @if($hasFiscal)
                                <i class="fa-solid fa-check text-green-400 text-[9px]"></i>
                            @else
                                <i class="fa-solid fa-receipt text-ink-50 text-[9px]"></i>
                            @endif
                        </div>
                        <span class="text-sm font-heading {{ $hasFiscal ? 'text-ink-50 line-through' : 'text-paper' }}">KVK- of BTW-nummer instellen</span>
                        @unless($hasFiscal)
                            <i class="fa-solid fa-arrow-right text-ink-70 text-[9px] ml-auto group-hover:text-amber transition"></i>
                        @endunless
                    </a>

                    {{-- Eerste klant --}}
                    <a href="{{ route('clients.create') }}" wire:navigate class="flex items-center gap-3 px-3 py-2.5 rounded-sm {{ $hasClient ? 'opacity-60' : 'hover:bg-ink-70/10' }} transition group">
                        <div class="w-6 h-6 rounded-full {{ $hasClient ? 'bg-green-500/15' : 'bg-ink-70/15' }} flex items-center justify-center shrink-0">
                            @if($hasClient)
                                <i class="fa-solid fa-check text-green-400 text-[9px]"></i>
                            @else
                                <i class="fa-solid fa-user-plus text-ink-50 text-[9px]"></i>
                            @endif
                        </div>
                        <span class="text-sm font-heading {{ $hasClient ? 'text-ink-50 line-through' : 'text-paper' }}">Eerste klant aanmaken</span>
                        @unless($hasClient)
                            <i class="fa-solid fa-arrow-right text-ink-70 text-[9px] ml-auto group-hover:text-amber transition"></i>
                        @endunless
                    </a>

                    {{-- Eerste invoer --}}
                    <a href="{{ route('invoeren.index') }}?type=voice" wire:navigate class="flex items-center gap-3 px-3 py-2.5 rounded-sm {{ $hasEntry ? 'opacity-60' : 'hover:bg-ink-70/10' }} transition group">
                        <div class="w-6 h-6 rounded-full {{ $hasEntry ? 'bg-green-500/15' : 'bg-ink-70/15' }} flex items-center justify-center shrink-0">
                            @if($hasEntry)
                                <i class="fa-solid fa-check text-green-400 text-[9px]"></i>
                            @else
                                <i class="fa-solid fa-microphone text-ink-50 text-[9px]"></i>
                            @endif
                        </div>
                        <span class="text-sm font-heading {{ $hasEntry ? 'text-ink-50 line-through' : 'text-paper' }}">Eerste werkbon aanmaken</span>
                        @unless($hasEntry)
                            <i class="fa-solid fa-arrow-right text-ink-70 text-[9px] ml-auto group-hover:text-amber transition"></i>
                        @endunless
                    </a>
                </div>
            </div>
        @endunless

        {{-- Verwerkingsbanner --}}
        @if($processingEntries > 0)
            <div class="flex items-center gap-3 px-4 py-3 bg-amber/10 border border-amber/20 rounded-sm mb-6">
                <i class="fa-solid fa-spinner fa-spin text-amber text-sm"></i>
                <span class="text-sm text-amber font-heading">{{ $processingEntries }} {{ $processingEntries === 1 ? 'invoer wordt' : 'invoeren worden' }} verwerkt</span>
            </div>
        @endif

        {{-- HERO: Actieknoppen --}}
        <div class="mb-10">
            {{-- Spraak: Primaire actie --}}
            <a href="{{ route('invoeren.index') }}?type=voice" wire:navigate
               class="flex items-center gap-4 bg-amber rounded-sm p-5 group transition hover:brightness-110 hover:shadow-[0_8px_32px_rgba(255,180,0,0.3)]">
                <div class="w-12 h-12 bg-ink/10 rounded-full flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-microphone text-ink text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="font-heading font-bold text-ink">Inspreken</span>
                        <span class="text-[9px] font-heading font-bold uppercase tracking-wider bg-ink/10 text-ink/70 px-2 py-0.5 rounded-full">Snelst</span>
                    </div>
                    <p class="text-sm text-ink/60 mt-0.5">Vertel wat je gedaan hebt</p>
                </div>
                <i class="fa-solid fa-arrow-right text-ink/30 group-hover:text-ink/60 group-hover:translate-x-1 transition-all"></i>
            </a>

            {{-- Foto + Handmatig: Secundaire acties --}}
            <div class="grid grid-cols-2 gap-3 mt-3">
                <a href="{{ route('invoeren.index') }}?type=photo" wire:navigate
                   class="bg-ink-90 rounded-sm p-5 border border-ink-70/20 group transition hover:border-amber/30">
                    <div class="w-10 h-10 bg-amber/10 rounded-full flex items-center justify-center mb-3 group-hover:bg-amber/15 transition">
                        <i class="fa-solid fa-camera text-amber text-sm"></i>
                    </div>
                    <h3 class="font-heading font-semibold text-paper text-sm">Foto</h3>
                    <p class="text-xs text-ink-50 mt-0.5">Bon of pakbon scannen</p>
                </a>

                <a href="{{ route('invoeren.index') }}?type=manual" wire:navigate
                   class="bg-ink-90 rounded-sm p-5 border border-ink-70/20 group transition hover:border-amber/30">
                    <div class="w-10 h-10 bg-amber/10 rounded-full flex items-center justify-center mb-3 group-hover:bg-amber/15 transition">
                        <i class="fa-solid fa-pen-to-square text-amber text-sm"></i>
                    </div>
                    <h3 class="font-heading font-semibold text-paper text-sm">Handmatig</h3>
                    <p class="text-xs text-ink-50 mt-0.5">Uren en materialen</p>
                </a>
            </div>
        </div>

        {{-- Omzet chart (multi-line) --}}
        @php
            $chartHours = 7;
            $hourlyData = [];
            $now = now();
            for ($i = $chartHours - 1; $i >= 0; $i--) {
                $hourStart = $now->copy()->subHours($i)->startOfHour();
                $hourEnd = $hourStart->copy()->endOfHour();

                $entryRev = (float) $workspace->entries()
                    ->whereBetween('created_at', [$hourStart, $hourEnd])
                    ->sum('total_amount');
                $invoicedRev = (float) $workspace->invoices()
                    ->whereBetween('created_at', [$hourStart, $hourEnd])
                    ->sum('total');
                $paidRev = (float) $workspace->invoices()
                    ->where('status', 'paid')
                    ->whereBetween('paid_at', [$hourStart, $hourEnd])
                    ->sum('total');

                $hourlyData[] = [
                    'label' => $hourStart->format('H:i'),
                    'entries' => $entryRev,
                    'invoiced' => $invoicedRev,
                    'paid' => $paidRev,
                ];
            }

            $allVals = array_merge(
                array_column($hourlyData, 'entries'),
                array_column($hourlyData, 'invoiced'),
                array_column($hourlyData, 'paid')
            );
            $maxVal = max($allVals) ?: 100;

            $totalEntriesToday = (float) $workspace->entries()->whereDate('created_at', today())->sum('total_amount');
            $totalInvoicedToday = (float) $workspace->invoices()->whereDate('created_at', today())->sum('total');
            $totalPaidToday = (float) $workspace->invoices()->where('status', 'paid')->whereDate('paid_at', today())->sum('total');

            // Projecten
            $activeProjects = $workspace->projects()->where('status', 'active')->withCount('entries')->withSum('entries', 'total_amount')->orderBy('name')->take(5)->get();

            $chartStartTs = $now->copy()->subHours($chartHours - 1)->startOfHour()->timestamp;
            $chartTotalSec = ($chartHours - 1) * 3600;

            $svgW = 500;
            $svgH = 120;
            $padT = 8;
            $padB = 4;
            $plotH = $svgH - $padT - $padB;

            // Build paths for each series
            $series = [
                'entries'  => ['color' => '#FFB400', 'fillColor' => '#FFB400', 'key' => 'entries'],
                'invoiced' => ['color' => '#3B82F6', 'fillColor' => '#3B82F6', 'key' => 'invoiced'],
                'paid'     => ['color' => '#22C55E', 'fillColor' => '#22C55E', 'key' => 'paid'],
            ];
            $paths = [];
            $count = count($hourlyData);

            foreach ($series as $name => $s) {
                $pts = [];
                foreach ($hourlyData as $i => $d) {
                    $x = round(($i / max($count - 1, 1)) * $svgW, 1);
                    $y = round($padT + $plotH - ($d[$s['key']] / $maxVal) * $plotH, 1);
                    $pts[] = ['x' => $x, 'y' => $y];
                }

                $parts = [];
                foreach ($pts as $i => $p) {
                    if ($i === 0) {
                        $parts[] = "M {$p['x']},{$p['y']}";
                    } else {
                        $prev = $pts[$i - 1];
                        $cpx = round(($prev['x'] + $p['x']) / 2, 1);
                        $parts[] = "C {$cpx},{$prev['y']} {$cpx},{$p['y']} {$p['x']},{$p['y']}";
                    }
                }
                $linePath = implode(' ', $parts);
                $lastPt = end($pts);
                $firstPt = $pts[0];
                $fillPath = $linePath . " L {$lastPt['x']},{$svgH} L {$firstPt['x']},{$svgH} Z";

                $paths[$name] = ['line' => $linePath, 'fill' => $fillPath, 'color' => $s['color']];
            }
        @endphp

        <div class="bg-ink-90 rounded-sm border border-ink-70/20 p-5 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-amber/10 flex items-center justify-center">
                        <i class="fa-solid fa-chart-line text-amber text-[10px]"></i>
                    </div>
                    <span class="text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50">Omzet vandaag</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber"></span>
                        <span class="text-[10px] font-heading text-ink-50">Werkbonnen</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <span class="text-[10px] font-heading text-ink-50">Gefactureerd</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        <span class="text-[10px] font-heading text-ink-50">Betaald</span>
                    </div>
                </div>
            </div>

            {{-- Interactive multi-line chart --}}
            <div class="w-full relative cursor-crosshair"
                 x-data="{
                    show: false, pct: 0, hTime: '',
                    vEntries: '', vInvoiced: '', vPaid: '',
                    yEntries: 0, yInvoiced: 0, yPaid: 0
                 }"
                 x-on:mousemove="
                    show = true;
                    var rect = $el.getBoundingClientRect();
                    var r = Math.max(0, Math.min(1, ($event.clientX - rect.left) / rect.width));
                    pct = r * 100;

                    var entries = {{ json_encode(array_column($hourlyData, 'entries')) }};
                    var invoiced = {{ json_encode(array_column($hourlyData, 'invoiced')) }};
                    var paid = {{ json_encode(array_column($hourlyData, 'paid')) }};
                    var n = entries.length;
                    var fi = r * (n - 1);
                    var lo = Math.max(0, Math.floor(fi));
                    var hi = Math.min(n - 1, lo + 1);
                    var f = fi - lo;

                    var fmt = (v) => new Intl.NumberFormat('nl-NL', { style: 'currency', currency: 'EUR' }).format(v);
                    var yCalc = (v) => ({{ $padT }} + {{ $plotH }} - (v / {{ $maxVal }}) * {{ $plotH }}) / {{ $svgH }} * 100;

                    var ve = entries[lo] + (entries[hi] - entries[lo]) * f;
                    var vi = invoiced[lo] + (invoiced[hi] - invoiced[lo]) * f;
                    var vp = paid[lo] + (paid[hi] - paid[lo]) * f;

                    vEntries = fmt(ve); vInvoiced = fmt(vi); vPaid = fmt(vp);
                    yEntries = yCalc(ve); yInvoiced = yCalc(vi); yPaid = yCalc(vp);

                    var ts = {{ $chartStartTs }} + r * {{ $chartTotalSec }};
                    var d = new Date(ts * 1000);
                    hTime = String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
                 "
                 x-on:mouseleave="show = false">

                {{-- Static SVG --}}
                <svg viewBox="0 0 {{ $svgW }} {{ $svgH }}" class="w-full" preserveAspectRatio="none" style="height: 120px;">
                    <defs>
                        <linearGradient id="fillEntries" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#FFB400" stop-opacity="0.15"/>
                            <stop offset="100%" stop-color="#FFB400" stop-opacity="0"/>
                        </linearGradient>
                        <linearGradient id="fillInvoiced" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#3B82F6" stop-opacity="0.10"/>
                            <stop offset="100%" stop-color="#3B82F6" stop-opacity="0"/>
                        </linearGradient>
                        <linearGradient id="fillPaid" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#22C55E" stop-opacity="0.10"/>
                            <stop offset="100%" stop-color="#22C55E" stop-opacity="0"/>
                        </linearGradient>
                    </defs>

                    {{-- Grid lines --}}
                    <line x1="0" y1="{{ $padT }}" x2="{{ $svgW }}" y2="{{ $padT }}" stroke="#5D574F" stroke-opacity="0.15" stroke-width="0.5"/>
                    <line x1="0" y1="{{ $padT + $plotH / 2 }}" x2="{{ $svgW }}" y2="{{ $padT + $plotH / 2 }}" stroke="#5D574F" stroke-opacity="0.1" stroke-width="0.5"/>
                    <line x1="0" y1="{{ $padT + $plotH }}" x2="{{ $svgW }}" y2="{{ $padT + $plotH }}" stroke="#5D574F" stroke-opacity="0.15" stroke-width="0.5"/>

                    {{-- Paid (green, back layer) --}}
                    <path d="{{ $paths['paid']['fill'] }}" fill="url(#fillPaid)"/>
                    <path d="{{ $paths['paid']['line'] }}" fill="none" stroke="#22C55E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.8"/>

                    {{-- Invoiced (blue, mid layer) --}}
                    <path d="{{ $paths['invoiced']['fill'] }}" fill="url(#fillInvoiced)"/>
                    <path d="{{ $paths['invoiced']['line'] }}" fill="none" stroke="#3B82F6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.8"/>

                    {{-- Entries (amber, front layer) --}}
                    <path d="{{ $paths['entries']['fill'] }}" fill="url(#fillEntries)"/>
                    <path d="{{ $paths['entries']['line'] }}" fill="none" stroke="#FFB400" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>

                {{-- Hover overlay --}}
                <div x-show="show" class="absolute inset-0 pointer-events-none" style="display: none;">
                    {{-- Vertical dashed line --}}
                    <div class="absolute top-0 bottom-0" :style="'left:' + pct + '%'">
                        <div class="w-px h-full" style="background: repeating-linear-gradient(to bottom, rgba(243,238,227,0.25) 0px, rgba(243,238,227,0.25) 4px, transparent 4px, transparent 7px);"></div>
                    </div>
                    {{-- Dots: entries --}}
                    <div class="absolute w-2.5 h-2.5 rounded-full bg-ink-90 border-2 border-amber -translate-x-1/2 -translate-y-1/2"
                         :style="'left:' + pct + '%; top:' + yEntries + '%'"></div>
                    {{-- Dots: invoiced --}}
                    <div class="absolute w-2.5 h-2.5 rounded-full bg-ink-90 border-2 border-blue-500 -translate-x-1/2 -translate-y-1/2"
                         :style="'left:' + pct + '%; top:' + yInvoiced + '%'"></div>
                    {{-- Dots: paid --}}
                    <div class="absolute w-2.5 h-2.5 rounded-full bg-ink-90 border-2 border-green-500 -translate-x-1/2 -translate-y-1/2"
                         :style="'left:' + pct + '%; top:' + yPaid + '%'"></div>
                </div>

                {{-- Tooltip --}}
                <div x-show="show"
                     class="absolute bottom-full mb-2 pointer-events-none -translate-x-1/2 z-10"
                     :style="'left:' + Math.max(15, Math.min(85, pct)) + '%'"
                     style="display: none;">
                    <div class="bg-ink border border-ink-70/30 rounded-sm px-3 py-2 shadow-lg whitespace-nowrap">
                        <span class="text-[10px] font-mono text-ink-50 block mb-1" x-text="hTime"></span>
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber shrink-0"></span>
                            <span class="text-[11px] font-mono font-bold text-amber" x-text="vEntries"></span>
                        </div>
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>
                            <span class="text-[11px] font-mono font-bold text-blue-400" x-text="vInvoiced"></span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 shrink-0"></span>
                            <span class="text-[11px] font-mono font-bold text-green-400" x-text="vPaid"></span>
                        </div>
                    </div>
                    <div class="w-2 h-2 bg-ink border-r border-b border-ink-70/30 rotate-45 mx-auto -mt-1"></div>
                </div>
            </div>

            {{-- Hour labels --}}
            <div class="flex justify-between mt-2 px-0.5">
                @foreach($hourlyData as $d)
                    <span class="text-[10px] font-mono text-ink-50">{{ $d['label'] }}</span>
                @endforeach
            </div>

            {{-- Today totals --}}
            <div class="flex items-center gap-4 mt-4 pt-3 border-t border-ink-70/15">
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber"></span>
                    <span class="text-[10px] font-heading text-ink-50">Werkbonnen</span>
                    <span class="font-mono text-xs font-bold text-amber">&euro;{{ number_format($totalEntriesToday, 2, ',', '.') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    <span class="text-[10px] font-heading text-ink-50">Gefactureerd</span>
                    <span class="font-mono text-xs font-bold text-blue-400">&euro;{{ number_format($totalInvoicedToday, 2, ',', '.') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    <span class="text-[10px] font-heading text-ink-50">Betaald</span>
                    <span class="font-mono text-xs font-bold text-green-400">&euro;{{ number_format($totalPaidToday, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Stat cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-10">
            <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50 block mb-1.5">Werkbonnen</span>
                <div class="font-display text-paper text-xl uppercase">{{ $entriesThisMonth }}</div>
                <p class="text-[10px] text-ink-50 mt-0.5">deze maand</p>
            </div>

            <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50 block mb-1.5">Concept</span>
                <div class="flex items-baseline gap-2">
                    <span class="font-display text-paper text-xl uppercase">{{ $draftEntries }}</span>
                    @if($draftTotal > 0)
                        <span class="font-mono text-xs text-ink-50">&euro;{{ number_format($draftTotal, 0, ',', '.') }}</span>
                    @endif
                </div>
                <p class="text-[10px] text-ink-50 mt-0.5">nog af te ronden</p>
            </div>

            <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50 block mb-1.5">Openstaand</span>
                <div class="font-mono text-amber text-lg font-bold">&euro;{{ number_format($openAmount, 0, ',', '.') }}</div>
                <p class="text-[10px] text-ink-50 mt-0.5">{{ $openInvoices }} {{ $openInvoices === 1 ? 'factuur' : 'facturen' }}</p>
            </div>

            <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50 block mb-1.5">Betaald</span>
                <div class="font-mono text-green-400 text-lg font-bold">&euro;{{ number_format($paidThisMonth, 0, ',', '.') }}</div>
                <p class="text-[10px] text-ink-50 mt-0.5">deze maand</p>
            </div>
        </div>

        {{-- Actieve projecten --}}
        @if($activeProjects->isNotEmpty())
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-heading font-semibold uppercase tracking-wider text-ink-50">Actieve projecten</span>
                    <a href="{{ route('projects.index') }}" wire:navigate class="text-xs text-amber font-heading font-semibold hover:text-amber/80 transition">Alle projecten &rarr;</a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($activeProjects as $project)
                        <a href="{{ route('projects.show', $project) }}" wire:navigate
                           class="bg-ink-90 rounded-sm p-4 border border-ink-70/20 hover:border-amber/30 transition group">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-6 h-6 rounded-full bg-green-500/15 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-hammer text-green-400 text-[9px]"></i>
                                </div>
                                <span class="font-heading font-semibold text-paper text-sm truncate group-hover:text-amber transition">{{ $project->name }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-[10px] text-ink-50">
                                <span>{{ $project->entries_count }} {{ $project->entries_count === 1 ? 'invoer' : 'invoeren' }}</span>
                                @if($project->entries_sum_total_amount)
                                    <span class="text-ink-70">&middot;</span>
                                    <span class="font-mono font-semibold text-paper/50">&euro;{{ number_format($project->entries_sum_total_amount, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Vervallen facturen waarschuwing --}}
        @if($overdueInvoices->isNotEmpty())
            <div class="flex items-center gap-3 px-4 py-3 bg-red-500/10 border border-red-500/20 rounded-sm mb-6">
                <i class="fa-solid fa-triangle-exclamation text-red-400 text-sm"></i>
                <span class="text-sm text-red-400 font-heading">{{ $overdueInvoices->count() }} {{ $overdueInvoices->count() === 1 ? 'factuur is' : 'facturen zijn' }} vervallen</span>
                <a href="{{ route('invoices.index') }}" wire:navigate class="ml-auto text-xs text-red-400 font-heading font-semibold hover:text-red-300 transition">Bekijken &rarr;</a>
            </div>
        @endif

        {{-- Openstaande facturen --}}
        @if($recentInvoices->isNotEmpty())
            <div class="mb-10">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-heading font-semibold uppercase tracking-wider text-ink-50">Openstaande facturen</span>
                    <a href="{{ route('invoices.index') }}" wire:navigate class="text-xs text-amber font-heading font-semibold hover:text-amber/80 transition">Alle facturen &rarr;</a>
                </div>

                <div class="bg-ink-90 rounded-sm border border-ink-70/20 overflow-hidden divide-y divide-ink-70/10">
                    @foreach($recentInvoices as $invoice)
                        <a href="{{ route('invoices.show', $invoice) }}" wire:navigate
                           class="flex items-center gap-3.5 px-4 py-3.5 hover:bg-ink-70/10 transition group">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                {{ $invoice->status === 'sent' && $invoice->due_date->isPast() ? 'bg-red-500/15' : ($invoice->status === 'sent' ? 'bg-blue-500/15' : 'bg-ink-70/15') }}">
                                @if($invoice->status === 'sent' && $invoice->due_date->isPast())
                                    <i class="fa-solid fa-triangle-exclamation text-red-400 text-xs"></i>
                                @elseif($invoice->status === 'sent')
                                    <i class="fa-solid fa-paper-plane text-blue-400 text-xs"></i>
                                @else
                                    <i class="fa-solid fa-file-invoice text-ink-50 text-xs"></i>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-heading font-semibold text-paper text-sm">
                                    <span class="font-mono">{{ $invoice->invoice_number }}</span>
                                    @if($invoice->client)
                                        <span class="text-ink-50 font-normal mx-1">&middot;</span>
                                        <span class="text-paper/60 font-normal">{{ $invoice->client->name }}</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-ink-50 mt-0.5">
                                    @if($invoice->status === 'sent' && $invoice->due_date->isPast())
                                        <span class="text-red-400">Vervallen {{ $invoice->due_date->diffForHumans() }}</span>
                                    @elseif($invoice->status === 'sent')
                                        Vervalt {{ $invoice->due_date->format('j M Y') }}
                                    @else
                                        Concept &middot; {{ $invoice->issue_date->format('j M Y') }}
                                    @endif
                                </div>
                            </div>
                            <span class="font-mono text-sm font-bold {{ $invoice->status === 'sent' && $invoice->due_date->isPast() ? 'text-red-400' : 'text-paper/60' }} shrink-0">
                                &euro;{{ number_format($invoice->total, 2, ',', '.') }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Recente activiteit --}}
        @php
            $recentEntries = $workspace->entries()->with(['project', 'client'])->latest()->take(8)->get();
        @endphp

        <div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-heading font-semibold uppercase tracking-wider text-ink-50">Recente activiteit</span>
                @if($recentEntries->isNotEmpty())
                    <a href="{{ route('werkbonnen.index') }}" wire:navigate class="text-xs text-amber font-heading font-semibold hover:text-amber/80 transition">Alles &rarr;</a>
                @endif
            </div>

            @if($recentEntries->isEmpty())
                <div class="text-center py-16">
                    <div class="w-14 h-14 rounded-full bg-ink-90 border border-ink-70/20 flex items-center justify-center mx-auto">
                        <i class="fa-solid fa-clock-rotate-left text-ink-50 text-lg"></i>
                    </div>
                    <p class="mt-4 text-sm text-ink-30 font-heading">Nog geen activiteit</p>
                    <p class="text-xs text-ink-50 mt-1">Maak je eerste invoer met de knoppen hierboven</p>
                </div>
            @else
                <div class="space-y-1">
                    @foreach($recentEntries as $entry)
                        <a href="{{ route('werkbonnen.show', $entry) }}" wire:navigate
                           class="flex items-center gap-3.5 px-4 py-3.5 rounded-sm hover:bg-ink-90/80 transition group">

                            <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                {{ $entry->type === 'voice' ? 'bg-amber/10' : ($entry->type === 'photo' ? 'bg-blue-500/10' : ($entry->type === 'video' ? 'bg-purple-500/10' : 'bg-ink-70/15')) }}">
                                @if($entry->type === 'voice')
                                    <i class="fa-solid fa-microphone text-amber text-xs"></i>
                                @elseif($entry->type === 'photo')
                                    <i class="fa-solid fa-camera text-blue-400 text-xs"></i>
                                @elseif($entry->type === 'video')
                                    <i class="fa-solid fa-video text-purple-400 text-xs"></i>
                                @else
                                    <i class="fa-solid fa-pen text-ink-50 text-xs"></i>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="font-heading font-semibold text-paper text-sm truncate">{{ $entry->title ?? 'Zonder titel' }}</div>
                                <div class="text-[11px] text-ink-50 mt-0.5 flex items-center gap-1.5">
                                    <span>{{ $entry->created_at->diffForHumans() }}</span>
                                    @if($entry->project)
                                        <span class="text-ink-70">&middot;</span>
                                        <span>{{ $entry->project->name }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-3 shrink-0">
                                @if($entry->status === 'processing')
                                    <div class="w-2 h-2 rounded-full bg-amber animate-pulse" title="Verwerken"></div>
                                @elseif($entry->status === 'final')
                                    <div class="w-2 h-2 rounded-full bg-green-500" title="Definitief"></div>
                                @endif

                                @if($entry->total_amount)
                                    <span class="font-mono text-xs font-bold text-paper/60">
                                        &euro;{{ number_format($entry->total_amount, 2, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
