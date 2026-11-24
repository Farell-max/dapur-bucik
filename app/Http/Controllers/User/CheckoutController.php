<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CheckoutRequest;
use App\Models\Keranjang;
use App\Models\Meja;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Controller halaman checkout dan pembuatan transaksi pesanan pelanggan.
 */
class CheckoutController extends Controller
{
    /**
     * Menampilkan pilihan metode penerimaan, metode pembayaran, dan nota pesanan.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $keranjang = Keranjang::with(['itemKeranjang.menu'])
            ->where('user_id', $user->id)
            ->first();

        if (! $keranjang || $keranjang->itemKeranjang->isEmpty()) {
            return redirect()
                ->route('user.keranjang.index')
                ->with('error', 'Keranjang Anda masih kosong. Silakan pilih menu terlebih dahulu.');
        }

        $items = $keranjang->itemKeranjang;
        $totalHarga = (float) $items->sum('subtotal');

        return view('user.checkout.index', [
            'items' => $items,
            'totalHarga' => $totalHarga,
            'tables' => Meja::orderBy('id')->get(),
            'addresses' => $user->alamat()->orderByDesc('id')->get(),
            'selectedMejaId' => $request->session()->get('selected_meja_id'),
        ]);
    }

    /**
     * Menyimpan pesanan, detail_pesanan, dan pembayaran dalam satu transaksi database.
     */
    public function store(CheckoutRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $pesanan = DB::transaction(function () use ($user, $validated): Pesanan {
            $keranjang = Keranjang::with(['itemKeranjang.menu'])
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (! $keranjang || $keranjang->itemKeranjang->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Keranjang belanja Anda kosong.',
                ]);
            }

            $totalHarga = 0.0;
            $detailRows = [];

            foreach ($keranjang->itemKeranjang as $item) {
                $menu = $item->menu;

                if (! $menu || $menu->status_ketersediaan !== 'tersedia') {
                    throw ValidationException::withMessages([
                        'cart' => 'Menu '.($menu?->nama ?? 'pilihan Anda').' sedang habis.',
                    ]);
                }

                // Hitung ulang subtotal dari harga DB terkini agar tidak bergantung pada input klien
                $subtotal = $menu->effectivePrice() * $item->qty;
                $totalHarga += $subtotal;

                $detailRows[] = [
                    'menu_id' => $menu->id,
                    'qty' => $item->qty,
                    'catatan' => $item->catatan,
                    'subtotal' => $subtotal,
                ];
            }

            $metodePenerimaan = $validated['metode_penerimaan'];
            $metodePembayaran = $validated['metode_pembayaran'];

            $pesanan = Pesanan::create([
                'user_id' => $user->id,
                'meja_id' => $metodePenerimaan === 'dine-in' ? (int) $validated['meja_id'] : null,
                'metode_penerimaan' => $metodePenerimaan,
                'metode_pembayaran' => $metodePembayaran,
                'status' => 'menunggu_konfirmasi',
                'alasan_tolak' => null,
                'total_harga' => $totalHarga,
                'estimasi_waktu_ambil' => now()->addMinutes(20),
            ]);

            foreach ($detailRows as $row) {
                $pesanan->detailPesanan()->create($row);
            }

            Pembayaran::create([
                'pesanan_id' => $pesanan->id,
                'metode' => $metodePembayaran,
                'status_pembayaran' => 'menunggu',
                'jumlah' => $totalHarga,
            ]);

            $keranjang->itemKeranjang()->delete();

            return $pesanan;
        });

        $request->session()->forget('selected_meja_id');

        return redirect()
            ->route('user.pesanan.konfirmasi', $pesanan->id)
            ->with('success', 'Pesanan berhasil dibuat! Silakan selesaikan pembayaran Anda.');
    }
}
