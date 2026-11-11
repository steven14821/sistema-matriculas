<?php

namespace Tests\Feature;

use App\Enums\UserRole;
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

        $response = $this->actingAs($administrator)
            ->post(route('admin.users.store'), [
                'name' => 'Estudiante Nuevo',
                'email' => 'nuevo@matriculas.test',
                'password' => 'Segura1234!',
                'password_confirmation' => 'Segura1234!',
                'role' => UserRole::Estudiante->value,
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'nuevo@matriculas.test',
            'role' => UserRole::Estudiante->value,
        ]);
        $this->assertTrue(Hash::check('Segura1234!', User::query()->where('email', 'nuevo@matriculas.test')->firstOrFail()->password));
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

    public function test_administrator_can_update_a_user_without_replacing_a_blank_password(): void
    {
        $administrator = User::factory()->create(['role' => UserRole::Administrador]);
        $user = User::factory()->create(['role' => UserRole::Estudiante]);

        $response = $this->actingAs($administrator)
            ->put(route('admin.users.update', $user), [
                'name' => 'Nombre actualizado',
                'email' => $user->email,
                'password' => '',
                'password_confirmation' => '',
                'role' => UserRole::Funcionario->value,
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nombre actualizado',
            'role' => UserRole::Funcionario->value,
        ]);
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_administrator_cannot_remove_their_own_administrator_role(): void
    {
        $administrator = User::factory()->create(['role' => UserRole::Administrador]);
        User::factory()->create(['role' => UserRole::Administrador]);

        $this->actingAs($administrator)
            ->from(route('admin.users.edit', $administrator))
            ->put(route('admin.users.update', $administrator), [
                'name' => $administrator->name,
                'email' => $administrator->email,
                'password' => '',
                'password_confirmation' => '',
                'role' => UserRole::Estudiante->value,
            ])
            ->assertRedirect(route('admin.users.edit', $administrator))
            ->assertSessionHasErrors('role');

        $this->assertDatabaseHas('users', [
            'id' => $administrator->id,
            'role' => UserRole::Administrador->value,
        ]);
    }

    public function test_last_administrator_cannot_be_deleted(): void
    {
        $administrator = User::factory()->create(['role' => UserRole::Administrador]);

        $this->actingAs($administrator)
            ->from(route('admin.users.index'))
            ->delete(route('admin.users.destroy', $administrator))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHasErrors('user');

        $this->assertModelExists($administrator);
    }

    public function test_administrator_cannot_delete_their_own_account_when_another_administrator_exists(): void
    {
        $administrator = User::factory()->create(['role' => UserRole::Administrador]);
        User::factory()->create(['role' => UserRole::Administrador]);

        $this->actingAs($administrator)
            ->from(route('admin.users.index'))
            ->delete(route('admin.users.destroy', $administrator))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHasErrors('user');

        $this->assertModelExists($administrator);
    }

    public function test_administrator_can_delete_another_user(): void
    {
        $administrator = User::factory()->create(['role' => UserRole::Administrador]);
        $user = User::factory()->create(['role' => UserRole::Estudiante]);

        $response = $this->actingAs($administrator)
            ->delete(route('admin.users.destroy', $user));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertModelMissing($user);
    }
}
