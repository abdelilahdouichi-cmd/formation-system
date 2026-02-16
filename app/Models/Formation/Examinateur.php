<?php

namespace App\Models\Formation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Examinateur extends Model
{
    /** @use HasFactory<\Database\Factories\Formation\ExaminateurFactory> */
    use HasFactory;

    protected $table = 'examinateurs';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'numero_licence',
        'organisme',
        'date_debut',
        'date_fin',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function formations(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Formation\Formation::class, 'formation_examinateur', 'examinateur_id', 'formation_id')
            ->withPivot('date_examen', 'lieu')
            ->withTimestamps();
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    public function isActive(): bool
    {
        return $this->is_active && (!$this->date_fin || $this->date_fin->isFuture());
    }
}
