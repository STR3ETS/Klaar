<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 pt-8 pb-12 lg:pt-12 lg:pb-16">

        @php
            $workspace = auth()->user()->currentWorkspace();
            $totalInvoices = $workspace->invoices()->count();
            $openAmount = $workspace->invoices()->whereIn('status', ['sent', 'draft'])->sum('total');
            $paidAmount = $workspace->invoices()->where('status', 'paid')->sum('total');
        @endphp

        {{-- Header --}}
        <div class="mb-8 pt-2 lg:pt-0">
            <p class="text-xs font-heading uppercase tracking-wider text-ink-50">Facturatie</p>
            <h1 class="font-display text-paper text-2xl lg:text-3xl uppercase mt-1">Facturen<span class="text-amber">.</span></h1>
        </div>

        {{-- Stat cards --}}
        <div class="grid grid-cols-3 gap-3 mb-10">
            <div class="bg-ink-90 rounded-sm p-5 border border-ink-70/20">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-full bg-amber/10 flex items-center justify-center">
                        <i class="fa-solid fa-file-invoice text-amber text-[10px]"></i>
                    </div>
                    <span class="text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50">Totaal</span>
                </div>
                <div class="font-display text-paper text-2xl uppercase">{{ $totalInvoices }}</div>
            </div>

            <div class="bg-ink-90 rounded-sm p-5 border border-ink-70/20">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-full bg-blue-500/10 flex items-center justify-center">
                        <i class="fa-solid fa-clock text-blue-400 text-[10px]"></i>
                    </div>
                    <span class="text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50">Openstaand</span>
                </div>
                <div class="font-mono text-amber text-lg font-bold">&euro;{{ number_format($openAmount, 0, ',', '.') }}</div>
            </div>

            <div class="bg-ink-90 rounded-sm p-5 border border-ink-70/20">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-full bg-green-500/10 flex items-center justify-center">
                        <i class="fa-solid fa-check text-green-500 text-[10px]"></i>
                    </div>
                    <span class="text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50">Betaald</span>
                </div>
                <div class="font-mono text-green-400 text-lg font-bold">&euro;{{ number_format($paidAmount, 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="mb-6" x-data="{ filtersOpen: {{ request()->hasAny(['status', 'client']) ? 'true' : 'false' }} }">
            <div class="flex items-center justify-between mb-3">
                <button @click="filtersOpen = !filtersOpen" class="inline-flex items-center gap-2 text-xs font-heading font-semibold uppercase tracking-wider text-ink-50 hover:text-paper transition cursor-pointer">
                    <i class="fa-solid fa-filter text-[10px]"></i>
                    Filters
                    @if(request()->hasAny(['status', 'client']))
                        <span class="w-1.5 h-1.5 rounded-full bg-amber"></span>
                    @endif
                </button>
                @if(request()->hasAny(['status', 'client']))
                    <a href="{{ route('invoices.index') }}" wire:navigate class="text-xs text-ink-50 hover:text-amber transition font-heading">Wis filters</a>
                @endif
            </div>

            <div x-show="filtersOpen"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 style="{{ request()->hasAny(['status', 'client']) ? '' : 'display: none;' }}">
                <form method="GET" action="{{ route('invoices.index') }}" class="bg-ink-90 rounded-sm border border-ink-70/20 p-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-1.5">Status</label>
                            <select name="status" class="klaar-dark-input w-full text-xs" onchange="this.form.submit()">
                                <option value="">Alle</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Concept</option>
                                <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Verstuurd</option>
                                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Betaald</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-1.5">Klant</label>
                            <select name="client" class="klaar-dark-input w-full text-xs" onchange="this.form.submit()">
                                <option value="">Alle</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ request('client') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Invoice list --}}
        @if($invoices->isEmpty())
            <div class="text-center py-20">
                <div class="w-16 h-16 rounded-full bg-ink-90 border border-ink-70/20 flex items-center justify-center mx-auto">
                    <i class="fa-solid fa-file-invoice text-ink-50 text-xl"></i>
                </div>
                @if(request()->hasAny(['status', 'client']))
                    <p class="mt-5 text-sm text-ink-30 font-heading font-semibold">Geen resultaten</p>
                    <p class="text-xs text-ink-50 mt-1.5 max-w-xs mx-auto leading-relaxed">Geen facturen gevonden met deze filters.</p>
                    <a href="{{ route('invoices.index') }}" wire:navigate class="mt-6 inline-flex items-center gap-2 border border-ink-70/30 px-5 py-2.5 text-ink-50 font-semibold text-sm font-heading rounded-sm transition hover:bg-ink-90 hover:text-paper">
                        Wis filters
                    </a>
                @else
                    <p class="mt-5 text-sm text-ink-30 font-heading font-semibold">Nog geen facturen</p>
                    <p class="text-xs text-ink-50 mt-1.5 max-w-xs mx-auto leading-relaxed">Maak een factuur aan vanuit een definitieve werkbon.</p>
                @endif
            </div>
        @else
            <div class="mb-4">
                <span class="text-xs font-heading font-semibold uppercase tracking-wider text-ink-50">Alle facturen</span>
            </div>

            <div class="bg-ink-90 rounded-sm border border-ink-70/20 overflow-hidden divide-y divide-ink-70/10">
                @foreach($invoices as $invoice)
                    <a href="{{ route('invoices.show', $invoice) }}" wire:navigate
                       class="flex items-center gap-4 px-5 py-4 hover:bg-ink-70/10 transition group">

                        <div class="w-11 h-11 rounded-full flex items-center justify-center shrink-0
                            {{ $invoice->status === 'paid' ? 'bg-green-500/15' : ($invoice->status === 'sent' ? 'bg-blue-500/15' : 'bg-ink-70/15') }}">
                            @if($invoice->status === 'paid')
                                <i class="fa-solid fa-check text-green-400 text-sm"></i>
                            @elseif($invoice->status === 'sent')
                                <i class="fa-solid fa-paper-plane text-blue-400 text-sm"></i>
                            @else
                                <i class="fa-solid fa-file-invoice text-ink-50 text-sm"></i>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="font-heading font-semibold text-paper text-sm">
                                <span class="font-mono">{{ $invoice->invoice_number }}</span>
                                @if($invoice->client)
                                    <span class="text-ink-50 font-normal mx-1">&middot;</span>
                                    <span class="text-paper/70">{{ $invoice->client->name }}</span>
                                @endif
                            </div>
                            <div class="text-[11px] text-ink-50 mt-0.5">
                                {{ $invoice->issue_date->format('j M Y') }}
                                @if($invoice->status === 'sent' && $invoice->due_date)
                                    <span class="text-ink-70 mx-1">&middot;</span>
                                    Vervalt {{ $invoice->due_date->format('j M Y') }}
                                @endif
                                @if($invoice->status === 'paid' && $invoice->paid_at)
                                    <span class="text-ink-70 mx-1">&middot;</span>
                                    Betaald {{ $invoice->paid_at->format('j M Y') }}
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            @if($invoice->status === 'draft')
                                <span class="text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 bg-ink-70/15 px-2 py-0.5 rounded-full">Concept</span>
                            @elseif($invoice->status === 'sent')
                                @if($invoice->isOverdue())
                                    <span class="inline-flex items-center gap-1.5 text-[10px] font-heading font-semibold uppercase tracking-wider text-red-400 bg-red-500/10 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                        Vervallen
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-[10px] font-heading font-semibold uppercase tracking-wider text-blue-400 bg-blue-500/10 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                        Verstuurd
                                    </span>
                                @endif
                            @elseif($invoice->status === 'paid')
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-heading font-semibold uppercase tracking-wider text-green-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Betaald
                                </span>
                            @endif

                            <span class="font-mono text-sm font-bold text-paper/70">
                                &euro;{{ number_format($invoice->total, 2, ',', '.') }}
                            </span>

                            <i class="fa-solid fa-chevron-right text-ink-70 text-[10px] group-hover:text-amber transition"></i>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($invoices->hasPages())
                <div class="mt-8">
                    {{ $invoices->links() }}
                </div>
            @endif
        @endif

    </div>
</x-app-layout>
