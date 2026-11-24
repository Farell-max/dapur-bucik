<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Menu;
use Illuminate\View\View;

/**
 * Controller beranda utama pelanggan yang terhubung ke database MySQL.
 */
class HomeController extends Controller
{
    /**
     * Menampilkan beranda dengan promo aktif, kategori, dan menu terlaris dari database.
     */
    public function index(): View
    {
        $categories = Kategori::orderBy('id')->get();
        $menus = Menu::with('kategori')
            ->withSum('detailPesanan as total_terjual', 'qty')
            ->orderByDesc('total_terjual')
            ->orderByDesc('rating')
            ->orderBy('id')
            ->get();

        $promoMenu = $menus->first(fn (Menu $menu): bool => $menu->harga_promo !== null) ?? $menus->first();

        return view('user.home', [
            'categories' => $categories,
            'menus' => $menus,
            'promoMenu' => $promoMenu,
        ]);
    }
}
