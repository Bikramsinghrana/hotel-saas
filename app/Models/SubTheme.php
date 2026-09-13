<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubTheme extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'config_schema' => 'array',
        'is_premium' => 'boolean',
    ];

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'plan_sub_themes')
            ->withTimestamps();
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    public function tenantAccesses()
    {
        return $this->hasMany(TenantSubThemeAccess::class);
    }
}
