<div
    x-data="voiceRecorder"
    class="space-y-6"
>
    {{-- Recording card --}}
    <div class="bg-snow rounded-lg border border-ink-10/50 shadow-sm overflow-hidden">

        {{-- Visualization area --}}
        <div class="relative flex items-center justify-center h-48 bg-gradient-to-b from-ink-10/30 to-transparent">
            {{-- Waveform / idle state --}}
            <template x-if="state === 'idle'">
                <div class="text-center">
                    <div class="w-20 h-20 bg-amber/15 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-10 h-10 text-amber" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                        </svg>
                    </div>
                    <p class="mt-3 text-sm text-ink-50">Tik op de knop om te beginnen met opnemen</p>
                </div>
            </template>

            {{-- Recording waveform --}}
            <template x-if="state === 'recording'">
                <div class="flex flex-col items-center gap-4 w-full px-8">
                    {{-- Animated bars --}}
                    <div class="flex items-center justify-center gap-1 h-16">
                        <template x-for="(bar, i) in bars" :key="i">
                            <div
                                class="w-1 rounded-full bg-amber transition-all duration-100"
                                :style="`height: ${bar}px`"
                            ></div>
                        </template>
                    </div>
                    {{-- Timer --}}
                    <div class="font-mono text-2xl font-medium text-ink tabular-nums" x-text="formattedTime"></div>
                </div>
            </template>

            {{-- Paused state --}}
            <template x-if="state === 'paused'">
                <div class="flex flex-col items-center gap-4">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-amber rounded-full animate-pulse"></div>
                        <span class="text-sm font-medium text-ink-70">Gepauzeerd</span>
                    </div>
                    <div class="font-mono text-2xl font-medium text-ink tabular-nums" x-text="formattedTime"></div>
                </div>
            </template>

            {{-- Done state --}}
            <template x-if="state === 'done'">
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                    </div>
                    <p class="mt-3 text-sm text-ink-50">
                        Opname klaar &mdash; <span class="font-mono" x-text="formattedTime"></span>
                    </p>
                </div>
            </template>
        </div>

        {{-- Controls --}}
        <div class="flex items-center justify-center gap-4 p-6 border-t border-ink-10/50">
            {{-- Record / Stop button --}}
            <template x-if="state === 'idle'">
                <button
                    @click="startRecording"
                    class="w-16 h-16 bg-amber rounded-full flex items-center justify-center shadow-md hover:bg-amber/90 active:scale-95 transition"
                >
                    <div class="w-5 h-5 bg-ink rounded-full"></div>
                </button>
            </template>

            <template x-if="state === 'recording'">
                <div class="flex items-center gap-6">
                    <button
                        @click="pauseRecording"
                        class="w-12 h-12 bg-snow border border-ink-10 rounded-full flex items-center justify-center hover:bg-ink-10/50 transition"
                        title="Pauzeer"
                    >
                        <svg class="w-5 h-5 text-ink" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z"/>
                        </svg>
                    </button>
                    <button
                        @click="stopRecording"
                        class="w-16 h-16 bg-red-500 rounded-full flex items-center justify-center shadow-md hover:bg-red-600 active:scale-95 transition"
                        title="Stop"
                    >
                        <div class="w-6 h-6 bg-white rounded-sm"></div>
                    </button>
                </div>
            </template>

            <template x-if="state === 'paused'">
                <div class="flex items-center gap-6">
                    <button
                        @click="resumeRecording"
                        class="w-12 h-12 bg-snow border border-ink-10 rounded-full flex items-center justify-center hover:bg-ink-10/50 transition"
                        title="Hervat"
                    >
                        <svg class="w-5 h-5 text-ink" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </button>
                    <button
                        @click="stopRecording"
                        class="w-16 h-16 bg-red-500 rounded-full flex items-center justify-center shadow-md hover:bg-red-600 active:scale-95 transition"
                        title="Stop"
                    >
                        <div class="w-6 h-6 bg-white rounded-sm"></div>
                    </button>
                </div>
            </template>

            <template x-if="state === 'done'">
                <div class="flex items-center gap-4">
                    <button
                        @click="resetRecording"
                        class="px-5 py-2.5 bg-snow border border-ink-10 text-ink-70 text-sm font-medium rounded-md hover:bg-ink-10/50 transition"
                    >
                        Opnieuw
                    </button>
                    <button
                        @click="upload"
                        class="px-5 py-2.5 bg-amber text-ink text-sm font-semibold rounded-md hover:bg-amber/90 transition shadow-sm"
                        :disabled="$wire.isUploading"
                    >
                        <span x-show="!$wire.isUploading">Verwerken</span>
                        <span x-show="$wire.isUploading" class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Uploaden...
                        </span>
                    </button>
                </div>
            </template>
        </div>
    </div>

    {{-- Error message --}}
    <template x-if="error">
        <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
            </svg>
            <span x-text="error"></span>
        </div>
    </template>

    {{-- Upload complete --}}
    @if($uploadComplete && $entryId)
        <div class="flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="font-medium">Opname wordt verwerkt!</p>
                <p class="mt-1 text-green-600">Je spraak wordt nu omgezet naar tekst en verwerkt door AI. Dit duurt even.</p>
                <a href="{{ route('entries.show', $entryId ?? 0) }}" class="mt-2 inline-flex items-center gap-1 text-sm font-medium text-green-700 hover:text-green-800">
                    Bekijk invoer
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
        </div>
    @endif

    {{-- Tips --}}
    <div class="bg-snow rounded-lg border border-ink-10/50 p-5">
        <h4 class="font-heading font-semibold text-ink text-sm">Tips voor de beste resultaten</h4>
        <ul class="mt-3 space-y-2 text-sm text-ink-50">
            <li class="flex items-start gap-2">
                <svg class="w-4 h-4 text-amber shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                Noem de klant en het project
            </li>
            <li class="flex items-start gap-2">
                <svg class="w-4 h-4 text-amber shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                Beschrijf wat je gedaan hebt en hoelang
            </li>
            <li class="flex items-start gap-2">
                <svg class="w-4 h-4 text-amber shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                Noem materiaalkosten en aantallen
            </li>
            <li class="flex items-start gap-2">
                <svg class="w-4 h-4 text-amber shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                Spreek duidelijk, ook in een rumoerige omgeving
            </li>
        </ul>
    </div>
