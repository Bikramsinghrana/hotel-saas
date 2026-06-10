<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id','room_order_id','payment_id','invoice_number','amount','tax_amount','total_amount','pdf_path','issued_at'
    ];

    protected $dates = ['issued_at'];

    public function order()
    {
        return $this->belongsTo(RoomOrder::class, 'room_order_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
