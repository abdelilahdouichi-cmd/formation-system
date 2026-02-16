<?php

namespace App\Http\Controllers\Formation;

use App\Http\Controllers\Controller;
use App\Models\Formation\Formation;
use App\Services\Formation\ReportService;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        public ReportService $reportService,
    ) {}

    public function dashboard(): View
    {
        $this->authorize('viewAny', Formation::class);

        $formationStats = $this->reportService->getFormationStatistics();
        $participantStats = $this->reportService->getParticipantStatistics();
        $qualificationStats = $this->reportService->getQualificationStatistics();
        $diplomationStats = $this->reportService->getDiplomationStatistics();
        $licenceStats = $this->reportService->getLicenceStatistics();

        return view('admin.reports.dashboard', compact(
            'formationStats',
            'participantStats',
            'qualificationStats',
            'diplomationStats',
            'licenceStats'
        ));
    }

    public function formationReport(Formation $formation): View
    {
        $this->authorize('view', $formation);

        $report = $this->reportService->generateFormationReport($formation);
        $situationSCE = $this->reportService->generateSituationSCE($formation);

        return view('admin.reports.formation', compact('formation', 'report', 'situationSCE'));
    }

    public function situationSCE(Formation $formation): View
    {
        $this->authorize('view', $formation);

        $situationSCE = $this->reportService->generateSituationSCE($formation);

        return view('admin.reports.situation-sce', compact('formation', 'situationSCE'));
    }

    public function qualificationRate(): View
    {
        $this->authorize('viewAny', Formation::class);

        $formations = Formation::with(['qualifications'])->get()->map(function ($formation) {
            return [
                'formation' => $formation,
                'qualification_rate' => $this->reportService->generateFormationReport($formation)['taux_qualification'],
            ];
        });

        return view('admin.reports.qualification-rate', compact('formations'));
    }

    public function diplomationRate(): View
    {
        $this->authorize('viewAny', Formation::class);

        $formations = Formation::with(['diplomes'])->get()->map(function ($formation) {
            return [
                'formation' => $formation,
                'diplomation_rate' => $this->reportService->generateFormationReport($formation)['taux_diplomation'],
            ];
        });

        return view('admin.reports.diplomation-rate', compact('formations'));
    }

    public function licenceRate(): View
    {
        $this->authorize('viewAny', Formation::class);

        $formations = Formation::with(['licences'])->get()->map(function ($formation) {
            return [
                'formation' => $formation,
                'licence_rate' => $this->reportService->generateFormationReport($formation)['taux_licence'],
            ];
        });

        return view('admin.reports.licence-rate', compact('formations'));
    }

    public function attendanceRate(): View
    {
        $this->authorize('viewAny', Formation::class);

        $formations = Formation::with(['participants'])->get()->map(function ($formation) {
            return [
                'formation' => $formation,
                'attendance_rate' => $this->reportService->generateFormationReport($formation)['taux_assiduite'],
            ];
        });

        return view('admin.reports.attendance-rate', compact('formations'));
    }

    public function exportFormationReport(Formation $formation): \Illuminate\Http\Response
    {
        $this->authorize('view', $formation);

        $report = $this->reportService->generateFormationReport($formation);

        // This would need a PDF export library like barryvdh/laravel-dompdf
        return response()->json($report);
    }
}
