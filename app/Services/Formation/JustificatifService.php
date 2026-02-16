<?php

namespace App\Services\Formation;

use App\Models\Formation\Justificatif;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class JustificatifService
{
    public function createJustificatif(array $data): Justificatif
    {
        return DB::transaction(function () use ($data) {
            $justificatif = Justificatif::create($data);

            $this->logAction($justificatif, 'Justificatif créé');

            return $justificatif;
        });
    }

    public function updateJustificatif(Justificatif $justificatif, array $data): Justificatif
    {
        return DB::transaction(function () use ($justificatif, $data) {
            $justificatif->update($data);

            $this->logAction($justificatif, 'Justificatif mis à jour');

            return $justificatif;
        });
    }

    public function verifyJustificatif(Justificatif $justificatif): Justificatif
    {
        return DB::transaction(function () use ($justificatif) {
            if ($justificatif->is_verified) {
                throw new \InvalidArgumentException('Ce justificatif a déjà été vérifié.');
            }

            $justificatif->update([
                'is_verified' => true,
                'verified_at' => now(),
                'verified_by' => auth()->user()->id,
            ]);

            $this->logAction($justificatif, 'Justificatif vérifié');

            return $justificatif;
        });
    }

    public function rejectJustificatif(Justificatif $justificatif, ?string $reason = null): Justificatif
    {
        return DB::transaction(function () use ($justificatif, $reason) {
            $justificatif->update([
                'is_verified' => false,
                'verified_at' => null,
                'verified_by' => null,
                'remarques' => $reason ?? $justificatif->remarques,
            ]);

            $this->logAction($justificatif, 'Justificatif rejeté');

            return $justificatif;
        });
    }

    public function deleteJustificatif(Justificatif $justificatif): void
    {
        DB::transaction(function () use ($justificatif) {
            // Delete file from storage
            if (Storage::disk('private')->exists($justificatif->chemin_fichier)) {
                Storage::disk('private')->delete($justificatif->chemin_fichier);
            }

            $this->logAction($justificatif, 'Justificatif supprimé');
            $justificatif->delete();
        });
    }

    public function getJustificatifsByParticipant(int $participantId): array
    {
        return Justificatif::where('participant_id', $participantId)->get()->toArray();
    }

    public function getUnverifiedJustificatifs(int $formationId): \Illuminate\Database\Eloquent\Collection
    {
        return Justificatif::where('formation_id', $formationId)
            ->where('is_verified', false)
            ->get();
    }

    private function logAction(Justificatif $justificatif, string $message): void
    {
        \App\Models\AuditLog::create([
            'user_id' => auth()->user()->id,
            'auditable_type' => Justificatif::class,
            'auditable_id' => $justificatif->id,
            'action' => 'update',
            'old_values' => [],
            'new_values' => ['message' => $message],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
