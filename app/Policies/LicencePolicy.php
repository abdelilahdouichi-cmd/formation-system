<?php

namespace App\Policies;

use App\Models\Formation\Licence;
use App\Models\User;

class LicencePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function view(User $user, Licence $licence): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function create(User $user): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function update(User $user, Licence $licence): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function delete(User $user, Licence $licence): bool
    {
        return $user->role->value === 'super_admin';
    }

    public function deliver(User $user, Licence $licence): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function renew(User $user, Licence $licence): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function suspend(User $user, Licence $licence): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }
}
