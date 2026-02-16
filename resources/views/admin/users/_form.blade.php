@php($isEdit = isset($user))
@php($canEditRole = $canEditRole ?? false)
@php($currentRole = old('role', $user->role->value ?? 'user'))

<div class="grid gap-4 lg:grid-cols-2">
    <div>
        <label class="text-sm font-medium text-slate-700" for="name">Nom complet</label>
        <input class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
               id="name"
               name="name"
               type="text"
               required
               value="{{ old('name', $user->name ?? '') }}">
        @error('name')
            <div class="mt-1 text-xs text-rose-600">{{ $message }}</div>
        @enderror
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700" for="email">Email</label>
        <input class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
               id="email"
               name="email"
               type="email"
               required
               value="{{ old('email', $user->email ?? '') }}">
        @error('email')
            <div class="mt-1 text-xs text-rose-600">{{ $message }}</div>
        @enderror
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700" for="phone">Téléphone</label>
        <input class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
               id="phone"
               name="phone"
               type="text"
               value="{{ old('phone', $user->phone ?? '') }}">
        @error('phone')
            <div class="mt-1 text-xs text-rose-600">{{ $message }}</div>
        @enderror
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700" for="role">Rôle</label>
        <select class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                id="role"
                name="role"
                required
                @disabled(! $canEditRole)>
            @foreach ($roles as $role)
                <option value="{{ $role['value'] }}" @selected($currentRole === $role['value'])>
                    {{ $role['label'] }}
                </option>
            @endforeach
        </select>
        @if (! $canEditRole)
            <input type="hidden" name="role" value="{{ $currentRole }}">
            <p class="mt-1 text-xs text-slate-400">Seul un super admin peut modifier le rôle.</p>
        @endif
        @error('role')
            <div class="mt-1 text-xs text-rose-600">{{ $message }}</div>
        @enderror
    </div>
    @php($activeValue = old('is_active', $user->is_active ?? true))
    @php($activeValue = $activeValue === false ? '0' : (string) $activeValue)
    <div>
        <label class="text-sm font-medium text-slate-700" for="is_active">Statut</label>
        <select class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" id="is_active" name="is_active" required>
            <option value="1" @selected($activeValue === '1')>Actif</option>
            <option value="0" @selected($activeValue === '0')>Inactif</option>
        </select>
        @error('is_active')
            <div class="mt-1 text-xs text-rose-600">{{ $message }}</div>
        @enderror
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700" for="password">
            {{ $isEdit ? 'Nouveau mot de passe (optionnel)' : 'Mot de passe' }}
        </label>
        <input class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
               id="password"
               name="password"
               type="password"
               {{ $isEdit ? '' : 'required' }}>
        @error('password')
            <div class="mt-1 text-xs text-rose-600">{{ $message }}</div>
        @enderror
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700" for="password_confirmation">Confirmation</label>
        <input class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
               id="password_confirmation"
               name="password_confirmation"
               type="password"
               {{ $isEdit ? '' : 'required' }}>
    </div>
</div>
