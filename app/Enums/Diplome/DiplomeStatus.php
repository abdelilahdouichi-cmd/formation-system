<?php

namespace App\Enums\Diplome;

enum DiplomeStatus: string
{
    case Livree = 'livrée';
    case AExaminer = 'à examiner';
    case ADelivrer = 'à livrer';
    case Refusee = 'refusée';
    case Annulee = 'annulée';

    public function label(): string
    {
        return match ($this) {
            self::Livree => 'Livrée',
            self::AExaminer => 'À examiner',
            self::ADelivrer => 'À livrer',
            self::Refusee => 'Refusée',
            self::Annulee => 'Annulée',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Livree => 'green',
            self::AExaminer => 'yellow',
            self::ADelivrer => 'orange',
            self::Refusee => 'red',
            self::Annulee => 'gray',
        };
    }
}
