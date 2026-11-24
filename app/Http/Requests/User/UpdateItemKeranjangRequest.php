<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi pembaruan jumlah atau catatan item pada keranjang belanja.
 */
class UpdateItemKeranjangRequest extends FormRequest
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
            'qty' => ['required', 'integer', 'min:1', 'max:99'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }
}
