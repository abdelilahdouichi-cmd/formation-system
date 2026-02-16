<?php

namespace App\Models\Formation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classe extends Model
{
    /** @use HasFactory<\Database\Factories\Formation\ClasseFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'formation_id',
        'nom',
        'code',
        'capacite',
        'capacite_maximal',
        'date_debut',
        'date_fin',
        'lieu',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
    ];

    public function formation(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Formation\Formation::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(\App\Models\Formation\Participant::class, 'classe_id');
    }

    public function getParticipantCountAttribute(): int
    {
        return $this->participants()->count();
    }

    public function getCapaciteMaximalAttribute(): int
    {
        return (int) ($this->attributes['capacite_maximal'] ?? 0);
    }

    public function setCapaciteMaximalAttribute(?int $capacite): void
    {
        $this->attributes['capacite_maximal'] = $capacite;
        $this->attributes['capacite'] = $capacite;
    }

    public function getCapaciteAttribute(): int
    {
        return $this->getCapaciteMaximalAttribute();
    }

    public function setCapaciteAttribute(?int $capacite): void
    {
        $this->attributes['capacite'] = $capacite;
        $this->attributes['capacite_maximal'] = $capacite;
    }

    public function getAvailableSeatsAttribute(): int
    {
        return $this->capacite - $this->participants_count;
    }
}
