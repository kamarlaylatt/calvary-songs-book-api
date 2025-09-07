<?php

namespace App\Enums;

enum RoleType: int
{
    case Superadmin = 1;
    case Admin = 2;
    case Guest = 3;

    public function name(): string
    {
        return match ($this) {
            self::Superadmin => 'Superadmin',
            self::Admin => 'Admin',
            self::Guest => 'Guest',
        };
    }
}
