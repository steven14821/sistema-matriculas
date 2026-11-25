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

    public function test_functionary_can_create_the_academic_catalog_and_associate_subjects_with_careers(): void
    {
        $functionary = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Funcionario]);
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
            'limite_creditos' => 18,
        ])->assertRedirect(route('staff.periodos.index'));

        $period = Periodo::query()->firstOrFail();
        $profesor = Profesor::query()->create(['nombre' => 'Laura Gómez']);
        $this->post(route('staff.grupos.store'), [
            'codigo' => 'SIS301-01',
            'asignatura_id' => $subject->id,
            'periodo_id' => $period->id,
            'profesor_id' => $profesor->id,
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
            'profesor_id' => $profesor->id,
            'cupo' => 25,
        ]);
        $this->assertDatabaseHas('periodos', [
            'id' => $period->id,
            'limite_creditos' => 18,
        ]);
    }

    public function test_functionary_cannot_create_a_period_with_end_before_start(): void
    {
        $functionary = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Funcionario]);

        $this->actingAs($functionary)
            ->from(route('staff.periodos.create'))
            ->post(route('staff.periodos.store'), [
                'nombre' => 'Periodo inválido',
                'fecha_inicio' => '2026-12-15',
                'fecha_fin' => '2026-07-01',
                'abierto' => '1',
                'limite_creditos' => 18,
            ])
            ->assertRedirect(route('staff.periodos.create'))
            ->assertSessionHasErrors('fecha_fin');

        $this->assertDatabaseCount('periodos', 0);
    }

    public function test_functionary_can_open_each_academic_management_page(): void
    {
        $functionary = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Funcionario]);

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

        $this->get(route('staff.profesores.index'))
            ->assertOk()
            ->assertViewIs('staff.profesores.index');
    }

    public function test_functionary_cannot_delete_a_program_with_associated_subjects(): void
    {
        $functionary = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Funcionario]);
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
        $administrator = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Administrador]);

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

    public function test_admin_can_view_the_catalog_and_student_records(): void
    {
        $admin = User::factory()->withTwoFactorEnabled()->create([
            'role' => UserRole::Administrador,
        ]);

        $this->withoutVite()
            ->actingAs($admin)
            ->get(route('admin.catalogo.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.estudiantes.index'))
            ->assertOk();
    }

    public function test_functionary_can_create_multiple_groups_for_the_same_subject_with_distinct_codes(): void
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
            'limite_creditos' => 18,
        ]);
        $functionary = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Funcionario]);

        $this->withoutVite()
            ->actingAs($functionary)
            ->get(route('staff.grupos.create'))
            ->assertOk()
            ->assertSee('Código del grupo')
            ->assertSee('Horario');

        foreach (['A101', 'A102'] as $codigo) {
            $this->post(route('staff.grupos.store'), [
                'codigo' => $codigo,
                'asignatura_id' => $asignatura->id,
                'periodo_id' => $periodo->id,
                'profesor_id' => '',
                'horario' => 'Lunes 08:00-10:00',
                'cupo' => 25,
            ])->assertRedirect(route('staff.grupos.index'));
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

        $this->from(route('staff.grupos.create'))
            ->post(route('staff.grupos.store'), [
                'codigo' => 'A101',
                'asignatura_id' => $asignatura->id,
                'periodo_id' => $periodo->id,
                'profesor_id' => '',
                'horario' => 'Lunes 08:00-10:00',
                'cupo' => 25,
            ])
            ->assertRedirect(route('staff.grupos.create'))
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
            'limite_creditos' => 18,
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

        $student = User::factory()->withTwoFactorEnabled()->create([
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
        $this->travelTo('2026-03-01 12:00:00');

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
            'limite_creditos' => 18,
        ]);

        $grupo = Grupo::query()->create([
            'codigo' => 'A101',
            'asignatura_id' => $asignatura->id,
            'periodo_id' => $periodo->id,
            'cupo' => 1,
        ]);

        $studentUser = User::factory()->withTwoFactorEnabled()->create([
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
            ->assertRedirect(route('student.enrollments.index'));

        $this->assertDatabaseHas('matriculas', [
            'estudiante_id' => $studentUser->estudiante->id,
            'grupo_id' => $grupo->id,
            'estado' => 'activa',
        ]);

        $this->actingAs($studentUser)
            ->post(route('matriculas.inscribir', $grupo))
            ->assertSessionHasErrors('grupo');
    }

    public function test_functionary_can_register_matriculas_without_duplicates_or_overbooking(): void
    {
        $this->travelTo('2027-03-01 12:00:00');

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
            'limite_creditos' => 18,
        ]);

        $grupo = Grupo::query()->create([
            'codigo' => 'A301',
            'asignatura_id' => $asignatura->id,
            'periodo_id' => $periodo->id,
            'cupo' => 1,
        ]);

        $studentUser = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Estudiante]);
        $estudiante = Estudiante::query()->create([
            'user_id' => $studentUser->id,
            'codigo' => 'EST-2027-301',
            'documento' => '1000000301',
            'programa_id' => $programa->id,
        ]);
        $funcionario = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Funcionario]);

        $this->withoutVite()
            ->actingAs($funcionario)
            ->get(route('staff.enrollments.index'))
            ->assertOk()
            ->assertSee('Matrículas');

        $this->actingAs($funcionario)
            ->post(route('staff.enrollments.store'), [
                'estudiante_id' => $estudiante->id,
                'grupo_id' => $grupo->id,
            ])
            ->assertRedirect(route('staff.enrollments.index'));

        $this->assertDatabaseHas('matriculas', [
            'estudiante_id' => $estudiante->id,
            'grupo_id' => $grupo->id,
            'estado' => 'activa',
        ]);

        $this->actingAs($funcionario)
            ->from(route('staff.enrollments.create'))
            ->post(route('staff.enrollments.store'), [
                'estudiante_id' => $estudiante->id,
                'grupo_id' => $grupo->id,
            ])
            ->assertRedirect(route('staff.enrollments.create'))
            ->assertSessionHasErrors('grupo');

        $anotherStudentUser = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Estudiante]);
        $anotherEstudiante = Estudiante::query()->create([
            'user_id' => $anotherStudentUser->id,
            'codigo' => 'EST-2027-302',
            'documento' => '1000000302',
            'programa_id' => $programa->id,
        ]);

        $this->actingAs($funcionario)
            ->from(route('staff.enrollments.create'))
            ->post(route('staff.enrollments.store'), [
                'estudiante_id' => $anotherEstudiante->id,
                'grupo_id' => $grupo->id,
            ])
            ->assertRedirect(route('staff.enrollments.create'))
            ->assertSessionHasErrors('grupo');

        $this->assertSame(1, Matricula::query()->where('grupo_id', $grupo->id)->count());
    }

    public function test_functionary_can_reactivate_a_soft_deleted_matricula_without_duplicating_it(): void
    {
        $this->travelTo('2027-07-12 12:00:00');

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
            'limite_creditos' => 18,
        ]);

        $grupo = Grupo::query()->create([
            'codigo' => 'A302',
            'asignatura_id' => $asignatura->id,
            'periodo_id' => $periodo->id,
            'cupo' => 1,
        ]);

        $studentUser = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Estudiante]);
        $estudiante = Estudiante::query()->create([
            'user_id' => $studentUser->id,
            'codigo' => 'EST-2027-302',
            'documento' => '1000000302',
            'programa_id' => $programa->id,
        ]);
        $funcionario = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Funcionario]);

        $matricula = Matricula::query()->create([
            'estudiante_id' => $estudiante->id,
            'grupo_id' => $grupo->id,
            'estado' => 'activa',
            'fecha' => '2027-07-10',
        ]);
        $matricula->delete();

        $this->actingAs($funcionario)
            ->post(route('staff.enrollments.store'), [
                'estudiante_id' => $estudiante->id,
                'grupo_id' => $grupo->id,
            ])
            ->assertRedirect(route('staff.enrollments.index'));

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
            'limite_creditos' => 18,
        ]);
        $grupo = Grupo::query()->create([
            'codigo' => 'A401',
            'asignatura_id' => $asignatura->id,
            'periodo_id' => $periodo->id,
            'cupo' => 20,
        ]);
        $funcionario = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Funcionario]);

        $this->actingAs($funcionario)
            ->post(route('staff.profesores.store'), ['nombre' => 'María Salazar'])
            ->assertRedirect(route('staff.profesores.index'));

        $profesor = Profesor::query()->where('nombre', 'María Salazar')->firstOrFail();

        $this->actingAs($funcionario)
            ->put(route('staff.grupos.update', $grupo), [
                'codigo' => $grupo->codigo,
                'asignatura_id' => $asignatura->id,
                'periodo_id' => $periodo->id,
                'profesor_id' => $profesor->id,
                'horario' => 'Martes 08:00-10:00',
                'cupo' => 20,
            ])
            ->assertRedirect(route('staff.grupos.index'));

        $this->assertDatabaseHas('grupos', [
            'id' => $grupo->id,
            'codigo' => 'A401',
            'profesor_id' => $profesor->id,
        ]);
    }

    public function test_student_cannot_manage_group_professors(): void
    {
        $student = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Estudiante]);

        $this->actingAs($student)
            ->get(route('staff.grupos.index'))
            ->assertForbidden();

        $this->actingAs($student)
            ->get(route('staff.profesores.index'))
            ->assertForbidden();

        $this->actingAs($student)
            ->get(route('staff.enrollments.index'))
            ->assertForbidden();
    }
}
