<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateStatusPesananRequest;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller manajemen pesanan masuk, verifikasi pembayaran, dan cetak struk dapur.
 */
class PesananController extends Controller
{
    /**
     * Menampilkan daftar pesanan dengan filter tab, status, dan pencarian pelanggan.
     */
    public function index(Request $request): View
    {
        $tab = (string) $request->query('tab', 'perlu_diproses');
        $statusFilter = (string) $request->query('status', '');
        $search = trim((string) $request->query('q', ''));

        $activeStatuses = [
            'menunggu_konfirmasi',
            'dimasak',
            'siap_diambil',
            'menunggu_driver',
        ];

        $query = Pesanan::with(['user', 'meja', 'detailPesanan.menu', 'pembayaran'])->latest();

        if ($tab === 'perlu_diproses' && $statusFilter === '') {
            $query->whereIn('status', $activeStatuses);
        }

        if ($statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $numeric = preg_replace('/\D+/', '', $search);
                $q->whereHas('user', function ($uq) use ($search): void {
                    $uq->where('nama', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
                if ($numeric !== '') {
                    $q->orWhere('id', (int) $numeric);
                }
            });
        }

        return view('admin.pesanan.index', [
            'orders' => $query->get(),
            'pendingCount' => Pesanan::whereIn('status', $activeStatuses)->count(),
            'totalCount' => Pesanan::count(),
            'filters' => [
                'tab' => $tab,
                'status' => $statusFilter,
                'q' => $search,
            ],
        ]);
    }

    /**
     * Memperbarui status alur pesanan atau menolak pesanan beserta alasannya.
     */
    public function updateStatus(UpdateStatusPesananRequest $request, Pesanan $pesanan): RedirectResponse
    {
        $validated = $request->validated();
        $status = $validated['status'];

        $pesanan->update([
            'status' => $status,
            'alasan_tolak' => $status === 'ditolak' ? ($validated['alasan_tolak'] ?? null) : null,
        ]);

        return back()->with('success', 'Status pesanan #INV-'.str_pad((string) $pesanan->id, 4, '0', STR_PAD_LEFT).' diperbarui menjadi '.str_replace('_', ' ', $status).'.');
    }

    public function konfirmasiBayar(Pesanan $pesanan): RedirectResponse
    {
        $pembayaran = $pesanan->pembayaran;

        if ($pembayaran) {
            $pembayaran->update(['status_pembayaran' => 'lunas']);
        } else {
            Pembayaran::create([
                'pesanan_id' => $pesanan->id,
                'metode' => $pesanan->metode_pembayaran,
                'status_pembayaran' => 'lunas',
                'jumlah' => $pesanan->total_harga,
            ]);
        }

        return back()->with('success', 'Pembayaran pesanan #INV-'.str_pad((string) $pesanan->id, 4, '0', STR_PAD_LEFT).' dikonfirmasi LUNAS.');
    }

    /**
     * Menampilkan tampilan khusus cetak struk dapur sederhana.
     */
    public function struk(Pesanan $pesanan): View
    {
        $pesanan->load(['user', 'meja', 'detailPesanan.menu', 'pembayaran']);

        return view('admin.pesanan.struk', [
            'order' => $pesanan,
        ]);
    }
}
