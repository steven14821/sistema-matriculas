<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Asignatura;
use App\Models\Grupo;
use App\Models\Periodo;
use App\Models\Programa;
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
        $user = User::factory()->withTwoFactorEnabled()->create(['role' => $role]);

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

    public function test_functionary_dashboard_links_to_staff_academic_management(): void
    {
        $staffMember = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Funcionario]);

        $this->withoutVite()
            ->actingAs($staffMember)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('staff.grupos.index'))
            ->assertSee(route('staff.profesores.index'))
            ->assertSee(route('staff.enrollments.index'));
    }

    public function test_admin_dashboard_links_to_read_only_academic_views(): void
    {
        $administrator = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Administrador]);

        $this->withoutVite()
            ->actingAs($administrator)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('admin.users.index'))
            ->assertSee(route('admin.estudiantes.index'))
            ->assertSee(route('admin.catalogo.index'))
            ->assertDontSee(route('staff.programas.index'))
            ->assertDontSee(route('staff.enrollments.index'));
    }

    public function test_dashboard_guest_page_explains_the_enrollment_flow_and_links_to_login(): void
    {
        $response = $this->withoutVite()->get(route('home'));

        $response->assertOk()
            ->assertViewIs('landing')
            ->assertSee('Conoce la oferta.')
            ->assertSee('Elige tu grupo.')
            ->assertSee('Programas')
            ->assertSee('Estudiantes')
            ->assertSee('No hay grupos asociados a periodos abiertos')
            ->assertSee(route('login'));
    }

    public function test_guest_homepage_shows_groups_from_open_periods_only(): void
    {
        $program = Programa::query()->create([
            'nombre' => 'Ingeniería de Sistemas',
            'nivel' => 'Pregrado',
        ]);
        $subject = Asignatura::query()->create([
            'programa_id' => $program->id,
            'codigo' => 'SIS101',
            'nombre' => 'Introducción a Sistemas',
            'creditos' => 3,
        ]);
        $openPeriod = Periodo::query()->create([
            'nombre' => '2026-2',
            'fecha_inicio' => '2026-07-01',
            'fecha_fin' => '2026-12-15',
            'abierto' => true,
        ]);
        $closedPeriod = Periodo::query()->create([
            'nombre' => '2027-1',
            'fecha_inicio' => '2027-01-15',
            'fecha_fin' => '2027-06-15',
            'abierto' => false,
        ]);

        Grupo::query()->create([
            'codigo' => 'A101',
            'asignatura_id' => $subject->id,
            'periodo_id' => $openPeriod->id,
            'cupo' => 25,
        ]);
        Grupo::query()->create([
            'codigo' => 'A102',
            'asignatura_id' => $subject->id,
            'periodo_id' => $closedPeriod->id,
            'cupo' => 25,
        ]);

        $this->withoutVite()
            ->get(route('home'))
            ->assertOk()
            ->assertSeeText('Ingeniería de Sistemas')
            ->assertSeeText('Introducción a Sistemas')
            ->assertSeeText('A101')
            ->assertSeeText('2026-2')
            ->assertDontSeeText('A102')
            ->assertDontSeeText('2027-1');
    }
}
