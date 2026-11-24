<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi penambahan kategori menu baru oleh admin.
 */
class StoreKategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'nama_kategori' => ['required', 'string', 'max:100', 'unique:kategori,nama_kategori'],
        ];
    }
}
