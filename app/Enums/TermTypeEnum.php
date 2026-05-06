<?php

namespace App\Enums;

enum TermTypeEnum: string
{
    case NEARBY = 'nearby';
    case FACILITY = 'facility';
    case EXTRA = 'extra';

    public function label(): string
    {
        return match($this) {
            self::NEARBY => 'Nearby Place',
            self::FACILITY => 'Facility',
            self::EXTRA => 'Extra Service',
        };
    }
}
