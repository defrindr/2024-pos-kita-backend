<?php

namespace App\Http\Requests\Bsi;

use Illuminate\Foundation\Http\FormRequest;

class UMKMGroupUpdateRequest extends FormRequest
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
            'name' => 'required',
            'id_province' => 'required|numeric',
            'umkms' => 'nullable|numeric',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nama Kelompok',
            'id_province' => 'Provinsi',
            'umkms' => 'UMKM',
        ];
    }

    public function messages()
    {

        return [
            'name' => [
                'required' => ':attribute tidak boleh kosong.',
            ],
            'id_province' => [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute tidak terdaftar di sistem.',
            ],
        ];
    }
}
