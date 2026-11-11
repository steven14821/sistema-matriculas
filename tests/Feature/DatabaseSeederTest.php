<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_local_seeding_creates_one_demo_account_for_each_role(): void
    {
        $this->assertDatabaseCount('users', 0);

        $this->app->make(DatabaseSeeder::class)->run();

        $this->assertDatabaseHas('users', [
            'email' => 'admin@matriculas.test',
            'role' => UserRole::Administrador->value,
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'funcionario@matriculas.test',
            'role' => UserRole::Funcionario->value,
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'estudiante@matriculas.test',
            'role' => UserRole::Estudiante->value,
        ]);
        $this->assertDatabaseHas('estudiantes', [
            'codigo' => 'EST-2026-001',
            'documento' => '1000000001',
        ]);
        $this->assertDatabaseHas('programas', [
            'nombre' => 'Ingeniería de Sistemas',
        ]);
        $this->assertDatabaseHas('periodos', [
            'nombre' => '2026-2',
            'abierto' => true,
        ]);
        $this->assertDatabaseCount('asignaturas', 2);
        $this->assertDatabaseCount('grupos', 2);
        $this->assertDatabaseCount('matriculas', 2);

        $student = User::query()
            ->where('email', 'estudiante@matriculas.test')
            ->with('estudiante.programa')
            ->firstOrFail();

        $this->assertSame('Ingeniería de Sistemas', $student->estudiante->programa->nombre);
    }

    public function test_running_local_seeder_twice_does_not_duplicate_academic_data(): void
    {
        $seeder = $this->app->make(DatabaseSeeder::class);
        $seeder->run();
        $seeder->run();

        $this->assertDatabaseCount('users', 3);
        $this->assertDatabaseCount('programas', 1);
        $this->assertDatabaseCount('asignaturas', 2);
        $this->assertDatabaseCount('periodos', 1);
        $this->assertDatabaseCount('estudiantes', 1);
        $this->assertDatabaseCount('grupos', 2);
        $this->assertDatabaseCount('matriculas', 2);
    }

    public function test_demo_accounts_are_not_created_in_production(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');
        $this->assertDatabaseCount('users', 0);

        $this->app->make(DatabaseSeeder::class)->run();

        $this->assertDatabaseCount('users', 0);
    }
}
