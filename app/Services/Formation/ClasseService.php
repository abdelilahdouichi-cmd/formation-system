<?php

namespace App\Services\Formation;

use App\Models\Formation\Classe;
use Illuminate\Support\Facades\DB;

class ClasseService
{
    public function createClasse(array $data): Classe
    {
        return DB::transaction(function () use ($data) {
            if (array_key_exists('capacite_maximal', $data)) {
                $data['capacite'] = $data['capacite_maximal'];
                unset($data['capacite_maximal']);
            }

            $classe = Classe::create($data);

            $this->logAction($classe, 'Classe créée');

            return $classe;
        });
    }

    public function updateClasse(Classe $classe, array $data): Classe
    {
        return DB::transaction(function () use ($classe, $data) {
            if (array_key_exists('capacite_maximal', $data)) {
                $data['capacite'] = $data['capacite_maximal'];
                unset($data['capacite_maximal']);
            }

            $classe->update($data);

            $this->logAction($classe, 'Classe mise à jour');

            return $classe;
        });
    }

    public function deleteClasse(Classe $classe): void
    {
        DB::transaction(function () use ($classe) {
            // Check if clase has participants
            if ($classe->participants()->exists()) {
                throw new \InvalidArgumentException(
                    'Impossible de supprimer une classe contenant des participants.'
                );
            }

            $this->logAction($classe, 'Classe supprimée');
            $classe->delete();
        });
    }

    public function getEnrolledCount(Classe $classe): int
    {
        return $classe->participants()->where('is_active', true)->count();
    }

    public function canEnrollParticipant(Classe $classe): bool
    {
        return $this->getEnrolledCount($classe) < $classe->capacite;
    }

    public function getRemainingCapacity(Classe $classe): int
    {
        return $classe->capacite - $this->getEnrolledCount($classe);
    }

    private function logAction(Classe $classe, string $message): void
    {
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'method' => 'SERVICE',
            'path' => 'classes/' . $classe->id,
            'ip_address' => null,
            'user_agent' => null,
            'action' => str_contains($message, 'créée') ? 'create' : (str_contains($message, 'supprimée') ? 'delete' : 'update'),
            'model_type' => Classe::class,
            'model_id' => $classe->id,
            'response_code' => 200,
        ]);
    }
}
