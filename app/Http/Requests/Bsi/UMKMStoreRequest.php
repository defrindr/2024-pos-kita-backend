<?php

namespace App\Http\Requests\Bsi;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class UMKMStoreRequest extends FormRequest
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
            'email' => 'required|email|unique:users,email',
            'umkm_name' => 'required',
            'id_province' => 'required|numeric|exists:user_province,id',
            'id_city' => 'required|numeric|exists:user_city,id',
            'kecamatan' => 'required',
            'kelurahan' => 'nullable',
            'instagram' => 'nullable',
            'whatsapp' => 'nullable|numeric|min_digits:10',
            'facebook' => 'nullable',
            'address' => 'required',
            'kode_pos' => 'required|numeric',
            'owner_name' => 'required',
            'nik' => 'required|numeric|min_digits:16',
            'phone_number' => 'required|numeric|min_digits:10',
            'password' => [
                'required',
                Password::min(6)
                    ->numbers(),
            ],
            'umkm_image' => 'required|image|mimes:jpeg,png,jpg|max:10240|dimensions:max_width=6016,max_height=6016',
            'umkm_email' => 'required|email'
        ];
    }

    public function attributes()
    {
        return [
            'email' => 'Email',
            'umkm_name' => 'Nama Toko',
            'id_province' => 'Provinsi',
            'id_city' => 'Kota',
            'owner_name' => 'Nama Pemilik',
            'kecamatan' => 'Kecamatan',
            'kelurahan' => 'Kelurahan',
            'instagram' => 'Instagram',
            'whatsapp' => 'Nomor WhatsApp',
            'facebook' => 'Facebook',
            'address' => 'Alamat Toko',
            'kode_pos' => 'Kode Pos',
            'nik' => 'NIK',
            'phone_number' => 'Nomor Telepon',
            'password' => 'Password',
            'umkm_image' => 'Gambar Toko',
            'umkm_email' => 'Email Toko',
        ];
    }

    public function messages()
    {

        return [
            'email' => [
                'required' => ':attribute tidak boleh kosong.',
                'email' => 'Format :attribute harus benar.',
                'unique' => ':attribute telah terdaftar di sistem.'
            ],
            'umkm_name' => [
                'required' => ':attribute tidak boleh kosong.',
            ],
            'id_province' => [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute tidak ditemukan di sistem.',
                'exists' => ':attribute belum terdaftar di sistem.'
            ],
            'id_city' => [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute tidak ditemukan di sistem.',
                'exists' => ':attribute belum terdaftar di sistem.'
            ],
            'owner_name' => [
                'required' => ':attribute tidak boleh kosong.',
            ],
            'kecamatan' => [
                'required' => ':attribute tidak boleh kosong.',
            ],
            'whatsapp' => [
                'numeric' => ':attribute harus berupa angka dan bukan decimal',
                'min_digits' => ':attribute harus memiliki setidaknya :min digit.'
            ],
            'address' => [
                'required' => ':attribute tidak boleh kosong.',
            ],
            'kode_pos' => [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute harus berupa angka dan bukan decimal.',
                'digits' => ':attribute harus :digits digit.'
            ],
            'nik' => [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute harus berupa angka dan bukan decimal',
                'min_digits' => ':attribute harus memiliki setidaknya :min digit.'
            ],
            'phone_number' => [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute harus berupa angka dan bukan decimal.',
                'min_digits' => ':attribute harus memiliki setidaknya :min digit.'
            ],
            'password' => [
                'required' => ':attribute tidak boleh kosong.',
            ],
            'umkm_image' => [
                'required' => ':attribute tidak boleh kosong.',
                'image' => ':attribute harus berupa gambar.',
                'mimes' => 'File :attribute harus berupa: :values.',
                'max' => ' :attribute tidak boleh lebih dari :max Kb.',
                'dimensions' => 'Resolusi :attribute tidak boleh melebihi :max_width x :max_height.',
            ],
            'umkm_email' => [
                'required' => ':attribute tidak boleh kosong.',
                'email' => 'Format :attribute harus benar.',
            ],
        ];
    }
}
