<?php

namespace App\Http\Requests\Phases;

use App\Models\Phase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PhaseStoreRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'integer', Rule::in(Phase::TYPES)],
            'criteria' => ['nullable', 'array'],
            'criteria.*' => ['nullable', 'string', 'max:255'],
            'colocations' => ['nullable', 'array'],
            'colocations.*.place' => ['required_with:colocations', 'string'],
            'colocations.*.points' => ['required_with:colocations', 'integer'],
        ];
    }
}
