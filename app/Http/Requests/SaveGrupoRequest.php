<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveGrupoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $grupo = $this->route('grupo');
        $uniqueCode = Rule::unique('grupos', 'codigo')
            ->where('asignatura_id', $this->input('asignatura_id'))
            ->where('periodo_id', $this->input('periodo_id'));

        if ($grupo !== null) {
            $uniqueCode->ignore($grupo);
        }

        return [
            'codigo' => [
                'required',
                'string',
                'max:255',
                $uniqueCode,
            ],
            'asignatura_id' => ['required', 'integer', Rule::exists('asignaturas', 'id')->whereNull('deleted_at')],
            'periodo_id' => ['required', 'integer', Rule::exists('periodos', 'id')->whereNull('deleted_at')],
            'docente' => ['required', 'string', 'max:255'],
            'horario' => ['required', 'string', 'max:255'],
            'cupo' => ['required', 'integer', 'min:1'],
        ];
    }
}
