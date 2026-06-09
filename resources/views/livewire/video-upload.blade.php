<div class="min-h-screen flex flex-col">

    {{-- ============ IDLE STATE — fullscreen hero ============ --}}
    @if(!$uploadComplete && !$video)
        <div class="flex-1 flex flex-col items-center justify-center px-6 -mt-16">

            {{-- Upload button --}}
            <div class="relative">
                {{-- Pulsing rings --}}
                <div class="absolute inset-0 w-32 h-32 rounded-full border-2 border-purple-400/15 animate-ping" style="animation-duration: 3s;"></div>
                <div class="absolute -inset-4 w-40 h-40 rounded-full border border-purple-400/8 animate-ping" style="animation-duration: 4s; animation-delay: 0.5s;"></div>

                <label class="relative z-10 w-32 h-32 bg-purple-500 rounded-full flex items-center justify-center shadow-[0_0_40px_rgba(168,85,247,0.35)] hover:shadow-[0_0_60px_rgba(168,85,247,0.5)] hover:scale-105 active:scale-95 transition-all duration-200 cursor-pointer">
                    <input
                        type="file"
                        wire:model="video"
                        accept="video/mp4,video/webm,video/quicktime,video/x-matroska"
                        class="sr-only"
                    >
                    <i class="fa-solid fa-video text-white text-4xl"></i>
                </label>
            </div>

            {{-- Instruction text --}}
            <p class="mt-8 text-sm text-ink-30 font-heading">Tik om video te kiezen</p>
            <p class="mt-1 text-[11px] text-ink-50">MP4, WebM, MOV of MKV &middot; max 200MB</p>

            {{-- Quick tips --}}
            <div class="mt-12 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-[11px] text-ink-50">
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-microphone text-purple-400/40 text-[9px]"></i> Duidelijk inspreken</span>
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-eye text-purple-400/40 text-[9px]"></i> Ruimte filmen</span>
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-ruler text-purple-400/40 text-[9px]"></i> Materialen tonen</span>
            </div>

            {{-- Alt methods --}}
            <div class="mt-16 flex items-center gap-4">
                <a href="{{ route('invoeren.index') }}" wire:navigate
                   class="flex items-center gap-2 px-4 py-2.5 rounded-full bg-ink-90/80 border border-ink-70/20 text-ink-50 text-xs font-heading hover:text-paper hover:border-paper/20 transition">
                    <i class="fa-solid fa-microphone text-[10px]"></i> Spraak
                </a>
                <a href="{{ route('invoeren.index', ['type' => 'photo']) }}" wire:navigate
                   class="flex items-center gap-2 px-4 py-2.5 rounded-full bg-ink-90/80 border border-ink-70/20 text-ink-50 text-xs font-heading hover:text-paper hover:border-paper/20 transition">
                    <i class="fa-solid fa-camera text-[10px]"></i> Foto
                </a>
                <a href="{{ route('invoeren.index', ['type' => 'manual']) }}" wire:navigate
                   class="flex items-center gap-2 px-4 py-2.5 rounded-full bg-ink-90/80 border border-ink-70/20 text-ink-50 text-xs font-heading hover:text-paper hover:border-paper/20 transition">
                    <i class="fa-solid fa-pen text-[10px]"></i> Handmatig
                </a>
            </div>
        </div>
    @endif

    {{-- ============ PREVIEW STATE — video selected ============ --}}
    @if(!$uploadComplete && $video)
        <div class="flex-1 flex flex-col items-center justify-center px-6 -mt-8">

            <div class="w-full max-w-md">
                <div class="bg-ink-90 rounded-sm border border-ink-70/20 p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-purple-500/10 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-film text-purple-400 text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-heading font-semibold text-paper truncate">{{ $video->getClientOriginalName() }}</p>
                            <p class="text-[10px] text-ink-50 font-heading uppercase tracking-wider">
                                {{ strtoupper(pathinfo($video->getClientOriginalName(), PATHINFO_EXTENSION)) }}
                                &middot;
                                {{ number_format($video->getSize() / 1024 / 1024, 1) }} MB
                            </p>
                        </div>
                        <button
                            wire:click="removeVideo"
                            class="w-8 h-8 bg-ink/80 text-paper rounded-full flex items-center justify-center hover:bg-red-500/20 hover:text-red-400 transition"
                        >
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>

                    <div class="mt-4 flex items-center justify-end">
                        <button
                            wire:click="uploadVideo"
                            wire:loading.attr="disabled"
                            class="border border-amber bg-amber px-5 py-2.5 text-ink font-semibold text-sm font-heading rounded-sm transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)] disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="uploadVideo">
                                <i class="fa-solid fa-wand-magic-sparkles text-xs mr-1.5"></i> Verwerken
                            </span>
                            <span wire:loading wire:target="uploadVideo" class="flex items-center gap-2">
                                <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                                Uploaden...
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Back link --}}
            <div class="mt-8">
                <a href="{{ route('invoeren.index', ['type' => 'video']) }}" wire:navigate class="text-xs text-ink-50 hover:text-paper transition font-heading">
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
                            <h4 class="font-heading font-semibold text-paper text-sm">Video wordt verwerkt</h4>
                            <p class="text-xs text-ink-50">Audio en beelden worden geanalyseerd door AI.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        {{-- Step 1: Upload --}}
                        <div class="flex-1 text-center">
                            <div class="w-10 h-10 rounded-full bg-amber/20 flex items-center justify-center mx-auto">
                                <i class="fa-solid fa-video text-amber text-sm"></i>
                            </div>
                            <p class="mt-2 text-[10px] font-heading font-semibold uppercase tracking-wider text-amber">Video</p>
                            <p class="text-[10px] text-ink-50">Ge&uuml;pload</p>
                        </div>

                        <div class="flex-shrink-0 w-8 h-px bg-amber/30 mt-5"></div>

                        {{-- Step 2: Audio --}}
                        <div class="flex-1 text-center">
                            <div class="w-10 h-10 rounded-full bg-amber/10 flex items-center justify-center mx-auto">
                                <i class="fa-solid fa-spinner fa-spin text-amber text-sm"></i>
                            </div>
                            <p class="mt-2 text-[10px] font-heading font-semibold uppercase tracking-wider text-amber">Audio</p>
                            <p class="text-[10px] text-ink-50">Transcriberen...</p>
                        </div>

                        <div class="flex-shrink-0 w-8 h-px bg-ink-70/30 mt-5"></div>

                        {{-- Step 3: Beelden --}}
                        <div class="flex-1 text-center">
                            <div class="w-10 h-10 rounded-full border-2 border-ink-70/30 flex items-center justify-center mx-auto">
                                <i class="fa-solid fa-images text-ink-50 text-sm"></i>
                            </div>
                            <p class="mt-2 text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50">Beelden</p>
                            <p class="text-[10px] text-ink-70">Wachten...</p>
                        </div>

                        <div class="flex-shrink-0 w-8 h-px bg-ink-70/30 mt-5"></div>

                        {{-- Step 4: Werkbon --}}
                        <div class="flex-1 text-center">
                            <div class="w-10 h-10 rounded-full border-2 border-ink-70/30 flex items-center justify-center mx-auto">
                                <i class="fa-solid fa-clipboard-list text-ink-50 text-sm"></i>
                            </div>
                            <p class="mt-2 text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50">Werkbon</p>
                            <p class="text-[10px] text-ink-70">Wachten...</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-ink-70/20 flex items-center justify-between">
                        <p class="text-xs text-ink-50">Video verwerken kan even duren.</p>
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
    @error('video')
        <div class="fixed bottom-20 left-4 right-4 max-w-md mx-auto flex items-start gap-3 p-4 bg-red-500/10 border border-red-500/30 rounded-sm text-sm text-red-400 z-50">
            <i class="fa-solid fa-triangle-exclamation shrink-0 mt-0.5"></i>
            <span>{{ $message }}</span>
        </div>
    @enderror
</div>
