<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Entry;
use App\Models\LineItem;
use Livewire\Component;

class EditEntry extends Component
{
    public Entry $entry;

    public string $title = '';
    public string $description = '';
    public string $entryDate = '';
    public ?int $clientId = null;
    public ?int $projectId = null;
    public array $lineItems = [];
    public bool $isSaving = false;

    public function mount(Entry $entry)
    {
        $workspace = auth()->user()->currentWorkspace();
        abort_unless($entry->workspace_id === $workspace->id, 403);
        abort_unless($entry->isDraft(), 403);

        $this->entry = $entry;
        $this->title = $entry->title ?? '';
        $this->entryDate = $entry->entry_date?->toDateString() ?? now()->toDateString();
        $this->clientId = $entry->client_id;
        $this->projectId = $entry->project_id;
        $this->description = $entry->ai_extracted_data['description']
            ?? $entry->ai_extracted_data['beschrijving']
            ?? '';

        $this->lineItems = $entry->lineItems->map(fn (LineItem $item) => [
            'id' => $item->id,
            'description' => $item->description,
            'quantity' => $item->quantity,
            'unit' => $item->unit,
            'unit_price' => $item->unit_price,
            'btw_rate' => $item->btw_rate,
        ])->toArray();

        if (empty($this->lineItems)) {
            $this->addLineItem();
        }
    }

    public function addLineItem()
    {
        $this->lineItems[] = [
            'id' => null,
            'description' => '',
            'quantity' => 1,
            'unit' => 'uur',
            'unit_price' => '',
            'btw_rate' => 21,
        ];
    }

    public function removeLineItem($index)
    {
        unset($this->lineItems[$index]);
        $this->lineItems = array_values($this->lineItems);

        if (empty($this->lineItems)) {
            $this->addLineItem();
        }
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'entryDate' => 'required|date',
            'clientId' => 'nullable|exists:clients,id',
            'projectId' => 'nullable|exists:projects,id',
            'lineItems' => 'required|array|min:1',
            'lineItems.*.description' => 'required|string|max:255',
            'lineItems.*.quantity' => 'required|numeric|min:0.01',
            'lineItems.*.unit' => 'required|string|max:20',
            'lineItems.*.unit_price' => 'required|numeric|min:0',
            'lineItems.*.btw_rate' => 'required|numeric|min:0|max:100',
        ]);

        $this->isSaving = true;

        // Update entry fields
        $aiData = $this->entry->ai_extracted_data ?? [];
        $aiData['beschrijving'] = $this->description;
        $aiData['description'] = $this->description;

        $this->entry->update([
            'title' => $this->title,
            'entry_date' => $this->entryDate,
            'client_id' => $this->clientId,
            'project_id' => $this->projectId,
            'ai_extracted_data' => $aiData,
        ]);

        // Sync line items: delete removed, update existing, create new
        $keepIds = collect($this->lineItems)->pluck('id')->filter()->toArray();
        $this->entry->lineItems()->whereNotIn('id', $keepIds)->delete();

        $totalAmount = 0;
        foreach ($this->lineItems as $i => $item) {
            $lineTotal = round((float) $item['quantity'] * (float) $item['unit_price'], 2);
            $totalAmount += $lineTotal;

            $data = [
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'unit_price' => $item['unit_price'],
                'btw_rate' => $item['btw_rate'],
                'total' => $lineTotal,
                'sort_order' => $i + 1,
            ];

            if (!empty($item['id'])) {
                LineItem::where('id', $item['id'])
                    ->where('entry_id', $this->entry->id)
                    ->update($data);
            } else {
                $this->entry->lineItems()->create($data);
            }
        }

        $this->entry->update(['total_amount' => $totalAmount]);

        $this->isSaving = false;

        return redirect()->route('werkbonnen.show', $this->entry);
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace();
        $clients = $workspace->clients()->orderBy('name')->get();
        $projects = $workspace->projects()->where('status', 'active')->orderBy('name')->get();

        return view('livewire.edit-entry', compact('clients', 'projects'));
    }
}
