<div class="space-y-6" x-data="{
    clientType: $wire.entangle('type'),

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
                body: JSON.stringify({ transcript: text, context: 'client' })
            });
            if (!res.ok) throw new Error();
            const { data } = await res.json();
            if (data) {
                await this.$wire.call('fillFromVoice', data);
                if (data.type) this.clientType = data.type;
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

            {{-- Idle state --}}
            <template x-if="voiceState === 'idle'">
                <button type="button" @click="startVoice" class="w-full flex items-center gap-4 px-5 py-4 group transition hover:bg-amber/5 cursor-pointer">
                    <div class="w-10 h-10 rounded-full bg-amber/15 flex items-center justify-center shrink-0 group-hover:bg-amber/25 transition">
                        <i class="fa-solid fa-microphone text-amber text-sm"></i>
                    </div>
                    <div class="text-left">
                        <span class="text-sm font-heading font-semibold text-paper">Spreek de klantgegevens in</span>
                        <p class="text-[11px] text-ink-50 mt-0.5">Bijv. "Jan de Vries, telefoon 06-12345678, Kerkstraat 12 Amsterdam"</p>
                    </div>
                    <i class="fa-solid fa-arrow-right text-ink-70 text-[10px] ml-auto group-hover:text-amber transition"></i>
                </button>
            </template>

            {{-- Recording state --}}
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

            {{-- Processing state --}}
            <template x-if="voiceState === 'processing'">
                <div class="flex items-center gap-3 px-5 py-4">
                    <i class="fa-solid fa-wand-magic-sparkles text-amber text-sm animate-pulse"></i>
                    <span class="text-sm text-ink-30 font-heading">Gegevens herkennen...</span>
                </div>
            </template>

            {{-- Done state --}}
            <template x-if="voiceState === 'done'">
                <div class="flex items-center gap-3 px-5 py-4">
                    <i class="fa-solid fa-check-circle text-green-400 text-sm"></i>
                    <span class="text-sm text-green-400 font-heading font-semibold">Gegevens ingevuld! Controleer en sla op.</span>
                </div>
            </template>
        </div>

        {{-- Voice error --}}
        <template x-if="voiceError">
            <div class="flex items-start gap-3 px-4 py-3 bg-red-500/10 border border-red-500/20 rounded-sm text-xs text-red-400">
                <i class="fa-solid fa-triangle-exclamation shrink-0 mt-0.5"></i>
                <span x-text="voiceError"></span>
            </div>
        </template>

        {{-- Basisgegevens --}}
        <div class="bg-ink rounded-sm border border-ink-70/20 p-6 space-y-5" style="background-image: radial-gradient(circle, rgba(255,180,0,0.05) 1px, transparent 1px); background-size: 20px 20px;">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-md bg-amber/10 flex items-center justify-center">
                    <i class="fa-solid fa-user text-amber text-sm"></i>
                </div>
                <h3 class="font-heading font-semibold text-paper text-sm">Basisgegevens</h3>
            </div>

            {{-- Type toggle --}}
            <div>
                <label class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-2">Type relatie</label>
                <div class="flex gap-2">
                    <button type="button" x-on:click="clientType = 'particulier'"
                        class="cursor-pointer flex-1 px-4 py-2.5 rounded-sm text-sm font-heading font-semibold border transition"
                        :class="clientType === 'particulier' ? 'bg-amber/15 border-amber/40 text-amber' : 'bg-ink-90 border-ink-70/20 text-ink-50 hover:border-ink-70/40 hover:text-paper'">
                        <i class="fa-solid fa-user text-xs mr-1.5"></i> Particulier
                    </button>
                    <button type="button" x-on:click="clientType = 'zakelijk'"
                        class="cursor-pointer flex-1 px-4 py-2.5 rounded-sm text-sm font-heading font-semibold border transition"
                        :class="clientType === 'zakelijk' ? 'bg-amber/15 border-amber/40 text-amber' : 'bg-ink-90 border-ink-70/20 text-ink-50 hover:border-ink-70/40 hover:text-paper'">
                        <i class="fa-solid fa-building text-xs mr-1.5"></i> Zakelijk
                    </button>
                </div>
            </div>

            <div>
                <label for="name" class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-2">Naam *</label>
                <input type="text" id="name" wire:model="name" placeholder="Bijv. Jan de Vries" class="klaar-dark-input w-full">
                @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div x-show="clientType === 'zakelijk'" x-cloak>
                <label for="company" class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-2">Bedrijfsnaam</label>
                <input type="text" id="company" wire:model="company" placeholder="Bijv. De Vries Bouw B.V." class="klaar-dark-input w-full">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-2">E-mail</label>
                    <input type="email" id="email" wire:model="email" placeholder="jan@voorbeeld.nl" class="klaar-dark-input w-full">
                    @error('email') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="phone" class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-2">Telefoon</label>
                    <input type="text" id="phone" wire:model="phone" placeholder="06-12345678" class="klaar-dark-input w-full">
                </div>
            </div>
        </div>

        {{-- Adres & zakelijk --}}
        <div class="bg-ink rounded-sm border border-ink-70/20 p-6 space-y-5" style="background-image: radial-gradient(circle, rgba(255,180,0,0.05) 1px, transparent 1px); background-size: 20px 20px;">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-md bg-amber/10 flex items-center justify-center">
                    <i class="fa-solid fa-location-dot text-amber text-sm"></i>
                </div>
                <h3 class="font-heading font-semibold text-paper text-sm">Adres & zakelijk</h3>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    <label for="address_street" class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-2">Straat</label>
                    <input type="text" id="address_street" wire:model="address_street" placeholder="Kerkstraat" class="klaar-dark-input w-full">
                </div>
                <div>
                    <label for="address_housenumber" class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-2">Huisnr.</label>
                    <input type="text" id="address_housenumber" wire:model="address_housenumber" placeholder="12A" class="klaar-dark-input w-full">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label for="address_postcode" class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-2">Postcode</label>
                    <input type="text" id="address_postcode" wire:model="address_postcode" placeholder="1234 AB" class="klaar-dark-input w-full">
                </div>
                <div class="col-span-2">
                    <label for="address_city" class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-2">Plaats</label>
                    <input type="text" id="address_city" wire:model="address_city" placeholder="Amsterdam" class="klaar-dark-input w-full">
                </div>
            </div>

            <div x-show="clientType === 'zakelijk'" x-cloak class="grid grid-cols-2 gap-4">
                <div>
                    <label for="kvk_number" class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-2">KVK-nummer</label>
                    <input type="text" id="kvk_number" wire:model="kvk_number" placeholder="12345678" class="klaar-dark-input w-full font-mono">
                </div>
                <div>
                    <label for="btw_number" class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-2">BTW-nummer</label>
                    <input type="text" id="btw_number" wire:model="btw_number" placeholder="NL123456789B01" class="klaar-dark-input w-full font-mono">
                </div>
            </div>

            <div>
                <label for="notes" class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-2">Notities</label>
                <textarea id="notes" wire:model="notes" rows="3" placeholder="Optioneel: interne notities over deze relatie" class="klaar-dark-input w-full resize-none"></textarea>
            </div>
        </div>

        {{-- Knoppen --}}
        <div class="flex items-center justify-end gap-3">
            @if($client && $client->exists)
                <a href="{{ route('clients.show', $client) }}" wire:navigate class="border border-ink-70/30 px-5 py-2.5 text-ink-50 font-semibold text-sm font-heading rounded-sm transition hover:bg-ink-90 hover:text-paper">
                    Annuleren
                </a>
            @else
                <a href="{{ route('clients.index') }}" wire:navigate class="border border-ink-70/30 px-5 py-2.5 text-ink-50 font-semibold text-sm font-heading rounded-sm transition hover:bg-ink-90 hover:text-paper">
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
