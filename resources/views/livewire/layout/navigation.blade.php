<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="flex items-center">
    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-ink-70 hover:text-ink focus:outline-none transition cursor-pointer">
                <div class="w-7 h-7 rounded-full bg-amber/20 flex items-center justify-center">
                    <span class="text-xs font-bold text-amber">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                </div>
                <i class="fa-solid fa-chevron-down text-[10px] text-ink-30"></i>
            </button>
        </x-slot>

        <x-slot name="content">
            <div class="px-4 py-2 border-b border-ink-10">
                <p class="text-sm font-semibold text-ink" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></p>
                <p class="text-xs text-ink-50">{{ auth()->user()->email }}</p>
            </div>

            <x-dropdown-link :href="route('profile')" wire:navigate>
                <i class="fa-solid fa-user text-ink-30 text-xs mr-2"></i> Profiel
            </x-dropdown-link>

            <button wire:click="logout" class="w-full text-start">
                <x-dropdown-link>
                    <i class="fa-solid fa-right-from-bracket text-ink-30 text-xs mr-2"></i> Uitloggen
                </x-dropdown-link>
            </button>
        </x-slot>
    </x-dropdown>
</div>
