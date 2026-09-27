<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'hotel_id',
        'code',
        'title',
        'description',
        'image',
        'discount_type',
        'discount_value',
        'start_date',
        'expire_date',
        'validity_type',
        'applicable_days',
        'start_time',
        'end_time',
        'time_slot',
        'min_spend',
        'usage_limit',
        'used_count',
        'type',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expire_date' => 'date',
        'applicable_days' => 'array',
        'min_spend' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Check if coupon is active based on date range, day of week, and time window.
     *
     * @param string|Carbon|null $date (e.g. check-in date or booking date)
     * @param string|Carbon|null $time (e.g. current booking time)
     * @return bool
     */
    public function isActive($date = null, $time = null): bool
    {
        if (!$this->status) {
            return false;
        }

        $dateObj = $date ? Carbon::parse($date) : now();

        // 1. Date Range Check
        if ($this->start_date && $this->start_date->startOfDay()->gt($dateObj->startOfDay())) {
            return false;
        }
        if ($this->expire_date && $this->expire_date->endOfDay()->lt($dateObj->startOfDay())) {
            return false;
        }

        // 2. Day-wise Applicability Check (Sunday, Monday, Tuesday, etc.)
        if (!empty($this->applicable_days) && is_array($this->applicable_days)) {
            $currentDay = strtolower($dateObj->format('l')); // 'sunday', 'monday', etc.
            $allowedDays = array_map('strtolower', $this->applicable_days);
            if (!in_array($currentDay, $allowedDays)) {
                return false;
            }
        }

        // 3. Time Window Check (e.g. 12:30 to 15:30)
        if (!empty($this->start_time) && !empty($this->end_time)) {
            $timeStr = $time ? Carbon::parse($time)->format('H:i:s') : now()->format('H:i:s');
            $startTimeStr = Carbon::parse($this->start_time)->format('H:i:s');
            $endTimeStr = Carbon::parse($this->end_time)->format('H:i:s');

            if ($timeStr < $startTimeStr || $timeStr > $endTimeStr) {
                return false;
            }
        }

        // 4. Usage Limit Check
        if (!is_null($this->usage_limit) && $this->usage_limit > 0 && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Human-friendly formatted applicable days
     */
    public function getDaysFormattedAttribute(): string
    {
        if (empty($this->applicable_days) || !is_array($this->applicable_days)) {
            return 'All Days';
        }

        if (count($this->applicable_days) === 7) {
            return 'All Days';
        }

        return implode(', ', array_map('ucfirst', $this->applicable_days));
    }

    /**
     * Human-friendly formatted time window
     */
    public function getTimeFormattedAttribute(): string
    {
        if (!empty($this->start_time) && !empty($this->end_time)) {
            return Carbon::parse($this->start_time)->format('h:i A') . ' - ' . Carbon::parse($this->end_time)->format('h:i A');
        }

        return 'All Day (24 Hours)';
    }

    /**
     * Determine validity mode (all, date, days, time, days_time, custom)
     */
    public function getValidityTypeAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        $hasDate = !empty($this->start_date) || !empty($this->expire_date);
        $hasDays = !empty($this->applicable_days) && count((array)$this->applicable_days) < 7;
        $hasTime = !empty($this->start_time) && !empty($this->end_time);

        if ($hasDate && $hasDays && $hasTime) return 'custom';
        if ($hasDays && $hasTime) return 'days_time';
        if ($hasDate) return 'date';
        if ($hasDays) return 'days';
        if ($hasTime) return 'time';

        return 'all';
    }
}
