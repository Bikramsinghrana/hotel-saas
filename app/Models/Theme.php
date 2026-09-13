<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_core' => 'boolean',
    ];

    public function subThemes()
    {
        return $this->hasMany(SubTheme::class);
    }

    public function features()
    {
        return $this->hasMany(Feature::class);
    }

    public function plans()
    {
        return $this->hasMany(Plan::class);
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }
}
