<div class="min-h-screen flex flex-col"
     @if($uploadComplete && !$processingDone && !$processingFailed)
         wire:poll.2s="pollStatus"
     @endif
>

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

            <div class="w-full max-w-lg">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-purple-500/15 rounded-full flex items-center justify-center mx-auto">
                        <i class="fa-solid fa-film text-purple-400 text-2xl"></i>
                    </div>
                    <h3 class="mt-3 font-heading font-semibold text-paper text-base">Video geselecteerd</h3>
                    <p class="text-xs text-ink-50 mt-1">Klaar om te verwerken.</p>
                </div>

                <div class="flex items-center gap-3 p-4 rounded-sm border border-ink-70/20 bg-ink-90/50 mb-6">
                    <div class="w-10 h-10 rounded-full bg-purple-500/10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-video text-purple-400 text-sm"></i>
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
                        class="w-8 h-8 bg-ink/80 text-paper rounded-full flex items-center justify-center hover:bg-red-500/20 hover:text-red-400 transition cursor-pointer"
                    >
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                </div>

                <div class="flex items-center gap-3">
                    <button wire:click="removeVideo"
                            class="flex-1 border border-paper/20 px-5 py-2.5 text-paper font-semibold text-sm font-heading rounded-sm transition hover:border-paper/40 hover:bg-paper/10 cursor-pointer">
                        <i class="fa-solid fa-arrow-left text-xs mr-1.5"></i> Opnieuw
                    </button>
                    <button
                        wire:click="uploadVideo"
                        wire:loading.attr="disabled"
                        class="flex-1 border border-purple-500 bg-purple-500 px-5 py-2.5 text-white font-semibold text-sm font-heading rounded-sm transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(168,85,247,0.3)] disabled:opacity-50 cursor-pointer"
                    >
                        <span wire:loading.remove wire:target="uploadVideo">
                            <i class="fa-solid fa-wand-magic-sparkles text-xs mr-1.5"></i> Verwerken
                        </span>
                        <span wire:loading wire:target="uploadVideo" class="flex items-center justify-center gap-2">
                            <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                            Uploaden...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ============ PROCESSING STATE — live steps ============ --}}
    @if($uploadComplete && !$processingDone && !$processingFailed)
        <div class="flex-1 flex flex-col items-center justify-center px-6 -mt-16">

            {{-- Animated spinner --}}
            <div class="w-20 h-20 bg-purple-500/10 rounded-full flex items-center justify-center mb-6">
                <i class="fa-solid fa-wand-magic-sparkles text-purple-400 text-2xl animate-pulse"></i>
            </div>

            <p class="text-sm text-paper font-heading font-semibold mb-1">
                @switch($processingStep)
                    @case('uploading')
                        Video uploaden...
                        @break
                    @case('extracting_audio')
                        Audio extraheren...
                        @break
                    @case('transcribing')
                        Spraak transcriberen...
                        @break
                    @case('extracting_frames')
                        Beelden extraheren...
                        @break
                    @case('analyzing_frames')
                        Beelden analyseren met AI...
                        @break
                    @case('generating_entry')
                        Werkbon genereren...
                        @break
                    @default
                        Verwerken...
                @endswitch
            </p>
            <p class="text-xs text-ink-50 mb-10">Even geduld, dit kan even duren.</p>

            {{-- Step indicators --}}
            <div class="flex items-center gap-2 w-full max-w-sm">
                @php
                    $steps = [
                        'extracting_audio' => ['icon' => 'fa-volume-high', 'label' => 'Audio'],
                        'transcribing' => ['icon' => 'fa-keyboard', 'label' => 'Transcriptie'],
                        'extracting_frames' => ['icon' => 'fa-images', 'label' => 'Beelden'],
                        'analyzing_frames' => ['icon' => 'fa-eye', 'label' => 'Analyse'],
                        'generating_entry' => ['icon' => 'fa-clipboard-list', 'label' => 'Werkbon'],
                    ];
                    $stepKeys = array_keys($steps);
                    $currentIndex = array_search($processingStep, $stepKeys);
                    if ($currentIndex === false) $currentIndex = -1;
                @endphp

                @foreach($steps as $key => $step)
                    @php
                        $index = array_search($key, $stepKeys);
                        $isComplete = $index < $currentIndex;
                        $isActive = $key === $processingStep;
                        $isPending = $index > $currentIndex;
                    @endphp

                    <div class="flex-1 text-center">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center mx-auto transition-all duration-300
                            {{ $isComplete ? 'bg-purple-500/20' : ($isActive ? 'bg-purple-500/15 ring-2 ring-purple-500/40' : 'bg-ink-90 border border-ink-70/30') }}">
                            @if($isComplete)
                                <i class="fa-solid fa-check text-purple-400 text-[10px]"></i>
                            @elseif($isActive)
                                <i class="fa-solid fa-spinner fa-spin text-purple-400 text-[10px]"></i>
                            @else
                                <i class="fa-solid {{ $step['icon'] }} text-ink-50 text-[10px]"></i>
                            @endif
                        </div>
                        <p class="mt-1.5 text-[9px] font-heading font-semibold uppercase tracking-wider
                            {{ $isComplete ? 'text-purple-400' : ($isActive ? 'text-purple-400' : 'text-ink-50') }}">
                            {{ $step['label'] }}
                        </p>
                    </div>

                    @if(!$loop->last)
                        <div class="flex-shrink-0 w-4 h-px mt-[-14px] transition-all duration-300
                            {{ $isComplete ? 'bg-purple-500/40' : 'bg-ink-70/30' }}"></div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    {{-- ============ DONE STATE — werkbon(nen) klaar ============ --}}
    @if($processingDone)
        <div class="flex-1 flex flex-col items-center justify-center px-6 -mt-16">

            <div class="w-full max-w-lg">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-green-500/15 rounded-full flex items-center justify-center mx-auto">
                        <i class="fa-solid fa-check text-green-400 text-2xl"></i>
                    </div>
                    <p class="mt-3 text-sm text-paper font-heading font-semibold">
                        @if(count($processedEntries) === 1)
                            Werkbon aangemaakt!
                        @else
                            {{ count($processedEntries) }} werkbonnen aangemaakt!
                        @endif
                    </p>
                </div>

                {{-- Entry cards --}}
                <div class="space-y-2 mb-6">
                    @foreach($processedEntries as $entry)
                        <a href="{{ route('werkbonnen.show', $entry['id']) }}"
                           class="flex items-center gap-3 p-3 rounded-sm border border-ink-70/20 bg-ink-90/50 hover:border-purple-500/30 hover:bg-purple-500/5 transition group">
                            <div class="w-8 h-8 rounded-full bg-purple-500/20 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-check text-purple-400 text-[10px]"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-paper font-medium truncate">{{ $entry['title'] }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    @if($entry['total'])
                                        <p class="text-[10px] text-ink-50">&euro;{{ $entry['total'] }} excl. BTW</p>
                                    @endif
                                    @if($entry['client'])
                                        <span class="text-ink-70">&middot;</span>
                                        <div class="flex items-center gap-1">
                                            <i class="fa-solid fa-user text-[8px] text-ink-50"></i>
                                            <span class="text-[10px] text-ink-50">{{ $entry['client']['name'] }}</span>
                                            @if($entry['client']['is_new'])
                                                <span class="text-[8px] font-heading font-bold uppercase tracking-wider bg-purple-500/20 text-purple-400 px-1.5 py-0.5 rounded-full">Nieuw</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <i class="fa-solid fa-arrow-right text-purple-400 text-[10px] opacity-0 group-hover:opacity-100 transition shrink-0"></i>
                        </a>
                    @endforeach
                </div>

                <div class="flex items-center justify-between">
                    <button wire:click="resetUpload" class="text-xs text-ink-50 hover:text-paper transition cursor-pointer">
                        <i class="fa-solid fa-plus text-[10px] mr-1"></i> Nieuwe video
                    </button>
                    @if(count($processedEntries) === 1)
                        <a href="{{ route('werkbonnen.show', $processedEntries[0]['id']) }}"
                           class="inline-flex items-center gap-2 border border-purple-500 bg-purple-500 px-4 py-2 text-white font-semibold text-xs font-heading rounded-sm transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(168,85,247,0.3)]">
                            Bekijk werkbon
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    @else
                        <a href="{{ route('werkbonnen.index') }}"
                           class="inline-flex items-center gap-2 border border-purple-500 bg-purple-500 px-4 py-2 text-white font-semibold text-xs font-heading rounded-sm transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(168,85,247,0.3)]">
                            Bekijk alle werkbonnen
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ============ FAILED STATE ============ --}}
    @if($processingFailed)
        <div class="flex-1 flex flex-col items-center justify-center px-6 -mt-16">

            <div class="w-full max-w-lg">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-red-500/15 rounded-full flex items-center justify-center mx-auto">
                        <i class="fa-solid fa-triangle-exclamation text-red-400 text-2xl"></i>
                    </div>
                    <p class="mt-3 text-sm text-paper font-heading font-semibold">Verwerking mislukt</p>
                    <p class="text-xs text-ink-50 mt-1">De video kon niet verwerkt worden. Je kunt het opnieuw proberen.</p>
                </div>

                @if($entryId)
                    <a href="{{ route('werkbonnen.show', $entryId) }}"
                       class="flex items-center gap-3 p-4 rounded-sm border border-ink-70/20 bg-ink-90/50 hover:border-amber/30 hover:bg-amber/5 transition group mb-6">
                        <div class="w-10 h-10 rounded-full bg-amber/20 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-pen text-amber text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-paper font-heading font-semibold">Handmatig aanvullen</p>
                            <p class="text-[10px] text-ink-50 mt-0.5">De werkbon is opgeslagen als concept.</p>
                        </div>
                        <i class="fa-solid fa-arrow-right text-amber text-[10px] opacity-0 group-hover:opacity-100 transition shrink-0"></i>
                    </a>
                @endif

                <div class="flex items-center justify-center">
                    <button wire:click="resetUpload"
                            class="inline-flex items-center gap-2 border border-purple-500 bg-purple-500 px-4 py-2 text-white font-semibold text-xs font-heading rounded-sm transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(168,85,247,0.3)] cursor-pointer">
                        <i class="fa-solid fa-rotate-left text-[10px]"></i> Opnieuw proberen
                    </button>
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
