<?php

namespace App\Http\Controllers\Formation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Formation\StoreParticipantRequest;
use App\Http\Requests\Formation\UpdateParticipantRequest;
use App\Models\Formation\Formation;
use App\Models\Formation\Participant;
use App\Services\Formation\ParticipantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ParticipantController extends Controller
{
    public function __construct(
        public ParticipantService $participantService,
    ) {}

    public function index(Formation $formation): View
    {
        $this->authorize('viewAny', Participant::class);

        $formation->load('participants');
        $participants = $formation->participants()->paginate(15);

        return view('admin.participants.index', compact('formation', 'participants'));
    }

    public function create(Formation $formation): View
    {
        $this->authorize('create', Participant::class);

        $formation->load('classes');

        return view('admin.participants.create', compact('formation'));
    }

    public function store(Formation $formation, StoreParticipantRequest $request): RedirectResponse
    {
        $participant = $this->participantService->createParticipant($formation, $request->validated());

        return redirect()->route('admin.participants.show', ['formation' => $formation, 'participant' => $participant])
            ->with('success', 'Participant ajouté avec succès.');
    }

    public function show(Formation $formation, Participant $participant): View
    {
        $this->authorize('view', $participant);

        $participant->load(['formation', 'classe', 'qualification', 'diplome', 'licence']);

        return view('admin.participants.show', compact('formation', 'participant'));
    }

    public function edit(Formation $formation, Participant $participant): View
    {
        $this->authorize('update', $participant);

        $formation->load('classes');

        return view('admin.participants.edit', compact('formation', 'participant'));
    }

    public function update(Formation $formation, UpdateParticipantRequest $request, Participant $participant): RedirectResponse
    {
        $this->participantService->updateParticipant($participant, $request->validated());

        return redirect()->route('admin.participants.show', ['formation' => $formation, 'participant' => $participant])
            ->with('success', 'Participant mis à jour avec succès.');
    }

    public function destroy(Formation $formation, Participant $participant): RedirectResponse
    {
        $this->authorize('delete', $participant);

        $this->participantService->deleteParticipant($participant);

        return redirect()->route('admin.participants.index', $formation)
            ->with('success', 'Participant supprimé avec succès.');
    }

    public function assignToClasse(Formation $formation, Participant $participant): RedirectResponse
    {
        $this->authorize('assignToClasse', $participant);

        try {
            $classeId = request('classe_id');
            $this->participantService->assignToClasse($participant, $classeId);

            return back()->with('success', 'Participant assigné à la classe avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function deactivate(Formation $formation, Participant $participant): RedirectResponse
    {
        $this->authorize('deactivate', $participant);

        $this->participantService->deactivateParticipant($participant);

        return back()->with('success', 'Participant désactivé avec succès.');
    }

    public function activate(Formation $formation, Participant $participant): RedirectResponse
    {
        $this->authorize('deactivate', $participant);

        $this->participantService->activateParticipant($participant);

        return back()->with('success', 'Participant réactivé avec succès.');
    }
}
