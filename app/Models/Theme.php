<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'name',
        'status',
        'description',
    ];

    public function subThemes()
    {
        return $this->hasMany(SubTheme::class);
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }
}
