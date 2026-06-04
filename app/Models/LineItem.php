<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineItem extends Model
{
    protected $fillable = [
        'entry_id', 'invoice_id', 'description', 'quantity',
        'unit', 'unit_price', 'btw_rate', 'total', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'btw_rate' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function calculateTotal(): float
    {
        return round($this->quantity * $this->unit_price, 2);
    }

    public function btwAmount(): float
    {
        return round($this->calculateTotal() * ($this->btw_rate / 100), 2);
    }
}
