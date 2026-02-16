@extends('layouts.admin')

@section('header', 'Ajouter un utilisateur')

@section('content')
    <div class="rounded-3xl border border-slate-200/70 bg-white/90 p-8 shadow-xl shadow-slate-200/50 backdrop-blur">
        <h1 class="text-lg font-semibold text-slate-900">Nouvel utilisateur</h1>
        <p class="mt-1 text-sm text-slate-500">Créez un compte et assignez un rôle.</p>

        <form class="mt-6 space-y-6" method="post" action="{{ route('admin.users.store') }}">
            @csrf
            @include('admin.users._form')

            <div class="flex items-center justify-end gap-3">
                <a class="rounded-full border border-slate-200 px-4 py-2 text-sm text-slate-600 transition hover:bg-slate-50" href="{{ route('admin.users.index') }}">
                    Annuler
                </a>
                <button class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800" type="submit">
                    Créer
                </button>
            </div>
        </form>
    </div>
@endsection
