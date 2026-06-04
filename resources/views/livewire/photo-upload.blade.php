<div class="space-y-6">

    {{-- Upload area --}}
    @if(!$uploadComplete)
        <div
            x-data="{ dragging: false }"
            @dragover.prevent="dragging = true"
            @dragleave.prevent="dragging = false"
            @drop.prevent="dragging = false; $wire.upload('photos', $event.dataTransfer.files)"
            class="bg-snow rounded-lg border-2 border-dashed transition"
            :class="dragging ? 'border-amber bg-amber/5' : 'border-ink-10'"
        >
            <label class="flex flex-col items-center justify-center cursor-pointer p-10">
                <input
                    type="file"
                    wire:model="photos"
                    accept="image/*"
                    capture="environment"
                    multiple
                    class="sr-only"
                >
                <div class="w-16 h-16 bg-amber/15 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-amber" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                    </svg>
                </div>
                <p class="mt-4 text-sm font-medium text-ink">Maak een foto of kies een bestand</p>
                <p class="mt-1 text-xs text-ink-50">JPG, PNG of HEIC &middot; max 20MB per foto</p>
            </label>
        </div>

        {{-- Photo previews --}}
        @if(count($photos) > 0)
            <div class="space-y-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($photos as $index => $photo)
                        <div class="relative group rounded-lg overflow-hidden bg-ink-10 aspect-square">
                            <img
                                src="{{ $photo->temporaryUrl() }}"
                                alt="Preview"
                                class="w-full h-full object-cover"
                            >
                            <button
                                wire:click="removePhoto({{ $index }})"
                                class="absolute top-2 right-2 w-7 h-7 bg-ink/60 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-between">
                    <p class="text-sm text-ink-50">{{ count($photos) }} foto('s) geselecteerd</p>
                    <button
                        wire:click="uploadPhotos"
                        wire:loading.attr="disabled"
                        class="px-5 py-2.5 bg-amber text-ink text-sm font-semibold rounded-md hover:bg-amber/90 transition shadow-sm disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="uploadPhotos">Verwerken</span>
                        <span wire:loading wire:target="uploadPhotos" class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Uploaden...
                        </span>
                    </button>
                </div>
            </div>
        @endif
    @endif

    {{-- Upload complete --}}
    @if($uploadComplete && $entryId)
        <div class="flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="font-medium">Foto('s) worden verwerkt!</p>
                <p class="mt-1 text-green-600">De tekst op je foto wordt herkend en verwerkt door AI.</p>
                <a href="{{ route('entries.show', $entryId ?? 0) }}" class="mt-2 inline-flex items-center gap-1 text-sm font-medium text-green-700 hover:text-green-800">
                    Bekijk invoer
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
        </div>
    @endif

    {{-- Validation errors --}}
    @error('photos.*')
        <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
            </svg>
            <span>{{ $message }}</span>
        </div>
    @enderror

    {{-- Tips --}}
    <div class="bg-snow rounded-lg border border-ink-10/50 p-5">
        <h4 class="font-heading font-semibold text-ink text-sm">Tips voor foto's</h4>
        <ul class="mt-3 space-y-2 text-sm text-ink-50">
            <li class="flex items-start gap-2">
                <svg class="w-4 h-4 text-amber shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                Zorg voor voldoende licht en scherp beeld
            </li>
            <li class="flex items-start gap-2">
                <svg class="w-4 h-4 text-amber shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                Leg de hele bon of materiaallijst vast
            </li>
            <li class="flex items-start gap-2">
                <svg class="w-4 h-4 text-amber shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                Meerdere foto's per invoer zijn mogelijk
            </li>
        </ul>
    </div>
</div>
