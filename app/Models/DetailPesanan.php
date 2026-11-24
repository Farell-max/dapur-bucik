<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model rincian item menu pada sebuah pesanan.
 */
class DetailPesanan extends Model
{
    /**
     * Nama tabel pada basis data.
     *
     * @var string
     */
    protected $table = 'detail_pesanan';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'pesanan_id',
        'menu_id',
        'qty',
        'catatan',
        'subtotal',
    ];

    /**
     * Konversi tipe data atribut model.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'subtotal' => 'decimal:2',
        ];
    }

    /**
     * Pesanan induk dari detail ini.
     */
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    /**
     * Menu yang dipesan pada baris detail ini.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}
