<?php

namespace App\Services\Formation;

use App\Enums\Qualification\QualificationStatus;
use App\Models\Formation\Qualification;
use Illuminate\Support\Facades\DB;

class QualificationService
{
    public function recordQualification(array $data): Qualification
    {
        return DB::transaction(function () use ($data) {
            $data['status'] = QualificationStatus::EnAttente;

            $qualification = Qualification::create($data);

            $this->logAction($qualification, 'Qualification enregistrée');

            return $qualification;
        });
    }

    public function approveQualification(Qualification $qualification, ?string $observation = null): Qualification
    {
        if ($qualification->status === QualificationStatus::Qualifie) {
            throw new \InvalidArgumentException('Cette qualification est déjà approuvée.');
        }

        return DB::transaction(function () use ($qualification, $observation) {
            $qualification->update([
                'status' => QualificationStatus::Qualifie,
                'date_evaluation' => now(),
                'observation' => $observation ?? $qualification->observation,
            ]);

            $this->logAction($qualification, 'Qualification approuvée');

            return $qualification;
        });
    }

    public function rejectQualification(Qualification $qualification, ?string $observation = null): Qualification
    {
        if ($qualification->status === QualificationStatus::Refuse) {
            throw new \InvalidArgumentException('Cette qualification est déjà rejetée.');
        }

        return DB::transaction(function () use ($qualification, $observation) {
            $qualification->update([
                'status' => QualificationStatus::Refuse,
                'date_evaluation' => now(),
                'observation' => $observation ?? $qualification->observation,
            ]);

            $this->logAction($qualification, 'Qualification rejetée');

            return $qualification;
        });
    }

    public function updateQualification(Qualification $qualification, array $data): Qualification
    {
        return DB::transaction(function () use ($qualification, $data) {
            $qualification->update($data);

            $this->logAction($qualification, 'Qualification mise à jour');

            return $qualification;
        });
    }

    public function deleteQualification(Qualification $qualification): void
    {
        DB::transaction(function () use ($qualification) {
            $this->logAction($qualification, 'Qualification supprimée');
            $qualification->delete();
        });
    }

    public function getQualificationRate(int $formationId): float
    {
        $totalParticipants = \App\Models\Formation\Participant::where('formation_id', $formationId)->count();

        if ($totalParticipants === 0) {
            return 0;
        }

        $qualifiedCount = Qualification::where('formation_id', $formationId)
            ->where('status', QualificationStatus::Qualifie)
            ->count();

        return ($qualifiedCount / $totalParticipants) * 100;
    }

    private function logAction(Qualification $qualification, string $message): void
    {
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'method' => 'SERVICE',
            'path' => 'qualifications/' . $qualification->id,
            'ip_address' => null,
            'user_agent' => null,
            'action' => str_contains($message, 'enregistrée') ? 'create' : (str_contains($message, 'supprimée') ? 'delete' : 'update'),
            'model_type' => Qualification::class,
            'model_id' => $qualification->id,
            'response_code' => 200,
        ]);
    }
}
