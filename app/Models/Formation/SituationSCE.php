<?php

namespace App\Models\Formation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SituationSCE extends Model
{
    /** @use HasFactory<\Database\Factories\Formation\SituationSceFactory> */
    use HasFactory;

    protected $table = 'situation_sces';

    protected $fillable = [
        'formation_id',
        'sce_name',
        'nombre_inscrits',
        'nombre_presents',
        'nombre_absents',
        'nombre_qualifies',
        'nombre_non_qualifies',
        'nombre_diplomes',
        'nombre_licences',
        'date_rapport',
        'observation',
        'statut_rapport',
    ];

    protected $casts = [
        'date_rapport' => 'date',
    ];

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function getTauxAssiduite(): float
    {
        if ($this->nombre_inscrits == 0) {
            return 0;
        }
        return round(($this->nombre_presents / $this->nombre_inscrits) * 100, 2);
    }

    public function getTauxQualification(): float
    {
        if ($this->nombre_presents == 0) {
            return 0;
        }
        return round(($this->nombre_qualifies / $this->nombre_presents) * 100, 2);
    }

    public function getTauxDiplomation(): float
    {
        if ($this->nombre_qualifies == 0) {
            return 0;
        }
        return round(($this->nombre_diplomes / $this->nombre_qualifies) * 100, 2);
    }
}
