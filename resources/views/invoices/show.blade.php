<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 pt-8 pb-12 lg:pt-12 lg:pb-16">

        {{-- Header --}}
        <div class="mb-8 pt-2 lg:pt-0">
            <div class="flex items-center gap-3 mb-3">
                <a href="{{ route('invoices.index') }}" wire:navigate class="w-8 h-8 rounded-full bg-ink-90 border border-ink-70/20 flex items-center justify-center text-paper/50 hover:text-paper hover:border-amber/30 transition shrink-0">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                </a>
                <p class="text-xs font-heading uppercase tracking-wider text-ink-50">Factuur</p>
            </div>
            <div class="flex items-start justify-between gap-4">
                <h1 class="font-display text-paper text-2xl lg:text-3xl uppercase leading-tight">{{ $invoice->invoice_number }}<span class="text-amber">.</span></h1>
                @if($invoice->status === 'draft')
                    <span class="inline-flex items-center gap-1.5 bg-ink-70/10 border border-ink-70/20 px-3 py-1.5 rounded-full shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-ink-50"></span>
                        <span class="text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50">Concept</span>
                    </span>
                @elseif($invoice->status === 'sent')
                    @if($invoice->isOverdue())
                        <span class="inline-flex items-center gap-1.5 bg-red-500/10 border border-red-500/20 px-3 py-1.5 rounded-full shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                            <span class="text-[10px] font-heading font-semibold uppercase tracking-wider text-red-400">Vervallen</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 bg-blue-500/10 border border-blue-500/20 px-3 py-1.5 rounded-full shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                            <span class="text-[10px] font-heading font-semibold uppercase tracking-wider text-blue-400">Verstuurd</span>
                        </span>
                    @endif
                @elseif($invoice->status === 'paid')
                    <span class="inline-flex items-center gap-1.5 bg-green-500/10 border border-green-500/20 px-3 py-1.5 rounded-full shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                        <span class="text-[10px] font-heading font-semibold uppercase tracking-wider text-green-400">Betaald</span>
                    </span>
                @endif
            </div>
        </div>

        {{-- Info cards row --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
            {{-- Factuurdatum --}}
            <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50 block mb-1.5">Factuurdatum</span>
                <span class="text-sm font-heading font-semibold text-paper">{{ $invoice->issue_date->format('j M Y') }}</span>
            </div>

            {{-- Vervaldatum --}}
            <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50 block mb-1.5">Vervaldatum</span>
                <span class="text-sm font-heading font-semibold {{ $invoice->isOverdue() ? 'text-red-400' : 'text-paper' }}">{{ $invoice->due_date->format('j M Y') }}</span>
            </div>

            {{-- Klant --}}
            <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50 block mb-1.5">Klant</span>
                @if($invoice->client)
                    <a href="{{ route('clients.show', $invoice->client) }}" wire:navigate class="text-sm font-heading font-semibold text-amber hover:text-amber/80 transition">
                        {{ $invoice->client->name }}
                    </a>
                @else
                    <span class="text-sm text-ink-50 italic">Verwijderd</span>
                @endif
            </div>

            {{-- Totaal --}}
            <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50 block mb-1.5">Totaal incl. BTW</span>
                <span class="font-mono text-lg font-bold text-amber">&euro;{{ number_format($invoice->total, 2, ',', '.') }}</span>
            </div>
        </div>

        <div class="space-y-4">

            {{-- Klantgegevens --}}
            @if($invoice->client)
                <div class="bg-ink-90 rounded-sm border border-ink-70/15 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-ink-70/15 flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-md bg-amber/10 flex items-center justify-center">
                            <i class="fa-solid fa-user text-amber text-[10px]"></i>
                        </div>
                        <span class="font-heading font-semibold text-paper text-sm">Aan</span>
                    </div>
                    <div class="px-5 py-4">
                        <p class="text-sm text-paper/80 font-heading font-semibold">{{ $invoice->client->name }}</p>
                        @if($invoice->client->company)
                            <p class="text-sm text-ink-30">{{ $invoice->client->company }}</p>
                        @endif
                        @if($invoice->client->fullAddress())
                            <p class="text-sm text-ink-30 mt-1">{{ $invoice->client->fullAddress() }}</p>
                        @endif
                        @if($invoice->client->btw_number)
                            <p class="text-xs text-ink-50 font-mono mt-2">BTW: {{ $invoice->client->btw_number }}</p>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Regelitems --}}
            @if($invoice->lineItems->isNotEmpty())
                <div class="bg-ink-90 rounded-sm border border-ink-70/15 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-ink-70/15 flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-md bg-amber/10 flex items-center justify-center">
                            <i class="fa-solid fa-clipboard-list text-amber text-[10px]"></i>
                        </div>
                        <span class="font-heading font-semibold text-paper text-sm">Regelitems</span>
                        <span class="ml-auto text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50">{{ $invoice->lineItems->count() }} {{ $invoice->lineItems->count() === 1 ? 'item' : 'items' }}</span>
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
                                @foreach($invoice->lineItems as $item)
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
                        @foreach($invoice->lineItems as $item)
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
                    <div class="border-t border-ink-70/15">
                        <div class="px-5 py-2.5 flex items-center justify-between">
                            <span class="text-sm font-heading text-ink-50">Subtotaal excl. BTW</span>
                            <span class="font-mono text-sm text-ink-30">&euro;{{ number_format($invoice->subtotal, 2, ',', '.') }}</span>
                        </div>
                        <div class="px-5 py-2.5 flex items-center justify-between">
                            <span class="text-sm font-heading text-ink-50">BTW</span>
                            <span class="font-mono text-sm text-ink-30">&euro;{{ number_format($invoice->btw_amount, 2, ',', '.') }}</span>
                        </div>
                        <div class="px-5 py-3.5 border-t border-amber/20 bg-amber/5 flex items-center justify-between">
                            <span class="text-sm font-heading font-semibold text-paper/70">Totaal incl. BTW</span>
                            <span class="font-mono text-xl font-bold text-amber">&euro;{{ number_format($invoice->total, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Gekoppelde werkbon --}}
            @if($invoice->entry)
                <div class="bg-ink-90 rounded-sm border border-ink-70/15 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-ink-70/15 flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-md bg-amber/10 flex items-center justify-center">
                            <i class="fa-solid fa-link text-amber text-[10px]"></i>
                        </div>
                        <span class="font-heading font-semibold text-paper text-sm">Werkbon</span>
                    </div>
                    <a href="{{ route('werkbonnen.show', $invoice->entry) }}" wire:navigate class="flex items-center gap-3 px-5 py-4 hover:bg-ink-70/10 transition group">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0
                            {{ $invoice->entry->type === 'voice' ? 'bg-amber/10' : ($invoice->entry->type === 'photo' ? 'bg-blue-500/10' : ($invoice->entry->type === 'video' ? 'bg-purple-500/10' : 'bg-ink-70/15')) }}">
                            @if($invoice->entry->type === 'voice')
                                <i class="fa-solid fa-microphone text-amber text-sm"></i>
                            @elseif($invoice->entry->type === 'photo')
                                <i class="fa-solid fa-camera text-blue-400 text-sm"></i>
                            @elseif($invoice->entry->type === 'video')
                                <i class="fa-solid fa-video text-purple-400 text-sm"></i>
                            @else
                                <i class="fa-solid fa-pen text-ink-50 text-sm"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-heading font-semibold text-paper text-sm truncate">{{ $invoice->entry->title ?? 'Zonder titel' }}</div>
                            <div class="text-[11px] text-ink-50 mt-0.5">{{ $invoice->entry->entry_date?->format('j M Y') }}</div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-ink-70 text-[10px] group-hover:text-amber transition"></i>
                    </a>
                </div>
            @endif

            {{-- Notities --}}
            @if($invoice->notes)
                <div class="bg-ink-90 rounded-sm border border-ink-70/15 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-ink-70/15 flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-md bg-amber/10 flex items-center justify-center">
                            <i class="fa-solid fa-sticky-note text-amber text-[10px]"></i>
                        </div>
                        <span class="font-heading font-semibold text-paper text-sm">Notities</span>
                    </div>
                    <div class="px-5 py-4">
                        <p class="text-sm text-ink-30 leading-relaxed whitespace-pre-wrap">{{ $invoice->notes }}</p>
                    </div>
                </div>
            @endif

        </div>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mt-8 bg-green-500/10 border border-green-500/20 rounded-sm px-5 py-3 flex items-center gap-3">
                <i class="fa-solid fa-check-circle text-green-400 text-sm"></i>
                <span class="text-sm text-green-400 font-heading font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Acties --}}
        <div class="flex flex-wrap items-center gap-3 mt-10 pt-8 border-t border-ink-70/10">
            @if($invoice->isDraft())
                @if($invoice->client && $invoice->client->email)
                    <form action="{{ route('invoices.send-email', $invoice) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 bg-amber px-5 py-2.5 text-ink font-semibold text-sm font-heading rounded-sm transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)] cursor-pointer">
                            <i class="fa-solid fa-envelope text-xs"></i>
                            Verstuur per e-mail
                        </button>
                    </form>
                @endif
                <form action="{{ route('invoices.mark-sent', $invoice) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="inline-flex items-center gap-2 border border-ink-70/30 px-5 py-2.5 text-ink-50 font-semibold text-sm font-heading rounded-sm transition hover:bg-ink-90 hover:text-paper cursor-pointer">
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                        Markeer als verstuurd
                    </button>
                </form>
            @endif

            @if($invoice->isSent())
                @if($invoice->client && $invoice->client->email)
                    <form action="{{ route('invoices.send-email', $invoice) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 border border-amber/40 px-5 py-2.5 text-amber font-semibold text-sm font-heading rounded-sm transition hover:bg-amber/10 hover:border-amber/60 cursor-pointer">
                            <i class="fa-solid fa-envelope text-xs"></i>
                            Herinnering sturen
                        </button>
                    </form>
                @endif
                <form action="{{ route('invoices.mark-paid', $invoice) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="inline-flex items-center gap-2 bg-green-600 px-5 py-2.5 text-white font-semibold text-sm font-heading rounded-sm transition hover:bg-green-500 cursor-pointer">
                        <i class="fa-solid fa-check text-xs"></i>
                        Markeer als betaald
                    </button>
                </form>
            @endif

            <a href="{{ route('invoices.download-pdf', $invoice) }}" class="inline-flex items-center gap-2 border border-ink-70/30 px-5 py-2.5 text-ink-50 font-semibold text-sm font-heading rounded-sm transition hover:bg-ink-90 hover:text-paper">
                <i class="fa-solid fa-download text-xs"></i>
                Download PDF
            </a>

            @if($invoice->isDraft())
                <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" onsubmit="return confirm('Weet je zeker dat je deze concept-factuur wilt verwijderen?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 border border-red-500/30 px-5 py-2.5 text-red-400 font-semibold text-sm font-heading rounded-sm transition hover:bg-red-500/10 hover:border-red-500/50 cursor-pointer">
                        <i class="fa-solid fa-trash text-xs"></i>
                        Verwijderen
                    </button>
                </form>
            @endif

            <a href="{{ route('invoices.index') }}" wire:navigate class="ml-auto px-4 py-2.5 text-ink-50 font-semibold text-sm font-heading transition hover:text-paper">
                &larr; Terug naar overzicht
            </a>
        </div>

    </div>
</x-app-layout>
