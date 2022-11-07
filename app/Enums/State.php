<?php

namespace App\Enums;

enum State: string
{
    case SUCCESS = 'success';
    case ERROR = 'error';
    case PENDING = 'pending';
    case INFO = 'info';

    public function label(): string
    {
        return match ($this) {
            static::PENDING => 'pending',
            static::SUCCESS => 'success',
            static::ERROR => 'error',
            static::INFO => 'info',
        };
    }
}
