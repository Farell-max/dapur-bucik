<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model alamat tersimpan milik pengguna.
 */
class Alamat extends Model
{
    /**
     * Nama tabel pada basis data.
     *
     * @var string
     */
    protected $table = 'alamat';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'label',
        'detail_alamat',
    ];

    /**
     * Pengguna pemilik alamat.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
