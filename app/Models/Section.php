<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $casts = [
        'settings' => 'array',
    ];

    protected $fillable = ['page_id','key','name','position','settings'];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function items()
    {
        return $this->hasMany(SectionItem::class)->orderBy('position');
    }
}
