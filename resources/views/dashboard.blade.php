@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="rounded-3xl border border-slate-200/70 bg-white/90 p-8 shadow-xl shadow-slate-200/50 backdrop-blur">
        <h1 class="text-2xl font-semibold text-slate-900">Bienvenue, {{ auth()->user()->name }}</h1>
        <p class="mt-2 text-sm text-slate-600">
            Votre compte est actif. Vous pouvez consulter vos informations ou accéder aux fonctionnalités avancées.
        </p>
        <div class="mt-6 flex flex-wrap gap-3">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                Dernière connexion :
                <span class="font-semibold text-slate-900">
                    {{ auth()->user()->last_login_at?->format('Y-m-d H:i') ?? '—' }}
                </span>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                Rôle :
                <span class="font-semibold text-slate-900">
                    {{ auth()->user()->role?->label() ?? auth()->user()->role }}
                </span>
            </div>
        </div>
        @if (auth()->user()->canAccessAdmin())
            <div class="mt-6">
                <a class="inline-flex items-center rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                   href="{{ route('admin.dashboard') }}">
                    Accéder au panneau admin
                </a>
            </div>
        @endif
    </div>
@endsection
