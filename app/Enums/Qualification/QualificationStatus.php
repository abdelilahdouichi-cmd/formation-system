<?php

namespace App\Enums\Qualification;

enum QualificationStatus: string
{
    case Qualifie = 'qualifié';
    case NonQualifie = 'non qualifié';
    case EnAttente = 'en attente';
    case Refuse = 'refusé';

    public function label(): string
    {
        return match ($this) {
            self::Qualifie => 'Qualifié',
            self::NonQualifie => 'Non qualifié',
            self::EnAttente => 'En attente',
            self::Refuse => 'Refusé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Qualifie => 'green',
            self::NonQualifie => 'red',
            self::EnAttente => 'yellow',
            self::Refuse => 'red',
        };
    }
}
