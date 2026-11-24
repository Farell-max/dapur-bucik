<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Meja;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller katalog dan detail menu pelanggan berbasis Eloquent.
 */
class MenuController extends Controller
{
    /**
     * Menampilkan katalog menu dengan pencarian, filter kategori, pengurutan, dan scan QR meja.
     */
    public function index(Request $request): View
    {
        // Simpan meja pilihan dari QR (?meja=1 atau kode QR) ke session browser untuk checkout
        if ($request->filled('meja')) {
            $mejaParam = trim((string) $request->query('meja'));
            $meja = Meja::where('id', $mejaParam)
                ->orWhere('qr_code', $mejaParam)
                ->first();

            if ($meja) {
                $request->session()->put('selected_meja_id', $meja->id);
            }
        }

        $selectedMeja = $request->session()->has('selected_meja_id')
            ? Meja::find($request->session()->get('selected_meja_id'))
            : null;

        $search = trim((string) $request->query('q', ''));
        $kategoriId = $request->query('kategori_id');
        $urut = (string) $request->query('urut', 'terlaris');

        $query = Menu::with('kategori')
            ->withSum('detailPesanan as total_terjual', 'qty');

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if (! empty($kategoriId)) {
            $query->where('kategori_id', $kategoriId);
        }

        match ($urut) {
            'harga_asc' => $query->orderByRaw('COALESCE(harga_promo, harga) ASC'),
            'harga_desc' => $query->orderByRaw('COALESCE(harga_promo, harga) DESC'),
            'rating_desc' => $query->orderByDesc('rating'),
            default => $query->orderByDesc('total_terjual')->orderByDesc('rating')->orderBy('id'),
        };

        return view('user.menu.index', [
            'categories' => Kategori::orderBy('id')->get(),
            'menus' => $query->get(),
            'selectedMeja' => $selectedMeja,
            'filters' => [
                'q' => $search,
                'kategori_id' => $kategoriId,
                'urut' => $urut,
            ],
        ]);
    }

    /**
     * Menampilkan halaman detail satu menu beserta menu terkait.
     */
    public function show(string $id): View
    {
        $menu = Menu::with('kategori')
            ->withSum('detailPesanan as total_terjual', 'qty')
            ->findOrFail($id);

        $relatedMenus = Menu::with('kategori')
            ->withSum('detailPesanan as total_terjual', 'qty')
            ->where('id', '!=', $menu->id)
            ->orderByDesc('rating')
            ->take(3)
            ->get();

        return view('user.menu.show', [
            'menu' => $menu,
            'relatedMenus' => $relatedMenus,
        ]);
    }
}
