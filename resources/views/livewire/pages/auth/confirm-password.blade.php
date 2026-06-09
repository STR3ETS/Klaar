<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $password = '';

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="max-w-md mx-auto bg-paper border border-ink-70/20 rounded-sm shadow-xl px-8 py-12">
    <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-full bg-amber/10 flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-lock text-amber text-xl"></i>
        </div>
        <h1 class="font-display text-3xl text-ink uppercase">Wachtwoord bevestigen</h1>
        <p class="text-sm text-ink-50 mt-2 leading-relaxed">Dit is een beveiligd gedeelte. Bevestig je wachtwoord om verder te gaan.</p>
    </div>

    <form wire:submit="confirmPassword">
        <!-- Password -->
        <div>
            <x-input-label for="password" value="Wachtwoord" />
            <x-text-input wire:model="password"
                          id="password"
                          class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="current-password"
                          placeholder="Je wachtwoord" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3">
                Bevestigen
            </x-primary-button>
        </div>
    </form>
</div>
