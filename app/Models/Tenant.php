<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'theme_config' => 'array',
        'settings'     => 'array',
        'address'      => 'array',
    ];

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

    public function subTheme()
    {
        return $this->belongsTo(SubTheme::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->whereIn('status', ['active', 'trialing'])
            ->latest('id');
    }

    public function featureOverrides()
    {
        return $this->hasMany(TenantFeatureOverride::class);
    }

    public function subThemeAccesses()
    {
        return $this->hasMany(TenantSubThemeAccess::class);
    }

    public function hotels()
    {
        return $this->hasMany(Hotel::class);
    }

    public function navigations()
    {
        return $this->hasMany(Navigation::class);
    }
}
