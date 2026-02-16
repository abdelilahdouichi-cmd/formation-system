<?php

namespace App\Http\Controllers\Formation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Formation\StoreLicenceRequest;
use App\Models\Formation\Formation;
use App\Models\Formation\Licence;
use App\Services\Formation\LicenceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LicenceController extends Controller
{
    public function __construct(
        public LicenceService $licenceService,
    ) {}

    public function index(Formation $formation): View
    {
        $this->authorize('viewAny', Licence::class);

        $licences = $formation->licences()->paginate(15);

        return view('admin.licences.index', compact('formation', 'licences'));
    }

    public function create(Formation $formation): View
    {
        $this->authorize('create', Licence::class);

        $formation->load('participants');

        return view('admin.licences.create', compact('formation'));
    }

    public function store(Formation $formation, StoreLicenceRequest $request): RedirectResponse
    {
        $licence = $this->licenceService->createLicence($request->validated());

        return redirect()->route('admin.licences.show', ['formation' => $formation, 'licence' => $licence])
            ->with('success', 'Licence créée avec succès.');
    }

    public function show(Formation $formation, Licence $licence): View
    {
        $this->authorize('view', $licence);

        $licence->load(['participant', 'formation', 'justificatifs']);

        return view('admin.licences.show', compact('formation', 'licence'));
    }

    public function deliver(Formation $formation, Licence $licence): RedirectResponse
    {
        $this->authorize('deliver', $licence);

        try {
            $this->licenceService->deliverLicence($licence);

            return back()->with('success', 'Licence livrée avec succès.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function renew(Formation $formation, Licence $licence): RedirectResponse
    {
        $this->authorize('renew', $licence);

        try {
            $dateExpiration = request('date_expiration');
            $this->licenceService->renewLicence($licence, ['date_expiration' => $dateExpiration]);

            return back()->with('success', 'Licence renouvelée avec succès.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function suspend(Formation $formation, Licence $licence): RedirectResponse
    {
        $this->authorize('suspend', $licence);

        try {
            $reason = request('reason');
            $this->licenceService->suspendLicence($licence, $reason);

            return back()->with('success', 'Licence suspendue avec succès.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Formation $formation, Licence $licence): RedirectResponse
    {
        $this->authorize('delete', $licence);

        $this->licenceService->deleteLicence($licence);

        return redirect()->route('admin.licences.index', $formation)
            ->with('success', 'Licence supprimée avec succès.');
    }
}
