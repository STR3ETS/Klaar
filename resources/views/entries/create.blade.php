<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="text-ink-50 hover:text-ink transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
            </a>
            <h2 class="font-heading text-2xl font-bold text-ink">Nieuwe invoer</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Type tabs --}}
            <div class="flex gap-2 mb-8">
                <a href="{{ route('entries.create', ['type' => 'voice']) }}"
                   class="flex items-center gap-2 px-4 py-2.5 rounded-md text-sm font-medium transition
                       {{ $type === 'voice' ? 'bg-amber text-ink' : 'bg-snow text-ink-50 border border-ink-10 hover:border-ink-30' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                    </svg>
                    Inspreken
                </a>
                <a href="{{ route('entries.create', ['type' => 'photo']) }}"
                   class="flex items-center gap-2 px-4 py-2.5 rounded-md text-sm font-medium transition
                       {{ $type === 'photo' ? 'bg-amber text-ink' : 'bg-snow text-ink-50 border border-ink-10 hover:border-ink-30' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                    </svg>
                    Foto
                </a>
                <a href="{{ route('entries.create', ['type' => 'manual']) }}"
                   class="flex items-center gap-2 px-4 py-2.5 rounded-md text-sm font-medium transition
                       {{ $type === 'manual' ? 'bg-amber text-ink' : 'bg-snow text-ink-50 border border-ink-10 hover:border-ink-30' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                    </svg>
                    Handmatig
                </a>
            </div>

            {{-- Content per type --}}
            @if($type === 'voice')
                <livewire:voice-recorder />
            @elseif($type === 'photo')
                <livewire:photo-upload />
            @else
                <livewire:manual-entry />
            @endif

        </div>
    </div>
</x-app-layout>
