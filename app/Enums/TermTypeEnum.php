<?php

namespace App\Enums;

enum TermTypeEnum: string
{
    case NEARBY = 'nearby';
    case FACILITY = 'facility';
    case EXTRA_SERVICE = 'extra_service';
    case AMENITY = 'amenity';

    public function label(): string
    {
        return match($this) {
            self::NEARBY => 'Nearby Place',
            self::FACILITY => 'Facility',
            self::EXTRA_SERVICE => 'Extra Service',
            self::AMENITY => 'Amenity',
        };
    }
}
