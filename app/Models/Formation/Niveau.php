<?php

namespace App\Models\Formation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Niveau extends Model
{
    /** @use HasFactory<\Database\Factories\Formation\NiveauFactory> */
    use HasFactory;

    protected $table = 'niveaux';

    protected $fillable = [
        'nom',
        'ordre',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function formations(): HasMany
    {
        return $this->hasMany(\App\Models\Formation\Formation::class, 'niveau_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(\App\Models\Formation\Categorie::class, 'niveau_id');
    }
}
