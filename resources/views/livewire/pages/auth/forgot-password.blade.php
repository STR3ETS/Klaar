<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="max-w-md mx-auto bg-paper border border-ink-70/20 rounded-sm shadow-xl px-8 py-12">
    <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-full bg-amber/10 flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-envelope text-amber text-xl"></i>
        </div>
        <h1 class="font-display text-3xl text-ink uppercase">Wachtwoord vergeten</h1>
        <p class="text-sm text-ink-50 mt-2 leading-relaxed">Geen probleem. Vul je e-mailadres in en we sturen je een link om je wachtwoord te herstellen.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="E-mailadres" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus placeholder="naam@bedrijf.nl" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3">
                Herstelmail versturen
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 pt-6 border-t border-ink-10 text-center">
        <a href="{{ route('login') }}" class="text-sm text-amber hover:underline" wire:navigate>
            <i class="fa-solid fa-arrow-left text-[10px] mr-1"></i> Terug naar inloggen
        </a>
    </div>
</div>
