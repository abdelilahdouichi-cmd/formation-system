@extends('layouts.admin')

@section('header', $participant->prenom . ' ' . $participant->nom)

@section('content')
<div class="space-y-6">
    <div class="rounded-3xl border p-6 bg-white/90">
        <div class="grid gap-4 lg:grid-cols-3">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">{{ $participant->prenom }} {{ $participant->nom }}</h2>
                <p class="text-sm text-slate-500">{{ $participant->email }}</p>
            </div>
            <div>
                <div class="text-xs text-slate-400">Numéro d'identité</div>
                <div class="mt-1 font-medium">{{ $participant->numero_identite }}</div>
                <div class="text-xs text-slate-400 mt-3">Période</div>
                <div class="mt-1 font-medium">{{ $participant->date_debut?->toDateString() ?? '—' }} — {{ $participant->date_fin?->toDateString() ?? '—' }}</div>
            </div>
            <div class="flex items-center justify-end gap-2">
                <a href="{{ route('admin.participants.edit', ['formation' => $formation, 'participant' => $participant]) }}" class="rounded-full border px-4 py-2 text-sm text-slate-700">Éditer</a>
                <form method="POST" action="{{ route('admin.participants.destroy', ['formation' => $formation, 'participant' => $participant]) }}" onsubmit="return confirm('Supprimer ce participant ?');">
                    @csrf
                    @method('DELETE')
                    <button class="rounded-full border border-rose-200 px-4 py-2 text-sm text-rose-600">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
