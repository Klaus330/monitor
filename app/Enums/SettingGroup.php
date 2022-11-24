<?php

namespace App\Enums;

enum SettingGroup
{
    case GENERAL;
    case BROKEN_LINKS;
    case LIGHTHOUSE;
    case MONITORS;

    public function id(): string
    {
        return match ($this) {
            static::GENERAL => 1,
            static::BROKEN_LINKS => 2,
            static::LIGHTHOUSE => 3,
            static::MONITORS => 4,
        };
    }
}
