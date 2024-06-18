<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWebProfileRequest extends FormRequest
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
            'file' => 'nullable|image|mimes:jpeg,png,jpg|max:10240|dimensions:max_width=6016,max_height=6016',
            'umkm_name' => 'required',
            'address' => 'required',
            'id_city' => 'required|integer|exists:user_city,id',
            'kecamatan' => 'required',
            'kelurahan' => 'nullable',
            'kode_pos' => 'required|numeric|digits:5',
            'phone_number' => 'required|numeric|min_digits:10',
            'instagram' => 'nullable',
            'whatsapp' => 'nullable|numeric|min_digits:10',
            'facebook' => 'nullable',
            'umkm_description' => 'required|string|min:50',
        ];
    }

    public function attributes()
    {
        return [
            'file' => 'Logo',
            'umkm_name' => 'Nama Toko',
            'address' => 'Alamat Toko',
            'id_city' => 'Kota',
            'kecamatan' => 'Kecamatan',
            'kelurahan' => 'Kelurahan',
            'kode_pos' => 'Kode Pos',
            'phone_number' => 'Nomor Telepon',
            'instagram' => 'Instagram',
            'whatsapp' => 'Nomor WhatsApp',
            'facebook' => 'Facebook',
            'umkm_description' => 'Deskripsi Toko',
        ];
    }

    public function messages()
    {

        return [
            'file' => [
                'image' => ':attribute harus berupa gambar.',
                'mimes' => 'File :attribute harus berupa: :values.',
                'max' => ' :attribute tidak boleh lebih dari :max Kb.',
                'dimensions' => 'Resolusi :attribute tidak boleh melebihi :max_width x :max_height.',
            ],
            'umkm_name' => [
                'required' => ':attribute tidak boleh kosong.',
            ],
            'address' => [
                'required' => ':attribute tidak boleh kosong.',
            ],
            'id_city' => [
                'required' => ':attribute tidak boleh kosong.',
                'integer' => ':attribute tidak ditemukan di sistem.',
                'exists' => ':attribute belum terdaftar di sistem.'
            ],
            'kecamatan' => [
                'required' => ':attribute tidak boleh kosong.',
            ],
            'kode_pos' => [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute harus berupa angka dan bukan decimal.',
                'digits' => ':attribute harus :digits digit.'
            ],
            'phone_number' => [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute harus berupa angka dan bukan decimal.',
                'min_digits' => ':attribute harus memiliki setidaknya :min digit.'
            ],
            'whatsapp' => [
                'numeric' => ':attribute harus berupa angka dan bukan decimal',
                'min_digits' => ':attribute harus memiliki setidaknya :min digit.'
            ],
            'umkm_description' => [
                'required' => ':attribute tidak boleh kosong.',
                'string' => ':attribute harus berupa huruf.',
                'min' => ':attribute butuh setidaknya :min huruf.'
            ]
        ];
    }
}
