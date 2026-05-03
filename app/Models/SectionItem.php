<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionItem extends Model
{
    use HasFactory;

    protected $casts = [
        'content' => 'array',
    ];

    protected $fillable = ['section_id','type','content','position','media_id'];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}
