<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PageModule extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'page-module-service';
    }
}
