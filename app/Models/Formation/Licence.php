<?php

namespace App\Models\Formation;

use App\Enums\Licence\LicenceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Licence extends Model
{
    /** @use HasFactory<\Database\Factories\Formation\LicenceFactory> */
    use HasFactory;

    protected $table = 'licences';

    protected $fillable = [
        'formation_id',
        'participant_id',
        'numero_licence',
        'status',
        'date_delivrance',
        'date_expiration',
        'date_renouvellement',
        'organisme_delivrance',
        'observation',
    ];

    protected $casts = [
        'status' => LicenceStatus::class,
        'date_delivrance' => 'date',
        'date_expiration' => 'date',
        'date_renouvellement' => 'date',
    ];

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function isDelivered(): bool
    {
        return $this->status === LicenceStatus::Livree;
    }

    public function canBeRenewed(): bool
    {
        return $this->status === LicenceStatus::Expiree && !$this->date_renouvellement;
    }

    public function isValid(): bool
    {
        return $this->status === LicenceStatus::Livree && (!$this->date_expiration || $this->date_expiration->isFuture());
    }

    public function isExpiringSoon(): bool
    {
        return $this->date_expiration && $this->date_expiration->diffInDays() <= 30;
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
