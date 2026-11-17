<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['nombre', 'fecha_inicio', 'fecha_fin', 'abierto'])]
class Periodo extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'abierto' => 'boolean',
        ];
    }

    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class);
    }
}
