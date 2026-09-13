<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantFeatureOverride extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }
}
