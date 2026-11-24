<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model meja makan di tempat (dine-in) Dapur Bucik.
 */
class Meja extends Model
{
    /**
     * Nama tabel pada basis data.
     *
     * @var string
     */
    protected $table = 'meja';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nomor_meja',
        'qr_code',
    ];

    /**
     * Daftar pesanan yang menggunakan meja ini.
     */
    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'meja_id');
    }
}
