<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model item menu yang berada di dalam keranjang belanja.
 */
class ItemKeranjang extends Model
{
    /**
     * Nama tabel pada basis data.
     *
     * @var string
     */
    protected $table = 'item_keranjang';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'keranjang_id',
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
     * Keranjang induk dari item ini.
     */
    public function keranjang(): BelongsTo
    {
        return $this->belongsTo(Keranjang::class, 'keranjang_id');
    }

    /**
     * Menu yang dipilih pada item keranjang.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}
