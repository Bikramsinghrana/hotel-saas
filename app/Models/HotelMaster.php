<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'type',
        'title',
        'content',
        'icon_or_image',
        'status',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
