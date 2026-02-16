@extends('layouts.admin')

@section('header', 'Ajouter un participant')

@section('content')
<div class="space-y-6">
    <div class="rounded-3xl border p-6 bg-white/90">
        <form method="POST" action="{{ route('admin.participants.store', $formation) }}">
            @csrf
            <div class="grid gap-4 lg:grid-cols-2">
                <div>
                    <label class="text-xs font-medium uppercase text-slate-400">Prénom</label>
                    <input name="prenom" value="{{ old('prenom') }}" class="mt-2 w-full rounded-xl border px-3 py-2 text-sm" />
                    @error('prenom') <div class="text-rose-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-medium uppercase text-slate-400">Nom</label>
                    <input name="nom" value="{{ old('nom') }}" class="mt-2 w-full rounded-xl border px-3 py-2 text-sm" />
                    @error('nom') <div class="text-rose-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-medium uppercase text-slate-400">Email</label>
                    <input name="email" type="email" value="{{ old('email') }}" class="mt-2 w-full rounded-xl border px-3 py-2 text-sm" />
                    @error('email') <div class="text-rose-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-medium uppercase text-slate-400">Numéro d'identité</label>
                    <input name="numero_identite" value="{{ old('numero_identite') }}" class="mt-2 w-full rounded-xl border px-3 py-2 text-sm" />
                    @error('numero_identite') <div class="text-rose-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-medium uppercase text-slate-400">Date début</label>
                    <input name="date_debut" type="date" value="{{ old('date_debut') }}" class="mt-2 w-full rounded-xl border px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="text-xs font-medium uppercase text-slate-400">Date fin</label>
                    <input name="date_fin" type="date" value="{{ old('date_fin') }}" class="mt-2 w-full rounded-xl border px-3 py-2 text-sm" />
                </div>
            </div>

            <div class="mt-4 flex justify-end gap-3">
                <a href="{{ route('admin.participants.index', $formation) }}" class="rounded-full border px-4 py-2 text-sm text-slate-700">Annuler</a>
                <button class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
