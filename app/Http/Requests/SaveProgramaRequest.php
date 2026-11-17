<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveProgramaRequest extends FormRequest
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
        $programa = $this->route('programa');
        $uniqueName = Rule::unique('programas', 'nombre');

        if ($programa !== null) {
            $uniqueName->ignore($programa);
        }

        return [
            'nombre' => ['required', 'string', 'max:255', $uniqueName],
            'nivel' => ['required', 'string', 'max:255'],
        ];
    }
}
