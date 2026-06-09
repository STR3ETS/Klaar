<x-app-layout>
    @if($type === 'voice')
        <livewire:voice-recorder />
    @elseif($type === 'photo')
        <div class="max-w-xl mx-auto px-4 pt-8 pb-12 lg:pt-12 lg:pb-16">
            <div class="flex items-center gap-4 mb-8 pt-2 lg:pt-0">
                <a href="{{ route('entries.create') }}" wire:navigate class="w-9 h-9 rounded-full bg-ink-90 border border-ink-70/20 flex items-center justify-center text-paper/50 hover:text-paper hover:border-amber/30 transition shrink-0">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                </a>
                <h1 class="font-heading font-bold text-paper text-lg">Foto uploaden</h1>
            </div>
            <livewire:photo-upload />
        </div>
    @else
        <div class="max-w-xl mx-auto px-4 pt-8 pb-12 lg:pt-12 lg:pb-16">
            <div class="flex items-center gap-4 mb-8 pt-2 lg:pt-0">
                <a href="{{ route('entries.create') }}" wire:navigate class="w-9 h-9 rounded-full bg-ink-90 border border-ink-70/20 flex items-center justify-center text-paper/50 hover:text-paper hover:border-amber/30 transition shrink-0">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                </a>
                <h1 class="font-heading font-bold text-paper text-lg">Handmatig invoeren</h1>
            </div>
            <livewire:manual-entry />
        </div>
    @endif
</x-app-layout>
