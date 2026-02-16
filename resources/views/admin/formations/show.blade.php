@extends('layouts.admin')

@section('header', $formation->nom ?? $formation->code)

@section('content')
<div class="space-y-6">
    <div class="rounded-3xl border p-6 bg-white/90">
        <div class="grid gap-4 lg:grid-cols-3">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">{{ $formation->nom ?? $formation->code }}</h2>
                <p class="text-sm text-slate-500">{{ $formation->description }}</p>
            </div>
            <div>
                <div class="text-xs text-slate-400">Dates prévues</div>
                <div class="mt-1 font-medium">{{ $formation->date_debut_prevue?->toDateString() ?? '—' }} — {{ $formation->date_fin_prevue?->toDateString() ?? '—' }}</div>
                <div class="text-xs text-slate-400 mt-3">Durée</div>
                <div class="mt-1 font-medium">{{ $formation->duree_heures ?? '—' }} heures</div>
            </div>
            <div class="flex items-center justify-end gap-2">
                <a href="{{ route('admin.formations.edit', $formation) }}" class="rounded-full border px-4 py-2 text-sm text-slate-700">Éditer</a>
                <form method="POST" action="{{ route('admin.formations.destroy', $formation) }}" onsubmit="return confirm('Supprimer cette formation ?');">
                    @csrf
                    @method('DELETE')
                    <button class="rounded-full border border-rose-200 px-4 py-2 text-sm text-rose-600">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
