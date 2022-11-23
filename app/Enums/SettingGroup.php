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
            static::GENERAL => 0,
            static::BROKEN_LINKS => 1,
            static::LIGHTHOUSE => 2,
            static::MONITORS => 3,
        };
    }
}
