<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $casts = [
        'address' => 'array',
        'nearby' => 'array',
        'facilities' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}
