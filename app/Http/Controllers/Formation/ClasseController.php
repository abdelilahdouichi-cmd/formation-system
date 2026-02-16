<?php

namespace App\Http\Controllers\Formation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Formation\StoreClasseRequest;
use App\Models\Formation\Classe;
use App\Models\Formation\Formation;
use App\Services\Formation\ClasseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClasseController extends Controller
{
    public function __construct(
        public ClasseService $classeService,
    ) {}

    public function index(Formation $formation): View
    {
        $this->authorize('viewAny', Classe::class);

        $classes = $formation->classes()->paginate(15);

        return view('admin.classes.index', compact('formation', 'classes'));
    }

    public function create(Formation $formation): View
    {
        $this->authorize('create', Classe::class);

        return view('admin.classes.create', compact('formation'));
    }

    public function store(Formation $formation, StoreClasseRequest $request): RedirectResponse
    {
        $data = array_merge($request->validated(), ['formation_id' => $formation->id]);
        $classe = $this->classeService->createClasse($data);

        return redirect()->route('admin.classes.show', ['formation' => $formation, 'classe' => $classe])
            ->with('success', 'Classe créée avec succès.');
    }

    public function show(Formation $formation, Classe $classe): View
    {
        $this->authorize('view', $classe);

        $classe->load(['formation', 'participants']);

        return view('admin.classes.show', compact('formation', 'classe'));
    }

    public function edit(Formation $formation, Classe $classe): View
    {
        $this->authorize('update', $classe);

        return view('admin.classes.edit', compact('formation', 'classe'));
    }

    public function update(Formation $formation, StoreClasseRequest $request, Classe $classe): RedirectResponse
    {
        $this->classeService->updateClasse($classe, $request->validated());

        return redirect()->route('admin.classes.show', ['formation' => $formation, 'classe' => $classe])
            ->with('success', 'Classe mise à jour avec succès.');
    }

    public function destroy(Formation $formation, Classe $classe): RedirectResponse
    {
        $this->authorize('delete', $classe);

        try {
            $this->classeService->deleteClasse($classe);

            return redirect()->route('admin.classes.index', $formation)
                ->with('success', 'Classe supprimée avec succès.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
