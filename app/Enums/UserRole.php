<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case User = 'user';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::User => 'Utilisateur',
        };
    }

    public static function adminRoles(): string
    {
        return self::Admin->value . ',' . self::SuperAdmin->value;
    }

    public static function allValues(): array
    {
        return array_map(fn (self $role) => $role->value, self::cases());
    }
}
