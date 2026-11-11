<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Asignatura;
use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\Matricula;
use App\Models\Periodo;
use App\Models\Programa;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $demoPassword = 'Matriculas2026!';

        $demoUsers = [];

        foreach ([
            ['name' => 'Administración', 'email' => 'admin@matriculas.test', 'role' => UserRole::Administrador],
            ['name' => 'Funcionario de prueba', 'email' => 'funcionario@matriculas.test', 'role' => UserRole::Funcionario],
            ['name' => 'Estudiante de prueba', 'email' => 'estudiante@matriculas.test', 'role' => UserRole::Estudiante],
        ] as $demoUserData) {
            $demoUsers[$demoUserData['role']->value] = User::query()->firstOrCreate(
                ['email' => $demoUserData['email']],
                [
                    'name' => $demoUserData['name'],
                    'role' => $demoUserData['role'],
                    'password' => $demoPassword,
                ],
            );
        }

        $programa = Programa::query()->firstOrCreate(
            ['nombre' => 'Ingeniería de Sistemas'],
            ['nivel' => 'Pregrado'],
        );

        $programacion = Asignatura::query()->firstOrCreate(
            ['codigo' => 'SIS101'],
            [
                'programa_id' => $programa->id,
                'nombre' => 'Fundamentos de Programación',
                'creditos' => 3,
            ],
        );

        $basesDeDatos = Asignatura::query()->firstOrCreate(
            ['codigo' => 'SIS201'],
            [
                'programa_id' => $programa->id,
                'nombre' => 'Bases de Datos',
                'creditos' => 4,
            ],
        );

        $periodo = Periodo::query()->firstOrCreate(
            ['nombre' => '2026-2'],
            [
                'fecha_inicio' => '2026-07-01',
                'fecha_fin' => '2026-12-15',
                'abierto' => true,
            ],
        );

        $estudiante = Estudiante::query()->firstOrCreate(
            ['user_id' => $demoUsers[UserRole::Estudiante->value]->id],
            [
                'codigo' => 'EST-2026-001',
                'documento' => '1000000001',
                'programa_id' => $programa->id,
            ],
        );

        foreach ([
            [
                'asignatura' => $programacion,
                'docente' => 'Laura Gómez',
                'horario' => 'Lunes y miércoles, 08:00-10:00',
            ],
            [
                'asignatura' => $basesDeDatos,
                'docente' => 'Carlos Rodríguez',
                'horario' => 'Martes y jueves, 10:00-12:00',
            ],
        ] as $groupData) {
            $grupo = Grupo::query()->firstOrCreate(
                [
                    'asignatura_id' => $groupData['asignatura']->id,
                    'periodo_id' => $periodo->id,
                    'horario' => $groupData['horario'],
                ],
                [
                    'docente' => $groupData['docente'],
                    'cupo' => 30,
                ],
            );

            Matricula::query()->firstOrCreate(
                [
                    'estudiante_id' => $estudiante->id,
                    'grupo_id' => $grupo->id,
                ],
                [
                    'estado' => 'activa',
                    'fecha' => '2026-11-11',
                ],
            );
        }
    }
}
