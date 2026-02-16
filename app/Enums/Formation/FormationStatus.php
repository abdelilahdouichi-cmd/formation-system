<?php

namespace App\Enums\Formation;

enum FormationStatus: string
{
    case Planifiee = 'planifiée';
    case AProgrammer = 'à programmer';
    case Executee = 'exécutée';
    case Annulee = 'annulée';
    case Archivee = 'archivée';

    public function label(): string
    {
        return match ($this) {
            self::Planifiee => 'Planifiée',
            self::AProgrammer => 'À programmer',
            self::Executee => 'Exécutée',
            self::Annulee => 'Annulée',
            self::Archivee => 'Archivée',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Planifiee => 'blue',
            self::AProgrammer => 'yellow',
            self::Executee => 'green',
            self::Annulee => 'red',
            self::Archivee => 'gray',
        };
    }
}
