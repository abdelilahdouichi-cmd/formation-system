<?php

namespace App\Models\Formation;

use App\Enums\Diplome\DiplomeStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Diplome extends Model
{
    /** @use HasFactory<\Database\Factories\Formation\DiplomeFactory> */
    use HasFactory;

    protected $table = 'diplomes';

    protected $fillable = [
        'formation_id',
        'participant_id',
        'numero_diplome',
        'status',
        'date_delivrance',
        'date_expiration',
        'lieu_delivrance',
        'signature_directeur',
        'observation',
    ];

    protected $casts = [
        'status' => DiplomeStatus::class,
        'date_delivrance' => 'date',
        'date_expiration' => 'date',
        'signature_directeur' => 'boolean',
    ];

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function justificatifs(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Formation\Justificatif::class, 'diplome_id');
    }

    public function isDelivered(): bool
    {
        return $this->status === DiplomeStatus::Livree;
    }

    public function canBeDelivered(): bool
    {
        return $this->status === DiplomeStatus::ADelivrer && $this->signature_directeur;
    }

    public function isExpired(): bool
    {
        return $this->date_expiration && $this->date_expiration->isPast();
    }

    public function getDateLivraisonAttribute(): mixed
    {
        return $this->date_delivrance;
    }

    public function setDateLivraisonAttribute(mixed $value): void
    {
        $this->attributes['date_delivrance'] = $value;
    }

    public function getDateEmissionAttribute(): mixed
    {
        return $this->date_delivrance;
    }

    public function setDateEmissionAttribute(mixed $value): void
    {
        $this->attributes['date_delivrance'] = $value;
    }
}
