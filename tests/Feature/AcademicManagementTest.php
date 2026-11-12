<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Asignatura;
use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\Matricula;
use App\Models\Periodo;
use App\Models\Profesor;
use App\Models\Programa;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AcademicManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_access_academic_management_views(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Administrador,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.programas.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.asignaturas.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.periodos.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.grupos.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.grupos.create'))
            ->assertOk();

        $this->withoutVite()
            ->actingAs($admin)
            ->get(route('admin.matriculas.index'))
            ->assertOk()
            ->assertSee('Matrículas');

        $this->actingAs($admin)
            ->get(route('admin.matriculas.create'))
            ->assertOk();
    }

    public function test_admin_can_create_multiple_groups_for_the_same_subject_with_distinct_codes(): void
    {
        $programa = Programa::query()->create([
            'nombre' => 'Ingeniería de Sistemas',
            'nivel' => 'Pregrado',
        ]);
        $asignatura = Asignatura::query()->create([
            'programa_id' => $programa->id,
            'codigo' => 'SIS100',
            'nombre' => 'Introducción a Sistemas',
            'creditos' => 3,
        ]);
        $periodo = Periodo::query()->create([
            'nombre' => '2028-2',
            'fecha_inicio' => '2028-07-10',
            'fecha_fin' => '2028-12-15',
            'abierto' => true,
        ]);
        $admin = User::factory()->create(['role' => UserRole::Administrador]);

        $this->withoutVite()
            ->actingAs($admin)
            ->get(route('admin.grupos.create'))
            ->assertOk()
            ->assertSee('Código del grupo')
            ->assertDontSee('Horario');

        foreach (['A101', 'A102'] as $codigo) {
            $this->post(route('admin.grupos.store'), [
                'codigo' => $codigo,
                'asignatura_id' => $asignatura->id,
                'periodo_id' => $periodo->id,
                'profesor_id' => '',
                'cupo' => 25,
            ])->assertRedirect(route('admin.grupos.index'));
        }

        $this->assertDatabaseHas('grupos', [
            'asignatura_id' => $asignatura->id,
            'periodo_id' => $periodo->id,
            'codigo' => 'A101',
        ]);
        $this->assertDatabaseHas('grupos', [
            'asignatura_id' => $asignatura->id,
            'periodo_id' => $periodo->id,
            'codigo' => 'A102',
        ]);

        $this->from(route('admin.grupos.create'))
            ->post(route('admin.grupos.store'), [
                'codigo' => 'A101',
                'asignatura_id' => $asignatura->id,
                'periodo_id' => $periodo->id,
                'profesor_id' => '',
                'cupo' => 25,
            ])
            ->assertRedirect(route('admin.grupos.create'))
            ->assertSessionHasErrors('codigo');

        $this->assertSame(2, Grupo::query()
            ->where('asignatura_id', $asignatura->id)
            ->where('periodo_id', $periodo->id)
            ->count());
    }

    public function test_student_offer_search_only_returns_matching_groups_from_open_periods(): void
    {
        $programa = Programa::query()->create([
            'nombre' => 'Ingeniería de Sistemas',
            'nivel' => 'Pregrado',
        ]);

        $asignaturaAbierta = Asignatura::query()->create([
            'programa_id' => $programa->id,
            'codigo' => 'OPEN101',
            'nombre' => 'Asignatura abierta',
            'creditos' => 3,
        ]);

        $asignaturaCerrada = Asignatura::query()->create([
            'programa_id' => $programa->id,
            'codigo' => 'SIS201',
            'nombre' => 'Asignatura de periodo cerrado',
            'creditos' => 3,
        ]);

        $periodoAbierto = Periodo::query()->create([
            'nombre' => '2026-2',
            'fecha_inicio' => '2026-07-01',
            'fecha_fin' => '2026-12-15',
            'abierto' => true,
        ]);

        $periodoCerrado = Periodo::query()->create([
            'nombre' => '2026-1',
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-06-30',
            'abierto' => false,
        ]);

        $profesor = Profesor::query()->create(['nombre' => 'Docente SIS201']);
        $profesorCerrado = Profesor::query()->create(['nombre' => 'Otro docente']);

        Grupo::query()->create([
            'codigo' => 'A101',
            'asignatura_id' => $asignaturaAbierta->id,
            'periodo_id' => $periodoAbierto->id,
            'profesor_id' => $profesor->id,
            'cupo' => 20,
        ]);

        Grupo::query()->create([
            'codigo' => 'A201',
            'asignatura_id' => $asignaturaCerrada->id,
            'periodo_id' => $periodoCerrado->id,
            'profesor_id' => $profesorCerrado->id,
            'cupo' => 20,
        ]);

        $student = User::factory()->create([
            'role' => UserRole::Estudiante,
        ]);

        $response = $this->withoutVite()
            ->actingAs($student)
            ->get(route('oferta.index', ['search' => 'SIS201']));

        $response->assertOk()
            ->assertSee('Docente SIS201')
            ->assertDontSee('Asignatura de periodo cerrado');
    }

    public function test_student_can_enroll_in_an_open_group_only_once(): void
    {
        $programa = Programa::query()->create([
            'nombre' => 'Ingeniería de Sistemas',
            'nivel' => 'Pregrado',
        ]);

        $asignatura = Asignatura::query()->create([
            'programa_id' => $programa->id,
            'codigo' => 'SIS999',
            'nombre' => 'Pruebas de Matrícula',
            'creditos' => 3,
        ]);

        $periodo = Periodo::query()->create([
            'nombre' => '2026-1',
            'fecha_inicio' => '2026-01-10',
            'fecha_fin' => '2026-06-15',
            'abierto' => true,
        ]);

        $grupo = Grupo::query()->create([
            'codigo' => 'A101',
            'asignatura_id' => $asignatura->id,
            'periodo_id' => $periodo->id,
            'cupo' => 1,
        ]);

        $studentUser = User::factory()->create([
            'role' => UserRole::Estudiante,
        ]);

        Estudiante::query()->create([
            'user_id' => $studentUser->id,
            'codigo' => 'EST-2026-099',
            'documento' => '1099999999',
            'programa_id' => $programa->id,
        ]);

        $this->actingAs($studentUser)
            ->post(route('matriculas.inscribir', $grupo))
            ->assertRedirect(route('matriculas.mine'));

        $this->actingAs($studentUser)
            ->post(route('matriculas.inscribir', $grupo))
            ->assertSessionHasErrors('grupo');
    }

    public function test_functionary_can_register_matriculas_without_duplicates_or_overbooking(): void
    {
        $programa = Programa::query()->create([
            'nombre' => 'Ingeniería de Sistemas',
            'nivel' => 'Pregrado',
        ]);

        $asignatura = Asignatura::query()->create([
            'programa_id' => $programa->id,
            'codigo' => 'SIS301',
            'nombre' => 'Arquitectura de Software',
            'creditos' => 3,
        ]);

        $periodo = Periodo::query()->create([
            'nombre' => '2027-1',
            'fecha_inicio' => '2027-01-10',
            'fecha_fin' => '2027-06-15',
            'abierto' => true,
        ]);

        $grupo = Grupo::query()->create([
            'codigo' => 'A301',
            'asignatura_id' => $asignatura->id,
            'periodo_id' => $periodo->id,
            'cupo' => 1,
        ]);

        $studentUser = User::factory()->create(['role' => UserRole::Estudiante]);
        $estudiante = Estudiante::query()->create([
            'user_id' => $studentUser->id,
            'codigo' => 'EST-2027-301',
            'documento' => '1000000301',
            'programa_id' => $programa->id,
        ]);
        $funcionario = User::factory()->create(['role' => UserRole::Funcionario]);

        $this->withoutVite()
            ->actingAs($funcionario)
            ->get(route('admin.matriculas.index'))
            ->assertOk()
            ->assertSee('Matrículas');

        $this->actingAs($funcionario)
            ->post(route('admin.matriculas.store'), [
                'estudiante_id' => $estudiante->id,
                'grupo_id' => $grupo->id,
                'estado' => 'activa',
                'fecha' => '2027-01-10',
            ])
            ->assertRedirect(route('admin.matriculas.index'));

        $this->assertDatabaseHas('matriculas', [
            'estudiante_id' => $estudiante->id,
            'grupo_id' => $grupo->id,
            'estado' => 'activa',
        ]);

        $this->actingAs($funcionario)
            ->from(route('admin.matriculas.create'))
            ->post(route('admin.matriculas.store'), [
                'estudiante_id' => $estudiante->id,
                'grupo_id' => $grupo->id,
                'estado' => 'activa',
                'fecha' => '2027-01-10',
            ])
            ->assertRedirect(route('admin.matriculas.create'))
            ->assertSessionHasErrors('grupo_id');

        $anotherStudentUser = User::factory()->create(['role' => UserRole::Estudiante]);
        $anotherEstudiante = Estudiante::query()->create([
            'user_id' => $anotherStudentUser->id,
            'codigo' => 'EST-2027-302',
            'documento' => '1000000302',
            'programa_id' => $programa->id,
        ]);

        $this->actingAs($funcionario)
            ->from(route('admin.matriculas.create'))
            ->post(route('admin.matriculas.store'), [
                'estudiante_id' => $anotherEstudiante->id,
                'grupo_id' => $grupo->id,
                'estado' => 'activa',
                'fecha' => '2027-01-10',
            ])
            ->assertRedirect(route('admin.matriculas.create'))
            ->assertSessionHasErrors('grupo_id');

        $this->assertSame(1, Matricula::query()->where('grupo_id', $grupo->id)->count());
    }

    public function test_functionary_can_reactivate_a_soft_deleted_matricula_without_duplicating_it(): void
    {
        $programa = Programa::query()->create([
            'nombre' => 'Ingeniería de Sistemas',
            'nivel' => 'Pregrado',
        ]);

        $asignatura = Asignatura::query()->create([
            'programa_id' => $programa->id,
            'codigo' => 'SIS302',
            'nombre' => 'Bases de Datos II',
            'creditos' => 3,
        ]);

        $periodo = Periodo::query()->create([
            'nombre' => '2027-2',
            'fecha_inicio' => '2027-07-10',
            'fecha_fin' => '2027-12-15',
            'abierto' => true,
        ]);

        $grupo = Grupo::query()->create([
            'codigo' => 'A302',
            'asignatura_id' => $asignatura->id,
            'periodo_id' => $periodo->id,
            'cupo' => 1,
        ]);

        $studentUser = User::factory()->create(['role' => UserRole::Estudiante]);
        $estudiante = Estudiante::query()->create([
            'user_id' => $studentUser->id,
            'codigo' => 'EST-2027-302',
            'documento' => '1000000302',
            'programa_id' => $programa->id,
        ]);
        $funcionario = User::factory()->create(['role' => UserRole::Funcionario]);

        $matricula = Matricula::query()->create([
            'estudiante_id' => $estudiante->id,
            'grupo_id' => $grupo->id,
            'estado' => 'activa',
            'fecha' => '2027-07-10',
        ]);
        $matricula->delete();

        $this->actingAs($funcionario)
            ->post(route('admin.matriculas.store'), [
                'estudiante_id' => $estudiante->id,
                'grupo_id' => $grupo->id,
                'estado' => 'activa',
                'fecha' => '2027-07-12',
            ])
            ->assertRedirect(route('admin.matriculas.index'));

        $this->assertDatabaseHas('matriculas', [
            'id' => $matricula->id,
            'deleted_at' => null,
        ]);
        $this->assertSame('2027-07-12', Matricula::withTrashed()->findOrFail($matricula->id)->fecha->toDateString());
        $this->assertSame(1, Matricula::withTrashed()
            ->where('estudiante_id', $estudiante->id)
            ->where('grupo_id', $grupo->id)
            ->count());
    }

    public function test_functionary_can_assign_a_professor_from_the_catalog_to_a_group(): void
    {
        $programa = Programa::query()->create([
            'nombre' => 'Ingeniería de Sistemas',
            'nivel' => 'Pregrado',
        ]);
        $asignatura = Asignatura::query()->create([
            'programa_id' => $programa->id,
            'codigo' => 'SIS401',
            'nombre' => 'Sistemas Distribuidos',
            'creditos' => 3,
        ]);
        $periodo = Periodo::query()->create([
            'nombre' => '2028-1',
            'fecha_inicio' => '2028-01-10',
            'fecha_fin' => '2028-06-15',
            'abierto' => true,
        ]);
        $grupo = Grupo::query()->create([
            'codigo' => 'A401',
            'asignatura_id' => $asignatura->id,
            'periodo_id' => $periodo->id,
            'cupo' => 20,
        ]);
        $funcionario = User::factory()->create(['role' => UserRole::Funcionario]);

        $this->actingAs($funcionario)
            ->post(route('admin.profesores.store'), ['nombre' => 'María Salazar'])
            ->assertRedirect(route('admin.profesores.index'));

        $profesor = Profesor::query()->where('nombre', 'María Salazar')->firstOrFail();

        $this->actingAs($funcionario)
            ->put(route('admin.grupos.assign-professor', $grupo), [
                'profesor_id' => $profesor->id,
            ])
            ->assertRedirect(route('admin.grupos.index'));

        $this->assertDatabaseHas('grupos', [
            'id' => $grupo->id,
            'codigo' => 'A401',
            'profesor_id' => $profesor->id,
        ]);
    }

    public function test_student_cannot_manage_group_professors(): void
    {
        $student = User::factory()->create(['role' => UserRole::Estudiante]);

        $this->actingAs($student)
            ->get(route('admin.grupos.index'))
            ->assertForbidden();

        $this->actingAs($student)
            ->get(route('admin.profesores.index'))
            ->assertForbidden();

        $this->actingAs($student)
            ->get(route('admin.matriculas.index'))
            ->assertForbidden();
    }
}
