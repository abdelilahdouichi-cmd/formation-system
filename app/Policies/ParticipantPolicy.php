<?php

namespace App\Policies;

use App\Models\Formation\Participant;
use App\Models\User;

class ParticipantPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function view(User $user, Participant $participant): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function create(User $user): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function update(User $user, Participant $participant): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function delete(User $user, Participant $participant): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function assignToClasse(User $user, Participant $participant): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function deactivate(User $user, Participant $participant): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }
}
