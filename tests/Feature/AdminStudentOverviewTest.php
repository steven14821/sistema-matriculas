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

class AdminStudentOverviewTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_administrator_can_find_a_student_and_view_their_enrollment_history(): void
    {
        $administrator = User::factory()->create(['role' => UserRole::Administrador]);
        $program = Programa::query()->create([
            'nombre' => 'Ingeniería de Sistemas',
            'nivel' => 'Pregrado',
        ]);
        $subject = Asignatura::query()->create([
            'programa_id' => $program->id,
            'codigo' => 'SIS201',
            'nombre' => 'Bases de Datos',
            'creditos' => 3,
        ]);
        $period = Periodo::query()->create([
            'nombre' => '2026-2',
            'fecha_inicio' => today()->subDay()->toDateString(),
            'fecha_fin' => today()->addDay()->toDateString(),
            'abierto' => true,
        ]);
        $group = Grupo::query()->create([
            'codigo' => 'SIS201-01',
            'asignatura_id' => $subject->id,
            'periodo_id' => $period->id,
            'docente' => 'Laura Gómez',
            'horario' => 'Lunes 08:00-10:00',
            'cupo' => 25,
        ]);
        $studentUser = User::factory()->create([
            'name' => 'Estudiante Consultado',
            'role' => UserRole::Estudiante,
        ]);
        $student = Estudiante::query()->create([
            'user_id' => $studentUser->id,
            'codigo' => 'EST-OV-1',
            'documento' => '1000000501',
            'programa_id' => $program->id,
        ]);
        Matricula::query()->create([
            'estudiante_id' => $student->id,
            'grupo_id' => $group->id,
            'estado' => 'activa',
            'fecha' => today()->toDateString(),
        ]);

        $this->withoutVite()
            ->actingAs($administrator)
            ->get(route('admin.estudiantes.index', ['search' => 'EST-OV-1']))
            ->assertOk()
            ->assertSee('Estudiante Consultado');

        $this->get(route('admin.estudiantes.show', $student))
            ->assertOk()
            ->assertSee('Estudiante Consultado')
            ->assertSee('Ingeniería de Sistemas')
            ->assertSee('Bases de Datos')
            ->assertSee('2026-2')
            ->assertSee('1 matrícula activa')
            ->assertSee('Activa');
    }

    public function test_administrator_can_view_the_academic_catalog_without_write_controls(): void
    {
        $administrator = User::factory()->create(['role' => UserRole::Administrador]);
        $program = Programa::query()->create([
            'nombre' => 'Ingeniería Industrial',
            'nivel' => 'Pregrado',
        ]);
        $subject = Asignatura::query()->create([
            'programa_id' => $program->id,
            'codigo' => 'IND101',
            'nombre' => 'Fundamentos de Procesos',
            'creditos' => 3,
        ]);
        $period = Periodo::query()->create([
            'nombre' => '2026-2',
            'fecha_inicio' => today()->subDay()->toDateString(),
            'fecha_fin' => today()->addDay()->toDateString(),
            'abierto' => true,
        ]);
        Grupo::query()->create([
            'codigo' => 'IND101-01',
            'asignatura_id' => $subject->id,
            'periodo_id' => $period->id,
            'docente' => 'Carlos Ruiz',
            'horario' => 'Martes 10:00-12:00',
            'cupo' => 20,
        ]);

        $this->withoutVite()
            ->actingAs($administrator)
            ->get(route('admin.catalogo.index'))
            ->assertOk()
            ->assertSee('Ingeniería Industrial')
            ->assertSee('Fundamentos de Procesos')
            ->assertSee('IND101-01')
            ->assertDontSee('Crear carrera')
            ->assertDontSee('Crear asignatura');
    }

    public function test_functionary_cannot_access_administrator_overviews(): void
    {
        $functionary = User::factory()->create(['role' => UserRole::Funcionario]);

        $this->actingAs($functionary)
            ->get(route('admin.estudiantes.index'))
            ->assertForbidden();
    }
}
