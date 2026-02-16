@extends('layouts.admin')

@section('header', 'Modifier utilisateur')

@section('content')
    <div class="rounded-3xl border border-slate-200/70 bg-white/90 p-8 shadow-xl shadow-slate-200/50 backdrop-blur">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-lg font-semibold text-slate-900">Éditer {{ $user->name }}</h1>
                <p class="mt-1 text-sm text-slate-500">Mettez à jour les informations et le rôle.</p>
            </div>
            <div class="text-right text-xs text-slate-400">
                Créé le {{ $user->created_at->format('Y-m-d') }}
            </div>
        </div>

        <form class="mt-6 space-y-6" method="post" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')
            @include('admin.users._form', ['user' => $user])

            <div class="flex items-center justify-end gap-3">
                <a class="rounded-full border border-slate-200 px-4 py-2 text-sm text-slate-600 transition hover:bg-slate-50" href="{{ route('admin.users.index') }}">
                    Retour
                </a>
                <button class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800" type="submit">
                    Enregistrer
                </button>
            </div>
        </form>

        <form class="mt-4" method="post" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Supprimer cet utilisateur ?');">
            @csrf
            @method('DELETE')
            <button class="rounded-full border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-50" type="submit">
                Supprimer
            </button>
        </form>
    </div>
@endsection
