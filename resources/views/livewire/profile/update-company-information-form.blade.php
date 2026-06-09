<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $company_name = '';
    public string $phone = '';
    public string $kvk_number = '';
    public string $btw_number = '';
    public string $address_street = '';
    public string $address_housenumber = '';
    public string $address_postcode = '';
    public string $address_city = '';
    public string $address_country = '';

    public function mount(): void
    {
        $user = Auth::user();
        $this->company_name = $user->company_name ?? '';
        $this->phone = $user->phone ?? '';
        $this->kvk_number = $user->kvk_number ?? '';
        $this->btw_number = $user->btw_number ?? '';
        $this->address_street = $user->address_street ?? '';
        $this->address_housenumber = $user->address_housenumber ?? '';
        $this->address_postcode = $user->address_postcode ?? '';
        $this->address_city = $user->address_city ?? '';
        $this->address_country = $user->address_country ?? 'NL';
    }

    public function updateCompanyInformation(): void
    {
        $validated = $this->validate([
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'kvk_number' => ['nullable', 'string', 'max:20'],
            'btw_number' => ['nullable', 'string', 'max:30'],
            'address_street' => ['nullable', 'string', 'max:255'],
            'address_housenumber' => ['nullable', 'string', 'max:20'],
            'address_postcode' => ['nullable', 'string', 'max:10'],
            'address_city' => ['nullable', 'string', 'max:255'],
            'address_country' => ['nullable', 'string', 'max:2'],
        ]);

        Auth::user()->update($validated);

        $this->dispatch('company-updated');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-paper">
            Bedrijfsgegevens
        </h2>
        <p class="mt-1 text-sm text-ink-50">
            Deze gegevens verschijnen op je facturen en offertes.
        </p>
    </header>

    <form wire:submit="updateCompanyInformation" class="mt-6 space-y-6">

        {{-- Bedrijf & telefoon --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="company_name" value="Bedrijfsnaam" />
                <x-text-input wire:model="company_name" id="company_name" type="text" class="mt-1 block w-full" autocomplete="organization" placeholder="Jouw Bedrijf B.V." />
                <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
            </div>

            <div>
                <x-input-label for="phone" value="Telefoon" />
                <x-text-input wire:model="phone" id="phone" type="tel" class="mt-1 block w-full" autocomplete="tel" placeholder="06 12345678" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>
        </div>

        {{-- Adres --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-2">
                <x-input-label for="address_street" value="Straat" />
                <x-text-input wire:model="address_street" id="address_street" type="text" class="mt-1 block w-full" autocomplete="address-line1" placeholder="Kerkstraat" />
                <x-input-error class="mt-2" :messages="$errors->get('address_street')" />
            </div>

            <div>
                <x-input-label for="address_housenumber" value="Huisnr." />
                <x-text-input wire:model="address_housenumber" id="address_housenumber" type="text" class="mt-1 block w-full" placeholder="12a" />
                <x-input-error class="mt-2" :messages="$errors->get('address_housenumber')" />
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <x-input-label for="address_postcode" value="Postcode" />
                <x-text-input wire:model="address_postcode" id="address_postcode" type="text" class="mt-1 block w-full" autocomplete="postal-code" placeholder="1234 AB" />
                <x-input-error class="mt-2" :messages="$errors->get('address_postcode')" />
            </div>

            <div class="col-span-2">
                <x-input-label for="address_city" value="Plaats" />
                <x-text-input wire:model="address_city" id="address_city" type="text" class="mt-1 block w-full" autocomplete="address-level2" placeholder="Amsterdam" />
                <x-input-error class="mt-2" :messages="$errors->get('address_city')" />
            </div>
        </div>

        {{-- Fiscaal --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="kvk_number" value="KVK-nummer" />
                <x-text-input wire:model="kvk_number" id="kvk_number" type="text" class="mt-1 block w-full" placeholder="12345678" />
                <x-input-error class="mt-2" :messages="$errors->get('kvk_number')" />
            </div>

            <div>
                <x-input-label for="btw_number" value="BTW-nummer" />
                <x-text-input wire:model="btw_number" id="btw_number" type="text" class="mt-1 block w-full" placeholder="NL123456789B01" />
                <x-input-error class="mt-2" :messages="$errors->get('btw_number')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Opslaan</x-primary-button>

            <x-action-message class="me-3" on="company-updated">
                Opgeslagen.
            </x-action-message>
        </div>
    </form>
</section>
