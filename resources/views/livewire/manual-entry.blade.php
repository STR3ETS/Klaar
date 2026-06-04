<div class="space-y-6">
    <form wire:submit="save" class="space-y-6">

        {{-- Basic info --}}
        <div class="bg-snow rounded-lg border border-ink-10/50 shadow-sm p-6 space-y-5">
            <h3 class="font-heading font-semibold text-ink">Basisgegevens</h3>

            <div>
                <label for="title" class="block text-sm font-medium text-ink-70 mb-1.5">Titel *</label>
                <input
                    type="text"
                    id="title"
                    wire:model="title"
                    placeholder="Bijv. Dagwerk badkamer renovatie"
                    class="w-full rounded-md border border-ink-30 bg-snow px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-30 focus:border-amber focus:ring-2 focus:ring-amber/30 focus:outline-none"
                >
                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="entryDate" class="block text-sm font-medium text-ink-70 mb-1.5">Datum *</label>
                <input
                    type="date"
                    id="entryDate"
                    wire:model="entryDate"
                    class="w-full rounded-md border border-ink-30 bg-snow px-3.5 py-2.5 text-sm text-ink focus:border-amber focus:ring-2 focus:ring-amber/30 focus:outline-none"
                >
                @error('entryDate') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-ink-70 mb-1.5">Beschrijving</label>
                <textarea
                    id="description"
                    wire:model="description"
                    rows="3"
                    placeholder="Optioneel: beschrijf wat je gedaan hebt"
                    class="w-full rounded-md border border-ink-30 bg-snow px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-30 focus:border-amber focus:ring-2 focus:ring-amber/30 focus:outline-none resize-none"
                ></textarea>
            </div>
        </div>

        {{-- Line items --}}
        <div class="bg-snow rounded-lg border border-ink-10/50 shadow-sm p-6 space-y-5">
            <div class="flex items-center justify-between">
                <h3 class="font-heading font-semibold text-ink">Regelitems</h3>
                <button
                    type="button"
                    wire:click="addLineItem"
                    class="inline-flex items-center gap-1 text-sm font-medium text-amber hover:text-amber/80 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Regel toevoegen
                </button>
            </div>

            <div class="space-y-4">
                @foreach($lineItems as $index => $item)
                    <div class="relative bg-paper/50 rounded-lg p-4 space-y-3">
                        {{-- Remove button --}}
                        @if(count($lineItems) > 1)
                            <button
                                type="button"
                                wire:click="removeLineItem({{ $index }})"
                                class="absolute top-3 right-3 w-7 h-7 text-ink-30 hover:text-red-500 transition flex items-center justify-center"
                                title="Verwijder regel"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        @endif

                        <div>
                            <label class="block text-xs font-medium text-ink-50 mb-1">Omschrijving *</label>
                            <input
                                type="text"
                                wire:model="lineItems.{{ $index }}.description"
                                placeholder="Bijv. Tegels zetten badkamer"
                                class="w-full rounded-md border border-ink-30 bg-snow px-3 py-2 text-sm text-ink placeholder:text-ink-30 focus:border-amber focus:ring-2 focus:ring-amber/30 focus:outline-none"
                            >
                            @error("lineItems.{$index}.description") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-ink-50 mb-1">Aantal *</label>
                                <input
                                    type="number"
                                    wire:model="lineItems.{{ $index }}.quantity"
                                    step="0.01"
                                    min="0.01"
                                    class="w-full rounded-md border border-ink-30 bg-snow px-3 py-2 text-sm text-ink font-mono focus:border-amber focus:ring-2 focus:ring-amber/30 focus:outline-none"
                                >
                                @error("lineItems.{$index}.quantity") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-ink-50 mb-1">Eenheid *</label>
                                <select
                                    wire:model="lineItems.{{ $index }}.unit"
                                    class="w-full rounded-md border border-ink-30 bg-snow px-3 py-2 text-sm text-ink focus:border-amber focus:ring-2 focus:ring-amber/30 focus:outline-none"
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
                                <label class="block text-xs font-medium text-ink-50 mb-1">Prijs *</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-ink-50">&euro;</span>
                                    <input
                                        type="number"
                                        wire:model="lineItems.{{ $index }}.unit_price"
                                        step="0.01"
                                        min="0"
                                        placeholder="0,00"
                                        class="w-full rounded-md border border-ink-30 bg-snow pl-7 pr-3 py-2 text-sm text-ink font-mono focus:border-amber focus:ring-2 focus:ring-amber/30 focus:outline-none"
                                    >
                                </div>
                                @error("lineItems.{$index}.unit_price") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Line total --}}
                        @php
                            $lineTotal = (float)($item['quantity'] ?? 0) * (float)($item['unit_price'] ?? 0);
                        @endphp
                        @if($lineTotal > 0)
                            <div class="text-right text-sm font-mono font-medium text-ink-70">
                                &euro;{{ number_format($lineTotal, 2, ',', '.') }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Total --}}
            @php
                $total = collect($lineItems)->sum(fn($i) => (float)($i['quantity'] ?? 0) * (float)($i['unit_price'] ?? 0));
            @endphp
            @if($total > 0)
                <div class="flex items-center justify-between pt-4 border-t border-ink-10">
                    <span class="text-sm font-medium text-ink-70">Totaal (excl. BTW)</span>
                    <span class="font-mono text-lg font-bold text-ink">&euro;{{ number_format($total, 2, ',', '.') }}</span>
                </div>
            @endif
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-snow border border-ink-10 text-ink-70 text-sm font-medium rounded-md hover:bg-ink-10/50 transition">
                Annuleren
            </a>
            <button
                type="submit"
                class="px-5 py-2.5 bg-amber text-ink text-sm font-semibold rounded-md hover:bg-amber/90 transition shadow-sm disabled:opacity-50"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="save">Opslaan als concept</span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Opslaan...
                </span>
            </button>
        </div>
    </form>
</div>
