<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ModuleStatusEnum;

class Sidebar extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'status' => ModuleStatusEnum::class,
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', ModuleStatusEnum::PUBLISHED);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
