<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\AlamatRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Models\Alamat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Controller profil pengguna, buku alamat, dan perubahan kata sandi.
 */
class ProfilController extends Controller
{
    /**
     * Menampilkan halaman dashboard / profil pelanggan beserta daftar alamat tersimpan.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // Jika admin mengakses /dashboard, arahkan langsung ke dashboard panel admin.
        if ($request->routeIs('user.dashboard') && $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $addresses = $user->alamat()->orderByDesc('id')->get();

        return view('user.profil.index', [
            'profile' => $user,
            'addresses' => $addresses,
            'activeTab' => (string) $request->query('tab', session('active_tab', 'data-diri')),
        ]);
    }

    /**
     * Memperbarui data diri dan foto profil pengguna.
     */
    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Email sengaja tidak diubah mengikuti kebijakan tampilan profil (readonly).
        $user->nama = $validated['nama'];
        $user->no_hp = $validated['no_hp'];

        if ($request->hasFile('foto_profil')) {
            if (! empty($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $user->foto_profil = $this->storeAsWebp($request->file('foto_profil'), 'profil');
        }

        $user->save();

        return redirect()
            ->route('user.profil.index', ['tab' => 'data-diri'])
            ->with('success', 'Data diri berhasil diperbarui.');
    }

    /**
     * Memperbarui kata sandi pengguna yang sedang login.
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->validated('password'),
        ]);

        return redirect()
            ->route('user.profil.index', ['tab' => 'ubah-sandi'])
            ->with('success', 'Kata sandi berhasil diperbarui.');
    }

    /**
     * Menambahkan alamat pengiriman baru milik pengguna.
     */
    public function storeAlamat(AlamatRequest $request): RedirectResponse
    {
        $request->user()->alamat()->create($request->validated());

        return redirect()
            ->route('user.profil.index', ['tab' => 'buku-alamat'])
            ->with('success', 'Alamat baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui alamat tersimpan milik pengguna.
     */
    public function updateAlamat(AlamatRequest $request, Alamat $alamat): RedirectResponse
    {
        abort_unless($alamat->user_id === $request->user()->id, 403);

        $alamat->update($request->validated());

        return redirect()
            ->route('user.profil.index', ['tab' => 'buku-alamat'])
            ->with('success', 'Alamat berhasil diperbarui.');
    }

    /**
     * Menghapus alamat tersimpan milik pengguna.
     */
    public function destroyAlamat(Request $request, Alamat $alamat): RedirectResponse
    {
        abort_unless($alamat->user_id === $request->user()->id, 403);

        $alamat->delete();

        return redirect()
            ->route('user.profil.index', ['tab' => 'buku-alamat'])
            ->with('success', 'Alamat berhasil dihapus.');
    }

    /**
     * Mengonversi foto profil unggahan ke format WebP dan menyimpannya ke disk public.
     */
    private function storeAsWebp(UploadedFile $file, string $directory): string
    {
        $raw = $file->get();
        $image = is_string($raw) ? @imagecreatefromstring($raw) : false;

        if ($image !== false) {
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);

            ob_start();
            imagewebp($image, null, 82);
            $webpData = (string) ob_get_clean();

            $path = trim($directory, '/').'/'.Str::uuid().'.webp';
            Storage::disk('public')->put($path, $webpData);

            return $path;
        }

        return (string) $file->store($directory, 'public');
    }
}
