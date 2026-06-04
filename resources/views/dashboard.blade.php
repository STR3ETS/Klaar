<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-2xl font-bold text-ink">
                    Dashboard
                </h2>
                <p class="mt-1 text-sm text-ink-50">Welkom terug, {{ auth()->user()->name }}.</p>
            </div>
            <a href="{{ route('entries.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber text-ink text-sm font-semibold rounded-md hover:bg-amber/90 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Nieuwe invoer
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Stats --}}
            @php
                $workspace = auth()->user()->currentWorkspace();
                $entriesThisMonth = $workspace->entries()->whereMonth('created_at', now()->month)->count();
                $draftEntries = $workspace->entries()->where('status', 'draft')->count();
                $unpaidInvoices = $workspace->invoices()->whereIn('status', ['sent', 'overdue'])->count();
                $monthRevenue = $workspace->invoices()->where('status', 'paid')->whereMonth('paid_at', now()->month)->sum('total');
            @endphp

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                <div class="bg-snow rounded-lg p-5 shadow-sm border border-ink-10/50">
                    <div class="text-sm font-medium text-ink-50">Invoeren deze maand</div>
                    <div class="mt-2 font-heading text-2xl font-bold text-ink">{{ $entriesThisMonth }}</div>
                </div>
                <div class="bg-snow rounded-lg p-5 shadow-sm border border-ink-10/50">
                    <div class="text-sm font-medium text-ink-50">Concepten</div>
                    <div class="mt-2 font-heading text-2xl font-bold text-ink">{{ $draftEntries }}</div>
                </div>
                <div class="bg-snow rounded-lg p-5 shadow-sm border border-ink-10/50">
                    <div class="text-sm font-medium text-ink-50">Open facturen</div>
                    <div class="mt-2 font-heading text-2xl font-bold text-ink">{{ $unpaidInvoices }}</div>
                </div>
                <div class="bg-snow rounded-lg p-5 shadow-sm border border-ink-10/50">
                    <div class="text-sm font-medium text-ink-50">Omzet deze maand</div>
                    <div class="mt-2 font-mono text-2xl font-bold text-ink">&euro;{{ number_format($monthRevenue, 2, ',', '.') }}</div>
                </div>
            </div>

            {{-- Quick actions --}}
            <div class="mt-8 grid sm:grid-cols-3 gap-4">
                <a href="{{ route('entries.create') }}?type=voice" class="flex items-center gap-4 bg-snow rounded-lg p-5 shadow-sm border border-ink-10/50 hover:border-amber hover:shadow-md transition group">
                    <div class="w-12 h-12 bg-amber/15 rounded-lg flex items-center justify-center group-hover:bg-amber/25 transition">
                        <svg class="w-6 h-6 text-amber" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="font-heading font-semibold text-ink">Inspreken</div>
                        <div class="text-sm text-ink-50">Vertel wat je gedaan hebt</div>
                    </div>
                </a>

                <a href="{{ route('entries.create') }}?type=photo" class="flex items-center gap-4 bg-snow rounded-lg p-5 shadow-sm border border-ink-10/50 hover:border-amber hover:shadow-md transition group">
                    <div class="w-12 h-12 bg-amber/15 rounded-lg flex items-center justify-center group-hover:bg-amber/25 transition">
                        <svg class="w-6 h-6 text-amber" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="font-heading font-semibold text-ink">Foto maken</div>
                        <div class="text-sm text-ink-50">Bon of materiaallijst</div>
                    </div>
                </a>

                <a href="{{ route('entries.create') }}?type=manual" class="flex items-center gap-4 bg-snow rounded-lg p-5 shadow-sm border border-ink-10/50 hover:border-amber hover:shadow-md transition group">
                    <div class="w-12 h-12 bg-amber/15 rounded-lg flex items-center justify-center group-hover:bg-amber/25 transition">
                        <svg class="w-6 h-6 text-amber" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                        </svg>
                    </div>
                    <div>
                        <div class="font-heading font-semibold text-ink">Handmatig</div>
                        <div class="text-sm text-ink-50">Zelf invoeren</div>
                    </div>
                </a>
            </div>

            {{-- Recent entries --}}
            <div class="mt-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-heading text-lg font-semibold text-ink">Recente invoeren</h3>
                    <a href="{{ route('entries.index') }}" class="text-sm text-amber font-medium hover:text-amber/80 transition">Bekijk alles &rarr;</a>
                </div>

                @php
                    $recentEntries = $workspace->entries()->with('project')->latest()->take(5)->get();
                @endphp

                @if($recentEntries->isEmpty())
                    <div class="bg-snow rounded-lg p-10 shadow-sm border border-ink-10/50 text-center">
                        <div class="w-16 h-16 bg-ink-10 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-8 h-8 text-ink-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                            </svg>
                        </div>
                        <h4 class="mt-4 font-heading font-semibold text-ink">Nog geen invoeren</h4>
                        <p class="mt-2 text-sm text-ink-50 max-w-sm mx-auto">
                            Begin met inspreken of maak een foto van een bon. Klaar doet de rest.
                        </p>
                        <a href="{{ route('entries.create') }}" class="mt-6 inline-flex items-center gap-2 px-6 py-2.5 bg-amber text-ink text-sm font-semibold rounded-md hover:bg-amber/90 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            Eerste invoer maken
                        </a>
                    </div>
                @else
                    <div class="bg-snow rounded-lg shadow-sm border border-ink-10/50 divide-y divide-ink-10/50">
                        @foreach($recentEntries as $entry)
                            <a href="{{ route('entries.show', $entry) }}" class="flex items-center gap-4 p-4 hover:bg-ink-10/30 transition first:rounded-t-lg last:rounded-b-lg">
                                {{-- Type icon --}}
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0
                                    {{ $entry->type === 'voice' ? 'bg-amber/15' : ($entry->type === 'photo' ? 'bg-blue-50' : 'bg-ink-10') }}">
                                    @if($entry->type === 'voice')
                                        <svg class="w-5 h-5 text-amber" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                                    @elseif($entry->type === 'photo')
                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/></svg>
                                    @else
                                        <svg class="w-5 h-5 text-ink-50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                                    @endif
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-ink truncate">{{ $entry->title ?? 'Zonder titel' }}</div>
                                    <div class="text-sm text-ink-50">
                                        {{ $entry->created_at->diffForHumans() }}
                                        @if($entry->project)
                                            &middot; {{ $entry->project->name }}
                                        @endif
                                    </div>
                                </div>

                                {{-- Status --}}
                                <div class="shrink-0">
                                    @if($entry->status === 'processing')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber/15 text-amber">Verwerken</span>
                                    @elseif($entry->status === 'draft')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-ink-10 text-ink-50">Concept</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700">Definitief</span>
                                    @endif
                                </div>

                                {{-- Amount --}}
                                @if($entry->total_amount)
                                    <div class="shrink-0 font-mono text-sm font-medium text-ink">
                                        &euro;{{ number_format($entry->total_amount, 2, ',', '.') }}
                                    </div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
