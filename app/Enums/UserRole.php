<?php

namespace App\Enums;

enum UserRole: string
{
    case Administrador = 'administrador';
    case Funcionario = 'funcionario';
    case Estudiante = 'estudiante';

    public function label(): string
    {
        return match ($this) {
            self::Administrador => 'Administrador',
            self::Funcionario => 'Funcionario',
            self::Estudiante => 'Estudiante',
        };
    }

    public function dashboardView(): string
    {
        return match ($this) {
            self::Administrador => 'dashboard.administrador',
            self::Funcionario => 'dashboard.funcionario',
            self::Estudiante => 'dashboard.estudiante',
        };
    }
}
