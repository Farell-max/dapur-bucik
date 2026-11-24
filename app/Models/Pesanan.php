<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Model transaksi pesanan pelanggan Dapur Bucik.
 */
class Pesanan extends Model
{
    /**
     * Nama tabel pada basis data.
     *
     * @var string
     */
    protected $table = 'pesanan';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'meja_id',
        'metode_penerimaan',
        'metode_pembayaran',
        'status',
        'alasan_tolak',
        'total_harga',
        'estimasi_waktu_ambil',
    ];

    /**
     * Konversi tipe data atribut model.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_harga' => 'decimal:2',
            'estimasi_waktu_ambil' => 'datetime',
        ];
    }

    /**
     * Pelanggan yang membuat pesanan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Meja yang digunakan untuk pesanan dine-in (jika ada).
     */
    public function meja(): BelongsTo
    {
        return $this->belongsTo(Meja::class, 'meja_id');
    }

    /**
     * Rincian item menu pada pesanan ini.
     */
    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'pesanan_id');
    }

    /**
     * Data pembayaran untuk pesanan ini.
     */
    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class, 'pesanan_id');
    }
}
