<?php

namespace App\Http\Controllers\Formation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Formation\StoreDiplomeRequest;
use App\Models\Formation\Diplome;
use App\Models\Formation\Formation;
use App\Services\Formation\DiplomeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DiplomeController extends Controller
{
    public function __construct(
        public DiplomeService $diplomeService,
    ) {}

    public function index(Formation $formation): View
    {
        $this->authorize('viewAny', Diplome::class);

        $diplomes = $formation->diplomes()->paginate(15);

        return view('admin.diplomes.index', compact('formation', 'diplomes'));
    }

    public function create(Formation $formation): View
    {
        $this->authorize('create', Diplome::class);

        $formation->load('participants');

        return view('admin.diplomes.create', compact('formation'));
    }

    public function store(Formation $formation, StoreDiplomeRequest $request): RedirectResponse
    {
        $diplome = $this->diplomeService->createDiplome($request->validated());

        return redirect()->route('admin.diplomes.show', ['formation' => $formation, 'diplome' => $diplome])
            ->with('success', 'Diplôme créé avec succès.');
    }

    public function show(Formation $formation, Diplome $diplome): View
    {
        $this->authorize('view', $diplome);

        $diplome->load(['participant', 'formation', 'justificatifs']);

        return view('admin.diplomes.show', compact('formation', 'diplome'));
    }

    public function deliver(Formation $formation, Diplome $diplome): RedirectResponse
    {
        $this->authorize('deliver', $diplome);

        try {
            $this->diplomeService->deliverDiplome($diplome);

            return back()->with('success', 'Diplôme livré avec succès.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Formation $formation, Diplome $diplome): RedirectResponse
    {
        $this->authorize('reject', $diplome);

        try {
            $reason = request('reason');
            $this->diplomeService->rejectDiplome($diplome, $reason);

            return back()->with('success', 'Diplôme refusé avec succès.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Formation $formation, Diplome $diplome): RedirectResponse
    {
        $this->authorize('cancel', $diplome);

        try {
            $this->diplomeService->cancelDiplome($diplome);

            return back()->with('success', 'Diplôme annulé avec succès.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Formation $formation, Diplome $diplome): RedirectResponse
    {
        $this->authorize('delete', $diplome);

        $this->diplomeService->deleteDiplome($diplome);

        return redirect()->route('admin.diplomes.index', $formation)
            ->with('success', 'Diplôme supprimé avec succès.');
    }
}
