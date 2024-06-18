<?php

namespace App\Http\Requests\Bsi;

use Illuminate\Foundation\Http\FormRequest;

class UMKMEvaluationStoreRequest extends FormRequest
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
            'id_user' => 'required|numeric',
            'gross_expenses' => 'required|numeric',
            'gross_incomes' => 'required|numeric',
            'net_income' => 'required|numeric',
            'market_uptake' => 'required|numeric',
            'equipment_total' => 'required|numeric',
            'is_sustain' => 'required|numeric|between:0,1',
            'is_mustahiq' => 'required|numeric|between:0,1',
            'is_licensed' => 'required|numeric|between:0,1',
            'is_proper' => 'required|numeric|between:0,1',
            'loan_amount' => 'required|numeric',
            'infrastructures' => 'nullable'
        ];
    }

    public function attributes()
    {
        return [
            'id_user' => 'UMKM',
            'gross_expenses' => 'Pengeluaran Kotor',
            'gross_incomes' => 'Jumlah Pendapatan Kotor',
            'net_income' => 'Pendapatan Bersih',
            'market_uptake' => 'Serapan Pasar',
            'equipment_total' => 'Jumlah Infrastruktur',
            'is_sustain' => 'Sustain',
            'is_mustahiq' => 'Mustahiq',
            'is_licensed' => 'Berizin',
            'is_proper' => 'Kelayakan',
            'loan_amount' => 'Jumlah Pinjaman',
            'infrastructures' => 'Infrastruktur',
        ];
    }

    public function messages()
    {

        return [
            'id_user' => [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute tidak terdaftar di sistem.',
            ],
            'gross_expenses' => [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute harus berupa angka dan bukan decimal.',
            ],
            'gross_incomes' => [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute harus berupa angka dan bukan decimal.',
            ],
            'net_income' => [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute harus berupa angka dan bukan decimal.',
            ],
            'market_uptake' => [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute harus berupa angka dan bukan decimal.',
            ],
            'equipment_total' => [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute harus berupa angka dan bukan decimal.',
            ],
            'is_sustain' => [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute tidak ditemukan di sistem.',
                'between' => ':attribute harus berupa Ya atau Tidak.'
            ],
            'is_mustahiq' => [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute tidak ditemukan di sistem.',
                'between' => ':attribute harus berupa Ya atau Tidak.'
            ],
            'is_licensed' => [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute tidak ditemukan di sistem.',
                'between' => ':attribute harus berupa Ya atau Tidak.'
            ],
            'is_proper' => [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute tidak ditemukan di sistem.',
                'between' => ':attribute harus berupa Ya atau Tidak.'
            ],
            'loan_amount' => [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute harus berupa angka dan bukan decimal.',
            ],
        ];
    }
}
