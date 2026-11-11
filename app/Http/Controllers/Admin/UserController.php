<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', Rule::enum(UserRole::class)],
        ]);

        $users = User::query()
            ->when($validated['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($validated['role'] ?? null, fn (Builder $query, string $role): Builder => $query->where('role', $role))
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => UserRole::cases(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.form', [
            'roles' => UserRole::cases(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create($request->validated());

        return to_route('admin.users.index')->with('status', 'El usuario fue creado correctamente.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', [
            'user' => $user,
            'roles' => UserRole::cases(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();
        $newRole = UserRole::from($validated['role']);

        if ($user->is($request->user()) && $newRole !== UserRole::Administrador) {
            return back()
                ->withErrors(['role' => 'No puedes quitarte el rol de administrador.'])
                ->withInput();
        }

        if ($user->role === UserRole::Administrador
            && $newRole !== UserRole::Administrador
            && ! $this->hasAnotherAdministrator($user)) {
            return back()
                ->withErrors(['role' => 'Debe existir al menos un administrador en el sistema.'])
                ->withInput();
        }

        if (blank($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return to_route('admin.users.index')->with('status', 'El usuario fue actualizado correctamente.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->role === UserRole::Administrador && ! $this->hasAnotherAdministrator($user)) {
            return back()->withErrors(['user' => 'Debe existir al menos un administrador en el sistema.']);
        }

        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'No puedes eliminar tu propia cuenta.']);
        }

        $user->delete();

        return to_route('admin.users.index')->with('status', 'El usuario fue eliminado correctamente.');
    }

    private function hasAnotherAdministrator(User $user): bool
    {
        return User::query()
            ->where('role', UserRole::Administrador->value)
            ->whereKeyNot($user->getKey())
            ->exists();
    }
}
