<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 pt-8 pb-12 lg:pt-12 lg:pb-16">

        {{-- Header --}}
        <div class="flex items-center gap-4 mb-8 pt-2 lg:pt-0">
            <div class="w-12 h-12 rounded-full bg-amber/20 flex items-center justify-center shrink-0">
                <span class="text-lg font-display text-amber">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
            </div>
            <div>
                <h1 class="font-heading font-bold text-paper text-lg">{{ auth()->user()->name }}</h1>
                <p class="text-xs text-ink-50">{{ auth()->user()->email }}</p>
            </div>
        </div>

        <div class="space-y-4">
            {{-- Profielgegevens --}}
            <div class="bg-ink-90 rounded-sm border border-ink-70/15 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-ink-70/15 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-md bg-amber/10 flex items-center justify-center">
                        <i class="fa-solid fa-user text-amber text-[10px]"></i>
                    </div>
                    <span class="font-heading font-semibold text-paper text-sm">Profielgegevens</span>
                </div>
                <div class="p-5">
                    <div class="max-w-xl">
                        <livewire:profile.update-profile-information-form />
                    </div>
                </div>
            </div>

            {{-- Wachtwoord --}}
            <div class="bg-ink-90 rounded-sm border border-ink-70/15 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-ink-70/15 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-md bg-amber/10 flex items-center justify-center">
                        <i class="fa-solid fa-lock text-amber text-[10px]"></i>
                    </div>
                    <span class="font-heading font-semibold text-paper text-sm">Wachtwoord wijzigen</span>
                </div>
                <div class="p-5">
                    <div class="max-w-xl">
                        <livewire:profile.update-password-form />
                    </div>
                </div>
            </div>

            {{-- Account verwijderen --}}
            <div class="bg-ink-90 rounded-sm border border-ink-70/15 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-ink-70/15 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-md bg-red-500/10 flex items-center justify-center">
                        <i class="fa-solid fa-triangle-exclamation text-red-400 text-[10px]"></i>
                    </div>
                    <span class="font-heading font-semibold text-paper text-sm">Account verwijderen</span>
                </div>
                <div class="p-5">
                    <div class="max-w-xl">
                        <livewire:profile.delete-user-form />
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
