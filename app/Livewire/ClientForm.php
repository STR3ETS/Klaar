<?php

namespace App\Livewire;

use App\Models\Client;
use Livewire\Component;

class ClientForm extends Component
{
    public ?Client $client = null;

    public string $type = 'particulier';
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $company = '';
    public string $address_street = '';
    public string $address_housenumber = '';
    public string $address_postcode = '';
    public string $address_city = '';
    public string $address_country = 'NL';
    public string $kvk_number = '';
    public string $btw_number = '';
    public string $notes = '';
    public bool $isSaving = false;

    public function mount(?Client $client = null)
    {
        if ($client && $client->exists) {
            $workspace = auth()->user()->currentWorkspace();
            abort_unless($client->workspace_id === $workspace->id, 403);

            $this->client = $client;
            $this->type = $client->type ?? 'particulier';
            $this->name = $client->name ?? '';
            $this->email = $client->email ?? '';
            $this->phone = $client->phone ?? '';
            $this->company = $client->company ?? '';
            $this->address_street = $client->address_street ?? '';
            $this->address_housenumber = $client->address_housenumber ?? '';
            $this->address_postcode = $client->address_postcode ?? '';
            $this->address_city = $client->address_city ?? '';
            $this->address_country = $client->address_country ?? 'NL';
            $this->kvk_number = $client->kvk_number ?? '';
            $this->btw_number = $client->btw_number ?? '';
            $this->notes = $client->notes ?? '';
        }
    }

    public function fillFromVoice($data)
    {
        if (!empty($data['type'])) $this->type = $data['type'];
        if (!empty($data['name'])) $this->name = $data['name'];
        if (!empty($data['email'])) $this->email = $data['email'];
        if (!empty($data['phone'])) $this->phone = $data['phone'];
        if (!empty($data['company'])) $this->company = $data['company'];
        if (!empty($data['address_street'])) $this->address_street = $data['address_street'];
        if (!empty($data['address_housenumber'])) $this->address_housenumber = $data['address_housenumber'];
        if (!empty($data['address_postcode'])) $this->address_postcode = $data['address_postcode'];
        if (!empty($data['address_city'])) $this->address_city = $data['address_city'];
        if (!empty($data['kvk_number'])) $this->kvk_number = $data['kvk_number'];
        if (!empty($data['btw_number'])) $this->btw_number = $data['btw_number'];
        if (!empty($data['notes'])) $this->notes = $data['notes'];
    }

    public function save()
    {
        $this->validate([
            'type' => 'required|in:particulier,zakelijk',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'address_street' => 'nullable|string|max:255',
            'address_housenumber' => 'nullable|string|max:10',
            'address_postcode' => 'nullable|string|max:10',
            'address_city' => 'nullable|string|max:255',
            'address_country' => 'nullable|string|max:2',
            'kvk_number' => 'nullable|string|max:20',
            'btw_number' => 'nullable|string|max:30',
            'notes' => 'nullable|string',
        ]);

        $this->isSaving = true;

        $data = [
            'type' => $this->type,
            'name' => $this->name,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'company' => $this->company ?: null,
            'address_street' => $this->address_street ?: null,
            'address_housenumber' => $this->address_housenumber ?: null,
            'address_postcode' => $this->address_postcode ?: null,
            'address_city' => $this->address_city ?: null,
            'address_country' => $this->address_country ?: 'NL',
            'kvk_number' => $this->kvk_number ?: null,
            'btw_number' => $this->btw_number ?: null,
            'notes' => $this->notes ?: null,
        ];

        if ($this->client && $this->client->exists) {
            $this->client->update($data);
            $client = $this->client;
        } else {
            $workspace = auth()->user()->currentWorkspace();
            $data['workspace_id'] = $workspace->id;
            $client = Client::create($data);
        }

        $this->isSaving = false;

        return redirect()->route('clients.show', $client);
    }

    public function render()
    {
        return view('livewire.client-form');
    }
}
