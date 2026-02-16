<?php

namespace App\Policies;

use App\Models\Formation\Qualification;
use App\Models\User;

class QualificationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function view(User $user, Qualification $qualification): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function create(User $user): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function update(User $user, Qualification $qualification): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function delete(User $user, Qualification $qualification): bool
    {
        return $user->role->value === 'super_admin';
    }

    public function approve(User $user, Qualification $qualification): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function reject(User $user, Qualification $qualification): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }
}
