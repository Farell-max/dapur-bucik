<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Validasi checkout pesanan pelanggan beserta aturan metode penerimaan & pembayaran.
 */
class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'metode_penerimaan' => ['required', Rule::in(['dine-in', 'pickup', 'kurir_mandiri'])],
            'meja_id' => ['required_if:metode_penerimaan,dine-in', 'nullable', 'integer', 'exists:meja,id'],
            'alamat_id' => [
                'required_if:metode_penerimaan,kurir_mandiri',
                'nullable',
                'integer',
                Rule::exists('alamat', 'id')->where('user_id', $this->user()?->id),
            ],
            'metode_pembayaran' => ['required', Rule::in(['transfer_bank', 'qris', 'bayar_di_tempat'])],
        ];
    }

    /**
     * Menolak metode pembayaran tunai apabila pelanggan memilih Kurir Mandiri.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->input('metode_penerimaan') === 'kurir_mandiri'
                && $this->input('metode_pembayaran') === 'bayar_di_tempat') {
                $validator->errors()->add(
                    'metode_pembayaran',
                    'Pembayaran tunai tidak tersedia untuk metode Kurir Mandiri.'
                );
            }
        });
    }
}
