@extends('layouts.admin')

@section('header', 'Formations')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-lg font-semibold text-slate-900">Formations</h1>
            <p class="text-sm text-slate-500">Gérez les formations disponibles.</p>
        </div>
        <a class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white" href="{{ route('admin.formations.create') }}">+ Nouvelle formation</a>
    </div>

    <div class="overflow-hidden rounded-3xl border p-4 bg-white/90 shadow-xl">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Code</th>
                    <th class="px-4 py-3">Nom</th>
                    <th class="px-4 py-3">Niveau</th>
                    <th class="px-4 py-3">Dates</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($formations as $formation)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $formation->code }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $formation->nom }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $formation->niveau?->nom ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $formation->date_debut_prevue?->toDateString() ?? '—' }} — {{ $formation->date_fin_prevue?->toDateString() ?? '—' }}</td>
                        <td class="px-4 py-3"><span class="rounded-full px-3 py-1 text-xs font-semibold bg-slate-100 text-slate-700">{{ $formation->status?->label() ?? $formation->status }}</span></td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.formations.show', $formation) }}" class="rounded-full border px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Voir</a>
                                <a href="{{ route('admin.formations.edit', $formation) }}" class="rounded-full border px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Éditer</a>
                                <form method="POST" action="{{ route('admin.formations.destroy', $formation) }}" onsubmit="return confirm('Supprimer cette formation ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-full border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-6 text-center text-slate-500" colspan="6">Aucune formation trouvée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $formations->links() }}
    </div>
</div>
@endsection
