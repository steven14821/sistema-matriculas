<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSolicitudProgramaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('self-enroll') ?? false;
    }

    public function rules(): array
    {
        return [
            'programa_id' => [
                'required',
                'integer',
                Rule::exists('programas', 'id')->whereNull('deleted_at'),
            ],
            'comprobante' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'extensions:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ];
    }
}
