<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 pt-8 pb-12 lg:pt-12 lg:pb-16">

        @php
            $workspace = auth()->user()->currentWorkspace();
            $totalClients = $workspace->clients()->count();
            $totalRevenue = $workspace->entries()->whereNotNull('client_id')->sum('total_amount');
        @endphp

        {{-- Header --}}
        <div class="mb-8 pt-2 lg:pt-0">
            <p class="text-xs font-heading uppercase tracking-wider text-ink-50">Klantenbeheer</p>
            <div class="flex items-center justify-between mt-1">
                <h1 class="font-display text-paper text-2xl lg:text-3xl uppercase">Relaties<span class="text-amber">.</span></h1>
                <a href="{{ route('clients.create') }}" wire:navigate class="inline-flex items-center gap-2 bg-amber px-4 py-2 text-ink font-semibold text-sm font-heading rounded-sm transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)]">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Nieuwe relatie
                </a>
            </div>
        </div>

        {{-- Stat cards --}}
        <div class="grid grid-cols-2 gap-3 mb-10">
            <div class="bg-ink-90 rounded-sm p-5 border border-ink-70/20">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-full bg-amber/10 flex items-center justify-center">
                        <i class="fa-solid fa-address-book text-amber text-[10px]"></i>
                    </div>
                    <span class="text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50">Relaties</span>
                </div>
                <div class="font-display text-paper text-2xl uppercase">{{ $totalClients }}</div>
                <p class="text-xs text-ink-50 mt-1">totaal</p>
            </div>

            <div class="bg-ink-90 rounded-sm p-5 border border-ink-70/20">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-full bg-green-500/10 flex items-center justify-center">
                        <i class="fa-solid fa-coins text-green-500 text-[10px]"></i>
                    </div>
                    <span class="text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50">Omzet</span>
                </div>
                <div class="font-display text-paper text-2xl uppercase">&euro;{{ number_format($totalRevenue, 0, ',', '.') }}</div>
                <p class="text-xs text-ink-50 mt-1 font-mono">gekoppeld aan relaties</p>
            </div>
        </div>

        {{-- Client list --}}
        @if($clients->isEmpty())
            <div class="text-center py-20">
                <div class="w-16 h-16 rounded-full bg-ink-90 border border-ink-70/20 flex items-center justify-center mx-auto">
                    <i class="fa-solid fa-address-book text-ink-50 text-xl"></i>
                </div>
                <p class="mt-5 text-sm text-ink-30 font-heading font-semibold">Nog geen relaties</p>
                <p class="text-xs text-ink-50 mt-1.5 max-w-xs mx-auto leading-relaxed">Relaties worden automatisch aangemaakt wanneer je een klantnaam noemt in je spraakopname.</p>
            </div>
        @else
            <div class="mb-4">
                <span class="text-xs font-heading font-semibold uppercase tracking-wider text-ink-50">Alle relaties</span>
            </div>

            <div class="bg-ink-90 rounded-sm border border-ink-70/20 overflow-hidden divide-y divide-ink-70/10">
                @foreach($clients as $client)
                    <a href="{{ route('clients.show', $client) }}" wire:navigate
                       class="flex items-center gap-4 px-5 py-4 hover:bg-ink-70/10 transition group">

                        <div class="w-11 h-11 rounded-full bg-amber/15 flex items-center justify-center shrink-0">
                            <span class="text-base font-bold text-amber font-heading">{{ strtoupper(substr($client->name, 0, 1)) }}</span>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="font-heading font-semibold text-paper text-sm">{{ $client->name }}</div>
                            <div class="text-[11px] text-ink-50 mt-0.5 flex items-center gap-1.5">
                                <span>{{ $client->entries_count }} {{ $client->entries_count === 1 ? 'invoer' : 'invoeren' }}</span>
                                @if($client->entries_sum_total_amount)
                                    <span class="text-ink-70">&middot;</span>
                                    <span class="font-mono font-semibold text-paper/50">&euro;{{ number_format($client->entries_sum_total_amount, 2, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>

                        <i class="fa-solid fa-chevron-right text-ink-70 text-[10px] group-hover:text-amber transition"></i>
                    </a>
                @endforeach
            </div>

            @if($clients->hasPages())
                <div class="mt-8">
                    {{ $clients->links() }}
                </div>
            @endif
        @endif

    </div>
</x-app-layout>
