<?php

namespace App\Services\Formation;

use App\Enums\Formation\FormationStatus;
use App\Models\Formation\Formation;

use Illuminate\Support\Facades\DB;

class FormationService
{
    public function createFormation(array $data): Formation
    {
        return DB::transaction(function () use ($data) {
            $data = $this->normalizeFormationData($data);
            $data['status'] = FormationStatus::Planifiee;

            $formation = Formation::create($data);

            $this->logAction($formation, 'Formation créée');

            return $formation;
        });
    }

    public function updateFormation(Formation $formation, array $data): Formation
    {
        return DB::transaction(function () use ($formation, $data) {
            $data = $this->normalizeFormationData($data);
            $oldStatus = $formation->status;

            $formation->update($data);

            if (isset($data['status']) && $data['status'] !== $oldStatus) {
                $this->logAction(
                    $formation,
                    "Statut changé de {$oldStatus->label()} à {$data['status']->label()}"
                );
            } else {
                $this->logAction($formation, 'Formation mise à jour');
            }

            return $formation;
        });
    }

    public function programFormation(Formation $formation): Formation
    {
        if ($formation->status !== FormationStatus::Planifiee) {
            throw new \InvalidArgumentException(
                'Seules les formations planifiées peuvent être programmées.'
            );
        }

        return $this->updateFormation($formation, [
            'status' => FormationStatus::AProgrammer,
        ]);
    }

    public function executeFormation(Formation $formation, array $data): Formation
    {
        if ($formation->status !== FormationStatus::AProgrammer) {
            throw new \InvalidArgumentException(
                'Seules les formations à programmer peuvent être exécutées.'
            );
        }

        $data = $this->normalizeFormationData($data);
        $data['status'] = FormationStatus::Executee;

        return $this->updateFormation($formation, $data);
    }

    public function cancelFormation(Formation $formation): Formation
    {
        if (in_array($formation->status, [FormationStatus::Executee, FormationStatus::Archivee])) {
            throw new \InvalidArgumentException(
                'Impossible d\'annuler une formation déjà exécutée ou archivée.'
            );
        }

        return $this->updateFormation($formation, [
            'status' => FormationStatus::Annulee,
        ]);
    }

    public function archiveFormation(Formation $formation): Formation
    {
        if ($formation->status !== FormationStatus::Executee) {
            throw new \InvalidArgumentException(
                'Seules les formations exécutées peuvent être archivées.'
            );
        }

        return $this->updateFormation($formation, [
            'status' => FormationStatus::Archivee,
        ]);
    }

    public function deleteFormation(Formation $formation): void
    {
        DB::transaction(function () use ($formation) {
            $this->logAction($formation, 'Formation supprimée');
            $formation->delete();
        });
    }

    public function assignInstructeur(Formation $formation, int $instructeurId, array $data): void
    {
        $formation->instructeurs()->attach($instructeurId, $data);
        $this->logAction($formation, "Instructeur assigné (ID: {$instructeurId})");
    }

    public function removeInstructeur(Formation $formation, int $instructeurId): void
    {
        $formation->instructeurs()->detach($instructeurId);
        $this->logAction($formation, "Instructeur supprimé (ID: {$instructeurId})");
    }

    public function assignExaminateur(Formation $formation, int $examinateurId, array $data): void
    {
        $formation->examinateurs()->attach($examinateurId, $data);
        $this->logAction($formation, "Examinateur assigné (ID: {$examinateurId})");
    }

    public function removeExaminateur(Formation $formation, int $examinateurId): void
    {
        $formation->examinateurs()->detach($examinateurId);
        $this->logAction($formation, "Examinateur supprimé (ID: {$examinateurId})");
    }

    private function logAction(Formation $formation, string $message): void
    {
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'method' => 'SERVICE',
            'path' => 'formations/' . $formation->id,
            'ip_address' => null,
            'user_agent' => null,
            'action' => str_contains($message, 'créée') ? 'create' : (str_contains($message, 'supprimée') ? 'delete' : 'update'),
            'model_type' => Formation::class,
            'model_id' => $formation->id,
            'response_code' => 200,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeFormationData(array $data): array
    {
        if (array_key_exists('date_debut', $data) && ! array_key_exists('date_debut_prevue', $data)) {
            $data['date_debut_prevue'] = $data['date_debut'];
            unset($data['date_debut']);
        }

        if (array_key_exists('date_fin', $data) && ! array_key_exists('date_fin_prevue', $data)) {
            $data['date_fin_prevue'] = $data['date_fin'];
            unset($data['date_fin']);
        }

        if (array_key_exists('date_debut_reel', $data) && ! array_key_exists('date_debut_reelle', $data)) {
            $data['date_debut_reelle'] = $data['date_debut_reel'];
            unset($data['date_debut_reel']);
        }

        if (array_key_exists('date_fin_reel', $data) && ! array_key_exists('date_fin_reelle', $data)) {
            $data['date_fin_reelle'] = $data['date_fin_reel'];
            unset($data['date_fin_reel']);
        }

        if (isset($data['status']) && is_string($data['status'])) {
            $data['status'] = FormationStatus::from($data['status']);
        }

        return $data;
    }
}
