<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomOrder extends Model
{
    use  SoftDeletes;

    protected $fillable = [

        'tenant_id',
        'hotel_id',
        'room_id',
        'user_id',

        'order_number',
        'order_type',

        'status',

        'start_date',
        'end_date',

        'total_person',
        'total_nights',

        'customer_name',
        'email',
        'phone',

        'address',

        'city',
        'state',
        'country',
        'postcode',

        'sub_total',
        'tax_amount',
        'discount_amount',
        'total_amount',

        'transaction_id',
        'payment_gateway',
        'payment_method',

        'payment_status',
        'paid_at',

        'payment_response',

        'notes',
        'special_request',

        'ip_address',
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

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
