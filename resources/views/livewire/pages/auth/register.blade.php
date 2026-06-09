<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public int $step = 1;

    // Stap 1: Vak
    public string $vak = '';

    // Stap 2: Pakket
    public string $pakket = '';

    // Stap 3: Account
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $bedrijfsnaam = '';

    public function selectVak(string $vak): void
    {
        $this->vak = $vak;
        $this->step = 2;
    }

    public function selectPakket(string $pakket): void
    {
        $this->pakket = $pakket;
        $this->step = 3;
    }

    public function prevStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'vak' => ['required', 'string'],
            'pakket' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'company_name' => $this->bedrijfsnaam ?: null,
            'plan' => $this->pakket,
        ]);

        event(new Registered($user));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    {{-- Stappen indicator --}}
    <div class="flex items-center justify-center gap-2 mb-10">
        @for ($i = 1; $i <= 3; $i++)
            <div class="h-1 rounded-full transition-all duration-300 {{ $i <= $step ? 'bg-amber w-10' : 'bg-paper/20 w-6' }}"></div>
        @endfor
    </div>

    {{-- ============================================ --}}
    {{-- STAP 1: Wat voor vakman ben je? --}}
    {{-- ============================================ --}}
    @if ($step === 1)
        <div class="text-center mb-10">
            <h1 class="font-display text-4xl text-paper uppercase tracking-wide">Wat voor vakman ben je?</h1>
            <p class="text-paper/60 mt-3">Kies wat het beste bij je past.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-w-2xl mx-auto">
            @php
                $vakken = [
                    ['id' => 'installateur', 'icon' => 'fa-screwdriver-wrench', 'label' => 'Installateur'],
                    ['id' => 'loodgieter', 'icon' => 'fa-faucet-drip', 'label' => 'Loodgieter'],
                    ['id' => 'elektricien', 'icon' => 'fa-bolt', 'label' => 'Elektricien'],
                    ['id' => 'schilder', 'icon' => 'fa-paint-roller', 'label' => 'Schilder'],
                    ['id' => 'aannemer', 'icon' => 'fa-helmet-safety', 'label' => 'Aannemer'],
                    ['id' => 'anders', 'icon' => 'fa-ellipsis', 'label' => 'Anders'],
                ];
            @endphp

            @foreach ($vakken as $item)
                <button
                    wire:click="selectVak('{{ $item['id'] }}')"
                    class="group bg-paper/5 hover:bg-paper border border-paper/10 hover:border-amber rounded-sm p-6 text-center transition-all cursor-pointer"
                >
                    <div class="w-12 h-12 rounded-full bg-amber/10 group-hover:bg-amber/20 flex items-center justify-center mx-auto mb-3 transition-colors">
                        <i class="fa-solid {{ $item['icon'] }} text-amber text-lg"></i>
                    </div>
                    <span class="text-sm font-semibold text-paper group-hover:text-ink transition-colors">{{ $item['label'] }}</span>
                </button>
            @endforeach
        </div>

        <div class="mt-10 text-center">
            <p class="text-sm text-paper/40">Al een account? <a href="{{ route('login') }}" class="text-amber hover:underline" wire:navigate>Inloggen</a></p>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- STAP 2: Kies je pakket --}}
    {{-- ============================================ --}}
    @if ($step === 2)
        <div class="text-center mb-6">
            <h1 class="font-display text-4xl text-paper uppercase tracking-wide">Kies je pakket</h1>
            <p class="text-paper/60 mt-3">Je kunt altijd nog wisselen.</p>
        </div>

        <div class="max-w-md mx-auto mb-8 bg-amber/10 border border-amber/25 rounded-sm px-5 py-3 flex items-center gap-3">
            <i class="fa-solid fa-gift text-amber text-lg shrink-0"></i>
            <p class="text-sm text-paper/80"><strong class="text-amber">14 dagen gratis</strong> &ndash; geen creditcard nodig. Je betaalt pas als je Klaar wilt houden.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-4 max-w-3xl mx-auto">

            {{-- ZZP --}}
            <button
                wire:click="selectPakket('zzp')"
                class="group bg-paper/5 hover:bg-paper border border-paper/10 hover:border-amber rounded-sm p-6 text-left transition-all cursor-pointer flex flex-col"
            >
                <div class="mb-4">
                    <h3 class="font-heading font-bold text-paper group-hover:text-ink text-lg transition-colors">ZZP</h3>
                    <p class="text-xs text-paper/40 group-hover:text-ink-50 transition-colors">Voor de zelfstandige vakman.</p>
                </div>
                <div class="flex items-baseline gap-1 mb-5">
                    <span class="font-display text-3xl text-amber">&euro;29</span>
                    <span class="text-xs text-paper/40 group-hover:text-ink-50 transition-colors">/ mnd</span>
                </div>
                <ul class="space-y-2 flex-1">
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-amber text-[9px] shrink-0"></i>
                        <span class="text-xs text-paper/60 group-hover:text-ink-70 transition-colors">Werkbonnen & facturen</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-amber text-[9px] shrink-0"></i>
                        <span class="text-xs text-paper/60 group-hover:text-ink-70 transition-colors">Fotoherkenning</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-amber text-[9px] shrink-0"></i>
                        <span class="text-xs text-paper/60 group-hover:text-ink-70 transition-colors">Urenregistratie</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-amber text-[9px] shrink-0"></i>
                        <span class="text-xs text-paper/60 group-hover:text-ink-70 transition-colors">50 inspraken / mnd</span>
                    </li>
                </ul>
            </button>

            {{-- Vakman (populair) --}}
            <button
                wire:click="selectPakket('vakman')"
                class="group bg-paper/10 hover:bg-paper border border-amber/30 hover:border-amber rounded-sm p-6 text-left transition-all cursor-pointer flex flex-col relative"
            >
                <div class="absolute top-3 right-3 bg-amber px-2 py-0.5 text-[9px] font-heading font-bold text-ink uppercase tracking-wider rounded-sm">Populair</div>
                <div class="mb-4">
                    <h3 class="font-heading font-bold text-paper group-hover:text-ink text-lg transition-colors">Vakman</h3>
                    <p class="text-xs text-paper/40 group-hover:text-ink-50 transition-colors">Alles uit Klaar halen.</p>
                </div>
                <div class="flex items-baseline gap-1 mb-5">
                    <span class="font-display text-3xl text-amber">&euro;49</span>
                    <span class="text-xs text-paper/40 group-hover:text-ink-50 transition-colors">/ mnd</span>
                </div>
                <ul class="space-y-2 flex-1">
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-amber text-[9px] shrink-0"></i>
                        <span class="text-xs text-paper/60 group-hover:text-ink-70 transition-colors">Alles van ZZP</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-amber text-[9px] shrink-0"></i>
                        <span class="text-xs text-paper/60 group-hover:text-ink-70 transition-colors">Onbeperkt inspraken</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-amber text-[9px] shrink-0"></i>
                        <span class="text-xs text-paper/60 group-hover:text-ink-70 transition-colors">Moneybird-koppeling</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-amber text-[9px] shrink-0"></i>
                        <span class="text-xs text-paper/60 group-hover:text-ink-70 transition-colors">Prioriteit support</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-amber text-[9px] shrink-0"></i>
                        <span class="text-xs text-paper/60 group-hover:text-ink-70 transition-colors">Klant- & projectbeheer</span>
                    </li>
                </ul>
            </button>

            {{-- Ploeg --}}
            <button
                wire:click="selectPakket('ploeg')"
                class="group bg-paper/5 hover:bg-paper border border-paper/10 hover:border-amber rounded-sm p-6 text-left transition-all cursor-pointer flex flex-col"
            >
                <div class="mb-4">
                    <h3 class="font-heading font-bold text-paper group-hover:text-ink text-lg transition-colors">Ploeg</h3>
                    <p class="text-xs text-paper/40 group-hover:text-ink-50 transition-colors">Voor teams vanaf 3 gebruikers.</p>
                </div>
                <div class="flex items-baseline gap-1 mb-5">
                    <span class="font-display text-3xl text-amber">&euro;44</span>
                    <span class="text-xs text-paper/40 group-hover:text-ink-50 transition-colors">/ gebruiker / mnd</span>
                </div>
                <ul class="space-y-2 flex-1">
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-amber text-[9px] shrink-0"></i>
                        <span class="text-xs text-paper/60 group-hover:text-ink-70 transition-colors">Alles van Vakman</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-amber text-[9px] shrink-0"></i>
                        <span class="text-xs text-paper/60 group-hover:text-ink-70 transition-colors">Teamoverzicht & planning</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-amber text-[9px] shrink-0"></i>
                        <span class="text-xs text-paper/60 group-hover:text-ink-70 transition-colors">Rapportages & export</span>
                    </li>
                </ul>
            </button>

        </div>

        <p class="text-center text-[11px] text-paper/30 mt-4">Alle prijzen excl. BTW</p>

        <div class="mt-6 text-center">
            <button wire:click="prevStep" class="text-sm text-paper/40 hover:text-amber transition-colors cursor-pointer">
                <i class="fa-solid fa-arrow-left text-[10px] mr-1"></i> Terug
            </button>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- STAP 3: Account aanmaken --}}
    {{-- ============================================ --}}
    @if ($step === 3)
        <div class="max-w-md mx-auto bg-paper border border-ink-70/20 rounded-sm shadow-xl px-8 py-12">
            <div class="text-center mb-8">
                <h1 class="font-display text-3xl text-ink uppercase">Bijna klaar.</h1>
                <p class="text-sm text-ink-50 mt-2">Nog even je gegevens en je kunt aan de slag.</p>

                {{-- Selectie badges --}}
                <div class="flex items-center justify-center gap-2 mt-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber/10 rounded-full text-xs text-ink-70">
                        <i class="fa-solid fa-circle-check text-amber text-[10px]"></i>
                        {{ ucfirst($vak) }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber/10 rounded-full text-xs text-ink-70">
                        <i class="fa-solid fa-circle-check text-amber text-[10px]"></i>
                        {{ $pakket === 'zzp' ? 'ZZP' : ($pakket === 'vakman' ? 'Vakman' : 'Ploeg') }}
                    </span>
                </div>
            </div>

            <form wire:submit="register">
                <!-- Naam -->
                <div>
                    <x-input-label for="name" value="Naam" />
                    <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" placeholder="Je volledige naam" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- E-mailadres -->
                <div class="mt-4">
                    <x-input-label for="email" value="E-mailadres" />
                    <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" placeholder="naam@bedrijf.nl" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Bedrijfsnaam -->
                <div class="mt-4">
                    <x-input-label for="bedrijfsnaam" value="Bedrijfsnaam (optioneel)" />
                    <x-text-input wire:model="bedrijfsnaam" id="bedrijfsnaam" class="block mt-1 w-full" type="text" name="bedrijfsnaam" autocomplete="organization" placeholder="Je bedrijfsnaam" />
                </div>

                <!-- Wachtwoord -->
                <div class="mt-4">
                    <x-input-label for="password" value="Wachtwoord" />
                    <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" placeholder="Minimaal 8 tekens" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Wachtwoord bevestigen -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" value="Wachtwoord bevestigen" />
                    <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Herhaal je wachtwoord" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="mt-6">
                    <x-primary-button class="w-full justify-center py-3">
                        Start 14 dagen gratis
                    </x-primary-button>
                </div>
            </form>

            {{-- Trust indicators --}}
            <div class="flex items-center justify-center gap-4 mt-6 flex-wrap">
                <div class="flex items-center gap-1.5">
                    <i class="fa-solid fa-check text-amber text-[10px]"></i>
                    <span class="text-xs text-ink-50">Geen creditcard nodig</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <i class="fa-solid fa-check text-amber text-[10px]"></i>
                    <span class="text-xs text-ink-50">Maandelijks opzegbaar</span>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-ink-10 flex items-center justify-between">
                <button wire:click="prevStep" class="text-sm text-ink-50 hover:text-amber transition-colors cursor-pointer">
                    <i class="fa-solid fa-arrow-left text-[10px] mr-1"></i> Terug
                </button>
                <p class="text-sm text-ink-50">Al een account? <a href="{{ route('login') }}" class="text-amber font-semibold hover:underline" wire:navigate>Inloggen</a></p>
            </div>
        </div>
    @endif
</div>
