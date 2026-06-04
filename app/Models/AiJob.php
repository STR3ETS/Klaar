<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiJob extends Model
{
    protected $table = 'ai_jobs';

    protected $fillable = [
        'entry_id', 'type', 'status', 'provider', 'input_path',
        'output', 'tokens_used', 'cost', 'error', 'started_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'output' => 'array',
            'tokens_used' => 'integer',
            'cost' => 'decimal:4',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }
}
