<?php

namespace App\Services\Formation;

use App\Enums\Licence\LicenceStatus;
use App\Models\Formation\Licence;
use Illuminate\Support\Facades\DB;

class LicenceService
{
    public function createLicence(array $data): Licence
    {
        return DB::transaction(function () use ($data) {
            if (array_key_exists('date_emission', $data) && ! array_key_exists('date_delivrance', $data)) {
                $data['date_delivrance'] = $data['date_emission'];
                unset($data['date_emission']);
            }

            $data['status'] = LicenceStatus::ADelivrer;

            $licence = Licence::create($data);

            $this->logAction($licence, 'Licence créée');

            return $licence;
        });
    }

    public function deliverLicence(Licence $licence, ?string $date_livraison = null): Licence
    {
        if ($licence->status === LicenceStatus::Livree) {
            throw new \InvalidArgumentException('Cette licence a déjà été livrée.');
        }

        return DB::transaction(function () use ($licence, $date_livraison) {
            $licence->update([
                'status' => LicenceStatus::Livree,
                'date_delivrance' => $date_livraison ?? now(),
            ]);

            $this->logAction($licence, 'Licence livrée');

            return $licence;
        });
    }

    public function renewLicence(Licence $licence, array $data): Licence
    {
        return DB::transaction(function () use ($licence, $data) {
            $licence->update([
                'status' => LicenceStatus::Renouvelee,
                'date_expiration' => $data['date_expiration'] ?? $licence->date_expiration,
                'date_renouvellement' => now(),
            ]);

            $this->logAction($licence, 'Licence renouvelée');

            return $licence;
        });
    }

    public function suspendLicence(Licence $licence, ?string $reason = null): Licence
    {
        if ($licence->status === LicenceStatus::Suspendue) {
            throw new \InvalidArgumentException('Cette licence est déjà suspendue.');
        }

        return DB::transaction(function () use ($licence, $reason) {
            $licence->update([
                'status' => LicenceStatus::Suspendue,
                'observation' => $reason ?? $licence->observation,
            ]);

            $this->logAction($licence, 'Licence suspendue');

            return $licence;
        });
    }

    public function updateLicence(Licence $licence, array $data): Licence
    {
        return DB::transaction(function () use ($licence, $data) {
            $licence->update($data);

            $this->logAction($licence, 'Licence mise à jour');

            return $licence;
        });
    }

    public function deleteLicence(Licence $licence): void
    {
        DB::transaction(function () use ($licence) {
            $this->logAction($licence, 'Licence supprimée');
            $licence->delete();
        });
    }

    public function checkExpiredLicences(): void
    {
        $expiredLicences = Licence::where('date_expiration', '<', now())
            ->where('status', '!=', LicenceStatus::Expiree)
            ->where('status', '!=', LicenceStatus::Renouvelee)
            ->get();

        foreach ($expiredLicences as $licence) {
            $licence->update(['status' => LicenceStatus::Expiree]);
            $this->logAction($licence, 'Licence expirée (détection automatique)');
        }
    }

    public function getLicencedRate(int $formationId): float
    {
        $totalParticipants = \App\Models\Formation\Participant::where('formation_id', $formationId)->count();

        if ($totalParticipants === 0) {
            return 0;
        }

        $licencedCount = Licence::where('formation_id', $formationId)
            ->whereIn('status', [LicenceStatus::Livree, LicenceStatus::Renouvelee])
            ->count();

        return ($licencedCount / $totalParticipants) * 100;
    }

    private function logAction(Licence $licence, string $message): void
    {
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'method' => 'SERVICE',
            'path' => 'licences/' . $licence->id,
            'ip_address' => null,
            'user_agent' => null,
            'action' => str_contains($message, 'créée') ? 'create' : (str_contains($message, 'supprimée') ? 'delete' : 'update'),
            'model_type' => Licence::class,
            'model_id' => $licence->id,
            'response_code' => 200,
        ]);
    }
}
