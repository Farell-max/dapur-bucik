<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi penambahan item menu ke keranjang belanja.
 */
class StoreItemKeranjangRequest extends FormRequest
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
            'menu_id' => ['required', 'integer', 'exists:menu,id'],
            'qty' => ['required', 'integer', 'min:1', 'max:99'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }
}
