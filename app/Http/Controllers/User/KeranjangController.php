<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreItemKeranjangRequest;
use App\Http\Requests\User\UpdateItemKeranjangRequest;
use App\Models\ItemKeranjang;
use App\Models\Keranjang;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller keranjang belanja pelanggan berbasis Eloquent.
 */
class KeranjangController extends Controller
{
    /**
     * Menampilkan daftar item keranjang dan ringkasan nota belanja.
     */
    public function index(Request $request): View
    {
        $keranjang = Keranjang::with(['itemKeranjang.menu.kategori'])
            ->firstOrCreate(['user_id' => $request->user()->id]);

        $items = $keranjang->itemKeranjang;
        $totalHarga = (float) $items->sum('subtotal');
        $totalQty = (int) $items->sum('qty');

        return view('user.keranjang.index', [
            'keranjang' => $keranjang,
            'items' => $items,
            'totalHarga' => $totalHarga,
            'totalQty' => $totalQty,
        ]);
    }

    /**
     * Menambahkan item menu beserta qty dan catatan ke keranjang pengguna.
     */
    public function store(StoreItemKeranjangRequest $request): RedirectResponse
    {
        $menu = Menu::findOrFail($request->integer('menu_id'));

        if ($menu->status_ketersediaan !== 'tersedia') {
            return back()->with('error', "Maaf, stok {$menu->nama} sedang habis.");
        }

        $keranjang = Keranjang::firstOrCreate(['user_id' => $request->user()->id]);
        $qty = $request->integer('qty');
        $catatan = trim((string) $request->input('catatan', ''));
        $catatanValue = $catatan !== '' ? $catatan : null;

        $existingQuery = $keranjang->itemKeranjang()->where('menu_id', $menu->id);
        if ($catatanValue === null) {
            $existingQuery->whereNull('catatan');
        } else {
            $existingQuery->where('catatan', $catatanValue);
        }

        $existingItem = $existingQuery->first();
        $unitPrice = $menu->effectivePrice();

        if ($existingItem) {
            $newQty = min(99, $existingItem->qty + $qty);
            $existingItem->update([
                'qty' => $newQty,
                'subtotal' => $unitPrice * $newQty,
            ]);
        } else {
            $keranjang->itemKeranjang()->create([
                'menu_id' => $menu->id,
                'qty' => $qty,
                'catatan' => $catatanValue,
                'subtotal' => $unitPrice * $qty,
            ]);
        }

        return back()->with('success', "{$menu->nama} berhasil ditambahkan ke keranjang.");
    }

    /**
     * Memperbarui jumlah atau catatan item pada keranjang milik pengguna.
     */
    public function update(UpdateItemKeranjangRequest $request, ItemKeranjang $itemKeranjang): RedirectResponse
    {
        $itemKeranjang->loadMissing(['keranjang', 'menu']);

        abort_unless(
            $itemKeranjang->keranjang && $itemKeranjang->keranjang->user_id === $request->user()->id,
            403
        );

        if (! $itemKeranjang->menu || $itemKeranjang->menu->status_ketersediaan !== 'tersedia') {
            return redirect()
                ->route('user.keranjang.index')
                ->with('error', 'Maaf, stok menu ini sedang habis dan tidak dapat ditambah.');
        }

        $qty = $request->integer('qty');
        $catatan = $request->has('catatan')
            ? (trim((string) $request->input('catatan', '')) ?: null)
            : $itemKeranjang->catatan;

        $unitPrice = $itemKeranjang->menu->effectivePrice();

        $itemKeranjang->update([
            'qty' => $qty,
            'catatan' => $catatan,
            'subtotal' => $unitPrice * $qty,
        ]);

        return redirect()
            ->route('user.keranjang.index')
            ->with('success', 'Item keranjang berhasil diperbarui.');
    }

    /**
     * Menghapus satu item dari keranjang milik pengguna.
     */
    public function destroy(Request $request, ItemKeranjang $itemKeranjang): RedirectResponse
    {
        $itemKeranjang->loadMissing('keranjang');

        abort_unless(
            $itemKeranjang->keranjang && $itemKeranjang->keranjang->user_id === $request->user()->id,
            403
        );

        $itemKeranjang->delete();

        return redirect()
            ->route('user.keranjang.index')
            ->with('success', 'Item dihapus dari keranjang.');
    }
}
