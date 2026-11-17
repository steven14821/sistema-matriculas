<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssignUserRoleRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\Programa;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
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
            'programas' => Programa::query()->orderBy('nombre')->get(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $userAttributes = Arr::except($validated, ['codigo', 'documento', 'programa_id']);
        $studentAttributes = Arr::only($validated, ['codigo', 'documento', 'programa_id']);

        DB::transaction(function () use ($userAttributes, $studentAttributes): void {
            $user = User::create($userAttributes);

            if ($user->role === UserRole::Estudiante) {
                $user->estudiante()->create($studentAttributes);
            }
        });

        return to_route('admin.users.index')->with('status', 'El usuario fue creado correctamente.');
    }

    public function updateRole(AssignUserRoleRequest $request, User $user): RedirectResponse
    {
        $newRole = UserRole::from($request->validated('role'));

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

        if ($newRole === UserRole::Estudiante && ! $user->estudiante()->exists()) {
            return back()
                ->withErrors(['role' => 'Para asignar el rol de estudiante, crea el usuario con sus datos académicos.'])
                ->withInput();
        }

        $user->update(['role' => $newRole]);

        return to_route('admin.users.index')->with('status', 'El rol del usuario fue actualizado correctamente.');
    }

    private function hasAnotherAdministrator(User $user): bool
    {
        return User::query()
            ->where('role', UserRole::Administrador->value)
            ->whereKeyNot($user->getKey())
            ->exists();
    }
}
