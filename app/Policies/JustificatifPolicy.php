<?php

namespace App\Policies;

use App\Models\Formation\Justificatif;
use App\Models\User;

class JustificatifPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function view(User $user, Justificatif $justificatif): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function create(User $user): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function update(User $user, Justificatif $justificatif): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function delete(User $user, Justificatif $justificatif): bool
    {
        return $user->role->value === 'super_admin';
    }

    public function verify(User $user, Justificatif $justificatif): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function reject(User $user, Justificatif $justificatif): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }
}
