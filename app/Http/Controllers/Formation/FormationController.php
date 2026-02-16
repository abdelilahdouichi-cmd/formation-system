<?php

namespace App\Http\Controllers\Formation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Formation\StoreFormationRequest;
use App\Http\Requests\Formation\UpdateFormationRequest;
use App\Models\Formation\Formation;
use App\Services\Formation\FormationService;
use App\Enums\Formation\FormationStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FormationController extends Controller
{
    public function __construct(
        public FormationService $formationService,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', Formation::class);

        $formations = Formation::paginate(15);

        return view('admin.formations.index', compact('formations'));
    }

    public function create(): View
    {
        $this->authorize('create', Formation::class);

        return view('admin.formations.create');
    }

    public function store(StoreFormationRequest $request): RedirectResponse
    {
        $formation = $this->formationService->createFormation($request->validated());

        return redirect()->route('admin.formations.show', $formation)
            ->with('success', 'Formation créée avec succès.');
    }

    public function show(Formation $formation): View
    {
        $this->authorize('view', $formation);

        $formation->load(['participants', 'qualifications', 'diplomes', 'licences']);

        return view('admin.formations.show', compact('formation'));
    }

    public function edit(Formation $formation): View
    {
        $this->authorize('update', $formation);

        return view('admin.formations.edit', compact('formation'));
    }

    public function update(UpdateFormationRequest $request, Formation $formation): RedirectResponse
    {
        $this->formationService->updateFormation($formation, $request->validated());

        return redirect()->route('admin.formations.show', $formation)
            ->with('success', 'Formation mise à jour avec succès.');
    }

    public function destroy(Formation $formation): RedirectResponse
    {
        $this->authorize('delete', $formation);

        $formationId = $formation->id;
        $this->formationService->deleteFormation($formation);

        return redirect()->route('admin.formations.index')
            ->with('success', 'Formation supprimée avec succès.');
    }

    public function execute(Formation $formation): RedirectResponse
    {
        $this->authorize('execute', $formation);

        try {
            // allow executing directly from "planifiée" by first programming the formation
            if ($formation->status === FormationStatus::Planifiee) {
                $this->formationService->programFormation($formation);
            }

            $this->formationService->executeFormation($formation, [
                'date_debut_reel' => now(),
                'date_fin_reel' => null,
            ]);

            return back()->with('success', 'Formation marquée comme exécutée.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Formation $formation): RedirectResponse
    {
        $this->authorize('cancel', $formation);

        try {
            $this->formationService->cancelFormation($formation);

            return back()->with('success', 'Formation annulée avec succès.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function archive(Formation $formation): RedirectResponse
    {
        $this->authorize('archive', $formation);

        try {
            $this->formationService->archiveFormation($formation);

            return back()->with('success', 'Formation archivée avec succès.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
