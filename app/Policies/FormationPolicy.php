<?php

namespace App\Policies;

use App\Models\Formation\Formation;
use App\Models\User;

class FormationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function view(User $user, Formation $formation): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function create(User $user): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function update(User $user, Formation $formation): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function delete(User $user, Formation $formation): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function execute(User $user, Formation $formation): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function cancel(User $user, Formation $formation): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }

    public function archive(User $user, Formation $formation): bool
    {
        return $user->role->value === 'admin' || $user->role->value === 'super_admin';
    }
}
