<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class, 'plan_features')
            ->withPivot('limit_value')
            ->withTimestamps();
    }

    public function subThemes()
    {
        return $this->belongsToMany(SubTheme::class, 'plan_sub_themes')
            ->withTimestamps();
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
