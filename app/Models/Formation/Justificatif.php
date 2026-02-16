<?php

namespace App\Models\Formation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Justificatif extends Model
{
    /** @use HasFactory<\Database\Factories\Formation\JustificatifFactory> */
    use HasFactory;

    protected $table = 'justificatifs';

    protected $fillable = [
        'formation_id',
        'participant_id',
        'diplome_id',
        'licence_id',
        'type',
        'nom_fichier',
        'chemin_fichier',
        'taille',
        'mime_type',
        'date_upload',
        'description',
        'is_verified',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'date_upload' => 'datetime',
    ];

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function diplome(): BelongsTo
    {
        return $this->belongsTo(Diplome::class);
    }

    public function licence(): BelongsTo
    {
        return $this->belongsTo(Licence::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(Instructeur::class, 'verified_by');
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->chemin_fichier);
    }

    public function getReadableSizeAttribute(): string
    {
        return $this->formatBytes($this->taille);
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
