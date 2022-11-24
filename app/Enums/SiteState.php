<?php

namespace App\Enums;

enum SiteState
{
    case ACTIVE;
    case DRAFT;

    public function id(): string
    {
        return match ($this) {
            static::ACTIVE => 1,
            static::DRAFT => 0,
        };
    }
}
