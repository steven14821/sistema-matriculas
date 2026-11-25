<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Asignatura;
use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\Periodo;
use App\Models\Programa;
use App\Models\SolicitudPrograma;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SolicitudProgramaTest extends TestCase
{
    use LazilyRefreshDatabase;

    public static function usersWithoutProgramReview(): array
    {
        return [
            'student' => [UserRole::Estudiante],
            'administrator' => [UserRole::Administrador],
        ];
    }

    public function test_unassigned_student_can_submit_a_private_receipt_and_program_request(): void
    {
        Storage::fake('local');
        ['user' => $user, 'student' => $student] = $this->createStudent();
        $program = $this->createProgram('Ingeniería de Sistemas');

        $response = $this->actingAs($user)
            ->post(route('student.solicitudes_programa.store'), [
                'programa_id' => $program->id,
                'comprobante' => $this->receipt(),
            ]);

        $response->assertRedirect(route('student.enrollments.index'));
        $response->assertSessionHas('status', 'Recibimos tu solicitud. El funcionario revisará el recibo antes de asignar el programa.');
        $this->assertDatabaseHas('solicitudes_programa', [
            'estudiante_id' => $student->id,
            'programa_id' => $program->id,
            'estado' => SolicitudPrograma::PENDIENTE,
        ]);
        $this->assertSame(null, $student->fresh()->programa_id);

        $application = SolicitudPrograma::query()->firstOrFail();
        Storage::disk('local')->assertExists($application->comprobante_path);

        $this->withoutVite()
            ->get(route('student.enrollments.index'))
            ->assertOk()
            ->assertSee('Solicitud en revisión')
            ->assertSee('Ingeniería de Sistemas')
            ->assertDontSee('Oferta de grupos');
    }

    public function test_student_cannot_submit_another_request_while_one_is_pending(): void
    {
        Storage::fake('local');
        ['user' => $user, 'student' => $student] = $this->createStudent();
        $program = $this->createProgram('Ingeniería de Sistemas');
        $this->createProgramRequest($student, $program);

        $this->actingAs($user)
            ->from(route('student.enrollments.index'))
            ->post(route('student.solicitudes_programa.store'), [
                'programa_id' => $program->id,
                'comprobante' => $this->receipt(),
            ])
            ->assertRedirect(route('student.enrollments.index'))
            ->assertSessionHasErrors([
                'comprobante' => 'Ya tienes una solicitud pendiente de revisión.',
            ]);

        $this->assertDatabaseCount('solicitudes_programa', 1);
        $this->assertCount(1, Storage::disk('local')->allFiles('solicitudes-programa'));
    }

    public function test_student_cannot_request_a_program_if_one_is_already_assigned(): void
    {
        Storage::fake('local');
        $program = $this->createProgram('Ingeniería de Sistemas');
        ['user' => $user, 'student' => $student] = $this->createStudent($program);

        $this->actingAs($user)
            ->from(route('student.enrollments.index'))
            ->post(route('student.solicitudes_programa.store'), [
                'programa_id' => $this->createProgram('Derecho')->id,
                'comprobante' => $this->receipt(),
            ])
            ->assertRedirect(route('student.enrollments.index'))
            ->assertSessionHasErrors([
                'programa_id' => 'Tu perfil ya tiene un programa asignado.',
            ]);

        $this->assertDatabaseCount('solicitudes_programa', 0);
        $this->assertSame($program->id, $student->fresh()->programa_id);
    }

    public function test_functionary_approval_assigns_the_program_and_unlocks_its_subjects(): void
    {
        Storage::fake('local');
        $this->travelTo('2026-11-16 12:00:00');
        ['user' => $studentUser, 'student' => $student] = $this->createStudent();
        $program = $this->createProgram('Ingeniería de Sistemas');
        $otherProgram = $this->createProgram('Derecho');
        $application = $this->createProgramRequest($student, $program);
        $period = Periodo::query()->create([
            'nombre' => '2026-2',
            'fecha_inicio' => today()->subDay()->toDateString(),
            'fecha_fin' => today()->addDay()->toDateString(),
            'abierto' => true,
            'limite_creditos' => 12,
        ]);
        $this->createGroup($program, $period, 'SIS101', 'Fundamentos de Programación');
        $this->createGroup($otherProgram, $period, 'DER101', 'Introducción al Derecho');
        $functionary = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Funcionario]);

        $response = $this->actingAs($functionary)
            ->patch(route('staff.solicitudes_programa.update', $application), [
                'estado' => SolicitudPrograma::APROBADA,
                'programa_id' => $otherProgram->id,
            ]);

        $response->assertRedirect(route('staff.solicitudes_programa.index'));
        $this->assertDatabaseHas('estudiantes', [
            'id' => $student->id,
            'programa_id' => $program->id,
        ]);
        $this->assertDatabaseHas('solicitudes_programa', [
            'id' => $application->id,
            'estado' => SolicitudPrograma::APROBADA,
            'revisado_por_user_id' => $functionary->id,
        ]);

        $this->withoutVite()
            ->actingAs($studentUser)
            ->get(route('student.enrollments.index'))
            ->assertOk()
            ->assertSee('Fundamentos de Programación')
            ->assertDontSee('Introducción al Derecho');
    }

    public function test_rejecting_a_program_request_requires_a_reason_and_keeps_the_program_unassigned(): void
    {
        Storage::fake('local');
        ['user' => $studentUser, 'student' => $student] = $this->createStudent();
        $program = $this->createProgram('Ingeniería de Sistemas');
        $application = $this->createProgramRequest($student, $program);
        $functionary = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Funcionario]);

        $this->actingAs($functionary)
            ->from(route('staff.solicitudes_programa.index'))
            ->patch(route('staff.solicitudes_programa.update', $application), [
                'estado' => SolicitudPrograma::RECHAZADA,
            ])
            ->assertRedirect(route('staff.solicitudes_programa.index'))
            ->assertSessionHasErrors('observacion');

        $this->patch(route('staff.solicitudes_programa.update', $application), [
            'estado' => SolicitudPrograma::RECHAZADA,
            'observacion' => '<script>alert("recibo ilegible")</script>',
        ])->assertRedirect(route('staff.solicitudes_programa.index'));

        $this->assertDatabaseHas('solicitudes_programa', [
            'id' => $application->id,
            'estado' => SolicitudPrograma::RECHAZADA,
            'observacion' => '<script>alert("recibo ilegible")</script>',
        ]);
        $this->assertSame(null, $student->fresh()->programa_id);

        $this->withoutVite()
            ->actingAs($studentUser)
            ->get(route('student.enrollments.index'))
            ->assertOk()
            ->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>', false)
            ->assertSee('Solicita tu programa');
    }

    #[DataProvider('usersWithoutProgramReview')]
    public function test_non_functionary_cannot_review_or_download_a_receipt(UserRole $role): void
    {
        Storage::fake('local');
        $program = $this->createProgram('Ingeniería de Sistemas');
        ['student' => $student] = $this->createStudent();
        $application = $this->createProgramRequest($student, $program);
        $user = User::factory()->withTwoFactorEnabled()->create(['role' => $role]);

        $this->actingAs($user)
            ->get(route('staff.solicitudes_programa.comprobante', $application))
            ->assertForbidden();

        $this->patch(route('staff.solicitudes_programa.update', $application), [
            'estado' => SolicitudPrograma::APROBADA,
        ])->assertForbidden();

        $this->assertDatabaseHas('solicitudes_programa', [
            'id' => $application->id,
            'estado' => SolicitudPrograma::PENDIENTE,
        ]);
    }

    public function test_functionary_can_download_a_receipt_through_the_protected_route(): void
    {
        Storage::fake('local');
        $program = $this->createProgram('Ingeniería de Sistemas');
        ['student' => $student] = $this->createStudent();
        $application = $this->createProgramRequest($student, $program);
        $functionary = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Funcionario]);

        $this->actingAs($functionary)
            ->get(route('staff.solicitudes_programa.comprobante', $application))
            ->assertDownload();
    }

    public function test_functionary_cannot_delete_a_program_with_a_request_associated(): void
    {
        Storage::fake('local');
        $program = $this->createProgram('Ingeniería de Sistemas');
        ['student' => $student] = $this->createStudent();
        $this->createProgramRequest($student, $program);
        $functionary = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Funcionario]);

        $this->actingAs($functionary)
            ->from(route('staff.programas.index'))
            ->delete(route('staff.programas.destroy', $program))
            ->assertRedirect(route('staff.programas.index'))
            ->assertSessionHasErrors([
                'programa' => 'No se puede eliminar un programa con asignaturas, estudiantes o solicitudes asociadas.',
            ]);

        $this->assertDatabaseHas('programas', [
            'id' => $program->id,
            'deleted_at' => null,
        ]);
    }

    public function test_receipt_must_be_a_supported_file_type(): void
    {
        Storage::fake('local');
        ['user' => $user] = $this->createStudent();
        $program = $this->createProgram('Ingeniería de Sistemas');

        $this->actingAs($user)
            ->from(route('student.enrollments.index'))
            ->post(route('student.solicitudes_programa.store'), [
                'programa_id' => $program->id,
                'comprobante' => UploadedFile::fake()->createWithContent('script.php', '<?php echo "no";'),
            ])
            ->assertRedirect(route('student.enrollments.index'))
            ->assertSessionHasErrors('comprobante');

        $this->assertDatabaseCount('solicitudes_programa', 0);
    }

    /**
     * @return array{user: User, student: Estudiante}
     */
    private function createStudent(?Programa $program = null): array
    {
        $user = User::factory()->withTwoFactorEnabled()->create(['role' => UserRole::Estudiante]);
        $student = Estudiante::query()->create([
            'user_id' => $user->id,
            'codigo' => 'EST-'.$user->id,
            'documento' => 'DOC-'.$user->id,
            'programa_id' => $program?->id,
        ]);

        return ['user' => $user, 'student' => $student];
    }

    private function createProgram(string $name): Programa
    {
        return Programa::query()->create([
            'nombre' => $name,
            'nivel' => 'Pregrado',
        ]);
    }

    private function createProgramRequest(Estudiante $student, Programa $program): SolicitudPrograma
    {
        $receiptPath = 'solicitudes-programa/'.$student->id.'-'.$program->id.'.pdf';
        Storage::disk('local')->put($receiptPath, '%PDF-1.4 test receipt');

        return SolicitudPrograma::query()->create([
            'estudiante_id' => $student->id,
            'programa_id' => $program->id,
            'comprobante_path' => $receiptPath,
            'estado' => SolicitudPrograma::PENDIENTE,
        ]);
    }

    private function createGroup(Programa $program, Periodo $period, string $code, string $name): Grupo
    {
        $subject = Asignatura::query()->create([
            'programa_id' => $program->id,
            'codigo' => $code,
            'nombre' => $name,
            'creditos' => 3,
        ]);

        return Grupo::query()->create([
            'codigo' => $code.'-01',
            'asignatura_id' => $subject->id,
            'periodo_id' => $period->id,
            'docente' => 'Laura Gómez',
            'horario' => 'Por definir',
            'cupo' => 25,
        ]);
    }

    private function receipt(): UploadedFile
    {
        $path = tempnam(__DIR__, 'receipt-');

        if ($path === false) {
            throw new \RuntimeException('Unable to create a temporary receipt fixture.');
        }

        if (file_put_contents($path, "%PDF-1.4\n% recibo de prueba\n") === false) {
            unlink($path);

            throw new \RuntimeException('Unable to write the temporary receipt fixture.');
        }

        $this->beforeApplicationDestroyed(static function () use ($path): void {
            if (file_exists($path) && ! unlink($path)) {
                throw new \RuntimeException('Unable to remove the temporary receipt fixture.');
            }
        });

        return new UploadedFile($path, 'recibo.pdf', 'application/pdf', null, true);
    }
}
