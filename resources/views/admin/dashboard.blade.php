@extends('layouts.admin')

@section('header', 'Admin Dashboard')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-slate-200/70 bg-white/90 p-6 shadow-xl shadow-slate-200/50 backdrop-blur">
            <div class="text-xs uppercase tracking-wide text-slate-400">Utilisateurs</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ $totalUsers }}</div>
            <div class="mt-2 text-sm text-slate-500">Comptes actifs et archivés.</div>
        </div>
        <div class="rounded-3xl border border-slate-200/70 bg-white/90 p-6 shadow-xl shadow-slate-200/50 backdrop-blur">
            <div class="text-xs uppercase tracking-wide text-slate-400">Admins</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ $adminCount }}</div>
            <div class="mt-2 text-sm text-slate-500">Accès au panneau admin.</div>
        </div>
        <div class="rounded-3xl border border-slate-200/70 bg-white/90 p-6 shadow-xl shadow-slate-200/50 backdrop-blur">
            <div class="text-xs uppercase tracking-wide text-slate-400">Super Admins</div>
            <div class="mt-3 text-3xl font-semibold text-slate-900">{{ $superAdminCount }}</div>
            <div class="mt-2 text-sm text-slate-500">Contrôle total de la plateforme.</div>
        </div>
    </div>
@endsection
