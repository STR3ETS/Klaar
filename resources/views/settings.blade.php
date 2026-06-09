<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 pt-8 pb-12 lg:pt-12 lg:pb-16">

        {{-- Header --}}
        <div class="flex items-center gap-4 mb-8 pt-2 lg:pt-0">
            <div class="w-12 h-12 rounded-full bg-amber/20 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-building text-amber text-lg"></i>
            </div>
            <div>
                <h1 class="font-heading font-bold text-paper text-lg">Bedrijfsinstellingen</h1>
                <p class="text-xs text-ink-50">Beheer je bedrijfsgegevens en facturatiedata</p>
            </div>
        </div>

        <div class="space-y-4">
            {{-- Bedrijfsgegevens --}}
            <div class="bg-ink-90 rounded-sm border border-ink-70/15 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-ink-70/15 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-md bg-amber/10 flex items-center justify-center">
                        <i class="fa-solid fa-building text-amber text-[10px]"></i>
                    </div>
                    <span class="font-heading font-semibold text-paper text-sm">Bedrijfsgegevens</span>
                </div>
                <div class="p-5">
                    <div class="max-w-xl">
                        <livewire:profile.update-company-information-form />
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
