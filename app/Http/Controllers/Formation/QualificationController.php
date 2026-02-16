<?php

namespace App\Http\Controllers\Formation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Formation\StoreQualificationRequest;
use App\Models\Formation\Formation;
use App\Models\Formation\Qualification;
use App\Services\Formation\QualificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QualificationController extends Controller
{
    public function __construct(
        public QualificationService $qualificationService,
    ) {}

    public function index(Formation $formation): View
    {
        $this->authorize('viewAny', Qualification::class);

        $qualifications = $formation->qualifications()->paginate(15);

        return view('admin.qualifications.index', compact('formation', 'qualifications'));
    }

    public function create(Formation $formation): View
    {
        $this->authorize('create', Qualification::class);

        $formation->load('participants', 'instructeurs');

        return view('admin.qualifications.create', compact('formation'));
    }

    public function store(Formation $formation, StoreQualificationRequest $request): RedirectResponse
    {
        $qualification = $this->qualificationService->recordQualification($request->validated());

        return redirect()->route('admin.qualifications.show', ['formation' => $formation, 'qualification' => $qualification])
            ->with('success', 'Qualification enregistrée avec succès.');
    }

    public function show(Formation $formation, Qualification $qualification): View
    {
        $this->authorize('view', $qualification);

        $qualification->load(['participant', 'formation', 'evaluateur']);

        return view('admin.qualifications.show', compact('formation', 'qualification'));
    }

    public function approve(Formation $formation, Qualification $qualification): RedirectResponse
    {
        $this->authorize('approve', $qualification);

        try {
            $this->qualificationService->approveQualification($qualification);

            return back()->with('success', 'Qualification approuvée avec succès.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Formation $formation, Qualification $qualification): RedirectResponse
    {
        $this->authorize('reject', $qualification);

        try {
            $observation = request('observation');
            $this->qualificationService->rejectQualification($qualification, $observation);

            return back()->with('success', 'Qualification rejetée avec succès.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Formation $formation, Qualification $qualification): RedirectResponse
    {
        $this->authorize('delete', $qualification);

        $this->qualificationService->deleteQualification($qualification);

        return redirect()->route('admin.qualifications.index', $formation)
            ->with('success', 'Qualification supprimée avec succès.');
    }
}
