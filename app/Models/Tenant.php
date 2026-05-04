<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    /**
     * Allow all columns to be saved (no mass-assignment protection issues).
     * Direct property assignment e.g. $tenant->theme_id = 1 always works,
     * but $guarded = [] also allows fill() and update() calls throughout the app.
     */
    protected $guarded = [];

    protected $casts = [
        'settings' => 'array',
        'address'  => 'array',
    ];

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

    public function subTheme()
    {
        return $this->belongsTo(SubTheme::class);
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
