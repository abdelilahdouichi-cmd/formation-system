@extends('layouts.admin')

@section('header', 'Modifier la formation')

@section('content')
<div class="space-y-6">
    <div class="rounded-3xl border p-6 bg-white/90">
        <form method="POST" action="{{ route('admin.formations.update', $formation) }}">
            @csrf
            @method('PATCH')
            <div class="grid gap-4 lg:grid-cols-2">
                <div>
                    <label class="text-xs font-medium uppercase text-slate-400">Code</label>
                    <input name="code" value="{{ old('code', $formation->code) }}" class="mt-2 w-full rounded-xl border px-3 py-2 text-sm" />
                    @error('code') <div class="text-rose-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-medium uppercase text-slate-400">Nom</label>
                    <input name="nom" value="{{ old('nom', $formation->nom) }}" class="mt-2 w-full rounded-xl border px-3 py-2 text-sm" />
                    @error('nom') <div class="text-rose-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="lg:col-span-2">
                    <label class="text-xs font-medium uppercase text-slate-400">Description</label>
                    <textarea name="description" class="mt-2 w-full rounded-xl border px-3 py-2 text-sm">{{ old('description', $formation->description) }}</textarea>
                </div>
                <div>
                    <label class="text-xs font-medium uppercase text-slate-400">Date début</label>
                    <input name="date_debut" type="date" value="{{ old('date_debut', $formation->date_debut_prevue?->toDateString()) }}" class="mt-2 w-full rounded-xl border px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="text-xs font-medium uppercase text-slate-400">Date fin</label>
                    <input name="date_fin" type="date" value="{{ old('date_fin', $formation->date_fin_prevue?->toDateString()) }}" class="mt-2 w-full rounded-xl border px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="text-xs font-medium uppercase text-slate-400">Durée (heures)</label>
                    <input name="duree_heures" type="number" value="{{ old('duree_heures', $formation->duree_heures) }}" class="mt-2 w-full rounded-xl border px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="text-xs font-medium uppercase text-slate-400">Nombre participants</label>
                    <input name="nombre_participants" type="number" value="{{ old('nombre_participants', $formation->nombreParticipantsMax ?? $formation->nombreParticipantsMin ?? '') }}" class="mt-2 w-full rounded-xl border px-3 py-2 text-sm" />
                </div>
            </div>

            <div class="mt-4 flex justify-end gap-3">
                <a href="{{ route('admin.formations.show', $formation) }}" class="rounded-full border px-4 py-2 text-sm text-slate-700">Annuler</a>
                <button class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
