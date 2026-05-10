<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'hotel_id',
        'room_type_id',
        'author_id',

        'room_type',
        'room_slug',
        'post_title',
        'post_content',
        'thumbnail_id',
        'gallery',
        'facilities',
        'extra_services',

        'total_rooms',
        'number_of_bed',
        'max_adults',
        'max_children',
        'room_footage',

        'base_price',
        'member_price',
        'price_per_day',
        'discount',
        'coupon',
        'tax',

        'check_in',
        'check_out',
        'day',

        'is_base',
        'status',
        'coupon',
    ];

    protected $casts = [
        'gallery'      => 'array',
        'facilities'   => 'array',
        'extra_services' => 'array',
        'check_in'     => 'date',
        'check_out'    => 'date',
        'is_base'      => 'boolean',
        'base_price'   => 'decimal:2',
        'member_price' => 'decimal:2',
        'price_per_day' => 'decimal:2',
        'discount'     => 'decimal:2',
        'tax'          => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
    public function media()
    {
        return $this->hasMany(Media::class);
    }

    public function thumbnail()
    {
        return $this->belongsTo(Media::class, 'thumbnail_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function orders()
    {
        return $this->hasMany(RoomOrder::class, 'room_id');
    }

    public function availabilities()
    {
        return $this->hasMany(RoomAvailability::class, 'post_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFinalPriceAttribute(): float
    {
        $price = $this->price_per_day ?: $this->base_price;

        if ($this->discount > 0) {
            $price -= ($price * $this->discount) / 100;
        }

        if ($this->tax > 0) {
            $price += ($price * $this->tax) / 100;
        }

        return round($price, 2);
    }
}
