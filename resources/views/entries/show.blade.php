<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('entries.index') }}" class="text-ink-50 hover:text-ink transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                </a>
                <div>
                    <h2 class="font-heading text-2xl font-bold text-ink">{{ $entry->title ?? 'Zonder titel' }}</h2>
                    <div class="mt-0.5 flex items-center gap-2 text-sm text-ink-50">
                        @if($entry->type === 'voice')
                            <svg class="w-4 h-4 text-amber" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                            <span>Spraak</span>
                        @elseif($entry->type === 'photo')
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/></svg>
                            <span>Foto</span>
                        @else
                            <svg class="w-4 h-4 text-ink-50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                            <span>Handmatig</span>
                        @endif
                        <span>&middot;</span>
                        <span>{{ $entry->entry_date?->format('j M Y') ?? $entry->created_at->format('j M Y') }}</span>
                        @if($entry->project)
                            <span>&middot;</span>
                            <span>{{ $entry->project->name }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Status badge --}}
            <div>
                @if($entry->status === 'processing')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-amber/15 text-amber">
                        <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Verwerken
                    </span>
                @elseif($entry->status === 'draft')
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-ink-10 text-ink-50">Concept</span>
                @else
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-green-50 text-green-700">Definitief</span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Processing notice --}}
            @if($entry->isProcessing())
                <div class="flex items-start gap-3 p-4 bg-amber/10 border border-amber/30 rounded-lg text-sm text-ink-70">
                    <svg class="w-5 h-5 text-amber shrink-0 mt-0.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <div>
                        <p class="font-medium text-ink">Deze invoer wordt nog verwerkt</p>
                        <p class="mt-1">Spraak wordt omgezet naar tekst en verwerkt door AI. Ververs de pagina over een moment.</p>
                    </div>
                </div>
            @endif

            {{-- Transcript --}}
            @if($entry->raw_transcript)
                <div class="bg-snow rounded-lg border border-ink-10/50 shadow-sm">
                    <div class="px-6 py-4 border-b border-ink-10/50">
                        <h3 class="font-heading font-semibold text-ink">Transcript</h3>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-sm text-ink-70 leading-relaxed whitespace-pre-wrap">{{ $entry->raw_transcript }}</p>
                    </div>
                </div>
            @endif

            {{-- AI Extracted data summary --}}
            @if($entry->ai_extracted_data)
                @php $extracted = $entry->ai_extracted_data; @endphp
                @if(!empty($extracted['description']) || !empty($extracted['beschrijving']))
                    <div class="bg-snow rounded-lg border border-ink-10/50 shadow-sm">
                        <div class="px-6 py-4 border-b border-ink-10/50">
                            <h3 class="font-heading font-semibold text-ink">Samenvatting</h3>
                        </div>
                        <div class="px-6 py-4">
                            <p class="text-sm text-ink-70 leading-relaxed">{{ $extracted['description'] ?? $extracted['beschrijving'] ?? '' }}</p>
                            @if(!empty($extracted['client_hint']))
                                <div class="mt-3 flex items-center gap-2 text-sm text-ink-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0"/></svg>
                                    <span>Klant: {{ $extracted['client_hint'] }}</span>
                                </div>
                            @endif
                            @if(!empty($extracted['project_hint']))
                                <div class="mt-1 flex items-center gap-2 text-sm text-ink-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 0h.008v.008h-.008v-.008z"/></svg>
                                    <span>Project: {{ $extracted['project_hint'] }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif

            {{-- Line items --}}
            @if($entry->lineItems->isNotEmpty())
                <div class="bg-snow rounded-lg border border-ink-10/50 shadow-sm">
                    <div class="px-6 py-4 border-b border-ink-10/50">
                        <h3 class="font-heading font-semibold text-ink">Regelitems</h3>
                    </div>

                    {{-- Desktop table --}}
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-ink-10/50 text-left text-xs font-medium text-ink-50 uppercase tracking-wider">
                                    <th class="px-6 py-3">Omschrijving</th>
                                    <th class="px-4 py-3 text-right">Aantal</th>
                                    <th class="px-4 py-3">Eenheid</th>
                                    <th class="px-4 py-3 text-right">Prijs</th>
                                    <th class="px-4 py-3 text-right">BTW</th>
                                    <th class="px-6 py-3 text-right">Totaal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-ink-10/50">
                                @foreach($entry->lineItems as $item)
                                    <tr>
                                        <td class="px-6 py-3 text-ink">{{ $item->description }}</td>
                                        <td class="px-4 py-3 text-right font-mono text-ink-70">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
                                        <td class="px-4 py-3 text-ink-50">{{ $item->unit }}</td>
                                        <td class="px-4 py-3 text-right font-mono text-ink-70">&euro;{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right text-ink-50">{{ (int)$item->btw_rate }}%</td>
                                        <td class="px-6 py-3 text-right font-mono font-medium text-ink">&euro;{{ number_format($item->total, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile cards --}}
                    <div class="sm:hidden divide-y divide-ink-10/50">
                        @foreach($entry->lineItems as $item)
                            <div class="px-6 py-4">
                                <div class="flex items-start justify-between">
                                    <span class="text-sm font-medium text-ink">{{ $item->description }}</span>
                                    <span class="text-sm font-mono font-medium text-ink ml-4">&euro;{{ number_format($item->total, 2, ',', '.') }}</span>
                                </div>
                                <div class="mt-1 text-xs text-ink-50">
                                    {{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }} {{ $item->unit }}
                                    &times; &euro;{{ number_format($item->unit_price, 2, ',', '.') }}
                                    &middot; {{ (int)$item->btw_rate }}% BTW
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Totals --}}
                    @if($entry->total_amount)
                        <div class="px-6 py-4 border-t border-ink-10/50 bg-paper/30">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-ink-70">Totaal (excl. BTW)</span>
                                <span class="font-mono text-lg font-bold text-ink">&euro;{{ number_format($entry->total_amount, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Documents / attachments --}}
            @if($entry->documents->isNotEmpty())
                <div class="bg-snow rounded-lg border border-ink-10/50 shadow-sm">
                    <div class="px-6 py-4 border-b border-ink-10/50">
                        <h3 class="font-heading font-semibold text-ink">Bestanden</h3>
                    </div>
                    <div class="divide-y divide-ink-10/50">
                        @foreach($entry->documents as $doc)
                            <div class="flex items-center gap-3 px-6 py-3">
                                @if(str_starts_with($doc->mime_type ?? '', 'image/'))
                                    <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.41a2.25 2.25 0 013.182 0l2.909 2.909M2.25 15.75V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-2.25"/></svg>
                                @elseif(str_starts_with($doc->mime_type ?? '', 'audio/'))
                                    <svg class="w-5 h-5 text-amber shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                                @else
                                    <svg class="w-5 h-5 text-ink-30 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm text-ink truncate">{{ $doc->original_name }}</div>
                                    <div class="text-xs text-ink-50">
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

            {{-- AI Jobs --}}
            @if($entry->aiJobs->isNotEmpty())
                <div class="bg-snow rounded-lg border border-ink-10/50 shadow-sm">
                    <div class="px-6 py-4 border-b border-ink-10/50">
                        <h3 class="font-heading font-semibold text-ink text-sm">Verwerkingsstappen</h3>
                    </div>
                    <div class="divide-y divide-ink-10/50">
                        @foreach($entry->aiJobs as $job)
                            <div class="flex items-center gap-3 px-6 py-3">
                                @if($job->isCompleted())
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                @elseif($job->hasFailed())
                                    <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                @elseif($job->isProcessing())
                                    <svg class="w-4 h-4 text-amber shrink-0 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                @else
                                    <div class="w-4 h-4 rounded-full border-2 border-ink-30 shrink-0"></div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm text-ink">
                                        @if($job->type === 'transcription') Spraak naar tekst
                                        @elseif($job->type === 'extraction') AI extractie
                                        @elseif($job->type === 'ocr') Tekstherkenning
                                        @else {{ ucfirst($job->type) }}
                                        @endif
                                    </div>
                                </div>
                                <div class="text-xs text-ink-50">
                                    {{ $job->provider }}
                                    @if($job->completed_at)
                                        &middot; {{ $job->completed_at->diffForHumans() }}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex items-center gap-3">
                @if($entry->isDraft())
                    <form action="{{ route('entries.finalize', $entry) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-5 py-2.5 bg-amber text-ink text-sm font-semibold rounded-md hover:bg-amber/90 transition shadow-sm">
                            Markeer als definitief
                        </button>
                    </form>
                @endif
                <a href="{{ route('entries.index') }}" class="px-5 py-2.5 bg-snow border border-ink-10 text-ink-70 text-sm font-medium rounded-md hover:bg-ink-10/50 transition">
                    Terug naar overzicht
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
