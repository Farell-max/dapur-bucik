<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi tambah atau ubah alamat tersimpan milik pelanggan.
 */
class AlamatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:100'],
            'detail_alamat' => ['required', 'string', 'max:1000'],
        ];
    }
}
