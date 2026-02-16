<?php

namespace App\Enums\Licence;

enum LicenceStatus: string
{
    case Livree = 'livrée';
    case ADelivrer = 'à livrer';
    case Suspendue = 'suspendue';
    case Expiree = 'expirée';
    case Renouvelee = 'renouvelée';

    public function label(): string
    {
        return match ($this) {
            self::Livree => 'Livrée',
            self::ADelivrer => 'À livrer',
            self::Suspendue => 'Suspendue',
            self::Expiree => 'Expirée',
            self::Renouvelee => 'Renouvelée',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Livree => 'green',
            self::ADelivrer => 'yellow',
            self::Suspendue => 'orange',
            self::Expiree => 'red',
            self::Renouvelee => 'blue',
        };
    }
}
