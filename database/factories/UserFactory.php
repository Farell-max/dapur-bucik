<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Kata sandi bawaan yang digunakan oleh factory.
     */
    protected static ?string $password;

    /**
     * Definisi status bawaan model User.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'no_hp' => fake()->numerify('08##########'),
            'foto_profil' => null,
            'role' => 'pelanggan',
            'status_akun' => 'aktif',
        ];
    }
}
