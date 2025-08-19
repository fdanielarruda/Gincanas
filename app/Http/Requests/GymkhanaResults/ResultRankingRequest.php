<?php

namespace App\Http\Requests\GymkhanaResults;

use Illuminate\Foundation\Http\FormRequest;

class ResultRankingRequest extends FormRequest
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
            'result_id' => ['required', 'exists:gymkhana_results,id']
        ];
    }
}
