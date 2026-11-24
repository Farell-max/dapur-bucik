<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model pengguna aplikasi Dapur Bucik (pelanggan dan admin).
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Nama tabel pada basis data.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Menonaktifkan remember_token karena kolom tidak digunakan pada skema ERD.
     *
     * @var string
     */
    protected $rememberTokenName = '';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'no_hp',
        'foto_profil',
        'role',
        'status_akun',
    ];

    /**
     * Atribut yang disembunyikan saat serialisasi.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Konversi tipe data atribut model.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Daftar alamat pengiriman milik pengguna.
     */
    public function alamat(): HasMany
    {
        return $this->hasMany(Alamat::class, 'user_id');
    }

    /**
     * Keranjang belanja aktif milik pengguna.
     */
    public function keranjang(): HasOne
    {
        return $this->hasOne(Keranjang::class, 'user_id');
    }

    /**
     * Riwayat pesanan milik pengguna.
     */
    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'user_id');
    }

    /**
     * Mengembalikan inisial nama pengguna untuk avatar fallback.
     */
    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->nama)) ?: [];
        $letters = array_map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)), array_slice($parts, 0, 2));

        return implode('', $letters) ?: 'DB';
    }
}
