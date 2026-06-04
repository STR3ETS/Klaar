<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $fillable = [
        'documentable_type', 'documentable_id', 'type',
        'disk', 'path', 'original_name', 'mime_type', 'size', 'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'size' => 'integer',
        ];
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function fullPath(): string
    {
        return Storage::disk($this->disk)->path($this->path);
    }
}
