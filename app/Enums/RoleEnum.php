<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case MERCHANT = 'merchant';
    case CUSTOMER = 'customer';
    case SELLER = 'seller';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
