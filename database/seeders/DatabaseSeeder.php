<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Asignatura;
use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\Matricula;
use App\Models\Periodo;
use App\Models\Profesor;
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

        $catalogo = [
            'Ingeniería de Sistemas' => [
                'nivel' => 'Pregrado',
                'asignaturas' => [
                    ['codigo' => 'SIS101', 'nombre' => 'Fundamentos de Programación', 'creditos' => 3],
                    ['codigo' => 'SIS201', 'nombre' => 'Bases de Datos', 'creditos' => 4],
                    ['codigo' => 'SIS202', 'nombre' => 'Programación Orientada a Objetos', 'creditos' => 4],
                    ['codigo' => 'SIS301', 'nombre' => 'Arquitectura de Computadores', 'creditos' => 3],
                    ['codigo' => 'SIS302', 'nombre' => 'Ingeniería de Software', 'creditos' => 4],
                    ['codigo' => 'SIS401', 'nombre' => 'Redes y Comunicaciones', 'creditos' => 3],
                ],
            ],
            'Administración de Empresas' => [
                'nivel' => 'Pregrado',
                'asignaturas' => [
                    ['codigo' => 'ADM101', 'nombre' => 'Fundamentos de Administración', 'creditos' => 3],
                    ['codigo' => 'ADM201', 'nombre' => 'Contabilidad Gerencial', 'creditos' => 4],
                    ['codigo' => 'ADM301', 'nombre' => 'Marketing Estratégico', 'creditos' => 3],
                    ['codigo' => 'ADM401', 'nombre' => 'Dirección Financiera', 'creditos' => 4],
                ],
            ],
            'Contaduría Pública' => [
                'nivel' => 'Pregrado',
                'asignaturas' => [
                    ['codigo' => 'CON101', 'nombre' => 'Contabilidad Básica', 'creditos' => 3],
                    ['codigo' => 'CON201', 'nombre' => 'Impuestos', 'creditos' => 4],
                    ['codigo' => 'CON301', 'nombre' => 'Auditoría', 'creditos' => 4],
                    ['codigo' => 'CON401', 'nombre' => 'Control Interno', 'creditos' => 3],
                ],
            ],
            'Derecho' => [
                'nivel' => 'Pregrado',
                'asignaturas' => [
                    ['codigo' => 'DER101', 'nombre' => 'Introducción al Derecho', 'creditos' => 3],
                    ['codigo' => 'DER201', 'nombre' => 'Derecho Constitucional', 'creditos' => 4],
                    ['codigo' => 'DER301', 'nombre' => 'Derecho Civil', 'creditos' => 4],
                ],
            ],
            'Psicología' => [
                'nivel' => 'Pregrado',
                'asignaturas' => [
                    ['codigo' => 'PSI101', 'nombre' => 'Fundamentos de Psicología', 'creditos' => 3],
                    ['codigo' => 'PSI201', 'nombre' => 'Psicología del Desarrollo', 'creditos' => 4],
                    ['codigo' => 'PSI301', 'nombre' => 'Psicometría', 'creditos' => 3],
                ],
            ],
        ];

        $asignaturas = [];

        foreach ($catalogo as $nombrePrograma => $datosPrograma) {
            $programaCatalogo = Programa::query()->firstOrCreate(
                ['nombre' => $nombrePrograma],
                ['nivel' => $datosPrograma['nivel']],
            );

            foreach ($datosPrograma['asignaturas'] as $datosAsignatura) {
                $asignaturas[$datosAsignatura['codigo']] = Asignatura::query()->firstOrCreate(
                    ['codigo' => $datosAsignatura['codigo']],
                    [
                        'programa_id' => $programaCatalogo->id,
                        'nombre' => $datosAsignatura['nombre'],
                        'creditos' => $datosAsignatura['creditos'],
                    ],
                );
            }
        }

        $periodo = Periodo::query()->firstOrCreate(
            ['nombre' => '2026-2'],
            [
                'fecha_inicio' => '2026-07-01',
                'fecha_fin' => '2026-12-15',
                'abierto' => true,
            ],
        );

        $estudiante = Estudiante::query()->updateOrCreate(
            ['codigo' => 'EST-2026-001'],
            [
                'user_id' => $demoUsers[UserRole::Estudiante->value]->id,
                'documento' => '1000000001',
                'programa_id' => $programa->id,
            ],
        );

        $profesores = [
            'Laura Gómez',
            'Carlos Rodríguez',
            'María Fernanda López',
            'Andrés Martínez',
            'Diana Pérez',
            'Juan Sebastián Ruiz',
            'Natalia Herrera',
            'Ricardo Sánchez',
            'Paola Ramírez',
            'Fernando Castillo',
            'Camila Torres',
            'Santiago Vargas',
        ];

        foreach ($profesores as $nombreProfesor) {
            Profesor::query()->firstOrCreate(['nombre' => $nombreProfesor]);
        }

        $gruposDemo = [
            ['asignatura' => 'SIS101', 'codigo' => 'A101', 'profesor' => 'Laura Gómez'],
            ['asignatura' => 'SIS201', 'codigo' => 'A201', 'profesor' => 'Carlos Rodríguez'],
        ];

        $profesorIndex = 0;

        foreach ($asignaturas as $codigo => $asignatura) {
            $profesor = Profesor::query()
                ->where('nombre', $profesores[$profesorIndex % count($profesores)])
                ->firstOrFail();
            $profesorIndex++;

            Grupo::query()->firstOrCreate(
                [
                    'asignatura_id' => $asignatura->id,
                    'periodo_id' => $periodo->id,
                    'codigo' => $codigo.'-01',
                ],
                [
                    'profesor_id' => $profesor->id,
                    'cupo' => 30,
                ],
            );

            Grupo::query()->firstOrCreate(
                [
                    'asignatura_id' => $asignatura->id,
                    'periodo_id' => $periodo->id,
                    'codigo' => $codigo.'-02',
                ],
                [
                    'profesor_id' => $profesor->id,
                    'cupo' => 25,
                ],
            );
        }

        foreach ($gruposDemo as $grupoDemo) {
            $grupo = Grupo::query()->where('codigo', $grupoDemo['codigo'])->first();

            if (! $grupo) {
                $grupo = Grupo::query()->firstOrCreate(
                    [
                        'asignatura_id' => $asignaturas[$grupoDemo['asignatura']]->id,
                        'periodo_id' => $periodo->id,
                        'codigo' => $grupoDemo['codigo'],
                    ],
                    [
                        'profesor_id' => Profesor::query()
                            ->where('nombre', $grupoDemo['profesor'])
                            ->firstOrFail()
                            ->id,
                        'cupo' => 30,
                    ],
                );
            }

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
