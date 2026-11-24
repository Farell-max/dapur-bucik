<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuRequest;
use App\Http\Requests\Admin\StoreKategoriRequest;
use App\Models\Kategori;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Controller CRUD menu dapur, kategori, harga promo, dan toggle ketersediaan.
 */
class MenuController extends Controller
{
    /**
     * Menampilkan daftar menu dan kategori dari database.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $kategoriId = $request->query('kategori_id');

        $query = Menu::with('kategori')->orderByDesc('id');

        if ($search !== '') {
            $query->where('nama', 'like', "%{$search}%");
        }

        if (! empty($kategoriId)) {
            $query->where('kategori_id', $kategoriId);
        }

        return view('admin.menu.index', [
            'categories' => Kategori::orderBy('id')->get(),
            'menus' => $query->get(),
            'filters' => [
                'q' => $search,
                'kategori_id' => $kategoriId,
            ],
        ]);
    }

    /**
     * Menyimpan menu baru beserta unggahan foto ke storage/app/public/menu.
     */
    public function store(MenuRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('foto')) {
            $validated['foto'] = $this->storeAsWebp($request->file('foto'), 'menu');
        }

        $validated['harga_promo'] = $validated['harga_promo'] ?? null;
        $validated['rating'] = 0;

        Menu::create($validated);

        return redirect()
            ->route('admin.menu.index')
            ->with('success', 'Menu baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data menu yang dipilih.
     */
    public function update(MenuRequest $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('foto')) {
            $this->deleteStoredPhoto($menu->foto);
            $validated['foto'] = $this->storeAsWebp($request->file('foto'), 'menu');
        }

        $validated['harga_promo'] = $validated['harga_promo'] ?? null;

        $menu->update($validated);

        return redirect()
            ->route('admin.menu.index')
            ->with('success', "Menu {$menu->nama} berhasil diperbarui.");
    }

    /**
     * Mengubah cepat status ketersediaan menu antara tersedia dan habis.
     */
    public function toggleStatus(Menu $menu): RedirectResponse
    {
        $nextStatus = $menu->status_ketersediaan === 'tersedia' ? 'habis' : 'tersedia';
        $menu->update(['status_ketersediaan' => $nextStatus]);

        return redirect()
            ->route('admin.menu.index')
            ->with('success', "Status {$menu->nama} diubah menjadi ".strtoupper($nextStatus).'.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        if ($menu->detailPesanan()->exists()) {
            return redirect()
                ->route('admin.menu.index')
                ->with('error', "Menu {$menu->nama} tidak dapat dihapus karena sudah tercatat pada riwayat pesanan. Ubah statusnya menjadi HABIS saja.");
        }

        $nama = $menu->nama;
        $this->deleteStoredPhoto($menu->foto);
        $menu->delete();

        return redirect()
            ->route('admin.menu.index')
            ->with('success', "Menu {$nama} berhasil dihapus.");
    }

    /**
     * Menambahkan kategori baru untuk pengelompokan menu.
     */
    public function storeKategori(StoreKategoriRequest $request): RedirectResponse
    {
        $kategori = Kategori::create($request->validated());

        return redirect()
            ->route('admin.menu.index')
            ->with('success', "Kategori {$kategori->nama_kategori} berhasil ditambahkan.");
    }

    /**
     * Mengonversi gambar unggahan ke format WebP dan menyimpannya ke disk public.
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

    /**
     * Menghapus foto yang tersimpan pada disk public (bukan aset bawaan public/images).
     */
    private function deleteStoredPhoto(?string $path): void
    {
        if (! empty($path) && ! str_starts_with($path, 'images/') && ! str_starts_with($path, 'http')) {
            Storage::disk('public')->delete($path);
        }
    }
}
