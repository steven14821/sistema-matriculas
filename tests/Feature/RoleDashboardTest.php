<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RoleDashboardTest extends TestCase
{
    use LazilyRefreshDatabase;

    public static function rolesAndDashboardViews(): array
    {
        return [
            'administrator' => [UserRole::Administrador, 'dashboard.administrador', 'Administración académica'],
            'staff member' => [UserRole::Funcionario, 'dashboard.funcionario', 'Gestión académica'],
            'student' => [UserRole::Estudiante, 'dashboard.estudiante', 'Tu próximo periodo empieza aquí.'],
        ];
    }

    #[DataProvider('rolesAndDashboardViews')]
    public function test_authenticated_user_sees_the_dashboard_for_their_role(
        UserRole $role,
        string $view,
        string $heading,
    ): void {
        $user = User::factory()->create(['role' => $role]);

        $response = $this->withoutVite()
            ->actingAs($user)
            ->get(route('dashboard'));

        $response->assertOk()
            ->assertViewIs($view)
            ->assertSee($heading);
    }

    public function test_guest_is_redirected_to_login_from_the_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_functionary_dashboard_links_to_group_assignment_and_professor_catalog(): void
    {
        $staffMember = User::factory()->create(['role' => UserRole::Funcionario]);

        $this->withoutVite()
            ->actingAs($staffMember)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('admin.grupos.index'))
            ->assertSee(route('admin.profesores.index'))
            ->assertSee(route('admin.matriculas.index'));
    }

    public function test_dashboard_guest_page_explains_the_enrollment_flow_and_links_to_login(): void
    {
        $response = $this->withoutVite()->get(route('home'));

        $response->assertOk()
            ->assertSee('Tu recorrido académico, en orden.')
            ->assertSee('Oferta')
            ->assertSee('Matrícula')
            ->assertSee('Seguimiento')
            ->assertSee(route('login'));
    }
}
