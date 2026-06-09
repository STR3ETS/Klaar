<div class="space-y-6">
    <form wire:submit="save" class="space-y-6">

        {{-- Basisgegevens --}}
        <div class="bg-ink rounded-sm border border-ink-70/20 p-6 space-y-5" style="background-image: radial-gradient(circle, rgba(255,180,0,0.05) 1px, transparent 1px); background-size: 20px 20px;">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-md bg-amber/10 flex items-center justify-center">
                    <i class="fa-solid fa-file-lines text-amber text-sm"></i>
                </div>
                <h3 class="font-heading font-semibold text-paper text-sm">Basisgegevens</h3>
            </div>

            <div>
                <label for="title" class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-2">Titel *</label>
                <input
                    type="text"
                    id="title"
                    wire:model="title"
                    placeholder="Bijv. Dagwerk badkamer renovatie"
                    class="klaar-dark-input w-full"
                >
                @error('title') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="entryDate" class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-2">Datum *</label>
                <input
                    type="date"
                    id="entryDate"
                    wire:model="entryDate"
                    class="klaar-dark-input w-full"
                >
                @error('entryDate') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="clientId" class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-2">Klant</label>
                    <select id="clientId" wire:model="clientId" class="klaar-dark-input w-full">
                        <option value="">Geen klant</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="projectId" class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-2">Project</label>
                    <select id="projectId" wire:model="projectId" class="klaar-dark-input w-full">
                        <option value="">Geen project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="description" class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-2">Beschrijving</label>
                <textarea
                    id="description"
                    wire:model="description"
                    rows="3"
                    placeholder="Optioneel: beschrijf wat je gedaan hebt"
                    class="klaar-dark-input w-full resize-none"
                ></textarea>
            </div>
        </div>

        {{-- Regelitems --}}
        <div class="bg-ink rounded-sm border border-ink-70/20 p-6 space-y-5" style="background-image: radial-gradient(circle, rgba(255,180,0,0.05) 1px, transparent 1px); background-size: 20px 20px;">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-md bg-amber/10 flex items-center justify-center">
                        <i class="fa-solid fa-clipboard-list text-amber text-sm"></i>
                    </div>
                    <h3 class="font-heading font-semibold text-paper text-sm">Regelitems</h3>
                </div>
                <button
                    type="button"
                    wire:click="addLineItem"
                    class="inline-flex items-center gap-1.5 text-sm font-heading font-semibold text-amber hover:text-amber/80 transition"
                >
                    <i class="fa-solid fa-plus text-xs"></i>
                    Regel toevoegen
                </button>
            </div>

            <div class="space-y-4">
                @foreach($lineItems as $index => $item)
                    <div class="relative bg-ink-90/60 rounded-sm p-4 space-y-3 border border-ink-70/15">
                        @if(count($lineItems) > 1)
                            <button
                                type="button"
                                wire:click="removeLineItem({{ $index }})"
                                class="absolute top-3 right-3 w-7 h-7 text-ink-50 hover:text-red-400 transition flex items-center justify-center"
                                title="Verwijder regel"
                            >
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>
                        @endif

                        <div>
                            <label class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-1.5">Omschrijving *</label>
                            <input
                                type="text"
                                wire:model="lineItems.{{ $index }}.description"
                                placeholder="Bijv. Tegels zetten badkamer"
                                class="klaar-dark-input w-full"
                            >
                            @error("lineItems.{$index}.description") <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-1.5">Aantal *</label>
                                <input
                                    type="number"
                                    wire:model="lineItems.{{ $index }}.quantity"
                                    step="0.01"
                                    min="0.01"
                                    class="klaar-dark-input w-full font-mono"
                                >
                                @error("lineItems.{$index}.quantity") <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-1.5">Eenheid *</label>
                                <select
                                    wire:model="lineItems.{{ $index }}.unit"
                                    class="klaar-dark-input w-full"
                                >
                                    <option value="uur">Uur</option>
                                    <option value="stuk">Stuk</option>
                                    <option value="m2">m&sup2;</option>
                                    <option value="m1">m&sup1;</option>
                                    <option value="dag">Dag</option>
                                    <option value="post">Post</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-1.5">Prijs *</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-ink-50">&euro;</span>
                                    <input
                                        type="number"
                                        wire:model="lineItems.{{ $index }}.unit_price"
                                        step="0.01"
                                        min="0"
                                        placeholder="0,00"
                                        class="klaar-dark-input w-full pl-7 font-mono"
                                    >
                                </div>
                                @error("lineItems.{$index}.unit_price") <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-1.5">BTW %</label>
                                <select
                                    wire:model="lineItems.{{ $index }}.btw_rate"
                                    class="klaar-dark-input w-full font-mono"
                                >
                                    <option value="21">21%</option>
                                    <option value="9">9%</option>
                                    <option value="0">0%</option>
                                </select>
                            </div>
                        </div>

                        {{-- Regeltotaal --}}
                        @php
                            $lineTotal = (float)($item['quantity'] ?? 0) * (float)($item['unit_price'] ?? 0);
                        @endphp
                        @if($lineTotal > 0)
                            <div class="text-right text-sm font-mono font-medium text-amber">
                                &euro;{{ number_format($lineTotal, 2, ',', '.') }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Totaal --}}
            @php
                $total = collect($lineItems)->sum(fn($i) => (float)($i['quantity'] ?? 0) * (float)($i['unit_price'] ?? 0));
                $totalBtw = collect($lineItems)->sum(fn($i) => (float)($i['quantity'] ?? 0) * (float)($i['unit_price'] ?? 0) * ((float)($i['btw_rate'] ?? 21) / 100));
            @endphp
            @if($total > 0)
                <div class="pt-4 border-t border-ink-70/20 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-heading text-ink-50">Subtotaal excl. BTW</span>
                        <span class="font-mono text-sm text-ink-30">&euro;{{ number_format($total, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-heading text-ink-50">BTW</span>
                        <span class="font-mono text-sm text-ink-30">&euro;{{ number_format($totalBtw, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-ink-70/20">
                        <span class="text-sm font-heading font-semibold text-paper/70">Totaal incl. BTW</span>
                        <span class="font-mono text-xl font-bold text-amber">&euro;{{ number_format($total + $totalBtw, 2, ',', '.') }}</span>
                    </div>
                </div>
            @endif
        </div>

        {{-- Knoppen --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('werkbonnen.show', $entry) }}" wire:navigate class="border border-ink-70/30 px-5 py-2.5 text-ink-50 font-semibold text-sm font-heading rounded-sm transition hover:bg-ink-90 hover:text-paper">
                Annuleren
            </a>
            <button
                type="submit"
                class="border border-amber bg-amber px-5 py-2.5 text-ink font-semibold text-sm font-heading rounded-sm transition hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)] disabled:opacity-50"
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
