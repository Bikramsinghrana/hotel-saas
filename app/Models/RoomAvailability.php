<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoomAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'post_id',
        'hotel_id',

        'total_room',
        'adult_number',
        'child_number',

        'check_in',
        'check_out',

        'number',
        'price',

        'booked',
        'status',
        'is_base',
    ];

    protected $casts = [
        'check_in'   => 'date',
        'check_out'  => 'date',
        'price'      => 'decimal:2',
        'booked'     => 'integer',
        'is_base'    => 'boolean',
        'total_room' => 'integer',
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

    public function room()
    {
        return $this->belongsTo(Room::class, 'post_id');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
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

    public function scopeAvailable($query)
    {
        return $query->whereColumn('booked', '<', 'total_room');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getAvailableRoomAttribute(): int
    {
        return max(0, ($this->total_room ?? 0) - ($this->booked ?? 0));
    }
}
