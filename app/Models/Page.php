<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $casts = [
        'settings' => 'array',
    ];

    protected $fillable = ['tenant_id','slug','title','type','status','settings'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('position');
    }
}
