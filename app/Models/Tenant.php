<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $casts = [
        'settings' => 'array',
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
}
