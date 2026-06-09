<div
    x-data="voiceRecorder"
    class="min-h-screen flex flex-col"
>
    {{-- ============ IDLE STATE — fullscreen hero ============ --}}
    <template x-if="state === 'idle'">
        <div class="flex-1 flex flex-col items-center justify-center px-6 -mt-16">

            {{-- Mic button --}}
            <div class="relative">
                {{-- Pulsing rings --}}
                <div class="absolute inset-0 w-32 h-32 rounded-full border-2 border-amber/15 animate-ping" style="animation-duration: 3s;"></div>
                <div class="absolute -inset-4 w-40 h-40 rounded-full border border-amber/8 animate-ping" style="animation-duration: 4s; animation-delay: 0.5s;"></div>

                <button
                    @click="startRecording"
                    @touchstart.prevent="handleTouchStart($event)"
                    @touchend.prevent="handleTouchEnd($event)"
                    class="relative z-10 w-32 h-32 bg-amber rounded-full flex items-center justify-center shadow-[0_0_40px_rgba(255,180,0,0.35)] hover:shadow-[0_0_60px_rgba(255,180,0,0.5)] hover:scale-105 active:scale-95 transition-all duration-200 cursor-pointer"
                >
                    <i class="fa-solid fa-microphone text-ink text-4xl"></i>
                </button>
            </div>

            {{-- Instruction text --}}
            <p class="mt-8 text-sm text-ink-30 font-heading">
                <span class="hidden lg:inline">Klik om op te nemen</span>
                <span class="lg:hidden">Houd ingedrukt om op te nemen</span>
            </p>
            <p class="mt-1 text-[11px] text-ink-50">Stopt automatisch bij stilte</p>

            {{-- Quick tips --}}
            <div class="mt-12 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-[11px] text-ink-50">
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-clipboard-list text-amber/40 text-[9px]"></i> Werkbon maken</span>
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-user-plus text-amber/40 text-[9px]"></i> Klant aanmaken</span>
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-folder-plus text-amber/40 text-[9px]"></i> Project starten</span>
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-file-invoice text-amber/40 text-[9px]"></i> Factureren</span>
            </div>

            {{-- Alt methods --}}
            <div class="mt-16 flex items-center gap-4">
                <a href="{{ route('invoeren.index', ['type' => 'photo']) }}" wire:navigate
                   class="flex items-center gap-2 px-4 py-2.5 rounded-full bg-ink-90/80 border border-ink-70/20 text-ink-50 text-xs font-heading hover:text-paper hover:border-paper/20 transition">
                    <i class="fa-solid fa-camera text-[10px]"></i> Foto
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

            {{-- Fallback notice --}}
            <template x-if="!speechSupported">
                <p class="mt-6 text-xs text-ink-50 max-w-sm text-center">
                    <i class="fa-solid fa-info-circle text-amber/50 mr-1"></i>
                    Live transcriptie niet beschikbaar in je browser. De opname wordt automatisch verwerkt via onze server.
                </p>
            </template>
        </div>
    </template>

    {{-- ============ RECORDING STATE — centered ============ --}}
    <template x-if="state === 'recording'">
        <div class="flex-1 flex flex-col items-center justify-center px-6 -mt-16">

            {{-- Status --}}
            <div class="flex items-center gap-2 mb-8">
                <div class="w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse"></div>
                <span class="text-[10px] font-heading font-semibold uppercase tracking-widest text-red-400">Opnemen</span>
                <span class="font-mono text-sm font-medium text-paper ml-2 tabular-nums" x-text="formattedTime"></span>
            </div>

            {{-- Waveform --}}
            <div class="flex items-center gap-0.5 h-16 mb-6">
                <template x-for="(bar, i) in bars" :key="i">
                    <div class="w-1 rounded-full bg-amber transition-all duration-100"
                         :style="`height: ${Math.max(3, bar * 0.9)}px`"></div>
                </template>
            </div>

            {{-- Live transcript --}}
            <div class="w-full max-w-lg min-h-[80px] max-h-[160px] overflow-y-auto mb-8 text-center">
                <p class="text-sm leading-relaxed" x-show="finalTranscript || interimTranscript">
                    <span class="text-paper" x-text="finalTranscript"></span><span class="text-ink-50 italic" x-text="interimTranscript"></span>
                </p>
                <p x-show="!finalTranscript && !interimTranscript" class="text-sm text-ink-50 italic">
                    Begin te spreken...
                </p>
            </div>

            {{-- Silence indicator --}}
            <div x-show="silenceSeconds > 0 && finalTranscript.trim().length > 0"
                 x-transition
                 class="w-full max-w-xs mb-8">
                <div class="h-1 bg-ink-90 rounded-full overflow-hidden">
                    <div class="h-full bg-amber/50 rounded-full transition-all duration-250"
                         :style="`width: ${Math.min(100, (silenceSeconds / silenceTimeout) * 100)}%`"></div>
                </div>
                <p class="text-[10px] text-ink-50 text-center mt-1.5">Stopt bij stilte</p>
            </div>

            {{-- Stop button --}}
            <button
                @click="stopRecording"
                class="w-20 h-20 bg-red-500 rounded-full flex items-center justify-center shadow-[0_0_30px_rgba(239,68,68,0.3)] hover:bg-red-600 active:scale-95 transition cursor-pointer"
            >
                <i class="fa-solid fa-stop text-white text-lg"></i>
            </button>
        </div>
    </template>

    {{-- ============ CLEANING STATE ============ --}}
    <template x-if="state === 'cleaning'">
        <div class="flex-1 flex flex-col items-center justify-center px-6 -mt-16">
            <div class="w-16 h-16 bg-amber/10 rounded-full flex items-center justify-center">
                <i class="fa-solid fa-wand-magic-sparkles text-amber text-lg animate-pulse"></i>
            </div>
            <p class="mt-4 text-sm text-ink-30">Tekst opschonen...</p>
        </div>
    </template>

    {{-- ============ CONFIRMING STATE ============ --}}
    <template x-if="state === 'confirming'">
        <div class="flex-1 flex flex-col items-center justify-center px-6 -mt-8">
            <div class="w-full max-w-lg">

                <div class="text-center mb-6">
                    <div class="w-12 h-12 bg-amber/10 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fa-solid fa-file-lines text-amber text-lg"></i>
                    </div>
                    <h3 class="font-heading font-semibold text-paper text-base">Klopt dit?</h3>
                    <p class="text-xs text-ink-50 mt-1">Pas de tekst aan of spreek een correctie in.</p>
                </div>

                <textarea
                    x-model="editableTranscript"
                    rows="5"
                    class="klaar-dark-input w-full min-h-[100px] resize-y text-sm leading-relaxed mb-4"
                    placeholder="Transcript verschijnt hier..."
                ></textarea>

                {{-- Voice correction --}}
                <div class="flex items-center gap-3 mb-6">
                    <button
                        @click="startVoiceCorrection"
                        x-show="!isCorreecting && !isApplyingCorrection"
                        class="inline-flex items-center gap-2 border border-ink-70/30 px-3 py-1.5 text-ink-30 text-xs font-heading rounded-sm transition hover:border-amber/50 hover:text-amber cursor-pointer"
                    >
                        <i class="fa-solid fa-microphone text-[10px]"></i>
                        Spreek correctie in
                    </button>
                    <div x-show="isCorreecting" class="flex items-center gap-3 flex-1">
                        <div class="flex items-center gap-2 shrink-0">
                            <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                            <span class="text-[10px] font-heading font-semibold uppercase tracking-widest text-red-400">Correctie</span>
                        </div>
                        <span class="text-xs text-ink-50 italic flex-1 truncate" x-text="correctionFinal + correctionInterim || 'Zeg wat er anders moet...'"></span>
                        <button
                            @click="stopVoiceCorrection"
                            class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center hover:bg-red-600 transition shrink-0 cursor-pointer"
                        >
                            <i class="fa-solid fa-stop text-white text-[10px]"></i>
                        </button>
                    </div>
                    <div x-show="isApplyingCorrection" class="flex items-center gap-2">
                        <i class="fa-solid fa-wand-magic-sparkles text-amber text-[10px] animate-pulse"></i>
                        <span class="text-xs text-ink-50">Correctie toepassen...</span>
                    </div>
                </div>

                <p class="text-[10px] text-ink-50 mb-6">
                    <i class="fa-solid fa-clock text-[8px] mr-1"></i>
                    Opnameduur: <span class="font-mono" x-text="formattedTime"></span>
                </p>

                <div class="flex items-center gap-3">
                    <button @click="resetRecording"
                            class="flex-1 border border-paper/20 px-5 py-2.5 text-paper font-semibold text-sm font-heading rounded-sm transition hover:border-paper/40 hover:bg-paper/10 cursor-pointer">
                        <i class="fa-solid fa-rotate-left text-xs mr-1.5"></i> Opnieuw
                    </button>
                    <button @click="confirmAndProcess"
                            class="flex-1 border border-amber bg-amber px-5 py-2.5 text-ink font-semibold text-sm font-heading rounded-sm transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)] cursor-pointer"
                            :disabled="!editableTranscript.trim() || isCorreecting || isApplyingCorrection">
                        <i class="fa-solid fa-check text-xs mr-1.5"></i> Ja, verwerken
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- ============ FALLBACK DONE STATE ============ --}}
    <template x-if="state === 'done'">
        <div class="flex-1 flex flex-col items-center justify-center px-6 -mt-16">
            <div class="w-16 h-16 bg-amber/10 rounded-full flex items-center justify-center">
                <i class="fa-solid fa-check text-amber text-2xl"></i>
            </div>
            <p class="mt-3 text-sm text-ink-30">
                Opname klaar &mdash; <span class="font-mono text-paper" x-text="formattedTime"></span>
            </p>
            <div class="flex items-center gap-4 mt-8">
                <button
                    @click="resetRecording"
                    class="border border-paper/20 px-5 py-2.5 text-paper font-semibold text-sm font-heading rounded-sm transition hover:border-paper/40 hover:bg-paper/10 cursor-pointer"
                >
                    Opnieuw
                </button>
                <button
                    @click="uploadFallback"
                    class="border border-amber bg-amber px-5 py-2.5 text-ink font-semibold text-sm font-heading rounded-sm transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)] cursor-pointer"
                    :disabled="$wire.isUploading"
                >
                    <span x-show="!$wire.isUploading">
                        <i class="fa-solid fa-wand-magic-sparkles text-xs mr-1.5"></i> Verwerken
                    </span>
                    <span x-show="$wire.isUploading" class="flex items-center gap-2">
                        <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                        Uploaden...
                    </span>
                </button>
            </div>
        </div>
    </template>

    {{-- ============ PROCESSING STATE ============ --}}
    <template x-if="state === 'processing'">
        <div class="flex-1 flex flex-col items-center justify-center px-6 -mt-16">

            {{-- Spinner --}}
            <div x-show="processedEntries.length === 0 && !smartResult" class="text-center">
                <div class="w-20 h-20 bg-amber/10 rounded-full flex items-center justify-center mx-auto">
                    <i class="fa-solid fa-wand-magic-sparkles text-amber text-2xl animate-pulse"></i>
                </div>
                <p class="mt-4 text-sm text-paper font-heading font-semibold" x-text="processingStatus"></p>
                <p class="mt-1 text-xs text-ink-50">Even geduld...</p>
            </div>

            {{-- Smart result (client/project/command/compound) --}}
            <div x-show="smartResult" x-transition class="w-full max-w-lg">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto"
                         :class="smartResult?.type === 'compound' ? 'bg-amber/15'
                                 : smartResult?.type === 'command' ? 'bg-amber/15'
                                 : 'bg-green-500/15'">
                        <i class="fa-solid text-2xl"
                           :class="smartResult?.type === 'compound' ? 'fa-wand-magic-sparkles text-amber'
                                   : smartResult?.type === 'command' ? 'fa-wand-magic-sparkles text-amber'
                                   : smartResult?.type === 'client' ? 'fa-user-check text-green-400'
                                   : 'fa-folder-plus text-green-400'"></i>
                    </div>
                    <p class="mt-3 text-sm text-paper font-heading font-semibold" x-text="processingStatus"></p>
                </div>

                {{-- Compound result (multiple actions) --}}
                <template x-if="smartResult?.type === 'compound'">
                    <div>
                        <div class="space-y-2 mb-6">
                            <template x-for="(step, idx) in smartResult.steps" :key="idx">
                                <div class="flex items-center gap-3 p-3 rounded-sm border border-ink-70/20 bg-ink-90/50"
                                     :class="step.link ? 'hover:border-amber/30 hover:bg-amber/5 transition cursor-pointer' : ''">
                                    <a :href="step.link || '#'" class="flex items-center gap-3 flex-1 min-w-0"
                                       :class="!step.link ? 'pointer-events-none' : ''">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"
                                             :class="step.type === 'entry_created' ? 'bg-green-500/20'
                                                     : step.type === 'finalize' ? 'bg-green-500/20'
                                                     : step.type === 'convert_to_invoice' ? 'bg-amber/20'
                                                     : step.type === 'delete' ? 'bg-red-500/20'
                                                     : step.type === 'reopen' ? 'bg-blue-500/20'
                                                     : step.type === 'client_created' ? 'bg-amber/20'
                                                     : step.type === 'project_created' ? 'bg-blue-500/20'
                                                     : 'bg-ink-70/20'">
                                            <i class="fa-solid text-[10px]"
                                               :class="step.type === 'entry_created' ? 'fa-clipboard-list text-green-400'
                                                       : step.type === 'finalize' ? 'fa-check text-green-400'
                                                       : step.type === 'convert_to_invoice' ? 'fa-file-invoice text-amber'
                                                       : step.type === 'delete' ? 'fa-trash text-red-400'
                                                       : step.type === 'reopen' ? 'fa-rotate-left text-blue-400'
                                                       : step.type === 'client_created' ? 'fa-user-check text-amber'
                                                       : step.type === 'project_created' ? 'fa-folder-plus text-blue-400'
                                                       : 'fa-check text-ink-50'"></i>
                                        </div>
                                        <p class="text-sm text-paper font-heading font-semibold truncate" x-text="step.text"></p>
                                    </a>
                                    <a x-show="step.link" :href="step.link"
                                       class="text-amber text-[10px] font-heading font-semibold hover:text-amber/80 transition shrink-0">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </template>
                        </div>

                        <div class="flex items-center justify-center">
                            <button @click="resetRecording" class="inline-flex items-center gap-2 border border-amber bg-amber px-4 py-2 text-ink font-semibold text-xs font-heading rounded-sm transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)] cursor-pointer">
                                <i class="fa-solid fa-plus text-[10px]"></i> Nieuwe opname
                            </button>
                        </div>
                    </div>
                </template>

                {{-- Command result --}}
                <template x-if="smartResult?.type === 'command'">
                    <div>
                        <template x-if="smartResult?.actions_taken?.length > 0">
                            <div class="space-y-2 mb-6">
                                <template x-for="action in smartResult.actions_taken" :key="action.type">
                                    <div class="flex items-center gap-3 p-4 rounded-sm border border-ink-70/20 bg-ink-90/50">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                                             :class="action.type === 'convert_to_invoice' ? 'bg-amber/20'
                                                     : action.type === 'finalize' ? 'bg-green-500/20'
                                                     : action.type === 'delete' ? 'bg-red-500/20'
                                                     : 'bg-blue-500/20'">
                                            <i class="fa-solid text-sm"
                                               :class="action.type === 'convert_to_invoice' ? 'fa-file-invoice text-amber'
                                                       : action.type === 'finalize' ? 'fa-check text-green-400'
                                                       : action.type === 'delete' ? 'fa-trash text-red-400'
                                                       : 'fa-rotate-left text-blue-400'"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-paper font-heading font-semibold"
                                               x-text="action.type === 'convert_to_invoice' ? action.count + ' factuur/facturen aangemaakt'
                                                       : action.type === 'finalize' ? action.count + ' werkbon(nen) definitief'
                                                       : action.type === 'delete' ? action.count + ' werkbon(nen) verwijderd'
                                                       : action.count + ' werkbon(nen) heropend'"></p>
                                        </div>
                                        <a x-show="action.redirect" :href="action.redirect"
                                           class="text-amber text-xs font-heading font-semibold hover:text-amber/80 transition shrink-0">
                                            Bekijken <i class="fa-solid fa-arrow-right text-[9px] ml-1"></i>
                                        </a>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <div class="flex items-center justify-center">
                            <button @click="resetRecording" class="inline-flex items-center gap-2 border border-amber bg-amber px-4 py-2 text-ink font-semibold text-xs font-heading rounded-sm transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)] cursor-pointer">
                                <i class="fa-solid fa-plus text-[10px]"></i> Nieuwe opname
                            </button>
                        </div>
                    </div>
                </template>

                {{-- Client/project result --}}
                <template x-if="smartResult?.type === 'client' || smartResult?.type === 'project'">
                    <div>
                        <a :href="smartResult?.redirect"
                           class="flex items-center gap-3 p-4 rounded-sm border border-ink-70/20 bg-ink-90/50 hover:border-amber/30 hover:bg-amber/5 transition group mb-6">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                                 :class="smartResult?.type === 'client' ? 'bg-amber/20' : 'bg-blue-500/20'">
                                <i class="fa-solid text-sm"
                                   :class="smartResult?.type === 'client' ? 'fa-user text-amber' : 'fa-folder-open text-blue-400'"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-paper font-heading font-semibold" x-text="smartResult?.data?.name"></p>
                                <p class="text-[10px] text-ink-50 mt-0.5" x-text="smartResult?.type === 'client' ? 'Nieuwe relatie' : 'Nieuw project'"></p>
                            </div>
                            <i class="fa-solid fa-arrow-right text-amber text-[10px] opacity-0 group-hover:opacity-100 transition shrink-0"></i>
                        </a>

                        <div class="flex items-center justify-between">
                            <button @click="resetRecording" class="text-xs text-ink-50 hover:text-paper transition cursor-pointer">
                                <i class="fa-solid fa-plus text-[10px] mr-1"></i> Nieuwe opname
                            </button>
                            <a :href="smartResult?.redirect"
                               class="inline-flex items-center gap-2 border border-amber bg-amber px-4 py-2 text-ink font-semibold text-xs font-heading rounded-sm transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)]">
                                <span x-text="smartResult?.type === 'client' ? 'Bekijk klant' : 'Bekijk project'"></span>
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Entry results --}}
            <div x-show="processedEntries.length > 0" x-transition class="w-full max-w-lg">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-green-500/15 rounded-full flex items-center justify-center mx-auto">
                        <i class="fa-solid fa-check text-green-400 text-2xl"></i>
                    </div>
                    <p class="mt-3 text-sm text-paper font-heading font-semibold" x-text="processingStatus"></p>
                </div>

                <div class="space-y-2 mb-6">
                    <template x-for="(entry, idx) in processedEntries" :key="entry.id">
                        <a :href="'/werkbonnen/' + entry.id"
                           class="flex items-center gap-3 p-3 rounded-sm border border-ink-70/20 bg-ink-90/50 hover:border-amber/30 hover:bg-amber/5 transition group">
                            <div class="w-8 h-8 rounded-full bg-amber/20 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-check text-amber text-[10px]"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-paper font-medium truncate" x-text="entry.title"></p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <p class="text-[10px] text-ink-50" x-text="'€' + (entry.total ? Number(entry.total).toFixed(2) : '0.00') + ' excl. BTW'"></p>
                                    <template x-if="entry.client">
                                        <div class="flex items-center gap-1">
                                            <span class="text-ink-70">&middot;</span>
                                            <i class="fa-solid fa-user text-[8px] text-ink-50"></i>
                                            <span class="text-[10px] text-ink-50" x-text="entry.client.name"></span>
                                            <template x-if="entry.client.is_new">
                                                <span class="text-[8px] font-heading font-bold uppercase tracking-wider bg-amber/20 text-amber px-1.5 py-0.5 rounded-full">Nieuw</span>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <i class="fa-solid fa-arrow-right text-amber text-[10px] opacity-0 group-hover:opacity-100 transition shrink-0"></i>
                        </a>
                    </template>
                </div>

                <div class="flex items-center justify-between">
                    <button @click="resetRecording" class="text-xs text-ink-50 hover:text-paper transition cursor-pointer">
                        <i class="fa-solid fa-plus text-[10px] mr-1"></i> Nieuwe opname
                    </button>
                    <a :href="processedEntries.length === 1 ? '/werkbonnen/' + processedEntries[0].id : '/werkbonnen'"
                       class="inline-flex items-center gap-2 border border-amber bg-amber px-4 py-2 text-ink font-semibold text-xs font-heading rounded-sm transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)]">
                        <span x-text="processedEntries.length === 1 ? 'Bekijk werkbon' : 'Bekijk alle werkbonnen'"></span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            {{-- Error --}}
            <div x-show="processingError" x-transition class="mt-4 w-full max-w-lg flex items-start gap-3 p-3 bg-amber/10 border border-amber/30 rounded-sm text-xs text-paper/80">
                <i class="fa-solid fa-info-circle text-amber shrink-0 mt-0.5"></i>
                <span x-text="processingError"></span>
            </div>
        </div>
    </template>

    {{-- Error toast (shown in any state) --}}
    <template x-if="error">
        <div class="fixed bottom-24 lg:bottom-8 left-1/2 -translate-x-1/2 z-50 max-w-md w-full mx-4">
            <div class="flex items-start gap-3 p-4 bg-red-500/15 border border-red-500/30 backdrop-blur-sm rounded-lg text-sm text-red-400 shadow-xl">
                <i class="fa-solid fa-triangle-exclamation shrink-0 mt-0.5"></i>
                <span x-text="error"></span>
            </div>
        </div>
    </template>
