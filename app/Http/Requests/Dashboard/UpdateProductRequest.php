<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name'                => 'required',
            'id_category_product' => 'required|integer',
            'status'              => 'required|integer|between:0,1',
            'price'               => 'required|integer|min:0',
            'file'                => 'nullable|image|mimes:jpeg,png,jpg|max:10240|dimensions:max_width=6016,max_height=6016', //max 10mb
            'file2'               => 'nullable|image|mimes:jpeg,png,jpg|max:10240|dimensions:max_width=6016,max_height=6016', //max 10mb
            'file3'               => 'nullable|image|mimes:jpeg,png,jpg|max:10240|dimensions:max_width=6016,max_height=6016', //max 10mb
            'file4'               => 'nullable|image|mimes:jpeg,png,jpg|max:10240|dimensions:max_width=6016,max_height=6016', //max 10mb
            'file5'               => 'nullable|image|mimes:jpeg,png,jpg|max:10240|dimensions:max_width=6016,max_height=6016', //max 10mb
            'description'         => 'required|string|min:100',
            'stock'               => 'required|integer|min:0',
            'weight'              => 'nullable|numeric|min:0',
            'length'              => 'nullable|numeric|min:0',
            'width'               => 'nullable|numeric|min:0',
            'height'              => 'nullable|numeric|min:0',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nama Produk',
            'id_category_product' => 'Kategori Produk',
            'status' => 'Status Produk',
            'price' => 'Harga Satuan',
            'file' => 'Foto Produk Utama',
            'file2' => 'Foto Produk Kedua',
            'file3' => 'Foto Produk Ketiga',
            'file4' => 'Foto Produk Keempat',
            'file5' => 'Foto Produk kelima',
            'description' => 'Deskripsi Produk',
            'stock' => 'Jumlah Stok',
            'weight' => 'Ukuran Berat',
            'length' => 'Ukuran Panjang',
            'width' => 'Ukuran Lebar',
            'height' => 'Ukuran Tinggi',
        ];
    }

    public function messages()
    {
        return [
            'name' => [
                'required' => ':attribute tidak boleh kosong.',
            ],
            'id_category_product' => [
                'required' => ':attribute tidak boleh kosong.',
                'integer' => ':attribute tidak ditemukan di sistem.',
            ],
            'status' => [
                'required' => ':attribute tidak boleh kosong.',
                'integer' => ':attribute tidak ditemukan di sistem.',
                'between' => ':attribute harus berupa Aktif atau Tidak Aktif.'
            ],
            'price' => [
                'required' => ':attribute tidak boleh kosong.',
                'integer' => ':attribute harus berupa angka dan bukan decimal.',
                'min' => 'Minimal :attribute adalah Rp. :min.'
            ],
            'file' => [
                'image' => ':attribute harus berupa gambar.',
                'mimes' => 'File :attribute harus berupa: :values.',
                'max' => ' :attribute tidak boleh lebih dari :max Kb.',
                'dimensions' => 'Resolusi :attribute tidak boleh melebihi :max_width x :max_height.',
            ],
            'file2' => [
                'image' => ':attribute harus berupa gambar.',
                'mimes' => 'File :attribute harus berupa: :values.',
                'max' => ' :attribute tidak boleh lebih dari :max Kb.',
                'dimensions' => 'Resolusi :attribute tidak boleh melebihi :max_width x :max_height.',
            ],
            'file3' => [
                'image' => ':attribute harus berupa gambar.',
                'mimes' => 'File :attribute harus berupa: :values.',
                'max' => ' :attribute tidak boleh lebih dari :max Kb.',
                'dimensions' => 'Resolusi :attribute tidak boleh melebihi :max_width x :max_height.',
            ],
            'file4' => [
                'image' => ':attribute harus berupa gambar.',
                'mimes' => 'File :attribute harus berupa: :values.',
                'max' => ' :attribute tidak boleh lebih dari :max Kb.',
                'dimensions' => 'Resolusi :attribute tidak boleh melebihi :max_width x :max_height.',
            ],
            'file5' => [
                'image' => ':attribute harus berupa gambar.',
                'mimes' => 'File :attribute harus berupa: :values.',
                'max' => ' :attribute tidak boleh lebih dari :max Kb.',
                'dimensions' => 'Resolusi :attribute tidak boleh melebihi :max_width x :max_height.',
            ],
            'description' => [
                'required' => ':attribute tidak boleh kosong.',
                'string' => ':attribute harus berupa huruf.',
                'min' => ':attribute butuh setidaknya :min huruf.'
            ],
            'stock' => [
                'required' => ':attribute tidak boleh kosong.',
                'integer' => ':attribute harus berupa angka dan bukan decimal.',
                'min' => 'Minimal :attribute adalah :min.'
            ],
            'weight' => [
                'numeric' => ':attribute harus berupa angka dan bukan decimal.',
                'min' => 'Minimal :attribute adalah :min.'
            ],
            'length' => [
                'numeric' => ':attribute harus berupa angka dan bukan decimal.',
                'min' => 'Minimal :attribute adalah :min.'
            ],
            'width' => [
                'numeric' => ':attribute harus berupa angka dan bukan decimal.',
                'min' => 'Minimal :attribute adalah :min.'
            ],
            'height' => [
                'numeric' => ':attribute harus berupa angka dan bukan decimal.',
                'min' => 'Minimal :attribute adalah :min.'
            ]
        ];
    }
}
