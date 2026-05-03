<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    public function subThemes()
    {
        return $this->hasMany(SubTheme::class);
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }
}
