<?php

namespace App\Services\Formation;

use App\Enums\Diplome\DiplomeStatus;
use App\Models\Formation\Diplome;
use Illuminate\Support\Facades\DB;

class DiplomeService
{
    public function createDiplome(array $data): Diplome
    {
        return DB::transaction(function () use ($data) {
            if (array_key_exists('date_emission', $data) && ! array_key_exists('date_delivrance', $data)) {
                $data['date_delivrance'] = $data['date_emission'];
                unset($data['date_emission']);
            }

            $data['status'] = DiplomeStatus::ADelivrer;

            $diplome = Diplome::create($data);

            $this->logAction($diplome, 'Diplôme créé');

            return $diplome;
        });
    }

    public function deliverDiplome(Diplome $diplome, ?string $date_livraison = null): Diplome
    {
        if ($diplome->status === DiplomeStatus::Livree) {
            throw new \InvalidArgumentException('Ce diplôme a déjà été livré.');
        }

        if ($diplome->status === DiplomeStatus::Refusee) {
            throw new \InvalidArgumentException('Ce diplôme a été refusé et ne peut pas être livré.');
        }

        return DB::transaction(function () use ($diplome, $date_livraison) {
            $diplome->update([
                'status' => DiplomeStatus::Livree,
                'date_delivrance' => $date_livraison ?? now(),
            ]);

            $this->logAction($diplome, 'Diplôme livré');

            return $diplome;
        });
    }

    public function rejectDiplome(Diplome $diplome, ?string $reason = null): Diplome
    {
        if ($diplome->status === DiplomeStatus::Refusee) {
            throw new \InvalidArgumentException('Ce diplôme a déjà été refusé.');
        }

        return DB::transaction(function () use ($diplome, $reason) {
            $diplome->update([
                'status' => DiplomeStatus::Refusee,
                'observation' => $reason ?? $diplome->observation,
            ]);

            $this->logAction($diplome, 'Diplôme refusé');

            return $diplome;
        });
    }

    public function sendForExamination(Diplome $diplome): Diplome
    {
        return DB::transaction(function () use ($diplome) {
            $diplome->update(['status' => DiplomeStatus::AExaminer]);

            $this->logAction($diplome, 'Diplôme envoyé à examen');

            return $diplome;
        });
    }

    public function updateDiplome(Diplome $diplome, array $data): Diplome
    {
        return DB::transaction(function () use ($diplome, $data) {
            $diplome->update($data);

            $this->logAction($diplome, 'Diplôme mis à jour');

            return $diplome;
        });
    }

    public function cancelDiplome(Diplome $diplome): Diplome
    {
        return DB::transaction(function () use ($diplome) {
            $diplome->update(['status' => DiplomeStatus::Annulee]);

            $this->logAction($diplome, 'Diplôme annulé');

            return $diplome;
        });
    }

    public function deleteDiplome(Diplome $diplome): void
    {
        DB::transaction(function () use ($diplome) {
            $this->logAction($diplome, 'Diplôme supprimé');
            $diplome->delete();
        });
    }

    public function getDiplomationRate(int $formationId): float
    {
        $totalParticipants = \App\Models\Formation\Participant::where('formation_id', $formationId)->count();

        if ($totalParticipants === 0) {
            return 0;
        }

        $diplomedCount = Diplome::where('formation_id', $formationId)
            ->where('status', DiplomeStatus::Livree)
            ->count();

        return ($diplomedCount / $totalParticipants) * 100;
    }

    private function logAction(Diplome $diplome, string $message): void
    {
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'method' => 'SERVICE',
            'path' => 'diplomes/' . $diplome->id,
            'ip_address' => null,
            'user_agent' => null,
            'action' => str_contains($message, 'créé') ? 'create' : (str_contains($message, 'supprimé') ? 'delete' : 'update'),
            'model_type' => Diplome::class,
            'model_id' => $diplome->id,
            'response_code' => 200,
        ]);
    }
}
