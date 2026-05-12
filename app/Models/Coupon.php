<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'hotel_id',
        'code',
        'title',
        'description',
        'image',
        'discount_type',
        'discount_value',
        'start_date',
        'expire_date',
        'type',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expire_date' => 'date',
        'status' => 'boolean',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isActive()
    {
        $now = now()->startOfDay();
        return $this->status && 
               (!$this->start_date || $this->start_date <= $now) && 
               (!$this->expire_date || $this->expire_date >= $now);
    }
}
