<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'plan_features')
            ->withPivot('limit_value')
            ->withTimestamps();
    }

    public function tenantOverrides()
    {
        return $this->hasMany(TenantFeatureOverride::class);
    }
}
