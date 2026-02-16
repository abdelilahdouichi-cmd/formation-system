<?php

namespace App\Models\Formation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SousCategorie extends Model
{
    /** @use HasFactory<\Database\Factories\Formation\SousCategorieFactory> */
    use HasFactory;

    protected $table = 'sous_categories';

    protected $fillable = [
        'categorie_id',
        'nom',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }

    public function formations(): HasMany
    {
        return $this->hasMany(\App\Models\Formation\Formation::class, 'sous_categorie_id');
    }
}
