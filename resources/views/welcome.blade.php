@extends('layouts.app')

@section('title', 'Bienvenue')

@section('content')
    <div class="grid items-center gap-10 lg:grid-cols-2">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Admin Suite</p>
            <h1 class="mt-4 text-4xl font-semibold leading-tight text-slate-900 sm:text-5xl">
                Centralisez votre gestion d’utilisateurs
                <span class="text-slate-500">avec précision.</span>
            </h1>
            <p class="mt-4 text-base text-slate-600">
                Authentification robuste, rôles hiérarchisés, et tableaux de bord
                clairs pour superviser votre application en toute confiance.
            </p>
            <div class="mt-6 flex flex-wrap items-center gap-3">
                @auth
                    <a class="rounded-full bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800"
                       href="{{ route('dashboard') }}">
                        Accéder au dashboard
                    </a>
                @endauth
                @guest
                    <a class="rounded-full bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800"
                       href="{{ route('login') }}">
                        Se connecter
                    </a>
                @endguest
                <span class="text-sm text-slate-500">Sécurisé · Flexible · Moderne</span>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-3xl border border-slate-200/70 bg-white/90 p-6 shadow-xl shadow-slate-200/50 backdrop-blur">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">Statut plateforme</p>
                        <p class="mt-2 text-lg font-semibold text-slate-900">Accès vérifié</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-900 text-xs font-semibold text-white">
                        OK
                    </div>
                </div>
                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-xs uppercase text-slate-400">Rôle</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">Super Admin</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-xs uppercase text-slate-400">Sessions</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">Actives</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-slate-200/70 bg-white/90 p-4 shadow-lg shadow-slate-200/40">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Rôles</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900">Super Admin · Admin · Utilisateur</p>
                </div>
                <div class="rounded-2xl border border-slate-200/70 bg-white/90 p-4 shadow-lg shadow-slate-200/40">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Sécurité</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900">Mots de passe chiffrés</p>
                </div>
            </div>
        </div>
    </div>
@endsection
