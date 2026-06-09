<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 pt-8 pb-12 lg:pt-12 lg:pb-16">

        @php
            $workspace = auth()->user()->currentWorkspace();
            $activeCount = $workspace->projects()->where('status', 'active')->count();
            $completedCount = $workspace->projects()->where('status', 'completed')->count();
            $totalRevenue = $workspace->entries()->whereNotNull('project_id')->sum('total_amount');
        @endphp

        {{-- Header --}}
        <div class="mb-8 pt-2 lg:pt-0">
            <p class="text-xs font-heading uppercase tracking-wider text-ink-50">Projectbeheer</p>
            <div class="flex items-center justify-between mt-1">
                <h1 class="font-display text-paper text-2xl lg:text-3xl uppercase">Projecten<span class="text-amber">.</span></h1>
                <a href="{{ route('projects.create') }}" wire:navigate class="inline-flex items-center gap-2 bg-amber px-4 py-2 text-ink font-semibold text-sm font-heading rounded-sm transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)]">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Nieuw project
                </a>
            </div>
        </div>

        {{-- Stat cards --}}
        <div class="grid grid-cols-3 gap-3 mb-10">
            <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                <div class="flex items-center gap-2 mb-2.5">
                    <div class="w-6 h-6 rounded-full bg-green-500/10 flex items-center justify-center">
                        <i class="fa-solid fa-hammer text-green-500 text-[9px]"></i>
                    </div>
                    <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50">Actief</span>
                </div>
                <div class="font-display text-paper text-xl uppercase">{{ $activeCount }}</div>
            </div>

            <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                <div class="flex items-center gap-2 mb-2.5">
                    <div class="w-6 h-6 rounded-full bg-ink-70/15 flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-ink-50 text-[9px]"></i>
                    </div>
                    <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50">Afgerond</span>
                </div>
                <div class="font-display text-paper text-xl uppercase">{{ $completedCount }}</div>
            </div>

            <div class="bg-ink-90 rounded-sm p-4 border border-ink-70/20">
                <div class="flex items-center gap-2 mb-2.5">
                    <div class="w-6 h-6 rounded-full bg-amber/10 flex items-center justify-center">
                        <i class="fa-solid fa-coins text-amber text-[9px]"></i>
                    </div>
                    <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50">Omzet</span>
                </div>
                <div class="font-display text-paper text-xl uppercase">&euro;{{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- Project list --}}
        @if($projects->isEmpty())
            <div class="text-center py-20">
                <div class="w-16 h-16 rounded-full bg-ink-90 border border-ink-70/20 flex items-center justify-center mx-auto">
                    <i class="fa-solid fa-diagram-project text-ink-50 text-xl"></i>
                </div>
                <p class="mt-5 text-sm text-ink-30 font-heading font-semibold">Nog geen projecten</p>
                <p class="text-xs text-ink-50 mt-1.5 max-w-xs mx-auto leading-relaxed">Maak een project aan om werkbonnen te groeperen per klus of locatie.</p>
                <a href="{{ route('projects.create') }}" wire:navigate class="mt-6 inline-flex items-center gap-2 bg-amber px-5 py-2.5 text-ink font-semibold text-sm font-heading rounded-sm transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)]">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Eerste project
                </a>
            </div>
        @else
            <div class="mb-4">
                <span class="text-xs font-heading font-semibold uppercase tracking-wider text-ink-50">Alle projecten</span>
            </div>

            <div class="bg-ink-90 rounded-sm border border-ink-70/20 overflow-hidden divide-y divide-ink-70/10">
                @foreach($projects as $project)
                    <a href="{{ route('projects.show', $project) }}" wire:navigate
                       class="flex items-center gap-4 px-5 py-4 hover:bg-ink-70/10 transition group">

                        <div class="w-11 h-11 rounded-full flex items-center justify-center shrink-0
                            {{ $project->status === 'active' ? 'bg-green-500/15' : ($project->status === 'completed' ? 'bg-ink-70/15' : 'bg-ink-70/10') }}">
                            @if($project->status === 'active')
                                <i class="fa-solid fa-hammer text-green-400 text-sm"></i>
                            @elseif($project->status === 'completed')
                                <i class="fa-solid fa-circle-check text-ink-50 text-sm"></i>
                            @else
                                <i class="fa-solid fa-box-archive text-ink-50 text-sm"></i>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="font-heading font-semibold text-paper text-sm">{{ $project->name }}</div>
                            <div class="text-[11px] text-ink-50 mt-0.5 flex items-center gap-1.5">
                                @if($project->client)
                                    <i class="fa-solid fa-user text-[8px]"></i>
                                    <span>{{ $project->client->name }}</span>
                                    <span class="text-ink-70">&middot;</span>
                                @endif
                                <span>{{ $project->entries_count }} {{ $project->entries_count === 1 ? 'invoer' : 'invoeren' }}</span>
                                @if($project->entries_sum_total_amount)
                                    <span class="text-ink-70">&middot;</span>
                                    <span class="font-mono font-semibold text-paper/50">&euro;{{ number_format($project->entries_sum_total_amount, 2, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            @if($project->status === 'active')
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-heading font-semibold uppercase tracking-wider text-green-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Actief
                                </span>
                            @elseif($project->status === 'completed')
                                <span class="text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 bg-ink-70/15 px-2 py-0.5 rounded-full">Afgerond</span>
                            @else
                                <span class="text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 bg-ink-70/15 px-2 py-0.5 rounded-full">Gearchiveerd</span>
                            @endif

                            <i class="fa-solid fa-chevron-right text-ink-70 text-[10px] group-hover:text-amber transition"></i>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($projects->hasPages())
                <div class="mt-8">
                    {{ $projects->links() }}
                </div>
            @endif
        @endif

    </div>
</x-app-layout>
