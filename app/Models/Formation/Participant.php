<?php

namespace App\Models\Formation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participant extends Model
{
    /** @use HasFactory<\Database\Factories\Formation\ParticipantFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'participants';

    protected $fillable = [
        'formation_id',
        'classe_id',
        'nom',
        'prenom',
        'email',
        'telephone',
        'entreprise',
        'poste',
        'numero_identite',
        'date_inscription',
        'date_debut',
        'date_fin',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'date_inscription' => 'date',
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    public function qualification(): HasOne
    {
        return $this->hasOne(\App\Models\Formation\Qualification::class, 'participant_id');
    }

    public function diplome(): HasOne
    {
        return $this->hasOne(\App\Models\Formation\Diplome::class, 'participant_id');
    }

    public function licence(): HasOne
    {
        return $this->hasOne(\App\Models\Formation\Licence::class, 'participant_id');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }
}
