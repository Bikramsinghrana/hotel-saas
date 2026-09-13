<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantSubThemeAccess extends Model
{
    use HasFactory;

    protected $table = 'tenant_sub_theme_access';
    protected $guarded = [];

    protected $casts = [
        'is_allowed' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function subTheme()
    {
        return $this->belongsTo(SubTheme::class);
    }
}
