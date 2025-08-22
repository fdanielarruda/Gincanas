<?php

namespace App\Http\Requests\Phases;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PhaseReorderRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'direction' => ['required', 'string', Rule::in(['up', 'down'])],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'direction.required' => 'A direção da ordenação é obrigatória.',
            'direction.string' => 'A direção deve ser uma string.',
            'direction.in' => 'A direção deve ser "up" ou "down".',
        ];
    }
}
