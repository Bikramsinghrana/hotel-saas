<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Format currency based on current locale or hotel setting.
     * Default currency: INR
     * 
     * @param float $amount
     * @param string|null $currency
     * @return string
     */
    public static function format($amount, $currency = null)
    {
        $currency = $currency ?? config('app.currency', 'INR');
        
        $symbols = [
            'INR' => '₹',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
        ];

        $symbol = $symbols[$currency] ?? $currency;

        // Simple formatting for now. Can use NumberFormatter for more complex locales.
        return $symbol . number_format($amount, 2);
    }
}
