<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller konfirmasi pembayaran, pelacakan status, dan riwayat pesanan pelanggan.
 */
class PesananController extends Controller
{
    /**
     * Menampilkan halaman instruksi pembayaran QRIS/Transfer/Tunai beserta timer 15 menit.
     */
    public function konfirmasi(Request $request, ?Pesanan $pesanan = null): View|RedirectResponse
    {
        $userId = $request->user()->id;

        if ($pesanan === null) {
            $pesanan = Pesanan::with(['detailPesanan.menu', 'meja', 'pembayaran'])
                ->where('user_id', $userId)
                ->latest()
                ->first();

            if (! $pesanan) {
                return redirect()
                    ->route('user.riwayat.index')
                    ->with('info', 'Belum ada pesanan yang perlu dikonfirmasi.');
            }
        } else {
            abort_unless($pesanan->user_id === $userId, 403);
            $pesanan->loadMissing(['detailPesanan.menu', 'meja', 'pembayaran']);
        }

        $deadline = $pesanan->created_at?->copy()->addMinutes(15) ?? now()->addMinutes(15);
        $remainingSeconds = max(0, (int) now()->diffInSeconds($deadline, false));

        return view('user.pesanan.konfirmasi', [
            'order' => $pesanan,
            'remainingSeconds' => $remainingSeconds,
        ]);
    }

    /**
     * Mensimulasikan konfirmasi pembayaran pelanggan menjadi lunas dan menunggu verifikasi dapur.
     */
    public function simulasiBayar(Request $request, Pesanan $pesanan): RedirectResponse
    {
        abort_unless($pesanan->user_id === $request->user()->id, 403);

        $pesanan->loadMissing('pembayaran');

        if ($pesanan->pembayaran && $pesanan->pembayaran->status_pembayaran === 'menunggu') {
            $pesanan->pembayaran->update([
                'status_pembayaran' => 'lunas',
            ]);
        }

        return redirect()
            ->route('user.pesanan.lacak', $pesanan->id)
            ->with('success', 'Konfirmasi pembayaran diterima. Pesanan Anda sedang menunggu verifikasi dapur.');
    }

    /**
     * Menampilkan halaman pelacakan status pesanan 4 tahap dari database.
     */
    public function lacak(Request $request, Pesanan $pesanan): View
    {
        abort_unless($pesanan->user_id === $request->user()->id, 403);

        $pesanan->loadMissing(['detailPesanan.menu', 'meja', 'pembayaran', 'user']);

        return view('user.pesanan.lacak', [
            'order' => $pesanan,
        ]);
    }

    /**
     * Menampilkan daftar riwayat pesanan milik pelanggan yang sedang login.
     */
    public function riwayat(Request $request): View
    {
        $orders = Pesanan::with(['detailPesanan.menu', 'meja', 'pembayaran'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('user.riwayat.index', [
            'orders' => $orders,
        ]);
    }
}
