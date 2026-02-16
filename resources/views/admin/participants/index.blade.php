@extends('layouts.admin')

@section('header', 'Participants')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-lg font-semibold text-slate-900">Participants — {{ $formation->nom ?? $formation->code }}</h1>
            <p class="text-sm text-slate-500">Liste des participants pour la formation.</p>
        </div>
        <a class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800" href="{{ route('admin.participants.create', $formation) }}">
            + Ajouter un participant
        </a>
    </div>

    <div class="overflow-hidden rounded-3xl border border-slate-200/70 bg-white/90 shadow-xl p-4">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nom</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3"># ID</th>
                    <th class="px-4 py-3">Dates</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($participants as $participant)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $participant->prenom }} {{ $participant->nom }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $participant->email }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $participant->numero_identite }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $participant->date_debut?->toDateString() ?? '—' }} — {{ $participant->date_fin?->toDateString() ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $participant->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                {{ $participant->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.participants.show', ['formation' => $formation, 'participant' => $participant]) }}" class="rounded-full border px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Voir</a>
                                <a href="{{ route('admin.participants.edit', ['formation' => $formation, 'participant' => $participant]) }}" class="rounded-full border px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Éditer</a>
                                <form method="POST" action="{{ route('admin.participants.destroy', ['formation' => $formation, 'participant' => $participant]) }}" onsubmit="return confirm('Supprimer ce participant ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-full border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50" type="submit">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-6 text-center text-slate-500" colspan="6">Aucun participant trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $participants->links() }}
    </div>
</div>
@endsection
