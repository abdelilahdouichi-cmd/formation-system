<?php

namespace App\Models\Formation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    /** @use HasFactory<\Database\Factories\Formation\CategorieFactory> */
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'niveau_id',
        'nom',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class);
    }

    public function sousCategories(): HasMany
    {
        return $this->hasMany(\App\Models\Formation\SousCategorie::class, 'categorie_id');
    }

    public function formations(): HasMany
    {
        return $this->hasMany(\App\Models\Formation\Formation::class, 'categorie_id');
    }
}
