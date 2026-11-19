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
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StudentEnrollmentTest extends TestCase
{
    use LazilyRefreshDatabase;

    public static function usersWithoutSelfEnrollment(): array
    {
        return [
            'administrator' => [UserRole::Administrador],
            'staff member' => [UserRole::Funcionario],
        ];
    }

    public function test_guest_is_redirected_to_login_from_enrollment_page(): void
    {
        $this->get(route('student.enrollments.index'))
            ->assertRedirect(route('login'));
    }

    #[DataProvider('usersWithoutSelfEnrollment')]
    public function test_user_without_student_role_cannot_open_enrollment_page(UserRole $role): void
    {
        $user = User::factory()->create(['role' => $role]);

        $this->actingAs($user)
            ->get(route('student.enrollments.index'))
            ->assertForbidden();
    }

    public function test_student_sees_only_groups_for_their_program_in_currently_open_periods(): void
    {
        $this->freezeEnrollmentDate();
        ['user' => $user, 'program' => $program] = $this->createStudent();
        $otherProgram = $this->createProgram('Diseño Industrial');
        $openPeriod = $this->createPeriod();
        $closedPeriod = $this->createPeriod(false);

        $this->createGroup(
            $this->createSubject($program, 'SIS101', 'Programación de Sistemas'),
            $openPeriod,
        );
        $this->createGroup(
            $this->createSubject($otherProgram, 'DIS101', 'Arquitectura de Software'),
            $openPeriod,
        );
        $this->createGroup(
            $this->createSubject($program, 'SIS201', 'Matemáticas cerradas'),
            $closedPeriod,
        );

        $this->withoutVite()
            ->actingAs($user)
            ->get(route('student.enrollments.index'))
            ->assertOk()
            ->assertViewIs('student.enrollments.index')
            ->assertSee('Programación de Sistemas')
            ->assertDontSee('Arquitectura de Software')
            ->assertDontSee('Matemáticas cerradas');
    }

    public function test_student_can_enroll_in_an_available_group_and_see_the_active_enrollment(): void
    {
        $this->freezeEnrollmentDate();
        ['user' => $user, 'student' => $student, 'program' => $program] = $this->createStudent();
        $group = $this->createGroup(
            $this->createSubject($program, 'SIS101', 'Programación de Sistemas'),
            $this->createPeriod(),
        );

        $response = $this->actingAs($user)
            ->from(route('student.enrollments.index'))
            ->post(route('student.enrollments.store', $group));

        $response->assertRedirect(route('student.enrollments.index'));
        $response->assertSessionHas('status', 'La matrícula se realizó correctamente.');
        $this->assertDatabaseHas('matriculas', [
            'estudiante_id' => $student->id,
            'grupo_id' => $group->id,
            'estado' => 'activa',
        ]);
        $this->assertSame(today()->toDateString(), Matricula::query()->firstOrFail()->fecha->toDateString());

        $this->withoutVite()
            ->get(route('student.enrollments.index'))
            ->assertOk()
            ->assertSee('Programación de Sistemas')
            ->assertSee('Inscrito');
    }

    public function test_student_cannot_enroll_when_the_period_is_closed_or_outside_its_dates(): void
    {
        $this->freezeEnrollmentDate();
        ['user' => $user, 'program' => $program] = $this->createStudent();
        $closedGroup = $this->createGroup(
            $this->createSubject($program, 'SIS101', 'Programación'),
            $this->createPeriod(false),
        );
        $expiredGroup = $this->createGroup(
            $this->createSubject($program, 'SIS201', 'Bases de datos'),
            $this->createPeriod(
                true,
                today()->subDays(10)->toDateString(),
                today()->subDay()->toDateString(),
            ),
        );

        foreach ([$closedGroup, $expiredGroup] as $group) {
            $this->actingAs($user)
                ->from(route('student.enrollments.index'))
                ->post(route('student.enrollments.store', $group))
                ->assertRedirect(route('student.enrollments.index'))
                ->assertSessionHasErrors([
                    'grupo' => 'El periodo académico no está habilitado para matrículas.',
                ]);
        }

        $this->assertDatabaseCount('matriculas', 0);
    }

    public function test_student_cannot_enroll_in_a_group_from_another_program(): void
    {
        $this->freezeEnrollmentDate();
        ['user' => $user] = $this->createStudent();
        $otherProgram = $this->createProgram('Diseño Industrial');
        $group = $this->createGroup(
            $this->createSubject($otherProgram, 'DIS101', 'Diseño'),
            $this->createPeriod(),
        );

        $this->actingAs($user)
            ->from(route('student.enrollments.index'))
            ->post(route('student.enrollments.store', $group))
            ->assertRedirect(route('student.enrollments.index'))
            ->assertSessionHasErrors([
                'grupo' => 'El grupo no pertenece al programa académico asociado a tu perfil.',
            ]);

        $this->assertDatabaseCount('matriculas', 0);
    }

    public function test_student_cannot_enroll_when_the_group_is_full(): void
    {
        $this->freezeEnrollmentDate();
        ['user' => $user, 'student' => $student, 'program' => $program] = $this->createStudent();
        $otherStudent = $this->createStudent($program)['student'];
        $group = $this->createGroup(
            $this->createSubject($program, 'SIS101', 'Programación'),
            $this->createPeriod(),
            1,
        );
        $this->createEnrollment($otherStudent, $group);

        $this->actingAs($user)
            ->from(route('student.enrollments.index'))
            ->post(route('student.enrollments.store', $group))
            ->assertRedirect(route('student.enrollments.index'))
            ->assertSessionHasErrors([
                'grupo' => 'El grupo ya no tiene cupos disponibles.',
            ]);

        $this->assertDatabaseMissing('matriculas', [
            'estudiante_id' => $student->id,
            'grupo_id' => $group->id,
        ]);
        $this->assertDatabaseCount('matriculas', 1);
    }

    public function test_student_cannot_enroll_in_two_groups_of_the_same_subject_for_one_period(): void
    {
        $this->freezeEnrollmentDate();
        ['user' => $user, 'student' => $student, 'program' => $program] = $this->createStudent();
        $period = $this->createPeriod();
        $subject = $this->createSubject($program, 'SIS101', 'Programación');
        $firstGroup = $this->createGroup($subject, $period);
        $secondGroup = $this->createGroup($subject, $period);
        $this->createEnrollment($student, $firstGroup);

        $this->actingAs($user)
            ->from(route('student.enrollments.index'))
            ->post(route('student.enrollments.store', $secondGroup))
            ->assertRedirect(route('student.enrollments.index'))
            ->assertSessionHasErrors([
                'grupo' => 'Ya tienes una matrícula activa en otro grupo de esta asignatura para el mismo periodo.',
            ]);

        $this->assertDatabaseCount('matriculas', 1);
    }

    public function test_student_cannot_submit_a_second_enrollment_for_the_same_group(): void
    {
        $this->freezeEnrollmentDate();
        ['user' => $user, 'student' => $student, 'program' => $program] = $this->createStudent();
        $group = $this->createGroup(
            $this->createSubject($program, 'SIS101', 'Programación'),
            $this->createPeriod(),
        );
        $this->createEnrollment($student, $group);

        $this->actingAs($user)
            ->from(route('student.enrollments.index'))
            ->post(route('student.enrollments.store', $group))
            ->assertRedirect(route('student.enrollments.index'))
            ->assertSessionHasErrors([
                'grupo' => 'Ya tienes una matrícula activa en este grupo.',
            ]);

        $this->assertDatabaseCount('matriculas', 1);
    }

    public function test_student_can_reenroll_after_a_previous_enrollment_was_cancelled(): void
    {
        $this->freezeEnrollmentDate();
        ['user' => $user, 'student' => $student, 'program' => $program] = $this->createStudent();
        $group = $this->createGroup(
            $this->createSubject($program, 'SIS101', 'Programación'),
            $this->createPeriod(),
        );
        $enrollment = $this->createEnrollment($student, $group);
        $enrollment->update(['estado' => 'cancelada']);

        $this->actingAs($user)
            ->from(route('student.enrollments.index'))
            ->post(route('student.enrollments.store', $group))
            ->assertRedirect(route('student.enrollments.index'))
            ->assertSessionHas('status', 'La matrícula se realizó correctamente.');

        $this->assertDatabaseCount('matriculas', 1);
        $this->assertDatabaseHas('matriculas', [
            'id' => $enrollment->id,
            'estado' => 'activa',
        ]);
    }

    public function test_student_without_an_academic_profile_cannot_submit_an_enrollment(): void
    {
        $this->freezeEnrollmentDate();
        $user = User::factory()->create(['role' => UserRole::Estudiante]);
        $program = $this->createProgram();
        $group = $this->createGroup(
            $this->createSubject($program, 'SIS101', 'Programación'),
            $this->createPeriod(),
        );

        $this->withoutVite()
            ->actingAs($user)
            ->get(route('student.enrollments.index'))
            ->assertOk()
            ->assertSee('Tu usuario no tiene un perfil de estudiante asociado');

        $this->from(route('student.enrollments.index'))
            ->post(route('student.enrollments.store', $group))
            ->assertRedirect(route('student.enrollments.index'))
            ->assertSessionHasErrors([
                'estudiante' => 'Tu usuario no tiene un perfil de estudiante asociado. Contacta a la administración.',
            ]);

        $this->assertDatabaseCount('matriculas', 0);
    }

    public function test_student_cannot_enroll_until_a_credit_limit_is_configured_for_the_period(): void
    {
        $this->freezeEnrollmentDate();
        ['user' => $user, 'program' => $program] = $this->createStudent();
        $group = $this->createGroup(
            $this->createSubject($program, 'SIS101', 'Programación'),
            $this->createPeriod(true, null, null, null),
        );

        $this->actingAs($user)
            ->from(route('student.enrollments.index'))
            ->post(route('student.enrollments.store', $group))
            ->assertRedirect(route('student.enrollments.index'))
            ->assertSessionHasErrors([
                'grupo' => 'Este periodo aún no tiene un límite de créditos configurado. Contacta al funcionario.',
            ]);

        $this->assertDatabaseCount('matriculas', 0);
    }

    public function test_student_cannot_exceed_the_credit_limit_for_a_period(): void
    {
        $this->freezeEnrollmentDate();
        ['user' => $user, 'student' => $student, 'program' => $program] = $this->createStudent();
        $period = $this->createPeriod(true, null, null, 5);
        $firstGroup = $this->createGroup(
            $this->createSubject($program, 'SIS101', 'Programación', 3),
            $period,
        );
        $secondGroup = $this->createGroup(
            $this->createSubject($program, 'SIS201', 'Bases de datos', 3),
            $period,
        );

        $this->actingAs($user)
            ->post(route('student.enrollments.store', $firstGroup))
            ->assertRedirect(route('student.enrollments.index'));

        $this->from(route('student.enrollments.index'))
            ->post(route('student.enrollments.store', $secondGroup))
            ->assertRedirect(route('student.enrollments.index'))
            ->assertSessionHasErrors([
                'grupo' => 'La asignatura excede el límite de 5 créditos del periodo. Te quedan 2 créditos disponibles.',
            ]);

        $this->assertDatabaseHas('matriculas', [
            'estudiante_id' => $student->id,
            'grupo_id' => $firstGroup->id,
            'estado' => 'activa',
        ]);
        $this->assertDatabaseMissing('matriculas', [
            'estudiante_id' => $student->id,
            'grupo_id' => $secondGroup->id,
        ]);
    }

    public function test_student_credit_limits_are_calculated_separately_for_each_period(): void
    {
        $this->freezeEnrollmentDate();
        ['user' => $user, 'student' => $student, 'program' => $program] = $this->createStudent();
        $firstGroup = $this->createGroup(
            $this->createSubject($program, 'SIS101', 'Programación'),
            $this->createPeriod(true, null, null, 3),
        );
        $secondGroup = $this->createGroup(
            $this->createSubject($program, 'SIS201', 'Bases de datos'),
            $this->createPeriod(true, null, null, 3),
        );

        foreach ([$firstGroup, $secondGroup] as $group) {
            $this->actingAs($user)
                ->post(route('student.enrollments.store', $group))
                ->assertRedirect(route('student.enrollments.index'));
        }

        $this->assertDatabaseCount('matriculas', 2);
        $this->assertDatabaseHas('matriculas', [
            'estudiante_id' => $student->id,
            'grupo_id' => $secondGroup->id,
            'estado' => 'activa',
        ]);
    }

    /**
     * @return array{user: User, student: Estudiante, program: Programa}
     */
    private function createStudent(?Programa $program = null): array
    {
        $program ??= $this->createProgram();
        $user = User::factory()->create(['role' => UserRole::Estudiante]);
        $student = Estudiante::query()->create([
            'user_id' => $user->id,
            'codigo' => 'EST-'.$user->id,
            'documento' => 'DOC-'.$user->id,
            'programa_id' => $program->id,
        ]);

        return [
            'user' => $user,
            'student' => $student,
            'program' => $program,
        ];
    }

    private function createProgram(string $name = 'Ingeniería de Sistemas'): Programa
    {
        return Programa::query()->create([
            'nombre' => $name,
            'nivel' => 'Pregrado',
        ]);
    }

    private function createSubject(Programa $program, string $code, string $name, int $credits = 3): Asignatura
    {
        return Asignatura::query()->create([
            'programa_id' => $program->id,
            'codigo' => $code,
            'nombre' => $name,
            'creditos' => $credits,
        ]);
    }

    private function createPeriod(
        bool $open = true,
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $limitCredits = 18,
    ): Periodo {
        return Periodo::query()->create([
            'nombre' => 'Periodo '.(Periodo::query()->count() + 1),
            'fecha_inicio' => $startDate ?? today()->subDay()->toDateString(),
            'fecha_fin' => $endDate ?? today()->addDay()->toDateString(),
            'abierto' => $open,
            'limite_creditos' => $limitCredits,
        ]);
    }

    private function createGroup(
        Asignatura $subject,
        Periodo $period,
        int $capacity = 30,
    ): Grupo {
        return Grupo::query()->create([
            'codigo' => 'GRP-'.(Grupo::withTrashed()->count() + 1),
            'asignatura_id' => $subject->id,
            'periodo_id' => $period->id,
            'docente' => 'Laura Gómez',
            'horario' => 'Lunes y miércoles, 08:00-10:00',
            'cupo' => $capacity,
        ]);
    }

    private function createEnrollment(Estudiante $student, Grupo $group): Matricula
    {
        return Matricula::query()->create([
            'estudiante_id' => $student->id,
            'grupo_id' => $group->id,
            'estado' => 'activa',
            'fecha' => today()->toDateString(),
        ]);
    }

    private function freezeEnrollmentDate(): void
    {
        $this->travelTo('2026-11-16 12:00:00');
    }
}
