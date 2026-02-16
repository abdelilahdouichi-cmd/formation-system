<?php

namespace App\Services\Formation;

use App\Models\Formation\Formation;
use App\Models\Formation\Participant;
use Illuminate\Support\Facades\DB;

class ParticipantService
{
    public function createParticipant(Formation $formation, array $data): Participant
    {
        return DB::transaction(function () use ($formation, $data) {
            $data['formation_id'] = $formation->id;
            if (! array_key_exists('date_inscription', $data)) {
                $data['date_inscription'] = now();
            }

            $participant = Participant::create($data);

            $this->logAction($participant, 'Participant créé');

            return $participant;
        });
    }

    public function updateParticipant(Participant $participant, array $data): Participant
    {
        return DB::transaction(function () use ($participant, $data) {
            $participant->update($data);

            $this->logAction($participant, 'Participant mis à jour');

            return $participant;
        });
    }

    public function assignToClasse(Participant $participant, int $classeId): Participant
    {
        return $this->updateParticipant($participant, ['classe_id' => $classeId]);
    }

    public function removeFromClasse(Participant $participant): Participant
    {
        return $this->updateParticipant($participant, ['classe_id' => null]);
    }

    public function deactivateParticipant(Participant $participant): Participant
    {
        return $this->updateParticipant($participant, ['is_active' => false]);
    }

    public function activateParticipant(Participant $participant): Participant
    {
        return $this->updateParticipant($participant, ['is_active' => true]);
    }

    public function deleteParticipant(Participant $participant): void
    {
        DB::transaction(function () use ($participant) {
            $this->logAction($participant, 'Participant supprimé');
            $participant->delete();
        });
    }

    public function getTauxAssiduite(Participant $participant): float
    {
        if (!$participant->classe) {
            return 0;
        }

        $formation = $participant->formation;
        $daysInFormation = $formation->date_fin->diffInDays($formation->date_debut) + 1;

        if ($daysInFormation === 0) {
            return 0;
        }

        $presenceCount = $participant->presences()->count() ?? 0;

        return ($presenceCount / $daysInFormation) * 100;
    }

    public function isQualified(Participant $participant): bool
    {
        $qualification = $participant->qualification;

        if (!$qualification) {
            return false;
        }

        return $qualification->status->value === 'qualifié';
    }

    public function canReceiveDiplome(Participant $participant): bool
    {
        return $this->isQualified($participant) && $participant->formation->status->value === 'exécutée';
    }

    public function canReceiveLicence(Participant $participant): bool
    {
        return $this->canReceiveDiplome($participant) && $participant->diplome !== null;
    }

    private function logAction(Participant $participant, string $message): void
    {
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'method' => 'SERVICE',
            'path' => 'participants/' . $participant->id,
            'ip_address' => null,
            'user_agent' => null,
            'action' => str_contains($message, 'créé') ? 'create' : (str_contains($message, 'supprimé') ? 'delete' : 'update'),
            'model_type' => Participant::class,
            'model_id' => $participant->id,
            'response_code' => 200,
        ]);
    }
}
