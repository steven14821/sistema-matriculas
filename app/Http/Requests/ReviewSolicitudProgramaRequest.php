<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewSolicitudProgramaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-academic') ?? false;
    }

    public function rules(): array
    {
        return [
            'estado' => ['required', Rule::in(['aprobada', 'rechazada'])],
            'observacion' => ['required_if:estado,rechazada', 'nullable', 'string', 'max:1000'],
        ];
    }
}
