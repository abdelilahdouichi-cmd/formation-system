<?php

namespace App\Services\Formation;

use App\Enums\Diplome\DiplomeStatus;
use App\Enums\Formation\FormationStatus;
use App\Enums\Licence\LicenceStatus;
use App\Enums\Qualification\QualificationStatus;
use App\Models\Formation\Formation;
use App\Models\Formation\Participant;
use App\Models\Formation\Qualification;
use App\Models\Formation\Diplome;
use App\Models\Formation\Licence;
use App\Models\Formation\SituationSCE;

class ReportService
{
    public function generateFormationReport(Formation $formation): array
    {
        $participants = $formation->participants()->where('is_active', true)->get();
        $totalParticipants = $participants->count();

        if ($totalParticipants === 0) {
            return $this->getEmptyReport();
        }

        $qualifications = $formation->qualifications;
        $qualifiedCount = $qualifications->where('status', QualificationStatus::Qualifie)->count();
        $rejectedCount = $qualifications->where('status', QualificationStatus::Refuse)->count();

        $diplomes = $formation->diplomes;
        $deliveredDiplomes = $diplomes->where('status', DiplomeStatus::Livree)->count();

        $licences = $formation->licences;
        $activeLicences = $licences->whereIn('status', [LicenceStatus::Livree, LicenceStatus::Renouvelee])->count();

        $tauxAssiduite = $this->calculateAverageAttendance($formation);
        $tauxQualification = ($totalParticipants > 0) ? ($qualifiedCount / $totalParticipants) * 100 : 0;
        $tauxDiplomation = ($totalParticipants > 0) ? ($deliveredDiplomes / $totalParticipants) * 100 : 0;
        $tauxLicence = ($totalParticipants > 0) ? ($activeLicences / $totalParticipants) * 100 : 0;

        return [
            'formation_id' => $formation->id,
            'formation_code' => $formation->code,
            'formation_nom' => $formation->nom,
            'date_debut' => $formation->date_debut,
            'date_fin' => $formation->date_fin,
            'status' => $formation->status->label(),
            'total_participants' => $totalParticipants,
            'qualified_participants' => $qualifiedCount,
            'rejected_participants' => $rejectedCount,
            'pending_participants' => $totalParticipants - $qualifiedCount - $rejectedCount,
            'diplomed_participants' => $deliveredDiplomes,
            'licensed_participants' => $activeLicences,
            'taux_assiduite' => round($tauxAssiduite, 2),
            'taux_qualification' => round($tauxQualification, 2),
            'taux_diplomation' => round($tauxDiplomation, 2),
            'taux_licence' => round($tauxLicence, 2),
        ];
    }

    public function generateSituationSCE(Formation $formation): SituationSCE
    {
        $report = $this->generateFormationReport($formation);

        return SituationSCE::updateOrCreate(
            ['formation_id' => $formation->id],
            [
                'date_rapport' => now(),
                'total_participants' => $report['total_participants'],
                'qualified_participants' => $report['qualified_participants'],
                'rejected_participants' => $report['rejected_participants'],
                'pending_participants' => $report['pending_participants'],
                'diplomed_participants' => $report['diplomed_participants'],
                'licensed_participants' => $report['licensed_participants'],
                'taux_assiduite' => $report['taux_assiduite'],
                'taux_qualification' => $report['taux_qualification'],
                'taux_diplomation' => $report['taux_diplomation'],
                'taux_licence' => $report['taux_licence'],
            ]
        );
    }

    public function getFormationsByStatus(FormationStatus $status): \Illuminate\Database\Eloquent\Collection
    {
        return Formation::where('status', $status->value)->get();
    }

    public function getFormationStatistics(): array
    {
        $totalFormations = Formation::count();
        $activeFormations = Formation::where('status', FormationStatus::Executee->value)->count();
        $plannedFormations = Formation::where('status', FormationStatus::Planifiee->value)->count();
        $cancelledFormations = Formation::where('status', FormationStatus::Annulee->value)->count();
        $archivedFormations = Formation::where('status', FormationStatus::Archivee->value)->count();

        return [
            'total_formations' => $totalFormations,
            'active_formations' => $activeFormations,
            'planned_formations' => $plannedFormations,
            'cancelled_formations' => $cancelledFormations,
            'archived_formations' => $archivedFormations,
        ];
    }

    public function getParticipantStatistics(): array
    {
        $totalParticipants = Participant::count();
        $activeParticipants = Participant::where('is_active', true)->count();
        $inactiveParticipants = Participant::where('is_active', false)->count();

        return [
            'total_participants' => $totalParticipants,
            'active_participants' => $activeParticipants,
            'inactive_participants' => $inactiveParticipants,
        ];
    }

    public function getQualificationStatistics(): array
    {
        $total = Qualification::count();
        $qualified = Qualification::where('status', QualificationStatus::Qualifie)->count();
        $rejected = Qualification::where('status', QualificationStatus::Refuse)->count();
        $pending = Qualification::where('status', QualificationStatus::EnAttente)->count();

        return [
            'total_qualifications' => $total,
            'qualified' => $qualified,
            'rejected' => $rejected,
            'pending' => $pending,
        ];
    }

    public function getDiplomationStatistics(): array
    {
        $total = Diplome::count();
        $delivered = Diplome::where('status', DiplomeStatus::Livree)->count();
        $pending = Diplome::where('status', DiplomeStatus::ALivrer)->count();
        $rejected = Diplome::where('status', DiplomeStatus::Refusee)->count();
        $cancelled = Diplome::where('status', DiplomeStatus::Annulee)->count();

        return [
            'total_diplomes' => $total,
            'delivered' => $delivered,
            'pending' => $pending,
            'rejected' => $rejected,
            'cancelled' => $cancelled,
        ];
    }

    public function getLicenceStatistics(): array
    {
        $total = Licence::count();
        $delivered = Licence::where('status', LicenceStatus::Livree)->count();
        $renewed = Licence::where('status', LicenceStatus::Renouvelee)->count();
        $suspended = Licence::where('status', LicenceStatus::Suspendue)->count();
        $expired = Licence::where('status', LicenceStatus::Expirée)->count();
        $pending = Licence::where('status', LicenceStatus::À_livrer)->count();

        return [
            'total_licences' => $total,
            'delivered' => $delivered,
            'renewed' => $renewed,
            'suspended' => $suspended,
            'expired' => $expired,
            'pending' => $pending,
        ];
    }

    private function calculateAverageAttendance(Formation $formation): float
    {
        $participants = $formation->participants()->where('is_active', true)->get();

        if ($participants->isEmpty()) {
            return 0;
        }

        $totalAssiduite = $participants->sum(function ($participant) {
            // This would need a presence tracking system
            // For now, returning a placeholder
            return 0;
        });

        return $participants->count() > 0 ? $totalAssiduite / $participants->count() : 0;
    }

    private function getEmptyReport(): array
    {
        return [
            'total_participants' => 0,
            'qualified_participants' => 0,
            'rejected_participants' => 0,
            'pending_participants' => 0,
            'diplomed_participants' => 0,
            'licensed_participants' => 0,
            'taux_assiduite' => 0,
            'taux_qualification' => 0,
            'taux_diplomation' => 0,
            'taux_licence' => 0,
        ];
    }
}