</div>

@script
<script>
Alpine.data('voiceRecorder', () => ({
    state: 'idle', // idle, recording, paused, done
    mediaRecorder: null,
    audioChunks: [],
    stream: null,
    analyser: null,
    animationId: null,
    startTime: null,
    pausedDuration: 0,
    pauseStart: null,
    elapsed: 0,
    timerInterval: null,
    bars: Array(32).fill(4),
    error: null,

    get formattedTime() {
        const mins = Math.floor(this.elapsed / 60);
        const secs = this.elapsed % 60;
        return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
    },

    async startRecording() {
        this.error = null;
        try {
            this.stream = await navigator.mediaDevices.getUserMedia({
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    sampleRate: 44100,
                }
            });

            // Set up analyser for visualization
            const audioContext = new AudioContext();
            const source = audioContext.createMediaStreamSource(this.stream);
            this.analyser = audioContext.createAnalyser();
            this.analyser.fftSize = 64;
            source.connect(this.analyser);

            // Determine supported format
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

            this.mediaRecorder.onstop = () => {
                this.state = 'done';
                this.stopVisualization();
                this.stopTimer();
            };

            this.mediaRecorder.start(250); // Collect data every 250ms
            this.state = 'recording';
            this.startTime = Date.now();
            this.pausedDuration = 0;
            this.elapsed = 0;
            this.startTimer();
            this.startVisualization();
        } catch (err) {
            if (err.name === 'NotAllowedError') {
                this.error = 'Microfoontoegang geweigerd. Sta microfoontoegang toe in je browser-instellingen.';
            } else if (err.name === 'NotFoundError') {
                this.error = 'Geen microfoon gevonden. Sluit een microfoon aan en probeer opnieuw.';
            } else {
                this.error = 'Kan opname niet starten: ' + err.message;
            }
        }
    },

    pauseRecording() {
        if (this.mediaRecorder && this.mediaRecorder.state === 'recording') {
            this.mediaRecorder.pause();
            this.state = 'paused';
            this.pauseStart = Date.now();
            this.stopVisualization();
            this.stopTimer();
        }
    },

    resumeRecording() {
        if (this.mediaRecorder && this.mediaRecorder.state === 'paused') {
            this.mediaRecorder.resume();
            this.state = 'recording';
            if (this.pauseStart) {
                this.pausedDuration += Date.now() - this.pauseStart;
                this.pauseStart = null;
            }
            this.startTimer();
            this.startVisualization();
        }
    },

    stopRecording() {
        if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
            this.mediaRecorder.stop();
            this.stream.getTracks().forEach(track => track.stop());
        }
    },

    resetRecording() {
        this.state = 'idle';
        this.audioChunks = [];
        this.elapsed = 0;
        this.bars = Array(32).fill(4);
        this.error = null;
    },

    async upload() {
        if (this.audioChunks.length === 0) return;

        const blob = new Blob(this.audioChunks, { type: this.mediaRecorder.mimeType || 'audio/webm' });
        const file = new File([blob], `opname-${Date.now()}.webm`, { type: blob.type });

        // Upload via Livewire
        this.$wire.upload('audioFile', file, () => {
            // Upload finished, now call the server method
            this.$wire.uploadAudio();
        }, () => {
            this.error = 'Upload mislukt. Controleer je internetverbinding en probeer opnieuw.';
        });
    },

    startVisualization() {
        const draw = () => {
            if (!this.analyser) return;
            const data = new Uint8Array(this.analyser.frequencyBinCount);
            this.analyser.getByteFrequencyData(data);

            this.bars = Array.from({ length: 32 }, (_, i) => {
                const val = data[i] || 0;
                return Math.max(4, (val / 255) * 56);
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
            const now = Date.now();
            this.elapsed = Math.floor((now - this.startTime - this.pausedDuration) / 1000);
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
