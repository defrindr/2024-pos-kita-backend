<?php

namespace App\Http\Requests\Bsi;

use Illuminate\Foundation\Http\FormRequest;

class HighlightStoreRequest extends FormRequest
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
            'title' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:10240|dimensions:width=1440,height=521',
            'id_product' => 'required|numeric|exists:products,id',
        ];
    }

    public function attributes()
    {
        return [
            'umkm_name' => 'Judul',
            'image' => 'Gambar',
            'id_product' => 'Produk',
        ];
    }

    public function messages()
    {
        return [
            'umkm_name' => [
                'required' => ':attribute tidak boleh kosong.',
            ],
            'image' => [
                'required' => ':attribute tidak boleh kosong.',
                'image' => ':attribute harus berupa gambar.',
                'mimes' => 'File :attribute harus berupa: :values.',
                'max' => ' :attribute tidak boleh lebih dari :max Kb.',
                'dimensions' => 'Resolusi :attribute harus :width x :height.',
            ],
            'id_product' => [
                'required' => ':attribute tidak boleh kosong.',
                'exists' => ':attribute belum terdaftar di sistem.',
                'numeric' => ':attribute tidak ditemukan di sistem.',
            ],
        ];
    }
}
