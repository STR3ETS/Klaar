<div class="space-y-6" x-data="{
    // Voice fill
    voiceState: 'idle',
    recognition: null,
    speechSupported: !!(window.SpeechRecognition || window.webkitSpeechRecognition),
    finalTranscript: '',
    interimTranscript: '',
    voiceError: null,

    startVoice() {
        if (!this.speechSupported) { this.voiceError = 'Spraakherkenning niet beschikbaar in deze browser.'; return; }
        this.voiceError = null;
        this.finalTranscript = '';
        this.interimTranscript = '';
        this.voiceState = 'recording';

        const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
        this.recognition = new SR();
        this.recognition.lang = 'nl-NL';
        this.recognition.interimResults = true;
        this.recognition.continuous = true;

        this.recognition.onresult = (e) => {
            let interim = '';
            for (let i = e.resultIndex; i < e.results.length; i++) {
                if (e.results[i].isFinal) this.finalTranscript += e.results[i][0].transcript + ' ';
                else interim += e.results[i][0].transcript;
            }
            this.interimTranscript = interim;
        };
        this.recognition.onerror = (e) => {
            if (e.error !== 'no-speech' && e.error !== 'aborted') this.voiceError = 'Fout: ' + e.error;
        };
        this.recognition.onend = () => { if (this.voiceState === 'recording') try { this.recognition.start(); } catch(e) {} };
        this.recognition.start();
    },

    async stopVoice() {
        if (this.recognition) try { this.recognition.stop(); } catch(e) {}
        this.recognition = null;

        const text = (this.finalTranscript + this.interimTranscript).trim();
        if (!text) { this.voiceState = 'idle'; return; }

        this.voiceState = 'processing';
        try {
            const res = await fetch('/api/voice-extract', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                body: JSON.stringify({ transcript: text, context: 'project' })
            });
            if (!res.ok) throw new Error();
            const { data } = await res.json();
            if (data) {
                await this.$wire.call('fillFromVoice', data);
            }
            this.voiceState = 'done';
            setTimeout(() => this.voiceState = 'idle', 3000);
        } catch (err) {
            this.voiceError = 'Extractie mislukt. Pas de velden handmatig aan.';
            this.voiceState = 'idle';
        }
    },

    cancelVoice() {
        if (this.recognition) try { this.recognition.stop(); } catch(e) {}
        this.recognition = null;
        this.voiceState = 'idle';
        this.finalTranscript = '';
        this.interimTranscript = '';
    }
}">
    <form wire:submit="save" class="space-y-6">

        {{-- Voice fill banner --}}
        <div class="bg-ink rounded-sm border border-amber/20 overflow-hidden" style="background-image: radial-gradient(circle, rgba(255,180,0,0.05) 1px, transparent 1px); background-size: 20px 20px;">
            <template x-if="voiceState === 'idle'">
                <button type="button" @click="startVoice" class="w-full flex items-center gap-4 px-5 py-4 group transition hover:bg-amber/5 cursor-pointer">
                    <div class="w-10 h-10 rounded-full bg-amber/15 flex items-center justify-center shrink-0 group-hover:bg-amber/25 transition">
                        <i class="fa-solid fa-microphone text-amber text-sm"></i>
                    </div>
                    <div class="text-left">
                        <span class="text-sm font-heading font-semibold text-paper">Spreek het project in</span>
                        <p class="text-[11px] text-ink-50 mt-0.5">Bijv. "Badkamer renovatie bij klant Jansen, Kerkstraat 12 Amsterdam"</p>
                    </div>
                    <i class="fa-solid fa-arrow-right text-ink-70 text-[10px] ml-auto group-hover:text-amber transition"></i>
                </button>
            </template>
            <template x-if="voiceState === 'recording'">
                <div class="px-5 py-4">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse"></div>
                        <span class="text-[10px] font-heading font-semibold uppercase tracking-widest text-red-400">Luisteren...</span>
                    </div>
                    <div class="min-h-[40px] mb-3">
                        <p class="text-sm leading-relaxed">
                            <span class="text-paper" x-text="finalTranscript"></span><span class="text-ink-50 italic" x-text="interimTranscript"></span>
                        </p>
                        <p x-show="!finalTranscript && !interimTranscript" class="text-sm text-ink-50 italic">Begin te spreken...</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="stopVoice" class="inline-flex items-center gap-2 bg-amber px-4 py-2 text-ink font-semibold text-xs font-heading rounded-sm transition hover:brightness-110 cursor-pointer">
                            <i class="fa-solid fa-check text-[10px]"></i> Klaar
                        </button>
                        <button type="button" @click="cancelVoice" class="inline-flex items-center gap-2 border border-ink-70/30 px-4 py-2 text-ink-50 font-semibold text-xs font-heading rounded-sm transition hover:text-paper cursor-pointer">
                            Annuleren
                        </button>
                    </div>
                </div>
            </template>
            <template x-if="voiceState === 'processing'">
                <div class="flex items-center gap-3 px-5 py-4">
                    <i class="fa-solid fa-wand-magic-sparkles text-amber text-sm animate-pulse"></i>
                    <span class="text-sm text-ink-30 font-heading">Gegevens herkennen...</span>
                </div>
            </template>
            <template x-if="voiceState === 'done'">
                <div class="flex items-center gap-3 px-5 py-4">
                    <i class="fa-solid fa-check-circle text-green-400 text-sm"></i>
                    <span class="text-sm text-green-400 font-heading font-semibold">Gegevens ingevuld! Controleer en sla op.</span>
                </div>
            </template>
        </div>

        <template x-if="voiceError">
            <div class="flex items-start gap-3 px-4 py-3 bg-red-500/10 border border-red-500/20 rounded-sm text-xs text-red-400">
                <i class="fa-solid fa-triangle-exclamation shrink-0 mt-0.5"></i>
                <span x-text="voiceError"></span>
            </div>
        </template>

        {{-- Projectgegevens --}}
        <div class="bg-ink rounded-sm border border-ink-70/20 p-6 space-y-5" style="background-image: radial-gradient(circle, rgba(255,180,0,0.05) 1px, transparent 1px); background-size: 20px 20px;">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-md bg-amber/10 flex items-center justify-center">
                    <i class="fa-solid fa-hammer text-amber text-sm"></i>
                </div>
                <h3 class="font-heading font-semibold text-paper text-sm">Projectgegevens</h3>
            </div>

            <div>
                <label for="name" class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-2">Projectnaam *</label>
                <input type="text" id="name" wire:model="name" placeholder="Bijv. Badkamer renovatie Kerkstraat 12" class="klaar-dark-input w-full">
                @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-2">Omschrijving</label>
                <textarea id="description" wire:model="description" rows="3" placeholder="Optioneel: beschrijving van het project" class="klaar-dark-input w-full resize-none"></textarea>
            </div>

            <div>
                <label for="address" class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-2">Locatie / adres</label>
                <input type="text" id="address" wire:model="address" placeholder="Bijv. Kerkstraat 12, Amsterdam" class="klaar-dark-input w-full">
            </div>
        </div>

        {{-- Koppeling & status --}}
        <div class="bg-ink rounded-sm border border-ink-70/20 p-6 space-y-5" style="background-image: radial-gradient(circle, rgba(255,180,0,0.05) 1px, transparent 1px); background-size: 20px 20px;">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-md bg-amber/10 flex items-center justify-center">
                    <i class="fa-solid fa-link text-amber text-sm"></i>
                </div>
                <h3 class="font-heading font-semibold text-paper text-sm">Koppeling & status</h3>
            </div>

            <div>
                <label for="client_id" class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-2">Relatie</label>
                <select id="client_id" wire:model="client_id" class="klaar-dark-input w-full">
                    <option value="">— Geen relatie gekoppeld —</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-2">Status</label>
                <div class="flex gap-2" x-data="{ status: $wire.entangle('status') }">
                    <button type="button" x-on:click="status = 'active'"
                        class="cursor-pointer flex-1 px-4 py-2.5 rounded-sm text-sm font-heading font-semibold border transition"
                        :class="status === 'active' ? 'bg-green-500/15 border-green-500/40 text-green-400' : 'bg-ink-90 border-ink-70/20 text-ink-50 hover:border-ink-70/40 hover:text-paper'">
                        <i class="fa-solid fa-hammer text-xs mr-1.5"></i> Actief
                    </button>
                    <button type="button" x-on:click="status = 'completed'"
                        class="cursor-pointer flex-1 px-4 py-2.5 rounded-sm text-sm font-heading font-semibold border transition"
                        :class="status === 'completed' ? 'bg-amber/15 border-amber/40 text-amber' : 'bg-ink-90 border-ink-70/20 text-ink-50 hover:border-ink-70/40 hover:text-paper'">
                        <i class="fa-solid fa-circle-check text-xs mr-1.5"></i> Afgerond
                    </button>
                    <button type="button" x-on:click="status = 'archived'"
                        class="cursor-pointer flex-1 px-4 py-2.5 rounded-sm text-sm font-heading font-semibold border transition"
                        :class="status === 'archived' ? 'bg-ink-70/30 border-ink-70/40 text-paper' : 'bg-ink-90 border-ink-70/20 text-ink-50 hover:border-ink-70/40 hover:text-paper'">
                        <i class="fa-solid fa-box-archive text-xs mr-1.5"></i> Archief
                    </button>
                </div>
            </div>
        </div>

        {{-- Knoppen --}}
        <div class="flex items-center justify-end gap-3">
            @if($project && $project->exists)
                <a href="{{ route('projects.show', $project) }}" wire:navigate class="border border-ink-70/30 px-5 py-2.5 text-ink-50 font-semibold text-sm font-heading rounded-sm transition hover:bg-ink-90 hover:text-paper">
                    Annuleren
                </a>
            @else
                <a href="{{ route('projects.index') }}" wire:navigate class="border border-ink-70/30 px-5 py-2.5 text-ink-50 font-semibold text-sm font-heading rounded-sm transition hover:bg-ink-90 hover:text-paper">
                    Annuleren
                </a>
            @endif
            <button
                type="submit"
                class="cursor-pointer border border-amber bg-amber px-5 py-2.5 text-ink font-semibold text-sm font-heading rounded-sm transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)] disabled:opacity-50"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="save">
                    <i class="fa-solid fa-check text-xs mr-1.5"></i> Opslaan
                </span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                    Opslaan...
                </span>
            </button>
        </div>
    </form>
</div>
