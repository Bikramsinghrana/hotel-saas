<?php

namespace App\Enums;

enum ModuleStatusEnum: string
{
    case PUBLISHED = 'published';
    case DRAFT = 'draft';

    public function label(): string
    {
        return match($this) {
            self::PUBLISHED => 'Published',
            self::DRAFT => 'Draft',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PUBLISHED => 'success',
            self::DRAFT => 'warning',
        };
    }
}
