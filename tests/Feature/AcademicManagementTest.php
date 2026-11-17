<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Asignatura;
use App\Models\Periodo;
use App\Models\Programa;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AcademicManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_functionary_can_create_the_academic_catalog_and_associate_subjects_with_careers(): void
    {
        $functionary = User::factory()->create(['role' => UserRole::Funcionario]);
        $this->travelTo('2026-11-16 12:00:00');
        $programResponse = $this->actingAs($functionary)
            ->post(route('staff.programas.store'), [
                'nombre' => 'Ingeniería de Sistemas',
                'nivel' => 'Pregrado',
            ]);

        $programResponse->assertRedirect(route('staff.programas.index'));
        $program = Programa::query()->firstOrFail();

        $this->post(route('staff.asignaturas.store'), [
            'programa_id' => $program->id,
            'codigo' => 'SIS301',
            'nombre' => 'Arquitectura de Software',
            'creditos' => 3,
        ])->assertRedirect(route('staff.asignaturas.index'));

        $subject = Asignatura::query()->firstOrFail();
        $this->post(route('staff.periodos.store'), [
            'nombre' => '2026-2',
            'fecha_inicio' => '2026-07-01',
            'fecha_fin' => '2026-12-15',
            'abierto' => '1',
        ])->assertRedirect(route('staff.periodos.index'));

        $period = Periodo::query()->firstOrFail();
        $this->post(route('staff.grupos.store'), [
            'codigo' => 'SIS301-01',
            'asignatura_id' => $subject->id,
            'periodo_id' => $period->id,
            'docente' => 'Laura Gómez',
            'horario' => 'Lunes y miércoles, 08:00-10:00',
            'cupo' => 25,
        ])->assertRedirect(route('staff.grupos.index'));

        $this->assertDatabaseHas('asignaturas', [
            'id' => $subject->id,
            'programa_id' => $program->id,
        ]);
        $this->assertDatabaseHas('grupos', [
            'codigo' => 'SIS301-01',
            'asignatura_id' => $subject->id,
            'periodo_id' => $period->id,
            'cupo' => 25,
        ]);
    }

    public function test_functionary_cannot_create_a_period_with_end_before_start(): void
    {
        $functionary = User::factory()->create(['role' => UserRole::Funcionario]);

        $this->actingAs($functionary)
            ->from(route('staff.periodos.create'))
            ->post(route('staff.periodos.store'), [
                'nombre' => 'Periodo inválido',
                'fecha_inicio' => '2026-12-15',
                'fecha_fin' => '2026-07-01',
                'abierto' => '1',
            ])
            ->assertRedirect(route('staff.periodos.create'))
            ->assertSessionHasErrors('fecha_fin');

        $this->assertDatabaseCount('periodos', 0);
    }

    public function test_functionary_can_open_each_academic_management_page(): void
    {
        $functionary = User::factory()->create(['role' => UserRole::Funcionario]);

        $this->withoutVite()
            ->actingAs($functionary)
            ->get(route('staff.programas.index'))
            ->assertOk()
            ->assertViewIs('staff.programas.index');

        $this->get(route('staff.asignaturas.index'))
            ->assertOk()
            ->assertViewIs('staff.asignaturas.index');

        $this->get(route('staff.periodos.index'))
            ->assertOk()
            ->assertViewIs('staff.periodos.index');

        $this->get(route('staff.grupos.index'))
            ->assertOk()
            ->assertViewIs('staff.grupos.index');
    }

    public function test_functionary_cannot_delete_a_program_with_associated_subjects(): void
    {
        $functionary = User::factory()->create(['role' => UserRole::Funcionario]);
        $program = Programa::query()->create([
            'nombre' => 'Ingeniería de Sistemas',
            'nivel' => 'Pregrado',
        ]);
        Asignatura::query()->create([
            'programa_id' => $program->id,
            'codigo' => 'SIS101',
            'nombre' => 'Programación',
            'creditos' => 3,
        ]);

        $this->actingAs($functionary)
            ->from(route('staff.programas.index'))
            ->delete(route('staff.programas.destroy', $program))
            ->assertRedirect(route('staff.programas.index'))
            ->assertSessionHasErrors('programa');

        $this->assertModelExists($program);
        $this->assertDatabaseHas('programas', [
            'id' => $program->id,
            'deleted_at' => null,
        ]);
    }

    public function test_administrator_cannot_manage_the_academic_catalog(): void
    {
        $administrator = User::factory()->create(['role' => UserRole::Administrador]);

        $this->actingAs($administrator)
            ->get(route('staff.programas.index'))
            ->assertForbidden();

        $this->post(route('staff.programas.store'), [
            'nombre' => 'Ingeniería de Sistemas',
            'nivel' => 'Pregrado',
        ])
            ->assertForbidden();

        $this->assertDatabaseCount('programas', 0);
    }
}
