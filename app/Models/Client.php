<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'workspace_id', 'name', 'email', 'phone', 'company',
        'address_street', 'address_housenumber', 'address_postcode',
        'address_city', 'address_country', 'kvk_number', 'btw_number', 'notes',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function fullAddress(): string
    {
        return collect([
            trim("{$this->address_street} {$this->address_housenumber}"),
            trim("{$this->address_postcode} {$this->address_city}"),
        ])->filter()->implode(', ');
    }
}
