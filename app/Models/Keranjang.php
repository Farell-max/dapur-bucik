<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model keranjang belanja milik pengguna.
 */
class Keranjang extends Model
{
    /**
     * Nama tabel pada basis data.
     *
     * @var string
     */
    protected $table = 'keranjang';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
    ];

    /**
     * Pengguna pemilik keranjang.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Daftar item di dalam keranjang.
     */
    public function itemKeranjang(): HasMany
    {
        return $this->hasMany(ItemKeranjang::class, 'keranjang_id');
    }
}
