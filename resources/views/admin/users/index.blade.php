@extends('layouts.admin')

@section('header', 'User Management')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-lg font-semibold text-slate-900">User Management</h1>
                <p class="text-sm text-slate-500">Gérez les comptes, rôles et statuts.</p>
            </div>
            <a class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
               href="{{ route('admin.users.create') }}">
                + Ajouter un utilisateur
            </a>
        </div>

        <form class="rounded-3xl border border-slate-200/70 bg-white/90 p-5 shadow-xl shadow-slate-200/50 backdrop-blur" method="get" action="{{ route('admin.users.index') }}">
            <div class="grid gap-4 lg:grid-cols-4">
                <div class="lg:col-span-2">
                    <label class="text-xs font-medium uppercase text-slate-400" for="search">Recherche</label>
                    <input class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                           id="search"
                           name="search"
                           placeholder="Nom, email, téléphone..."
                           value="{{ $filters['search'] }}">
                </div>
                <div>
                    <label class="text-xs font-medium uppercase text-slate-400" for="role">Rôle</label>
                    <select class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" id="role" name="role">
                        <option value="">Tous les rôles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role['value'] }}" @selected($filters['role'] === $role['value'])>
                                {{ $role['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium uppercase text-slate-400" for="status">Statut</label>
                    <select class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" id="status" name="status">
                        <option value="">Tous les statuts</option>
                        <option value="active" @selected($filters['status'] === 'active')>Actif</option>
                        <option value="inactive" @selected($filters['status'] === 'inactive')>Inactif</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <label class="text-xs font-medium uppercase text-slate-400" for="sort">Trier par</label>
                    <select class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" id="sort" name="sort">
                        <option value="name_asc" @selected($filters['sort'] === 'name_asc' || $filters['sort'] === '')>Nom A-Z</option>
                        <option value="name_desc" @selected($filters['sort'] === 'name_desc')>Nom Z-A</option>
                        <option value="last_login_desc" @selected($filters['sort'] === 'last_login_desc')>Dernière connexion (récent)</option>
                        <option value="last_login_asc" @selected($filters['sort'] === 'last_login_asc')>Dernière connexion (ancien)</option>
                    </select>
                </div>
                <button class="rounded-full border border-slate-200 px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-50" type="submit">
                    Appliquer
                </button>
            </div>
        </form>

        <div class="overflow-hidden rounded-3xl border border-slate-200/70 bg-white/90 shadow-xl shadow-slate-200/50 backdrop-blur">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Profil</th>
                        <th class="px-4 py-3">Nom</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Téléphone</th>
                        <th class="px-4 py-3">Rôle</th>
                        <th class="px-4 py-3">Statut</th>
                        <th class="px-4 py-3">Dernière connexion</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-xs font-semibold text-white">
                                    {{ str($user->name)->substr(0, 2)->upper() }}
                                </div>
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $user->email }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $user->phone ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                    {{ $user->role?->label() ?? $user->role }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $user->last_login_at?->format('Y-m-d H:i') ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a class="rounded-full border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100"
                                       href="{{ route('admin.users.edit', $user) }}">
                                        Éditer
                                    </a>
                                    <form method="post" action="{{ route('admin.users.destroy', $user) }}"
                                          onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-full border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-50" type="submit">
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-6 text-center text-slate-500" colspan="8">
                                Aucun utilisateur trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $users->links() }}
        </div>
    </div>
@endsection
