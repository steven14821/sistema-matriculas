<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SavePeriodoRequest extends FormRequest
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
        $periodo = $this->route('periodo');
        $uniqueName = Rule::unique('periodos', 'nombre');

        if ($periodo !== null) {
            $uniqueName->ignore($periodo);
        }

        return [
            'nombre' => ['required', 'string', 'max:255', $uniqueName],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'abierto' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'abierto' => $this->boolean('abierto'),
        ]);
    }
}
