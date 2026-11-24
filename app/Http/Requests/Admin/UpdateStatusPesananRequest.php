<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi pembaruan status pesanan oleh admin beserta alasan penolakan.
 */
class UpdateStatusPesananRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::in([
                    'menunggu_konfirmasi',
                    'dimasak',
                    'siap_diambil',
                    'menunggu_driver',
                    'selesai',
                    'ditolak',
                ]),
            ],
            'alasan_tolak' => ['required_if:status,ditolak', 'nullable', 'string', 'max:255'],
        ];
    }
}