</div>

@script
<script>
Alpine.data('voiceRecorder', () => ({
    state: 'idle',

    // Touch hold for mobile
    touchTimer: null,
    isTouchRecording: false,

    // Media recording
    mediaRecorder: null,
    audioChunks: [],
    stream: null,
    analyser: null,
    audioContext: null,
    animationId: null,
    startTime: null,
    elapsed: 0,
    timerInterval: null,
    bars: Array(40).fill(4),
    error: null,

    // Speech recognition
    recognition: null,
    speechSupported: false,
    finalTranscript: '',
    interimTranscript: '',
    editableTranscript: '',

    // Voice correction
    isCorreecting: false,
    isApplyingCorrection: false,
    correctionRecognition: null,
    correctionFinal: '',
    correctionInterim: '',

    // Silence detection
    silenceSeconds: 0,
    silenceTimer: null,
    silenceThreshold: 15,
    silenceTimeout: 3,

    // Processing
    processedEntries: [],
    processingStatus: 'Audio uploaden...',
    processingError: null,

    get formattedTime() {
        const mins = Math.floor(this.elapsed / 60);
        const secs = this.elapsed % 60;
        return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
    },

    init() {
        const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
        this.speechSupported = !!SR;
    },

    handleTouchStart(e) {
        this.touchTimer = setTimeout(() => {
            this.isTouchRecording = true;
            this.startRecording();
        }, 200);
    },

    handleTouchEnd(e) {
        clearTimeout(this.touchTimer);
        if (this.isTouchRecording && this.state === 'recording') {
            this.isTouchRecording = false;
            this.stopRecording();
        } else if (!this.isTouchRecording) {
            this.startRecording();
        }
    },

    async startRecording() {
        if (this.state === 'recording') return;
        this.error = null;
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            this.error = 'Microfoon niet beschikbaar. Gebruik HTTPS of localhost.';
            return;
        }
        try {
            this.stream = await navigator.mediaDevices.getUserMedia({
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    sampleRate: 44100,
                }
            });

            this.audioContext = new AudioContext();
            const source = this.audioContext.createMediaStreamSource(this.stream);
            this.analyser = this.audioContext.createAnalyser();
            this.analyser.fftSize = 128;
            source.connect(this.analyser);

            const mimeType = MediaRecorder.isTypeSupported('audio/webm;codecs=opus')
                ? 'audio/webm;codecs=opus'
                : MediaRecorder.isTypeSupported('audio/webm')
                    ? 'audio/webm'
                    : '';

            this.mediaRecorder = new MediaRecorder(this.stream, mimeType ? { mimeType } : {});
            this.audioChunks = [];

            this.mediaRecorder.ondataavailable = (e) => {
                if (e.data.size > 0) this.audioChunks.push(e.data);
            };

            this.mediaRecorder.onstop = () => {};

            this.mediaRecorder.start(250);
            this.state = 'recording';
            this.startTime = Date.now();
            this.elapsed = 0;
            this.finalTranscript = '';
            this.interimTranscript = '';

            this.startTimer();
            this.startVisualization();

            if (this.speechSupported) {
                this.initSpeechRecognition();
            }

            this.startSilenceDetection();

        } catch (err) {
            if (err.name === 'NotAllowedError') {
                this.error = 'Microfoontoegang geweigerd. Sta dit toe in je browser-instellingen.';
            } else if (err.name === 'NotFoundError') {
                this.error = 'Geen microfoon gevonden.';
            } else {
                this.error = 'Kan opname niet starten: ' + err.message;
            }
        }
    },

    initSpeechRecognition() {
        const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
        this.recognition = new SR();
        this.recognition.lang = 'nl-NL';
        this.recognition.interimResults = true;
        this.recognition.continuous = true;
        this.recognition.maxAlternatives = 1;

        this.recognition.onresult = (event) => {
            let interim = '';
            for (let i = event.resultIndex; i < event.results.length; i++) {
                const transcript = event.results[i][0].transcript;
                if (event.results[i].isFinal) {
                    this.finalTranscript += transcript + ' ';
                } else {
                    interim += transcript;
                }
            }
            this.interimTranscript = interim;
        };

        this.recognition.onerror = (event) => {
            if (event.error !== 'no-speech' && event.error !== 'aborted') {
                console.warn('Speech recognition error:', event.error);
            }
        };

        this.recognition.onend = () => {
            if (this.state === 'recording') {
                try { this.recognition.start(); } catch (e) {}
            }
        };

        this.recognition.start();
    },

    startSilenceDetection() {
        let lastSpeechTime = Date.now();
        this.silenceSeconds = 0;

        this.silenceTimer = setInterval(() => {
            if (!this.analyser || this.state !== 'recording') return;

            const data = new Uint8Array(this.analyser.frequencyBinCount);
            this.analyser.getByteFrequencyData(data);
            const rms = Math.sqrt(data.reduce((sum, val) => sum + val * val, 0) / data.length);

            if (rms > this.silenceThreshold) {
                lastSpeechTime = Date.now();
                this.silenceSeconds = 0;
            } else {
                this.silenceSeconds = (Date.now() - lastSpeechTime) / 1000;
            }

            if (this.silenceSeconds >= this.silenceTimeout && this.finalTranscript.trim().length > 0) {
                this.stopRecording();
            }
        }, 250);
    },

    stopSilenceDetection() {
        if (this.silenceTimer) {
            clearInterval(this.silenceTimer);
            this.silenceTimer = null;
        }
        this.silenceSeconds = 0;
    },

    async stopRecording() {
        if (this.state !== 'recording') return;

        this.stopSilenceDetection();
        this.stopVisualization();
        this.stopTimer();

        if (this.recognition) {
            try { this.recognition.stop(); } catch (e) {}
        }

        if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
            this.mediaRecorder.stop();
        }
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
        }

        if (this.speechSupported && this.finalTranscript.trim().length > 0) {
            const rawText = this.finalTranscript.trim();
            this.state = 'cleaning';
            this.editableTranscript = rawText;

            fetch('/api/clean-transcript', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ text: rawText }),
            })
            .then(r => r.ok ? r.json() : Promise.reject())
            .then(data => {
                if (data.cleaned && data.cleaned !== rawText) {
                    this.editableTranscript = data.cleaned;
                }
                this.state = 'confirming';
            })
            .catch(() => {
                this.state = 'confirming';
            });
        } else if (this.speechSupported) {
            this.error = 'Geen spraak gedetecteerd. Probeer opnieuw.';
            this.state = 'idle';
        } else {
            this.state = 'done';
        }
    },

    // Smart voice result (client/project)
    smartResult: null,

    async confirmAndProcess() {
        if (this.audioChunks.length === 0 || !this.editableTranscript.trim()) return;

        this.state = 'processing';
        this.processedEntries = [];
        this.smartResult = null;
        this.processingStatus = 'Herkennen wat je wilt...';
        this.processingError = null;

        const blob = new Blob(this.audioChunks, {
            type: this.mediaRecorder?.mimeType || 'audio/webm'
        });
        const transcript = this.editableTranscript.trim();
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        try {
            // Send audio + transcript to smart-voice (handles all intents including compound)
            const formData = new FormData();
            formData.append('audio', blob, `opname-${Date.now()}.webm`);
            formData.append('transcript', transcript);

            const smartRes = await fetch('/api/smart-voice', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                body: formData,
            });

            if (!smartRes.ok) throw new Error('Server error');
            const smartData = await smartRes.json();

            // Compound result (multiple actions performed)
            if (smartData.type === 'compound') {
                this.smartResult = smartData;
                this.processingStatus = smartData.message;
                return;
            }

            // Single client or project created
            if (smartData.type === 'client' || smartData.type === 'project') {
                this.smartResult = smartData;
                this.processingStatus = smartData.message;
                return;
            }

            // Single command executed
            if (smartData.type === 'command') {
                this.smartResult = smartData;
                this.processingStatus = smartData.message;
                return;
            }

            // Single entry creation — passthrough to process-entry
            if (smartData.passthrough) {
                this.processingStatus = 'Werkbon(nen) genereren met AI...';

                const entryFormData = new FormData();
                entryFormData.append('audio', blob, `opname-${Date.now()}.webm`);
                entryFormData.append('transcript', transcript);

                const response = await fetch('/api/process-entry', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: entryFormData,
                });

                if (!response.ok) throw new Error('Server error');

                const data = await response.json();
                this.processedEntries = data.entries || [];

                const count = this.processedEntries.length;
                this.processingStatus = count === 1
                    ? 'Werkbon aangemaakt!'
                    : `${count} werkbonnen aangemaakt!`;

                if (data.error) {
                    this.processingError = data.error;
                }
                return;
            }
        } catch (err) {
            this.processingError = 'Verwerking mislukt. Probeer opnieuw.';
            this.processingStatus = 'Er ging iets mis.';
            console.error('Process entry failed:', err);
        }
    },

    async uploadFallback() {
        if (this.audioChunks.length === 0) return;

        const blob = new Blob(this.audioChunks, {
            type: this.mediaRecorder?.mimeType || 'audio/webm'
        });
        const file = new File([blob], `opname-${Date.now()}.webm`, { type: blob.type });

        this.$wire.upload('audioFile', file, () => {
            this.$wire.uploadAudio();
        }, () => {
            this.error = 'Upload mislukt. Controleer je internetverbinding en probeer opnieuw.';
        });
    },

    resetRecording() {
        this.stopVoiceCorrection();
        this.state = 'idle';
        this.audioChunks = [];
        this.elapsed = 0;
        this.bars = Array(40).fill(4);
        this.error = null;
        this.finalTranscript = '';
        this.interimTranscript = '';
        this.editableTranscript = '';
        this.silenceSeconds = 0;
        this.processedEntries = [];
        this.smartResult = null;
        this.processingStatus = '';
        this.processingError = null;
        this.isTouchRecording = false;
    },

    startVoiceCorrection() {
        if (!this.speechSupported || this.isCorreecting) return;

        const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
        this.correctionRecognition = new SR();
        this.correctionRecognition.lang = 'nl-NL';
        this.correctionRecognition.interimResults = true;
        this.correctionRecognition.continuous = true;
        this.correctionRecognition.maxAlternatives = 1;

        this.correctionFinal = '';
        this.correctionInterim = '';
        this.isCorreecting = true;

        this.correctionRecognition.onresult = (event) => {
            let interim = '';
            for (let i = event.resultIndex; i < event.results.length; i++) {
                const text = event.results[i][0].transcript;
                if (event.results[i].isFinal) {
                    this.correctionFinal += text + ' ';
                    this.correctionInterim = '';
                } else {
                    interim += text;
                }
            }
            this.correctionInterim = interim;
        };

        this.correctionRecognition.onerror = (event) => {
            if (event.error !== 'no-speech' && event.error !== 'aborted') {
                console.warn('Correction recognition error:', event.error);
            }
        };

        this.correctionRecognition.onend = () => {
            if (this.isCorreecting) {
                try { this.correctionRecognition.start(); } catch (e) {}
            }
        };

        this.correctionRecognition.start();
    },

    stopVoiceCorrection() {
        if (!this.correctionRecognition) return;

        this.isCorreecting = false;
        try { this.correctionRecognition.stop(); } catch (e) {}
        this.correctionRecognition = null;

        const correctionText = (this.correctionFinal + this.correctionInterim).trim();
        this.correctionFinal = '';
        this.correctionInterim = '';

        if (!correctionText || correctionText.length < 2) return;

        this.isApplyingCorrection = true;

        fetch('/api/apply-correction', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                original: this.editableTranscript,
                correction: correctionText,
            }),
        })
        .then(r => r.ok ? r.json() : Promise.reject())
        .then(data => {
            if (data.result) {
                this.editableTranscript = data.result;
            }
            this.isApplyingCorrection = false;
        })
        .catch(() => {
            this.isApplyingCorrection = false;
        });
    },

    startVisualization() {
        const draw = () => {
            if (!this.analyser) return;
            const data = new Uint8Array(this.analyser.frequencyBinCount);
            this.analyser.getByteFrequencyData(data);

            this.bars = Array.from({ length: 40 }, (_, i) => {
                const val = data[i] || 0;
                return Math.max(4, (val / 255) * 60);
            });

            this.animationId = requestAnimationFrame(draw);
        };
        draw();
    },

    stopVisualization() {
        if (this.animationId) {
            cancelAnimationFrame(this.animationId);
            this.animationId = null;
        }
    },

    startTimer() {
        this.timerInterval = setInterval(() => {
            this.elapsed = Math.floor((Date.now() - this.startTime) / 1000);
        }, 250);
    },

    stopTimer() {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }
    },
}));
</script>
@endscript
