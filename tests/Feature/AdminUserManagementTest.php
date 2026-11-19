<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Programa;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_administrator_can_create_a_user_with_a_role(): void
    {
        $administrator = User::factory()->create(['role' => UserRole::Administrador]);
        $program = Programa::query()->create([
            'nombre' => 'Ingeniería de Sistemas',
            'nivel' => 'Pregrado',
        ]);

        $response = $this->actingAs($administrator)
            ->post(route('admin.users.store'), [
                'name' => 'Estudiante Nuevo',
                'email' => 'nuevo@matriculas.test',
                'password' => 'Segura1234!',
                'password_confirmation' => 'Segura1234!',
                'role' => UserRole::Estudiante->value,
                'codigo' => 'EST-2026-101',
                'documento' => '1000000101',
                'programa_id' => $program->id,
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'nuevo@matriculas.test',
            'role' => UserRole::Estudiante->value,
        ]);
        $this->assertDatabaseHas('estudiantes', [
            'codigo' => 'EST-2026-101',
            'documento' => '1000000101',
            'programa_id' => $program->id,
        ]);
        $this->assertTrue(Hash::check('Segura1234!', User::query()->where('email', 'nuevo@matriculas.test')->firstOrFail()->password));
    }

    public function test_administrator_cannot_create_a_student_without_academic_profile_data(): void
    {
        $administrator = User::factory()->create(['role' => UserRole::Administrador]);

        $this->actingAs($administrator)
            ->from(route('admin.users.create'))
            ->post(route('admin.users.store'), [
                'name' => 'Estudiante sin perfil',
                'email' => 'sin-perfil@matriculas.test',
                'password' => 'Segura1234!',
                'password_confirmation' => 'Segura1234!',
                'role' => UserRole::Estudiante->value,
            ])
            ->assertRedirect(route('admin.users.create'))
            ->assertSessionHasErrors(['codigo', 'documento']);

        $this->assertDatabaseMissing('users', ['email' => 'sin-perfil@matriculas.test']);
    }

    public function test_administrator_can_create_a_student_without_a_program(): void
    {
        $administrator = User::factory()->create(['role' => UserRole::Administrador]);

        $this->actingAs($administrator)
            ->post(route('admin.users.store'), [
                'name' => 'Estudiante sin programa',
                'email' => 'sin-programa@matriculas.test',
                'password' => 'Segura1234!',
                'password_confirmation' => 'Segura1234!',
                'role' => UserRole::Estudiante->value,
                'codigo' => 'EST-2026-102',
                'documento' => '1000000102',
                'programa_id' => '',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('estudiantes', [
            'codigo' => 'EST-2026-102',
            'documento' => '1000000102',
            'programa_id' => null,
        ]);
    }

    public function test_non_administrator_cannot_manage_users(): void
    {
        $staffMember = User::factory()->create(['role' => UserRole::Funcionario]);

        $this->actingAs($staffMember)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_administrator_can_view_the_user_list_and_creation_form(): void
    {
        $administrator = User::factory()->create(['role' => UserRole::Administrador]);

        $this->withoutVite()
            ->actingAs($administrator)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertViewIs('admin.users.index')
            ->assertSee('Crear usuario')
            ->assertSee('Asignar rol')
            ->assertSee('Administrador');

        $this->get(route('admin.users.create'))
            ->assertOk()
            ->assertViewIs('admin.users.form')
            ->assertSee('Confirmar contraseña');
    }

    public function test_user_role_must_be_one_of_the_defined_roles(): void
    {
        $administrator = User::factory()->create(['role' => UserRole::Administrador]);

        $this->actingAs($administrator)
            ->from(route('admin.users.create'))
            ->post(route('admin.users.store'), [
                'name' => 'Usuario sin perfil válido',
                'email' => 'invalido@matriculas.test',
                'password' => 'Segura1234!',
                'password_confirmation' => 'Segura1234!',
                'role' => 'superadmin',
            ])
            ->assertRedirect(route('admin.users.create'))
            ->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'invalido@matriculas.test']);
    }

    public function test_administrator_can_assign_a_role_without_changing_other_user_data(): void
    {
        $administrator = User::factory()->create(['role' => UserRole::Administrador]);
        $user = User::factory()->create([
            'name' => 'Usuario existente',
            'role' => UserRole::Funcionario,
        ]);

        $response = $this->actingAs($administrator)
            ->patch(route('admin.users.role.update', $user), [
                'role' => UserRole::Administrador->value,
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Usuario existente',
            'role' => UserRole::Administrador->value,
        ]);
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_administrator_cannot_change_user_account_details(): void
    {
        $administrator = User::factory()->create(['role' => UserRole::Administrador]);
        $user = User::factory()->create(['name' => 'Nombre original']);

        $this->actingAs($administrator)
            ->put('/admin/users/'.$user->id, ['name' => 'Nombre cambiado'])
            ->assertNotFound();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nombre original',
        ]);
    }

    public function test_administrator_cannot_assign_student_role_without_academic_profile(): void
    {
        $administrator = User::factory()->create(['role' => UserRole::Administrador]);
        $user = User::factory()->create(['role' => UserRole::Funcionario]);

        $this->actingAs($administrator)
            ->from(route('admin.users.index'))
            ->patch(route('admin.users.role.update', $user), [
                'role' => UserRole::Estudiante->value,
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHasErrors([
                'role' => 'Para asignar el rol de estudiante, crea el usuario con sus datos académicos.',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => UserRole::Funcionario->value,
        ]);
    }

    public function test_administrator_cannot_remove_their_own_administrator_role(): void
    {
        $administrator = User::factory()->create(['role' => UserRole::Administrador]);
        User::factory()->create(['role' => UserRole::Administrador]);

        $this->actingAs($administrator)
            ->from(route('admin.users.index'))
            ->patch(route('admin.users.role.update', $administrator), [
                'role' => UserRole::Funcionario->value,
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHasErrors('role');

        $this->assertDatabaseHas('users', [
            'id' => $administrator->id,
            'role' => UserRole::Administrador->value,
        ]);
    }

    public function test_last_administrator_cannot_be_demoted(): void
    {
        $administrator = User::factory()->create(['role' => UserRole::Administrador]);

        $this->actingAs($administrator)
            ->from(route('admin.users.index'))
            ->patch(route('admin.users.role.update', $administrator), [
                'role' => UserRole::Funcionario->value,
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHasErrors('role');

        $this->assertDatabaseHas('users', [
            'id' => $administrator->id,
            'role' => UserRole::Administrador->value,
        ]);
    }

    public function test_administrator_cannot_delete_users(): void
    {
        $administrator = User::factory()->create(['role' => UserRole::Administrador]);
        $user = User::factory()->create();

        $this->actingAs($administrator)
            ->delete('/admin/users/'.$user->id)
            ->assertNotFound();

        $this->assertModelExists($user);
    }
}
