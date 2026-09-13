<?php

namespace App\Enums;

enum RoleEnum: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN       = 'admin';
    case MERCHANT    = 'merchant';
    case MANAGER     = 'manager';
    case STAFF       = 'staff';
    case SELLER      = 'seller';
    case CUSTOMER    = 'customer';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
