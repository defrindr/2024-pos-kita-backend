<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIncomeRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id_income_type' => [
                'required',
                'exists:m_income_type,id'
            ],
            'notes' => [
                'required',
                'max:255'
            ],
            'nominal' => [
                'required',
                'integer',
                'min:0'
            ],
            'date' => [
                'required',
                'date_format:Y-m-d'
            ]
        ];
    }
}
