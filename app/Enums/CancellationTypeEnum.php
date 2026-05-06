<?php

namespace App\Enums;

enum CancellationTypeEnum: string
{
    case FREE = 'free';
    case PAID = 'paid';

    public function label(): string
    {
        return match($this) {
            self::FREE => 'Free Cancellation',
            self::PAID => 'Paid Cancellation',
        };
    }
}
