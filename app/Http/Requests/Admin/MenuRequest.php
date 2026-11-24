<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi pembuatan atau pembaruan data menu oleh admin.
 */
class MenuRequest extends FormRequest
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
            'kategori_id' => ['required', 'integer', 'exists:kategori,id'],
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'max:2000'],
            'harga' => ['required', 'numeric', 'min:0'],
            'harga_promo' => ['nullable', 'numeric', 'min:0', 'lt:harga'],
            'status_ketersediaan' => ['required', Rule::in(['tersedia', 'habis'])],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
