<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubTheme extends Model
{
    use HasFactory;

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }
}
