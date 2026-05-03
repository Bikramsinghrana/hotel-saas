<?php

namespace App\Enums;

enum CancellationType: string
{
    case FLEXIBLE = 'flexible';
    case MODERATE = 'moderate';
    case STRICT = 'strict';
}
