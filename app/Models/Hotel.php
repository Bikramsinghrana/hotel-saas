<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\HotelStatusEnum;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'author_id',
        'name',
        'hotel_slug',
        'description',
        'rating',
        'status',
        'address',
        'nearby',
        'base_price',
        'discount',
        'tax',
        'thumbnail_id',
        'gallery',
        'facilities',
        'policies',
    ];

    protected $casts = [
        'status' => HotelStatusEnum::class,
        'address' => 'array',
        'nearby' => 'array',
        'facilities' => 'array',
        'policies' => 'array',
        'gallery' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Polymorphic relationship for all media (images, galleries, etc.)
     */
    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}
