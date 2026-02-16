@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
    <div class="mx-auto max-w-md rounded-3xl border border-slate-200/70 bg-white/90 p-8 shadow-xl shadow-slate-200/50 backdrop-blur">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-2xl bg-slate-900"></div>
            <div>
                <h1 class="text-xl font-semibold text-slate-900">Connexion</h1>
                <p class="text-sm text-slate-500">Accédez à votre espace sécurisé.</p>
            </div>
        </div>

        <form class="mt-6 space-y-4" method="post" action="{{ route('login') }}">
            @csrf
            <div>
                <label class="text-sm font-medium text-slate-700" for="email">Email</label>
                <input class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                       id="email"
                       name="email"
                       type="email"
                       required
                       autofocus
                       value="{{ old('email') }}">
                @error('email')
                    <div class="mt-1 text-xs text-rose-600">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-slate-700" for="password">Mot de passe</label>
                <input class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                       id="password"
                       name="password"
                       type="password"
                       required>
                @error('password')
                    <div class="mt-1 text-xs text-rose-600">{{ $message }}</div>
                @enderror
            </div>
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 text-slate-600">
                    <input class="rounded border-slate-300" type="checkbox" name="remember">
                    Se souvenir de moi
                </label>
                <a class="text-slate-700 hover:underline" href="{{ route('password.request') }}">Mot de passe oublié ?</a>
            </div>
            <button class="w-full rounded-xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white transition hover:bg-slate-800" type="submit">
                Se connecter
            </button>
        </form>
    </div>
@endsection
