<?php

namespace App\Models;

use App\Enums\TermTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Term extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'slug',
        'title',
        'type',
        'price',
        'price_type',
        'description',
    ];

    protected $casts = [
        'type' => TermTypeEnum::class,
        'price' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
