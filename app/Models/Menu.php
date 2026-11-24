<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model menu makanan dan minuman Dapur Bucik.
 */
class Menu extends Model
{
    /**
     * Nama tabel pada basis data.
     *
     * @var string
     */
    protected $table = 'menu';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'kategori_id',
        'nama',
        'deskripsi',
        'harga',
        'harga_promo',
        'foto',
        'status_ketersediaan',
        'rating',
    ];

    /**
     * Konversi tipe data atribut model.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'harga_promo' => 'decimal:2',
            'rating' => 'float',
        ];
    }

    /**
     * Kategori dari menu ini.
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    /**
     * Item keranjang yang merujuk pada menu ini.
     */
    public function itemKeranjang(): HasMany
    {
        return $this->hasMany(ItemKeranjang::class, 'menu_id');
    }

    /**
     * Detail pesanan yang merujuk pada menu ini.
     */
    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'menu_id');
    }

    /**
     * Mengembalikan harga aktif menu (memprioritaskan harga promo jika tersedia).
     */
    public function effectivePrice(): float
    {
        return (float) ($this->harga_promo ?? $this->harga);
    }

    /**
     * Mengembalikan URL gambar menu beserta fallback ilustrasi lokal jika foto kosong.
     */
    public function fotoUrl(): string
    {
        if (! empty($this->foto)) {
            if (str_starts_with($this->foto, 'http://') || str_starts_with($this->foto, 'https://')) {
                return $this->foto;
            }

            if (file_exists(public_path($this->foto))) {
                return asset($this->foto);
            }

            return asset('storage/'.ltrim($this->foto, '/'));
        }

        $fallbackMap = [
            'Ayam Goreng Lengkuas' => 'images/menu/ayam-goreng-lengkuas.webp',
            'Sayur Asem' => 'images/menu/sayur-asem.webp',
            'Nasi Goreng Kampung' => 'images/menu/nasi-goreng-kampung.webp',
            'Nasi Kuning Bumbu Rempah' => 'images/menu/nasi-kuning.webp',
            'Pisang Goreng Madu' => 'images/menu/pisang-goreng-madu.webp',
        ];

        if (isset($fallbackMap[$this->nama])) {
            return asset($fallbackMap[$this->nama]);
        }

        $categoryFallbackMap = [
            'Cemilan Tradisional' => 'images/menu/pisang-goreng-madu.webp',
            'Minuman Segar' => 'images/menu/minuman-segar.webp',
            'Menu Anak' => 'images/menu/menu-anak.webp',
        ];

        $kategoriNama = $this->relationLoaded('kategori') ? $this->kategori?->nama_kategori : null;
        $path = ($kategoriNama && isset($categoryFallbackMap[$kategoriNama]))
            ? $categoryFallbackMap[$kategoriNama]
            : 'images/menu/ayam-goreng-lengkuas.webp';

        return asset($path);
    }
}
