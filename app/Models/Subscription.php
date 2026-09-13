<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function isActive(): bool
    {
        if ($this->status === 'trialing' && $this->trial_ends_at && $this->trial_ends_at->isFuture()) {
            return true;
        }

        if ($this->status === 'active') {
            return is_null($this->ends_at) || $this->ends_at->isFuture();
        }

        return false;
    }
}
