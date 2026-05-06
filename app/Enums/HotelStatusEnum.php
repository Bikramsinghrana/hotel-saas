<?php

namespace App\Enums;

enum HotelStatusEnum: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case DRAFT = 'draft';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::DRAFT => 'Draft',
        };
    }
}
