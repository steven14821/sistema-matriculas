<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'estudiante_id',
    'programa_id',
    'comprobante_path',
    'estado',
    'observacion',
    'revisado_por_user_id',
    'revisado_en',
])]
class SolicitudPrograma extends Model
{
    protected $table = 'solicitudes_programa';

    public const string PENDIENTE = 'pendiente';

    public const string APROBADA = 'aprobada';

    public const string RECHAZADA = 'rechazada';

    protected function casts(): array
    {
        return [
            'revisado_en' => 'datetime',
        ];
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class);
    }

    public function programa(): BelongsTo
    {
        return $this->belongsTo(Programa::class);
    }

    public function revisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisado_por_user_id');
    }
}
