<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 pt-8 pb-12 lg:pt-12 lg:pb-16">

        @php
            $workspace = auth()->user()->currentWorkspace();
            $draftCount = $workspace->entries()->where('status', 'draft')->count();
            $draftTotal = $workspace->entries()->where('status', 'draft')->sum('total_amount');
            $finalCount = $workspace->entries()->where('status', 'final')->count();
            $finalTotal = $workspace->entries()->where('status', 'final')->sum('total_amount');
            $thisMonthCount = $workspace->entries()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        @endphp

        {{-- Header --}}
        <div class="mb-8 pt-2 lg:pt-0">
            <p class="text-xs font-heading uppercase tracking-wider text-ink-50">Overzicht</p>
            <div class="flex items-center justify-between mt-1">
                <h1 class="font-display text-paper text-2xl lg:text-3xl uppercase">Werkbonnen<span class="text-amber">.</span></h1>
                <a href="{{ route('invoeren.index') }}" wire:navigate class="inline-flex items-center gap-2 bg-amber px-4 py-2 text-ink font-semibold text-sm font-heading rounded-sm transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)]">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Nieuwe invoer
                </a>
            </div>
        </div>

        {{-- Stat cards --}}
        <div class="grid grid-cols-3 gap-3 mb-10">
            <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                <div class="flex items-center gap-2 mb-2.5">
                    <div class="w-6 h-6 rounded-full bg-amber/10 flex items-center justify-center">
                        <i class="fa-solid fa-pen-ruler text-amber text-[9px]"></i>
                    </div>
                    <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50">Concepten</span>
                </div>
                <div class="font-display text-paper text-xl uppercase">{{ $draftCount }}</div>
                <p class="text-[11px] text-ink-50 mt-0.5 font-mono">&euro;{{ number_format($draftTotal, 0, ',', '.') }}</p>
            </div>

            <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                <div class="flex items-center gap-2 mb-2.5">
                    <div class="w-6 h-6 rounded-full bg-green-500/10 flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-green-500 text-[9px]"></i>
                    </div>
                    <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50">Definitief</span>
                </div>
                <div class="font-display text-paper text-xl uppercase">{{ $finalCount }}</div>
                <p class="text-[11px] text-ink-50 mt-0.5 font-mono">&euro;{{ number_format($finalTotal, 0, ',', '.') }}</p>
            </div>

            <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                <div class="flex items-center gap-2 mb-2.5">
                    <div class="w-6 h-6 rounded-full bg-amber/10 flex items-center justify-center">
                        <i class="fa-solid fa-calendar text-amber text-[9px]"></i>
                    </div>
                    <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50">Deze maand</span>
                </div>
                <div class="font-display text-paper text-xl uppercase">{{ $thisMonthCount }}</div>
                <p class="text-[11px] text-ink-50 mt-0.5">{{ now()->translatedFormat('F') }}</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="mb-6" x-data="{ filtersOpen: {{ request()->hasAny(['status', 'type', 'client', 'project', 'search']) ? 'true' : 'false' }} }">
            <div class="flex items-center justify-between mb-3">
                <button @click="filtersOpen = !filtersOpen" class="inline-flex items-center gap-2 text-xs font-heading font-semibold uppercase tracking-wider text-ink-50 hover:text-paper transition cursor-pointer">
                    <i class="fa-solid fa-filter text-[10px]"></i>
                    Filters
                    @if(request()->hasAny(['status', 'type', 'client', 'project', 'search']))
                        <span class="w-1.5 h-1.5 rounded-full bg-amber"></span>
                    @endif
                </button>
                @if(request()->hasAny(['status', 'type', 'client', 'project', 'search']))
                    <a href="{{ route('werkbonnen.index') }}" wire:navigate class="text-xs text-ink-50 hover:text-amber transition font-heading">Wis filters</a>
                @endif
            </div>

            <div x-show="filtersOpen"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 style="{{ request()->hasAny(['status', 'type', 'client', 'project', 'search']) ? '' : 'display: none;' }}">
                <form method="GET" action="{{ route('werkbonnen.index') }}" class="bg-ink-90 rounded-sm border border-ink-70/20 p-4 space-y-3">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-1.5">Status</label>
                            <select name="status" class="klaar-dark-input w-full text-xs" onchange="this.form.submit()">
                                <option value="">Alle</option>
                                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Verwerken</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Concept</option>
                                <option value="final" {{ request('status') === 'final' ? 'selected' : '' }}>Definitief</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-1.5">Type</label>
                            <select name="type" class="klaar-dark-input w-full text-xs" onchange="this.form.submit()">
                                <option value="">Alle</option>
                                <option value="voice" {{ request('type') === 'voice' ? 'selected' : '' }}>Spraak</option>
                                <option value="photo" {{ request('type') === 'photo' ? 'selected' : '' }}>Foto</option>
                                <option value="manual" {{ request('type') === 'manual' ? 'selected' : '' }}>Handmatig</option>
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
                        <div>
                            <label class="block text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-1.5">Project</label>
                            <select name="project" class="klaar-dark-input w-full text-xs" onchange="this.form.submit()">
                                <option value="">Alle</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ request('project') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Entry list --}}
        @if($entries->isEmpty())
            <div class="text-center py-20">
                <div class="w-16 h-16 rounded-full bg-ink-90 border border-ink-70/20 flex items-center justify-center mx-auto">
                    <i class="fa-solid fa-file-lines text-ink-50 text-xl"></i>
                </div>
                @if(request()->hasAny(['status', 'type', 'client', 'project', 'search']))
                    <p class="mt-5 text-sm text-ink-30 font-heading font-semibold">Geen resultaten</p>
                    <p class="text-xs text-ink-50 mt-1.5 max-w-xs mx-auto leading-relaxed">Geen werkbonnen gevonden met deze filters.</p>
                    <a href="{{ route('werkbonnen.index') }}" wire:navigate class="mt-6 inline-flex items-center gap-2 border border-ink-70/30 px-5 py-2.5 text-ink-50 font-semibold text-sm font-heading rounded-sm transition hover:bg-ink-90 hover:text-paper">
                        Wis filters
                    </a>
                @else
                    <p class="mt-5 text-sm text-ink-30 font-heading font-semibold">Nog geen werkbonnen</p>
                    <p class="text-xs text-ink-50 mt-1.5 max-w-xs mx-auto leading-relaxed">Begin met inspreken, een foto maken of handmatig invoeren.</p>
                    <a href="{{ route('invoeren.index') }}" wire:navigate class="mt-6 inline-flex items-center gap-2 bg-amber px-5 py-2.5 text-ink font-semibold text-sm font-heading rounded-sm transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)]">
                        <i class="fa-solid fa-plus text-xs"></i>
                        Eerste invoer
                    </a>
                @endif
            </div>
        @else
            <div class="mb-4">
                <span class="text-xs font-heading font-semibold uppercase tracking-wider text-ink-50">Alle werkbonnen</span>
            </div>

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
                            <div class="text-[11px] text-ink-50 mt-0.5 flex items-center gap-1.5">
                                <span>{{ $entry->entry_date?->format('j M Y') ?? $entry->created_at->diffForHumans() }}</span>
                                @if($entry->client)
                                    <span class="text-ink-70">&middot;</span>
                                    <i class="fa-solid fa-user text-[8px]"></i>
                                    <span>{{ $entry->client->name }}</span>
                                @endif
                                @if($entry->project)
                                    <span class="text-ink-70">&middot;</span>
                                    <span>{{ $entry->project->name }}</span>
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

    </div>

    {{-- Voice command floating button + overlay --}}
    <div x-data="voiceCommand" class="fixed bottom-6 right-6 z-50">

        {{-- Result toast --}}
        <div x-show="state === 'result'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute bottom-16 right-0 w-72 bg-ink-90 border border-ink-70/20 rounded-sm shadow-xl p-4"
             style="display: none;">
            <div class="flex items-start gap-3">
                <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0"
                     :class="hasActions ? 'bg-green-500/15' : 'bg-amber/15'">
                    <i class="text-[10px]"
                       :class="hasActions ? 'fa-solid fa-check text-green-400' : 'fa-solid fa-info text-amber'"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-paper leading-relaxed" x-text="resultMessage"></p>
                </div>
            </div>
        </div>

        {{-- Listening overlay --}}
        <div x-show="state === 'listening' || state === 'processing'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute bottom-16 right-0 w-72 bg-ink-90 border border-ink-70/20 rounded-sm shadow-xl p-4"
             style="display: none;">

            {{-- Listening state --}}
            <template x-if="state === 'listening'">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                        <span class="text-[10px] font-heading font-semibold uppercase tracking-widest text-red-400">Luisteren</span>
                    </div>
                    <p class="text-sm leading-relaxed min-h-[2.5rem]">
                        <span class="text-paper" x-text="finalTranscript"></span><span class="text-ink-50 italic" x-text="interimTranscript"></span>
                        <span x-show="!finalTranscript && !interimTranscript" class="text-ink-50 italic">Geef je commando...</span>
                    </p>
                </div>
            </template>

            {{-- Processing state --}}
            <template x-if="state === 'processing'">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-spinner fa-spin text-amber text-sm"></i>
                    <span class="text-sm text-ink-30">Commando uitvoeren...</span>
                </div>
            </template>
        </div>

        {{-- Floating mic button --}}
        <button @click="toggle"
                class="w-14 h-14 rounded-full flex items-center justify-center shadow-lg transition-all"
                :class="state === 'listening'
                    ? 'bg-red-500 hover:bg-red-600 shadow-red-500/30 animate-pulse'
                    : state === 'processing'
                        ? 'bg-ink-90 border border-ink-70/20 cursor-wait'
                        : 'bg-amber hover:brightness-110 shadow-[0_4px_20px_rgba(255,180,0,0.4)]'"
                :disabled="state === 'processing'">
            <i class="text-lg"
               :class="state === 'listening'
                   ? 'fa-solid fa-stop text-white'
                   : state === 'processing'
                       ? 'fa-solid fa-spinner fa-spin text-amber'
                       : 'fa-solid fa-microphone text-ink'"></i>
        </button>
    </div>

</x-app-layout>
