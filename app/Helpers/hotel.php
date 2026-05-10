<?php

namespace App\Helpers;

class HotelPath
{
    /**
     * View path prefix for Hotel themes
     */
    const VIEW = 'themes.hotel.';

    /**
     * Asset path prefix for Hotel themes
     */
    const ASSET = 'themes/hotel/';

    /**
     * Get the full view path
     */
    public static function view($path)
    {
        $return = self::VIEW . $path;
        // dd($return);
        return $return;
    }

    /**
     * Get the full asset path
     */
    public static function asset($path)
    {
        return asset(self::ASSET . $path);
    }
}
