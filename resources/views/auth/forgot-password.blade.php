@extends('layouts.app')

@section('title', 'Mot de passe oublié')

@section('content')
    <div class="mx-auto max-w-md rounded-3xl border border-slate-200/70 bg-white/90 p-8 shadow-xl shadow-slate-200/50 backdrop-blur">
        <h1 class="text-xl font-semibold text-slate-900">Réinitialiser le mot de passe</h1>
        <p class="mt-1 text-sm text-slate-500">Recevez un lien sécurisé pour définir un nouveau mot de passe.</p>

        <form class="mt-6 space-y-4" method="post" action="{{ route('password.email') }}">
            @csrf
            <div>
                <label class="text-sm font-medium text-slate-700" for="email">Email</label>
                <input class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                       id="email"
                       name="email"
                       type="email"
                       required
                       value="{{ old('email') }}">
                @error('email')
                    <div class="mt-1 text-xs text-rose-600">{{ $message }}</div>
                @enderror
            </div>
            <button class="w-full rounded-xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white transition hover:bg-slate-800" type="submit">
                Envoyer le lien
            </button>
        </form>
    </div>
@endsection
