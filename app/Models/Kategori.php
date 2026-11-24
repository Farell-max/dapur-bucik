<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model kategori menu kuliner Dapur Bucik.
 */
class Kategori extends Model
{
    /**
     * Nama tabel pada basis data.
     *
     * @var string
     */
    protected $table = 'kategori';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama_kategori',
    ];

    /**
     * Daftar menu dalam kategori ini.
     */
    public function menu(): HasMany
    {
        return $this->hasMany(Menu::class, 'kategori_id');
    }
}
