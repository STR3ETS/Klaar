<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="max-w-md mx-auto bg-paper border border-ink-70/20 rounded-sm shadow-xl px-8 py-12">
    <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-full bg-amber/10 flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-envelope-circle-check text-amber text-xl"></i>
        </div>
        <h1 class="font-display text-3xl text-ink uppercase">E-mail verifi&euml;ren</h1>
        <p class="text-sm text-ink-50 mt-2 leading-relaxed">Bedankt voor je registratie! Klik op de link in de e-mail die we je zojuist hebben gestuurd om je account te verifi&euml;ren. Geen e-mail ontvangen? We sturen je graag een nieuwe.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-3 bg-amber/10 border border-amber/20 rounded-sm">
            <p class="text-sm text-ink-70">
                <i class="fa-solid fa-check text-amber text-[10px] mr-1.5"></i>
                Er is een nieuwe verificatielink naar je e-mailadres gestuurd.
            </p>
        </div>
    @endif

    <div class="flex items-center justify-between">
        <x-primary-button wire:click="sendVerification" class="py-3">
            Verificatiemail opnieuw versturen
        </x-primary-button>

        <button wire:click="logout" type="submit" class="text-sm text-amber hover:underline">
            Uitloggen
        </button>
    </div>
</div>
