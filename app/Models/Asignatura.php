<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['programa_id', 'codigo', 'nombre', 'creditos'])]
class Asignatura extends Model
{
    use SoftDeletes;

    public function programa(): BelongsTo
    {
        return $this->belongsTo(Programa::class);
    }

    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class);
    }
}
