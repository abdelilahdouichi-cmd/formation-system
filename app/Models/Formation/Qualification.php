<?php

namespace App\Models\Formation;

use App\Enums\Qualification\QualificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Qualification extends Model
{
    /** @use HasFactory<\Database\Factories\Formation\QualificationFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'qualifications';

    protected $fillable = [
        'formation_id',
        'participant_id',
        'status',
        'score',
        'date_evaluation',
        'observation',
        'evaluateur_id',
    ];

    protected $casts = [
        'status' => QualificationStatus::class,
        'date_evaluation' => 'date',
        'score' => 'float',
    ];

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function evaluateur(): BelongsTo
    {
        return $this->belongsTo(Instructeur::class, 'evaluateur_id');
    }

    public function isQualified(): bool
    {
        return $this->status === QualificationStatus::Qualifie;
    }

    public function canBeApproved(): bool
    {
        return $this->status === QualificationStatus::EnAttente;
    }
}
