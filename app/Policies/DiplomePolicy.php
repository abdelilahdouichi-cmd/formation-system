<?php

namespace App\Policies;

use App\Models\Formation\Diplome;
use App\Models\User;

class DiplomePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function view(User $user, Diplome $diplome): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function create(User $user): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function update(User $user, Diplome $diplome): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function delete(User $user, Diplome $diplome): bool
    {
        return $user->role->value === 'super_admin';
    }

    public function deliver(User $user, Diplome $diplome): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function reject(User $user, Diplome $diplome): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function cancel(User $user, Diplome $diplome): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }
}
