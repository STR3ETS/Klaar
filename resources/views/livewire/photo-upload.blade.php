<div class="min-h-screen flex flex-col">

    {{-- ============ IDLE STATE — fullscreen hero ============ --}}
    @if(!$uploadComplete && count($photos) === 0)
        <div class="flex-1 flex flex-col items-center justify-center px-6 -mt-16">

            {{-- Upload button --}}
            <div class="relative">
                {{-- Pulsing rings --}}
                <div class="absolute inset-0 w-32 h-32 rounded-full border-2 border-blue-400/15 animate-ping" style="animation-duration: 3s;"></div>
                <div class="absolute -inset-4 w-40 h-40 rounded-full border border-blue-400/8 animate-ping" style="animation-duration: 4s; animation-delay: 0.5s;"></div>

                <label class="relative z-10 w-32 h-32 bg-blue-500 rounded-full flex items-center justify-center shadow-[0_0_40px_rgba(59,130,246,0.35)] hover:shadow-[0_0_60px_rgba(59,130,246,0.5)] hover:scale-105 active:scale-95 transition-all duration-200 cursor-pointer">
                    <input
                        type="file"
                        wire:model="photos"
                        accept="image/*"
                        capture="environment"
                        multiple
                        class="sr-only"
                    >
                    <i class="fa-solid fa-camera text-white text-4xl"></i>
                </label>
            </div>

            {{-- Instruction text --}}
            <p class="mt-8 text-sm text-ink-30 font-heading">Tik om foto te kiezen</p>
            <p class="mt-1 text-[11px] text-ink-50">JPG, PNG of HEIC &middot; max 20MB per foto</p>

            {{-- Quick tips --}}
            <div class="mt-12 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-[11px] text-ink-50">
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-sun text-blue-400/40 text-[9px]"></i> Goed licht</span>
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-image text-blue-400/40 text-[9px]"></i> Scherp beeld</span>
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-images text-blue-400/40 text-[9px]"></i> Meerdere foto's</span>
            </div>

            {{-- Alt methods --}}
            <div class="mt-16 flex items-center gap-4">
                <a href="{{ route('invoeren.index') }}" wire:navigate
                   class="flex items-center gap-2 px-4 py-2.5 rounded-full bg-ink-90/80 border border-ink-70/20 text-ink-50 text-xs font-heading hover:text-paper hover:border-paper/20 transition">
                    <i class="fa-solid fa-microphone text-[10px]"></i> Spraak
                </a>
                <a href="{{ route('invoeren.index', ['type' => 'video']) }}" wire:navigate
                   class="flex items-center gap-2 px-4 py-2.5 rounded-full bg-ink-90/80 border border-ink-70/20 text-ink-50 text-xs font-heading hover:text-paper hover:border-paper/20 transition">
                    <i class="fa-solid fa-video text-[10px]"></i> Video
                </a>
                <a href="{{ route('invoeren.index', ['type' => 'manual']) }}" wire:navigate
                   class="flex items-center gap-2 px-4 py-2.5 rounded-full bg-ink-90/80 border border-ink-70/20 text-ink-50 text-xs font-heading hover:text-paper hover:border-paper/20 transition">
                    <i class="fa-solid fa-pen text-[10px]"></i> Handmatig
                </a>
            </div>
        </div>
    @endif

    {{-- ============ PREVIEW STATE — photos selected ============ --}}
    @if(!$uploadComplete && count($photos) > 0)
        <div class="flex-1 flex flex-col items-center justify-center px-6 -mt-8">

            <div class="w-full max-w-md space-y-5">
                {{-- Photo grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($photos as $index => $photo)
                        <div class="relative group rounded-sm overflow-hidden bg-ink-90 aspect-square border border-ink-70/20">
                            <img
                                src="{{ $photo->temporaryUrl() }}"
                                alt="Preview"
                                class="w-full h-full object-cover"
                            >
                            <button
                                wire:click="removePhoto({{ $index }})"
                                class="absolute top-2 right-2 w-7 h-7 bg-ink/80 text-paper rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition"
                            >
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>
                        </div>
                    @endforeach
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between">
                    <p class="text-sm text-ink-50">
                        <i class="fa-solid fa-images text-blue-400 text-xs mr-1.5"></i>
                        {{ count($photos) }} foto('s)
                    </p>
                    <div class="flex items-center gap-3">
                        <label class="text-xs text-ink-50 font-heading cursor-pointer hover:text-paper transition">
                            <input type="file" wire:model="photos" accept="image/*" multiple class="sr-only">
                            <i class="fa-solid fa-plus text-[9px] mr-1"></i> Meer
                        </label>
                        <button
                            wire:click="uploadPhotos"
                            wire:loading.attr="disabled"
                            class="border border-amber bg-amber px-5 py-2.5 text-ink font-semibold text-sm font-heading rounded-sm transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)] disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="uploadPhotos">
                                <i class="fa-solid fa-wand-magic-sparkles text-xs mr-1.5"></i> Verwerken
                            </span>
                            <span wire:loading wire:target="uploadPhotos" class="flex items-center gap-2">
                                <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                                Uploaden...
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Back link --}}
            <div class="mt-12">
                <a href="{{ route('invoeren.index', ['type' => 'photo']) }}" wire:navigate class="text-xs text-ink-50 hover:text-paper transition font-heading">
                    <i class="fa-solid fa-arrow-left text-[9px] mr-1"></i> Opnieuw kiezen
                </a>
            </div>
        </div>
    @endif

    {{-- ============ PROCESSING STATE — centered ============ --}}
    @if($uploadComplete && $entryId)
        <div class="flex-1 flex flex-col items-center justify-center px-6 -mt-16">
            <div class="w-full max-w-md">

                <div class="bg-ink rounded-sm border border-ink-70/20 p-6 relative overflow-hidden" style="background-image: radial-gradient(circle, rgba(255,180,0,0.05) 1px, transparent 1px); background-size: 20px 20px;">

                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 rounded-md bg-amber/10 flex items-center justify-center">
                            <i class="fa-solid fa-wand-magic-sparkles text-amber text-sm"></i>
                        </div>
                        <div>
                            <h4 class="font-heading font-semibold text-paper text-sm">Foto('s) worden verwerkt</h4>
                            <p class="text-xs text-ink-50">Tekst wordt herkend en verwerkt door AI.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-1 text-center">
                            <div class="w-10 h-10 rounded-full bg-amber/20 flex items-center justify-center mx-auto">
                                <i class="fa-solid fa-camera text-amber text-sm"></i>
                            </div>
                            <p class="mt-2 text-[10px] font-heading font-semibold uppercase tracking-wider text-amber">Foto</p>
                            <p class="text-[10px] text-ink-50">Ge&uuml;pload</p>
                        </div>

                        <div class="flex-shrink-0 w-12 h-px bg-amber/30 mt-5"></div>

                        <div class="flex-1 text-center">
                            <div class="w-10 h-10 rounded-full bg-amber/10 flex items-center justify-center mx-auto">
                                <i class="fa-solid fa-spinner fa-spin text-amber text-sm"></i>
                            </div>
                            <p class="mt-2 text-[10px] font-heading font-semibold uppercase tracking-wider text-amber">Scannen</p>
                            <p class="text-[10px] text-ink-50">Tekst herkennen...</p>
                        </div>

                        <div class="flex-shrink-0 w-12 h-px bg-ink-70/30 mt-5"></div>

                        <div class="flex-1 text-center">
                            <div class="w-10 h-10 rounded-full border-2 border-ink-70/30 flex items-center justify-center mx-auto">
                                <i class="fa-solid fa-clipboard-list text-ink-50 text-sm"></i>
                            </div>
                            <p class="mt-2 text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50">Werkbon</p>
                            <p class="text-[10px] text-ink-70">Wachten...</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-ink-70/20 flex items-center justify-between">
                        <p class="text-xs text-ink-50">Je kunt deze pagina veilig verlaten.</p>
                        <a href="{{ route('werkbonnen.show', $entryId ?? 0) }}" class="inline-flex items-center gap-2 border border-amber bg-amber px-4 py-2 text-ink font-semibold text-xs font-heading rounded-sm transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)]">
                            Bekijk invoer
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Validation errors --}}
    @error('photos.*')
        <div class="fixed bottom-20 left-4 right-4 max-w-md mx-auto flex items-start gap-3 p-4 bg-red-500/10 border border-red-500/30 rounded-sm text-sm text-red-400 z-50">
            <i class="fa-solid fa-triangle-exclamation shrink-0 mt-0.5"></i>
            <span>{{ $message }}</span>
        </div>
    @enderror
</div>
