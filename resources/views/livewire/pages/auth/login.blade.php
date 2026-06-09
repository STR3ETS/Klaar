<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex items-stretch border border-ink-70/20 rounded-sm shadow-xl overflow-hidden">
    {{-- Vakman afbeelding (alleen desktop) --}}
    <div class="hidden lg:flex w-1/2 bg-paper/5 border-l border-ink-70/10 overflow-hidden items-center justify-center relative">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle, rgba(255,180,0,0.06) 1px, transparent 1px); background-size: 20px 20px;"></div>
        <img src="/vakman/vakman_login_transparant.png" alt="Vakman" class="absolute -bottom-24 z-10 h-[110%] w-auto object-contain drop-shadow-2xl" />
    </div>
    {{-- Form --}}
    <div class="w-full lg:w-1/2 px-8 py-12 bg-paper">
        <div class="text-center mb-8">
            <h1 class="font-display text-3xl text-ink uppercase">Inloggen</h1>
            <p class="text-sm text-ink-50 mt-2">Welkom terug. Log in om verder te gaan.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form wire:submit="login">
            <!-- Email Address -->
            <div>
                <x-input-label for="email" value="E-mailadres" />
                <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" placeholder="naam@bedrijf.nl" />
                <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" value="Wachtwoord" />
                <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password"
                                placeholder="Je wachtwoord" />
                <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
            </div>

            <!-- Remember Me + Forgot -->
            <div class="flex items-center justify-between mt-4">
                <label for="remember" class="inline-flex items-center">
                    <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-ink-30 text-amber shadow-sm focus:ring-amber" name="remember">
                    <span class="ms-2 text-sm text-ink-50">Onthoud mij</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-amber hover:underline" href="{{ route('password.request') }}" wire:navigate>
                        Wachtwoord vergeten?
                    </a>
                @endif
            </div>

            <div class="mt-6">
                <x-primary-button class="w-full justify-center py-3">
                    Inloggen
                </x-primary-button>
            </div>
        </form>

        <div class="mt-6 pt-6 border-t border-ink-10 text-center">
            <p class="text-sm text-ink-50">Nog geen account? <a href="{{ route('register') }}" class="text-amber font-semibold hover:underline" wire:navigate>Gratis proberen</a></p>
        </div>
    </div>
</div>
