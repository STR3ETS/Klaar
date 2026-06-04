<?php

namespace App\Livewire;

use App\Models\Entry;
use App\Models\LineItem;
use Livewire\Component;

class ManualEntry extends Component
{
    public string $title = '';
    public string $description = '';
    public string $entryDate = '';
    public array $lineItems = [];
    public bool $isSaving = false;
    public ?int $entryId = null;

    public function mount()
    {
        $this->entryDate = now()->toDateString();
        $this->addLineItem();
    }

    public function addLineItem()
    {
        $this->lineItems[] = [
            'description' => '',
            'quantity' => 1,
            'unit' => 'uur',
            'unit_price' => '',
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

    public function getCalculatedTotalProperty()
    {
        return collect($this->lineItems)->sum(function ($item) {
            return (float) ($item['quantity'] ?? 0) * (float) ($item['unit_price'] ?? 0);
        });
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'entryDate' => 'required|date',
            'lineItems' => 'required|array|min:1',
            'lineItems.*.description' => 'required|string|max:255',
            'lineItems.*.quantity' => 'required|numeric|min:0.01',
            'lineItems.*.unit' => 'required|string|max:20',
            'lineItems.*.unit_price' => 'required|numeric|min:0',
        ]);

        $this->isSaving = true;

        $workspace = auth()->user()->currentWorkspace();

        $entry = Entry::create([
            'workspace_id' => $workspace->id,
            'type' => 'manual',
            'status' => 'draft',
            'title' => $this->title,
            'entry_date' => $this->entryDate,
            'ai_extracted_data' => [
                'beschrijving' => $this->description,
            ],
        ]);

        $totalAmount = 0;
        foreach ($this->lineItems as $i => $item) {
            $lineTotal = (float) $item['quantity'] * (float) $item['unit_price'];
            $totalAmount += $lineTotal;

            LineItem::create([
                'entry_id' => $entry->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'unit_price' => $item['unit_price'],
                'btw_rate' => 21.00,
                'total' => $lineTotal,
                'sort_order' => $i + 1,
            ]);
        }

        $entry->update(['total_amount' => $totalAmount]);

        $this->entryId = $entry->id;
        $this->isSaving = false;

        return redirect()->route('entries.show', $entry);
    }

    public function render()
    {
        return view('livewire.manual-entry');
    }
}
