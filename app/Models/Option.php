<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Option extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'hotel_id',
        'group',
        'key',
        'value',
        'type',
        'label',
        'description',
        'options_list',
        'is_autoload',
        'is_public',
        'is_system',
        'status',
    ];

    protected $casts = [
        'options_list' => 'array',
        'is_autoload' => 'boolean',
        'is_public' => 'boolean',
        'is_system' => 'boolean',
        'status' => 'boolean',
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

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeAutoload($query)
    {
        return $query->where('is_autoload', true);
    }

    public function scopePublicOnly($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public function scopeForTenant($query, $tenantId = null, $hotelId = null)
    {
        return $query->where(function($q) use ($tenantId, $hotelId) {
            if ($hotelId) {
                $q->where('hotel_id', $hotelId);
            } elseif ($tenantId) {
                $q->where('tenant_id', $tenantId)->whereNull('hotel_id');
            } else {
                $q->whereNull('tenant_id')->whereNull('hotel_id');
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Dynamic Typed Value Accessor / Mutator
    |--------------------------------------------------------------------------
    */

    public function getTypedValueAttribute()
    {
        return self::castValue($this->value, $this->type);
    }

    /**
     * Cast raw database string value to its native PHP type
     */
    public static function castValue($value, string $type)
    {
        if (is_null($value)) {
            return null;
        }

        switch (strtolower($type)) {
            case 'number':
            case 'integer':
            case 'int':
                return is_numeric($value) ? (int)$value : 0;

            case 'float':
            case 'double':
            case 'decimal':
                return is_numeric($value) ? (float)$value : 0.0;

            case 'boolean':
            case 'bool':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);

            case 'json':
            case 'array':
                if (is_array($value)) return $value;
                $decoded = json_decode($value, true);
                return (json_last_error() === JSON_ERROR_NONE) ? $decoded : [];

            case 'string':
            default:
                return (string)$value;
        }
    }

    /**
     * Serialize native value for database storage
     */
    public static function prepareValue($value, string $type)
    {
        if (is_null($value)) {
            return null;
        }

        switch (strtolower($type)) {
            case 'boolean':
            case 'bool':
                return $value ? '1' : '0';

            case 'json':
            case 'array':
                return is_string($value) ? $value : json_encode($value);

            default:
                return (string)$value;
        }
    }
}
