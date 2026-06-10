<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id','room_order_id','order_number','amount','currency',
        'payment_method','gateway','transaction_id','status','payment_response','paid_at'
    ];

    protected $dates = ['paid_at'];

    public function order()
    {
        return $this->belongsTo(RoomOrder::class, 'room_order_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
