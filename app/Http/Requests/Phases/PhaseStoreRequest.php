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

            'criteria' => ['nullable', Rule::requiredIf($this->type == Phase::TYPE_CRITERIA), 'array'],
            'criteria.*' => ['nullable', 'string', 'max:255'],

            'colocations' => ['nullable', Rule::requiredIf($this->type != Phase::TYPE_CRITERIA), 'array'],
            'colocations.*.place' => [Rule::requiredIf($this->type != Phase::TYPE_CRITERIA), 'string'],
            'colocations.*.points' => [Rule::requiredIf($this->type != Phase::TYPE_CRITERIA), 'integer'],

            'checklist_colocations' => ['nullable', Rule::requiredIf($this->type == Phase::TYPE_CHECKLIST), 'array'],
            'checklist_colocations.*.place' => [Rule::requiredIf($this->type != Phase::TYPE_CHECKLIST), 'string'],
            'checklist_colocations.*.points' => [Rule::requiredIf($this->type != Phase::TYPE_CHECKLIST), 'integer'],
        ];
    }
}
