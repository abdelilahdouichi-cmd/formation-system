<?php

namespace App\Http\Controllers\Formation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Formation\StoreJustificatifRequest;
use App\Models\Formation\Formation;
use App\Models\Formation\Justificatif;
use App\Services\Formation\JustificatifService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class JustificatifController extends Controller
{
    public function __construct(
        public JustificatifService $justificatifService,
    ) {}

    public function index(Formation $formation): View
    {
        $this->authorize('viewAny', Justificatif::class);

        $justificatifs = $formation->justificatifs()->paginate(15);

        return view('admin.justificatifs.index', compact('formation', 'justificatifs'));
    }

    public function create(Formation $formation): View
    {
        $this->authorize('create', Justificatif::class);

        $formation->load('participants');

        return view('admin.justificatifs.create', compact('formation'));
    }

    public function store(Formation $formation, StoreJustificatifRequest $request): RedirectResponse
    {
        $justificatif = $this->justificatifService->createJustificatif($request->validated());

        return redirect()->route('admin.justificatifs.show', ['formation' => $formation, 'justificatif' => $justificatif])
            ->with('success', 'Justificatif créé avec succès.');
    }

    public function show(Formation $formation, Justificatif $justificatif): View
    {
        $this->authorize('view', $justificatif);

        $justificatif->load(['formation', 'participant']);

        return view('admin.justificatifs.show', compact('formation', 'justificatif'));
    }

    public function verify(Formation $formation, Justificatif $justificatif): RedirectResponse
    {
        $this->authorize('verify', $justificatif);

        try {
            $this->justificatifService->verifyJustificatif($justificatif);

            return back()->with('success', 'Justificatif vérifié avec succès.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Formation $formation, Justificatif $justificatif): RedirectResponse
    {
        $this->authorize('reject', $justificatif);

        try {
            $reason = request('reason');
            $this->justificatifService->rejectJustificatif($justificatif, $reason);

            return back()->with('success', 'Justificatif rejeté avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Formation $formation, Justificatif $justificatif): RedirectResponse
    {
        $this->authorize('delete', $justificatif);

        try {
            $this->justificatifService->deleteJustificatif($justificatif);

            return redirect()->route('admin.justificatifs.index', $formation)
                ->with('success', 'Justificatif supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
