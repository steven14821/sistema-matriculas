<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveAsignaturaRequest extends FormRequest
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
        $asignatura = $this->route('asignatura');
        $uniqueCode = Rule::unique('asignaturas', 'codigo');

        if ($asignatura !== null) {
            $uniqueCode->ignore($asignatura);
        }

        return [
            'programa_id' => ['required', 'integer', Rule::exists('programas', 'id')->whereNull('deleted_at')],
            'codigo' => ['required', 'string', 'max:255', $uniqueCode],
            'nombre' => ['required', 'string', 'max:255'],
            'creditos' => ['required', 'integer', 'min:1', 'max:255'],
        ];
    }
}
