<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 pt-8 pb-12 lg:pt-12 lg:pb-16">

        @php
            $totalAmount = $project->entries()->sum('total_amount');
            $draftCount = $project->entries()->where('status', 'draft')->count();
            $finalCount = $project->entries()->where('status', 'final')->count();
            $invoiceTotal = $invoices->sum('total');
        @endphp

        {{-- Header --}}
        <div class="mb-8 pt-2 lg:pt-0">
            <div class="flex items-center gap-3 mb-3">
                <a href="{{ route('projects.index') }}" wire:navigate class="w-8 h-8 rounded-full bg-ink-90 border border-ink-70/20 flex items-center justify-center text-paper/50 hover:text-paper hover:border-amber/30 transition shrink-0">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                </a>
                <p class="text-xs font-heading uppercase tracking-wider text-ink-50">Project</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full flex items-center justify-center shrink-0
                    {{ $project->status === 'active' ? 'bg-green-500/15' : 'bg-ink-70/15' }}">
                    @if($project->status === 'active')
                        <i class="fa-solid fa-hammer text-green-400 text-xl"></i>
                    @elseif($project->status === 'completed')
                        <i class="fa-solid fa-circle-check text-ink-50 text-xl"></i>
                    @else
                        <i class="fa-solid fa-box-archive text-ink-50 text-xl"></i>
                    @endif
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-3">
                        <h1 class="font-display text-paper text-2xl lg:text-3xl uppercase leading-tight">{{ $project->name }}<span class="text-amber">.</span></h1>
                        @if($project->status === 'active')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-heading font-semibold uppercase tracking-wider bg-green-500/15 text-green-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                Actief
                            </span>
                        @elseif($project->status === 'completed')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-heading font-semibold uppercase tracking-wider bg-ink-70/15 text-ink-50">
                                Afgerond
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-heading font-semibold uppercase tracking-wider bg-ink-70/15 text-ink-50">
                                Gearchiveerd
                            </span>
                        @endif
                    </div>
                    @if($project->client)
                        <p class="text-sm text-ink-50 font-heading mt-0.5">
                            <i class="fa-solid fa-user text-[9px] mr-1"></i>
                            <a href="{{ route('clients.show', $project->client) }}" wire:navigate class="hover:text-amber transition">{{ $project->client->name }}</a>
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Stat cards --}}
        <div class="grid grid-cols-4 gap-3 mb-8">
            <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50 block mb-1.5">Werkbonnen</span>
                <div class="font-display text-paper text-xl uppercase">{{ $project->entries_count }}</div>
            </div>

            <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50 block mb-1.5">Facturen</span>
                <div class="font-display text-paper text-xl uppercase">{{ $invoices->count() }}</div>
            </div>

            <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50 block mb-1.5">Concept</span>
                <div class="font-display text-paper text-xl uppercase">{{ $draftCount }}</div>
            </div>

            <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50 block mb-1.5">Totaal</span>
                <div class="font-mono text-amber text-lg font-bold">&euro;{{ number_format($totalAmount, 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- Project details --}}
        @if($project->description || $project->address)
            <div class="bg-ink-90 rounded-sm border border-ink-70/15 overflow-hidden mb-8">
                <div class="px-5 py-3.5 border-b border-ink-70/15 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-md bg-amber/10 flex items-center justify-center">
                        <i class="fa-solid fa-info text-amber text-[10px]"></i>
                    </div>
                    <span class="font-heading font-semibold text-paper text-sm">Projectgegevens</span>
                </div>
                <div class="px-5 py-4 space-y-3">
                    @if($project->description)
                        <div>
                            <span class="text-[9px] font-heading uppercase tracking-wider text-ink-50 block mb-1">Omschrijving</span>
                            <p class="text-sm text-paper/80 leading-relaxed">{{ $project->description }}</p>
                        </div>
                    @endif
                    @if($project->address)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-md bg-ink-70/10 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-location-dot text-ink-50 text-[10px]"></i>
                            </div>
                            <div>
                                <span class="text-[9px] font-heading uppercase tracking-wider text-ink-50 block">Locatie</span>
                                <span class="text-sm text-paper/80">{{ $project->address }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Werkbonnen van dit project --}}
        <div class="mb-4">
            <span class="text-xs font-heading font-semibold uppercase tracking-wider text-ink-50">Werkbonnen</span>
        </div>

        @if($entries->isEmpty())
            <div class="text-center py-12">
                <div class="w-12 h-12 rounded-full bg-ink-90 border border-ink-70/20 flex items-center justify-center mx-auto">
                    <i class="fa-solid fa-clipboard-list text-ink-50 text-base"></i>
                </div>
                <p class="mt-4 text-sm text-ink-30 font-heading">Nog geen werkbonnen voor dit project</p>
            </div>
        @else
            <div class="bg-ink-90 rounded-sm border border-ink-70/20 overflow-hidden divide-y divide-ink-70/10">
                @foreach($entries as $entry)
                    <a href="{{ route('werkbonnen.show', $entry) }}" wire:navigate
                       class="flex items-center gap-3.5 px-5 py-4 hover:bg-ink-70/10 transition group">

                        <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0
                            {{ $entry->type === 'voice' ? 'bg-amber/10' : ($entry->type === 'photo' ? 'bg-blue-500/10' : ($entry->type === 'video' ? 'bg-purple-500/10' : 'bg-ink-70/15')) }}">
                            @if($entry->type === 'voice')
                                <i class="fa-solid fa-microphone text-amber text-sm"></i>
                            @elseif($entry->type === 'photo')
                                <i class="fa-solid fa-camera text-blue-400 text-sm"></i>
                            @elseif($entry->type === 'video')
                                <i class="fa-solid fa-video text-purple-400 text-sm"></i>
                            @else
                                <i class="fa-solid fa-pen text-ink-50 text-sm"></i>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="font-heading font-semibold text-paper text-sm truncate">{{ $entry->title ?? 'Zonder titel' }}</div>
                            <div class="text-[11px] text-ink-50 mt-0.5">
                                {{ $entry->entry_date?->format('j M Y') ?? $entry->created_at->diffForHumans() }}
                                @if($entry->client)
                                    <span class="text-ink-70 mx-1">&middot;</span>
                                    {{ $entry->client->name }}
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            @if($entry->status === 'processing')
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-heading font-semibold uppercase tracking-wider text-amber">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber animate-pulse"></span>
                                    Verwerken
                                </span>
                            @elseif($entry->status === 'draft')
                                <span class="text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 bg-ink-70/15 px-2 py-0.5 rounded-full">Concept</span>
                            @elseif($entry->status === 'final')
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-heading font-semibold uppercase tracking-wider text-green-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Definitief
                                </span>
                            @endif

                            @if($entry->total_amount)
                                <span class="font-mono text-xs font-bold text-paper/70">
                                    &euro;{{ number_format($entry->total_amount, 2, ',', '.') }}
                                </span>
                            @endif

                            <i class="fa-solid fa-chevron-right text-ink-70 text-[10px] group-hover:text-amber transition"></i>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($entries->hasPages())
                <div class="mt-8">
                    {{ $entries->links() }}
                </div>
            @endif
        @endif

        {{-- Facturen van dit project --}}
        <div class="mb-4 mt-10">
            <span class="text-xs font-heading font-semibold uppercase tracking-wider text-ink-50">Facturen</span>
        </div>

        @if($invoices->isEmpty())
            <div class="text-center py-12">
                <div class="w-12 h-12 rounded-full bg-ink-90 border border-ink-70/20 flex items-center justify-center mx-auto">
                    <i class="fa-solid fa-file-invoice text-ink-50 text-base"></i>
                </div>
                <p class="mt-4 text-sm text-ink-30 font-heading">Nog geen facturen voor dit project</p>
            </div>
        @else
            <div class="bg-ink-90 rounded-sm border border-ink-70/20 overflow-hidden divide-y divide-ink-70/10">
                @foreach($invoices as $invoice)
                    <a href="{{ route('invoices.show', $invoice) }}" wire:navigate
                       class="flex items-center gap-3.5 px-5 py-4 hover:bg-ink-70/10 transition group">

                        <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0
                            {{ $invoice->isPaid() ? 'bg-green-500/10' : ($invoice->isOverdue() ? 'bg-red-500/10' : 'bg-amber/10') }}">
                            @if($invoice->isPaid())
                                <i class="fa-solid fa-check text-green-400 text-sm"></i>
                            @elseif($invoice->isOverdue())
                                <i class="fa-solid fa-clock text-red-400 text-sm"></i>
                            @else
                                <i class="fa-solid fa-file-invoice text-amber text-sm"></i>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="font-heading font-semibold text-paper text-sm truncate">{{ $invoice->invoice_number }}</div>
                            <div class="text-[11px] text-ink-50 mt-0.5">
                                {{ $invoice->issue_date?->format('j M Y') }}
                                @if($invoice->client)
                                    <span class="text-ink-70 mx-1">&middot;</span>
                                    {{ $invoice->client->name }}
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            @if($invoice->isPaid())
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-heading font-semibold uppercase tracking-wider text-green-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Betaald
                                </span>
                            @elseif($invoice->isSent())
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-heading font-semibold uppercase tracking-wider {{ $invoice->isOverdue() ? 'text-red-400' : 'text-blue-400' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $invoice->isOverdue() ? 'bg-red-500' : 'bg-blue-500' }}"></span>
                                    {{ $invoice->isOverdue() ? 'Verlopen' : 'Verzonden' }}
                                </span>
                            @else
                                <span class="text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 bg-ink-70/15 px-2 py-0.5 rounded-full">Concept</span>
                            @endif

                            <span class="font-mono text-xs font-bold text-paper/70">
                                &euro;{{ number_format($invoice->total, 2, ',', '.') }}
                            </span>

                            <i class="fa-solid fa-chevron-right text-ink-70 text-[10px] group-hover:text-amber transition"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Acties --}}
        <div class="flex items-center gap-3 mt-10 pt-8 border-t border-ink-70/10">
            <a href="{{ route('projects.edit', $project) }}" wire:navigate class="inline-flex items-center gap-2 border border-amber/40 px-5 py-2.5 text-amber font-semibold text-sm font-heading rounded-sm transition hover:bg-amber/10 hover:border-amber/60">
                <i class="fa-solid fa-pen text-xs"></i>
                Bewerken
            </a>
            <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Weet je zeker dat je dit project wilt verwijderen? Werkbonnen blijven behouden.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 text-red-400/70 font-semibold text-sm font-heading rounded-sm transition hover:text-red-400 hover:bg-red-500/10">
                    <i class="fa-solid fa-trash text-xs"></i>
                    Verwijderen
                </button>
            </form>
            <a href="{{ route('projects.index') }}" wire:navigate class="ml-auto px-4 py-2.5 text-ink-50 font-semibold text-sm font-heading transition hover:text-paper">
                &larr; Terug naar overzicht
            </a>
        </div>

    </div>
</x-app-layout>
