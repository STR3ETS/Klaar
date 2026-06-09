<x-app-layout>
    @if($type === 'voice')
        <livewire:voice-recorder />

        {{-- Recent feed --}}
        @if($recentEntries->isNotEmpty())
            <div class="max-w-xl mx-auto px-4 pb-12 lg:pb-16">
                <div class="mb-4">
                    <span class="text-[10px] font-heading font-semibold uppercase tracking-widest text-ink-50">Recente invoeren</span>
                </div>
                <div class="bg-ink-90 rounded-sm border border-ink-70/20 overflow-hidden divide-y divide-ink-70/10">
                    @foreach($recentEntries as $entry)
                        <a href="{{ route('werkbonnen.show', $entry) }}" wire:navigate
                           class="flex items-center gap-3 px-4 py-3 hover:bg-ink-70/10 transition group">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0
                                {{ $entry->type === 'voice' ? 'bg-amber/10' : ($entry->type === 'photo' ? 'bg-blue-500/10' : ($entry->type === 'video' ? 'bg-purple-500/10' : 'bg-ink-70/15')) }}">
                                @if($entry->type === 'voice')
                                    <i class="fa-solid fa-microphone text-amber text-[10px]"></i>
                                @elseif($entry->type === 'photo')
                                    <i class="fa-solid fa-camera text-blue-400 text-[10px]"></i>
                                @elseif($entry->type === 'video')
                                    <i class="fa-solid fa-video text-purple-400 text-[10px]"></i>
                                @else
                                    <i class="fa-solid fa-pen text-ink-50 text-[10px]"></i>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-heading font-semibold text-paper text-sm truncate">{{ $entry->title ?? 'Zonder titel' }}</div>
                                <div class="text-[11px] text-ink-50 mt-0.5 flex items-center gap-1.5">
                                    <span>{{ $entry->created_at->diffForHumans() }}</span>
                                    @if($entry->client)
                                        <span class="text-ink-70">&middot;</span>
                                        <span>{{ $entry->client->name }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                @if($entry->status === 'processing')
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber animate-pulse"></span>
                                @elseif($entry->status === 'draft')
                                    <span class="text-[9px] font-heading font-semibold uppercase tracking-wider text-ink-50 bg-ink-70/15 px-1.5 py-0.5 rounded-full">Concept</span>
                                @elseif($entry->status === 'final')
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                @endif
                                @if($entry->total_amount)
                                    <span class="font-mono text-xs font-bold text-paper/50">&euro;{{ number_format($entry->total_amount, 0, ',', '.') }}</span>
                                @endif
                                <i class="fa-solid fa-chevron-right text-ink-70 text-[9px] group-hover:text-amber transition"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="mt-3 text-center">
                    <a href="{{ route('werkbonnen.index') }}" wire:navigate class="text-xs text-ink-50 hover:text-amber transition font-heading">
                        Alle werkbonnen bekijken &rarr;
                    </a>
                </div>
            </div>
        @endif
    @elseif($type === 'photo')
        <livewire:photo-upload />
    @elseif($type === 'video')
        <livewire:video-upload />
    @else
        <div class="max-w-xl mx-auto px-4 pt-8 pb-12 lg:pt-12 lg:pb-16">
            <div class="flex items-center gap-4 mb-8 pt-2 lg:pt-0">
                <a href="{{ route('invoeren.index') }}" wire:navigate class="w-9 h-9 rounded-full bg-ink-90 border border-ink-70/20 flex items-center justify-center text-paper/50 hover:text-paper hover:border-amber/30 transition shrink-0">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                </a>
                <h1 class="font-heading font-bold text-paper text-lg">Handmatig invoeren</h1>
            </div>
            <livewire:manual-entry />
        </div>
    @endif
</x-app-layout>
