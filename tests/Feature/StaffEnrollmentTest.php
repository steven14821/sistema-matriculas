<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Asignatura;
use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\Matricula;
use App\Models\Periodo;
use App\Models\Programa;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class StaffEnrollmentTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_functionary_can_register_a_student_and_search_enrollments(): void
    {
        $this->travelTo('2026-11-16 12:00:00');
        $functionary = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Funcionario]);
        $program = $this->createProgram();
        $studentUser = User::factory()->withTwoFactorEnabled()->create([
            'name' => 'Estudiante de Consulta',
            'role' => UserRole::Estudiante,
        ]);
        $student = Estudiante::query()->create([
            'user_id' => $studentUser->id,
            'codigo' => 'EST-STAFF-1',
            'documento' => '1000000301',
            'programa_id' => $program->id,
        ]);
        $group = $this->createGroup($program);

        $this->actingAs($functionary)
            ->post(route('staff.enrollments.store'), [
                'estudiante_id' => $student->id,
                'grupo_id' => $group->id,
            ])
            ->assertRedirect(route('staff.enrollments.index'))
            ->assertSessionHas('status', 'La matrícula fue registrada correctamente.');

        $this->assertDatabaseHas('matriculas', [
            'estudiante_id' => $student->id,
            'grupo_id' => $group->id,
            'estado' => 'activa',
        ]);

        $this->withoutVite()
            ->get(route('staff.enrollments.index', ['search' => 'EST-STAFF-1', 'periodo_id' => $group->periodo_id]))
            ->assertOk()
            ->assertSee('Estudiante de Consulta')
            ->assertSee('Programación de Sistemas');
    }

    public function test_functionary_can_cancel_an_active_enrollment(): void
    {
        $this->travelTo('2026-11-16 12:00:00');
        $functionary = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Funcionario]);
        $program = $this->createProgram();
        $studentUser = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Estudiante]);
        $student = Estudiante::query()->create([
            'user_id' => $studentUser->id,
            'codigo' => 'EST-STAFF-2',
            'documento' => '1000000302',
            'programa_id' => $program->id,
        ]);
        $group = $this->createGroup($program);
        $enrollment = Matricula::query()->create([
            'estudiante_id' => $student->id,
            'grupo_id' => $group->id,
            'estado' => 'activa',
            'fecha' => today()->toDateString(),
        ]);

        $this->actingAs($functionary)
            ->delete(route('staff.enrollments.destroy', $enrollment))
            ->assertRedirect(route('staff.enrollments.index'))
            ->assertSessionHas('status', 'La matrícula fue cancelada correctamente.');

        $this->assertDatabaseHas('matriculas', [
            'id' => $enrollment->id,
            'estado' => 'cancelada',
        ]);
    }

    public function test_student_cannot_access_staff_enrollment_tools(): void
    {
        $student = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Estudiante]);

        $this->actingAs($student)
            ->get(route('staff.enrollments.index'))
            ->assertForbidden();
    }

    public function test_administrator_cannot_manage_staff_enrollment_tools(): void
    {
        $administrator = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Administrador]);

        $this->actingAs($administrator)
            ->get(route('staff.enrollments.index'))
            ->assertForbidden();
    }

    private function createProgram(): Programa
    {
        return Programa::query()->create([
            'nombre' => 'Ingeniería de Sistemas',
            'nivel' => 'Pregrado',
        ]);
    }

    private function createGroup(Programa $program): Grupo
    {
        $subject = Asignatura::query()->create([
            'programa_id' => $program->id,
            'codigo' => 'SIS101',
            'nombre' => 'Programación de Sistemas',
            'creditos' => 3,
        ]);
        $period = Periodo::query()->create([
            'nombre' => '2026-2',
            'fecha_inicio' => today()->subDay()->toDateString(),
            'fecha_fin' => today()->addDay()->toDateString(),
            'abierto' => true,
            'limite_creditos' => 18,
        ]);

        return Grupo::query()->create([
            'codigo' => 'SIS101-01',
            'asignatura_id' => $subject->id,
            'periodo_id' => $period->id,
            'docente' => 'Laura Gómez',
            'horario' => 'Lunes y miércoles, 08:00-10:00',
            'cupo' => 25,
        ]);
    }
}
