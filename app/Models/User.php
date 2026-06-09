<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name', 'email', 'password', 'phone', 'company_name',
    'kvk_number', 'btw_number', 'address_street', 'address_housenumber',
    'address_postcode', 'address_city', 'address_country', 'plan',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'trial_ends_at' => 'datetime',
        ];
    }

    public function workspaces(): HasMany
    {
        return $this->hasMany(Workspace::class);
    }

    public function currentWorkspace(): Workspace
    {
        return $this->workspaces()->firstOrCreate(
            [],
            ['name' => $this->company_name ?? $this->name]
        );
    }

    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function onFreePlan(): bool
    {
        return $this->plan === 'free' && !$this->onTrial();
    }

    public function fullAddress(): string
    {
        return collect([
            trim(($this->address_street ?? '') . ' ' . ($this->address_housenumber ?? '')),
            trim(($this->address_postcode ?? '') . ' ' . ($this->address_city ?? '')),
        ])->filter()->implode(', ');
    }
}
