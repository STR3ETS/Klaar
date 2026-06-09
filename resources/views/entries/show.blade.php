<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 pt-8 pb-12 lg:pt-12 lg:pb-16">

        {{-- Header --}}
        <div class="mb-8 pt-2 lg:pt-0">
            <div class="flex items-center gap-3 mb-3">
                <a href="{{ route('entries.index') }}" wire:navigate class="w-8 h-8 rounded-full bg-ink-90 border border-ink-70/20 flex items-center justify-center text-paper/50 hover:text-paper hover:border-amber/30 transition shrink-0">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                </a>
                <p class="text-xs font-heading uppercase tracking-wider text-ink-50">Werkbon</p>
            </div>
            <div class="flex items-start justify-between gap-4">
                <h1 class="font-display text-paper text-2xl lg:text-3xl uppercase leading-tight">{{ $entry->title ?? 'Zonder titel' }}<span class="text-amber">.</span></h1>
                @if($entry->status === 'processing')
                    <span class="inline-flex items-center gap-1.5 bg-amber/10 border border-amber/20 px-3 py-1.5 rounded-full shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber animate-pulse"></span>
                        <span class="text-[10px] font-heading font-semibold uppercase tracking-wider text-amber">Verwerken</span>
                    </span>
                @elseif($entry->status === 'draft')
                    <span class="inline-flex items-center gap-1.5 bg-ink-70/10 border border-ink-70/20 px-3 py-1.5 rounded-full shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-ink-50"></span>
                        <span class="text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50">Concept</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 bg-green-500/10 border border-green-500/20 px-3 py-1.5 rounded-full shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                        <span class="text-[10px] font-heading font-semibold uppercase tracking-wider text-green-400">Definitief</span>
                    </span>
                @endif
            </div>
        </div>

        {{-- Info cards row --}}
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-8">
            {{-- Type --}}
            <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50 block mb-1.5">Type</span>
                <div class="flex items-center gap-2">
                    @if($entry->type === 'voice')
                        <div class="w-6 h-6 rounded-full bg-amber/10 flex items-center justify-center">
                            <i class="fa-solid fa-microphone text-amber text-[9px]"></i>
                        </div>
                        <span class="text-sm font-heading font-semibold text-paper">Spraak</span>
                    @elseif($entry->type === 'photo')
                        <div class="w-6 h-6 rounded-full bg-blue-500/10 flex items-center justify-center">
                            <i class="fa-solid fa-camera text-blue-400 text-[9px]"></i>
                        </div>
                        <span class="text-sm font-heading font-semibold text-paper">Foto</span>
                    @else
                        <div class="w-6 h-6 rounded-full bg-ink-70/15 flex items-center justify-center">
                            <i class="fa-solid fa-pen text-ink-50 text-[9px]"></i>
                        </div>
                        <span class="text-sm font-heading font-semibold text-paper">Handmatig</span>
                    @endif
                </div>
            </div>

            {{-- Datum --}}
            <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50 block mb-1.5">Datum</span>
                <span class="text-sm font-heading font-semibold text-paper">{{ $entry->entry_date?->format('j M Y') ?? $entry->created_at->format('j M Y') }}</span>
            </div>

            {{-- Klant --}}
            <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50 block mb-1.5">Klant</span>
                @if($entry->client)
                    <a href="{{ route('clients.show', $entry->client) }}" wire:navigate class="text-sm font-heading font-semibold text-amber hover:text-amber/80 transition">
                        {{ $entry->client->name }}
                    </a>
                @else
                    <span class="text-sm text-ink-50 italic">Geen klant</span>
                @endif
            </div>

            {{-- Project --}}
            <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50 block mb-1.5">Project</span>
                @if($entry->project)
                    <a href="{{ route('projects.show', $entry->project) }}" wire:navigate class="text-sm font-heading font-semibold text-amber hover:text-amber/80 transition">
                        {{ $entry->project->name }}
                    </a>
                @else
                    <span class="text-sm text-ink-50 italic">Geen project</span>
                @endif
            </div>

            {{-- Totaal --}}
            <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50 block mb-1.5">Totaal incl. BTW</span>
                @if($entry->total_amount)
                    @php
                        $headerTotalExBtw = $entry->lineItems->sum(fn($item) => $item->calculateTotal());
                        $headerTotalBtw = $entry->lineItems->sum(fn($item) => $item->btwAmount());
                        $headerTotalInclBtw = $headerTotalExBtw + $headerTotalBtw;
                    @endphp
                    <span class="font-mono text-lg font-bold text-amber">&euro;{{ number_format($headerTotalInclBtw, 2, ',', '.') }}</span>
                @else
                    <span class="text-sm text-ink-50 italic">&mdash;</span>
                @endif
            </div>
        </div>

        <div class="space-y-4">

            {{-- Verwerkingsmelding --}}
            @if($entry->isProcessing())
                <div class="flex items-center gap-3 px-4 py-3 bg-amber/10 border border-amber/20 rounded-sm">
                    <i class="fa-solid fa-spinner fa-spin text-amber text-sm"></i>
                    <div>
                        <span class="text-sm text-amber font-heading font-semibold">Wordt verwerkt</span>
                        <span class="text-xs text-amber/60 ml-2">Spraak &rarr; tekst &rarr; werkbon</span>
                    </div>
                </div>
            @endif

            <div class="grid {{ ($entry->raw_transcript && $entry->type !== 'photo') ? 'grid-cols-2' : 'grid-cols-1' }} gap-4">
                {{-- Transcript --}}
                @if($entry->raw_transcript && $entry->type !== 'photo')
                    <div class="bg-ink-90 rounded-sm border border-ink-70/15 overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-ink-70/15 flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-md bg-amber/10 flex items-center justify-center">
                                <i class="fa-solid fa-quote-left text-amber text-[10px]"></i>
                            </div>
                            <span class="font-heading font-semibold text-paper text-sm">Transcript</span>
                        </div>
                        <div class="px-5 py-4">
                            <p class="text-sm text-ink-30 leading-relaxed whitespace-pre-wrap">{{ $entry->raw_transcript }}</p>
                        </div>
                    </div>
                @endif

                {{-- AI Samenvatting --}}
                @if($entry->ai_extracted_data)
                    @php $extracted = $entry->ai_extracted_data; @endphp
                    @if(!empty($extracted['description']) || !empty($extracted['beschrijving']))
                        <div class="bg-ink-90 rounded-sm border border-ink-70/15 overflow-hidden">
                            <div class="px-5 py-3.5 border-b border-ink-70/15 flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-md bg-amber/10 flex items-center justify-center">
                                    <i class="fa-solid fa-wand-magic-sparkles text-amber text-[10px]"></i>
                                </div>
                                <span class="font-heading font-semibold text-paper text-sm">Samenvatting</span>
                            </div>
                            <div class="px-5 py-4">
                                <p class="text-sm text-ink-30 leading-relaxed">{{ $extracted['description'] ?? $extracted['beschrijving'] ?? '' }}</p>
                                @if(!empty($extracted['project_hint']))
                                    <div class="mt-3 flex items-center gap-2 text-xs text-ink-50">
                                        <i class="fa-solid fa-building text-amber/50 text-[9px]"></i>
                                        <span>Project: <strong class="text-paper/70 font-heading">{{ $extracted['project_hint'] }}</strong></span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            {{-- Regelitems --}}
            @if($entry->lineItems->isNotEmpty())
                <div class="bg-ink-90 rounded-sm border border-ink-70/15 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-ink-70/15 flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-md bg-amber/10 flex items-center justify-center">
                            <i class="fa-solid fa-clipboard-list text-amber text-[10px]"></i>
                        </div>
                        <span class="font-heading font-semibold text-paper text-sm">Regelitems</span>
                        <span class="ml-auto text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50">{{ $entry->lineItems->count() }} {{ $entry->lineItems->count() === 1 ? 'item' : 'items' }}</span>
                    </div>

                    {{-- Desktop tabel --}}
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-ink-70/15 text-left">
                                    <th class="px-5 py-3 text-[10px] font-heading font-semibold text-ink-50 uppercase tracking-wider">Omschrijving</th>
                                    <th class="px-4 py-3 text-right text-[10px] font-heading font-semibold text-ink-50 uppercase tracking-wider">Aantal</th>
                                    <th class="px-4 py-3 text-[10px] font-heading font-semibold text-ink-50 uppercase tracking-wider">Eenheid</th>
                                    <th class="px-4 py-3 text-right text-[10px] font-heading font-semibold text-ink-50 uppercase tracking-wider">Prijs</th>
                                    <th class="px-4 py-3 text-right text-[10px] font-heading font-semibold text-ink-50 uppercase tracking-wider">BTW</th>
                                    <th class="px-5 py-3 text-right text-[10px] font-heading font-semibold text-ink-50 uppercase tracking-wider">Totaal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-ink-70/10">
                                @foreach($entry->lineItems as $item)
                                    <tr>
                                        <td class="px-5 py-3 text-paper/80 font-medium">{{ $item->description }}</td>
                                        <td class="px-4 py-3 text-right font-mono text-ink-30">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
                                        <td class="px-4 py-3 text-ink-50">{{ $item->unit }}</td>
                                        <td class="px-4 py-3 text-right font-mono text-ink-30">&euro;{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right text-ink-50">{{ (int)$item->btw_rate }}%</td>
                                        <td class="px-5 py-3 text-right font-mono font-bold text-paper/80">&euro;{{ number_format($item->total, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobiele kaarten --}}
                    <div class="sm:hidden divide-y divide-ink-70/10">
                        @foreach($entry->lineItems as $item)
                            <div class="px-5 py-4">
                                <div class="flex items-start justify-between">
                                    <span class="text-sm font-heading font-semibold text-paper/80">{{ $item->description }}</span>
                                    <span class="text-sm font-mono font-bold text-amber ml-4">&euro;{{ number_format($item->total, 2, ',', '.') }}</span>
                                </div>
                                <div class="mt-1 text-xs text-ink-50">
                                    {{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }} {{ $item->unit }}
                                    &times; &euro;{{ number_format($item->unit_price, 2, ',', '.') }}
                                    &middot; {{ (int)$item->btw_rate }}% BTW
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Totalen --}}
                    @if($entry->total_amount)
                        @php
                            $totalExBtw = $entry->lineItems->sum(fn($item) => $item->calculateTotal());
                            $totalBtw = $entry->lineItems->sum(fn($item) => $item->btwAmount());
                            $totalInclBtw = $totalExBtw + $totalBtw;
                        @endphp
                        <div class="border-t border-ink-70/15">
                            <div class="px-5 py-2.5 flex items-center justify-between">
                                <span class="text-sm font-heading text-ink-50">Subtotaal excl. BTW</span>
                                <span class="font-mono text-sm text-ink-30">&euro;{{ number_format($totalExBtw, 2, ',', '.') }}</span>
                            </div>
                            <div class="px-5 py-2.5 flex items-center justify-between">
                                <span class="text-sm font-heading text-ink-50">BTW</span>
                                <span class="font-mono text-sm text-ink-30">&euro;{{ number_format($totalBtw, 2, ',', '.') }}</span>
                            </div>
                            <div class="px-5 py-3.5 border-t border-amber/20 bg-amber/5 flex items-center justify-between">
                                <span class="text-sm font-heading font-semibold text-paper/70">Totaal incl. BTW</span>
                                <span class="font-mono text-xl font-bold text-amber">&euro;{{ number_format($totalInclBtw, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Gekoppelde facturen --}}
            @if($entry->invoices->isNotEmpty())
                <div class="bg-ink-90 rounded-sm border border-ink-70/15 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-ink-70/15 flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-md bg-amber/10 flex items-center justify-center">
                            <i class="fa-solid fa-file-invoice text-amber text-[10px]"></i>
                        </div>
                        <span class="font-heading font-semibold text-paper text-sm">Facturen</span>
                    </div>
                    <div class="divide-y divide-ink-70/10">
                        @foreach($entry->invoices as $invoice)
                            <a href="{{ route('invoices.show', $invoice) }}" wire:navigate class="flex items-center gap-3 px-5 py-3.5 hover:bg-ink-70/10 transition group">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                    {{ $invoice->status === 'paid' ? 'bg-green-500/15' : ($invoice->status === 'sent' ? 'bg-blue-500/15' : 'bg-ink-70/15') }}">
                                    @if($invoice->status === 'paid')
                                        <i class="fa-solid fa-check text-green-400 text-xs"></i>
                                    @elseif($invoice->status === 'sent')
                                        <i class="fa-solid fa-paper-plane text-blue-400 text-xs"></i>
                                    @else
                                        <i class="fa-solid fa-file-invoice text-ink-50 text-xs"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="text-sm font-heading font-semibold text-paper/80 font-mono">{{ $invoice->invoice_number }}</span>
                                    <div class="text-[11px] text-ink-50 mt-0.5">{{ $invoice->issue_date->format('j M Y') }}</div>
                                </div>
                                <span class="font-mono text-sm font-bold text-paper/60 shrink-0">&euro;{{ number_format($invoice->total, 2, ',', '.') }}</span>
                                <i class="fa-solid fa-chevron-right text-ink-70 text-[10px] group-hover:text-amber transition"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Bestanden --}}
            @if($entry->documents->isNotEmpty())
                <div class="bg-ink-90 rounded-sm border border-ink-70/15 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-ink-70/15 flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-md bg-amber/10 flex items-center justify-center">
                            <i class="fa-solid fa-paperclip text-amber text-[10px]"></i>
                        </div>
                        <span class="font-heading font-semibold text-paper text-sm">Bestanden</span>
                    </div>
                    <div class="divide-y divide-ink-70/10">
                        @foreach($entry->documents as $doc)
                            <div class="flex items-center gap-3 px-5 py-3">
                                <div class="w-8 h-8 rounded-md flex items-center justify-center shrink-0
                                    {{ str_starts_with($doc->mime_type ?? '', 'image/') ? 'bg-blue-500/10' : (str_starts_with($doc->mime_type ?? '', 'audio/') ? 'bg-amber/10' : 'bg-ink-70/15') }}">
                                    @if(str_starts_with($doc->mime_type ?? '', 'image/'))
                                        <i class="fa-solid fa-image text-blue-400 text-xs"></i>
                                    @elseif(str_starts_with($doc->mime_type ?? '', 'audio/'))
                                        <i class="fa-solid fa-microphone text-amber text-xs"></i>
                                    @else
                                        <i class="fa-solid fa-file text-ink-50 text-xs"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-heading text-paper/80 truncate">{{ $doc->original_name }}</div>
                                    <div class="text-[10px] text-ink-50 font-heading uppercase tracking-wider">
                                        {{ strtoupper(pathinfo($doc->original_name, PATHINFO_EXTENSION)) }}
                                        &middot;
                                        {{ number_format(($doc->size ?? 0) / 1024, 0) }} KB
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- Acties --}}
        <div class="flex items-center gap-3 mt-10 pt-8 border-t border-ink-70/10">
            @if($entry->isDraft())
                <a href="{{ route('entries.edit', $entry) }}" wire:navigate class="inline-flex items-center gap-2 border border-amber/40 px-5 py-2.5 text-amber font-semibold text-sm font-heading rounded-sm transition hover:bg-amber/10 hover:border-amber/60">
                    <i class="fa-solid fa-pen text-xs"></i>
                    Bewerken
                </a>
                <form action="{{ route('entries.finalize', $entry) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="inline-flex items-center gap-2 bg-amber px-5 py-2.5 text-ink font-semibold text-sm font-heading rounded-sm transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)]">
                        <i class="fa-solid fa-check text-xs"></i>
                        Markeer als definitief
                    </button>
                </form>
            @endif

            @if($entry->isFinal() && $entry->client_id)
                <form action="{{ route('invoices.create-from-entry', $entry) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 bg-amber px-5 py-2.5 text-ink font-semibold text-sm font-heading rounded-sm transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)] cursor-pointer">
                        <i class="fa-solid fa-file-invoice text-xs"></i>
                        Factureren
                    </button>
                </form>
            @elseif($entry->isFinal() && !$entry->client_id)
                <a href="{{ route('entries.edit', $entry) }}" wire:navigate class="inline-flex items-center gap-2 border border-amber/30 px-5 py-2.5 text-amber/70 font-semibold text-sm font-heading rounded-sm transition hover:bg-amber/10 hover:text-amber">
                    <i class="fa-solid fa-user-plus text-xs"></i>
                    Koppel klant om te factureren
                </a>
            @endif

            <form action="{{ route('entries.destroy', $entry) }}" method="POST" onsubmit="return confirm('Weet je zeker dat je deze werkbon wilt verwijderen?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 text-red-400/70 font-semibold text-sm font-heading rounded-sm transition hover:text-red-400 hover:bg-red-500/10">
                    <i class="fa-solid fa-trash text-xs"></i>
                    Verwijderen
                </button>
            </form>

            <a href="{{ route('entries.index') }}" wire:navigate class="ml-auto px-4 py-2.5 text-ink-50 font-semibold text-sm font-heading transition hover:text-paper">
                &larr; Terug naar overzicht
            </a>
        </div>

    </div>
</x-app-layout>
