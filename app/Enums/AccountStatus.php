<?php

namespace App\Enums;

enum AccountStatus: string
{
    case Active = 'active';
    case Blocked = 'blocked';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Aktif',
            self::Blocked => 'Diblokir',
        };
    }
}