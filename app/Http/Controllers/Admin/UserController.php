<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $role = $request->string('role')->toString();
        $status = $request->string('status')->toString();
        $sort = $request->string('sort')->toString();

        $query = User::query();

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($role !== '') {
            $query->where('role', $role);
        }

        if (in_array($status, ['active', 'inactive'], true)) {
            $query->where('is_active', $status === 'active');
        }

        match ($sort) {
            'name_desc' => $query->orderByDesc('name'),
            'last_login_desc' => $query->orderByDesc('last_login_at'),
            'last_login_asc' => $query->orderBy('last_login_at'),
            default => $query->orderBy('name'),
        };

        $users = $query->paginate(10)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => $this->roleOptions(),
            'filters' => [
                'search' => $search,
                'role' => $role,
                'status' => $status,
                'sort' => $sort,
            ],
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.users.create', [
            'roles' => $this->roleOptions(),
            'canEditRole' => $request->user()->isSuperAdmin(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (! $request->user()->isSuperAdmin()) {
            $data['role'] = UserRole::User->value;
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'is_active' => $data['is_active'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('status', 'Utilisateur créé avec succès.');
    }

    public function edit(Request $request, User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => $this->roleOptions(),
            'canEditRole' => $request->user()->can('updateRole', $user),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (! $request->user()->can('updateRole', $user)) {
            $data['role'] = $user->role->value;
        }

        if ($request->user()->id === $user->id) {
            $data['is_active'] = true;
        }

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'is_active' => $data['is_active'],
        ]);

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        return back()->with('status', 'Utilisateur mis à jour.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Utilisateur supprimé.');
    }

    /**
     * @return Collection<int, array{value: string, label: string}>
     */
    private function roleOptions(): Collection
    {
        return collect(UserRole::cases())->map(fn (UserRole $role): array => [
            'value' => $role->value,
            'label' => $role->label(),
        ]);
    }
}
