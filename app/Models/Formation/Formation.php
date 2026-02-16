<?php

namespace App\Models\Formation;

use App\Enums\Formation\FormationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Formation extends Model
{
    /** @use HasFactory<\Database\Factories\Formation\FormationFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'formations';

    protected $fillable = [
        'niveau_id',
        'categorie_id',
        'sous_categorie_id',
        'nom',
        'code',
        'description',
        'objectif',
        'duree_heures',
        'prix',
        'date_debut_prevue',
        'date_fin_prevue',
        'date_debut_reelle',
        'date_fin_reelle',
        'status',
        'lieu',
        'nombreParticipantsMin',
        'nombreParticipantsMax',
        'is_active',
    ];

    protected $casts = [
        'status' => FormationStatus::class,
        'is_active' => 'boolean',
        'date_debut_prevue' => 'datetime',
        'date_fin_prevue' => 'datetime',
        'date_debut_reelle' => 'datetime',
        'date_fin_reelle' => 'datetime',
        'prix' => 'decimal:2',
    ];

    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class);
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }

    public function sousCategorie(): BelongsTo
    {
        return $this->belongsTo(SousCategorie::class, 'sous_categorie_id');
    }

    public function classes(): HasMany
    {
        return $this->hasMany(Classe::class, 'formation_id');
    }

    public function instructeurs(): BelongsToMany
    {
        return $this->belongsToMany(Instructeur::class, 'formation_instructeur', 'formation_id', 'instructeur_id')
            ->withPivot('role', 'date_debut', 'date_fin')
            ->withTimestamps();
    }

    public function examinateurs(): BelongsToMany
    {
        return $this->belongsToMany(Examinateur::class, 'formation_examinateur', 'formation_id', 'examinateur_id')
            ->withPivot('date_examen', 'lieu')
            ->withTimestamps();
    }

    public function participants(): HasMany
    {
        return $this->hasMany(\App\Models\Formation\Participant::class, 'formation_id');
    }

    public function qualifications(): HasMany
    {
        return $this->hasMany(\App\Models\Formation\Qualification::class, 'formation_id');
    }

    public function diplomes(): HasMany
    {
        return $this->hasMany(\App\Models\Formation\Diplome::class, 'formation_id');
    }

    public function licences(): HasMany
    {
        return $this->hasMany(\App\Models\Formation\Licence::class, 'formation_id');
    }

    public function justificatifs(): HasMany
    {
        return $this->hasMany(\App\Models\Formation\Justificatif::class, 'formation_id');
    }

    public function situationSCEs(): HasMany
    {
        return $this->hasMany(\App\Models\Formation\SituationSCE::class, 'formation_id');
    }

    public function canBePlanned(): bool
    {
        return $this->status === FormationStatus::Planifiee;
    }

    public function canBeExecuted(): bool
    {
        return $this->status === FormationStatus::AProgrammer;
    }

    public function getDateDebutAttribute(): mixed
    {
        return $this->date_debut_prevue;
    }

    public function setDateDebutAttribute(mixed $value): void
    {
        $this->attributes['date_debut_prevue'] = $value;
    }

    public function getDateFinAttribute(): mixed
    {
        return $this->date_fin_prevue;
    }

    public function setDateFinAttribute(mixed $value): void
    {
        $this->attributes['date_fin_prevue'] = $value;
    }

    public function getDateDebutReelAttribute(): mixed
    {
        return $this->date_debut_reelle;
    }

    public function setDateDebutReelAttribute(mixed $value): void
    {
        $this->attributes['date_debut_reelle'] = $value;
    }

    public function getDateFinReelAttribute(): mixed
    {
        return $this->date_fin_reelle;
    }

    public function setDateFinReelAttribute(mixed $value): void
    {
        $this->attributes['date_fin_reelle'] = $value;
    }
}
