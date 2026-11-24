<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller manajemen akun pelanggan (Buku Pelanggan) oleh administrator.
 */
class PelangganController extends Controller
{
    /**
     * Menampilkan daftar akun pelanggan beserta jumlah transaksi dan status akun.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');

        $query = User::where('role', 'pelanggan')
            ->withCount('pesanan')
            ->withSum('pesanan as total_belanja', 'total_harga')
            ->orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        if (in_array($status, ['aktif', 'diblokir'], true)) {
            $query->where('status_akun', $status);
        }

        return view('admin.pelanggan.index', [
            'customers' => $query->get(),
            'filters' => [
                'q' => $search,
                'status' => $status,
            ],
        ]);
    }

    /**
     * Mengubah status akun pelanggan antara aktif dan diblokir.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        abort_unless($user->role === 'pelanggan', 403, 'Hanya akun pelanggan yang dapat diubah statusnya.');

        $nextStatus = $user->status_akun === 'aktif' ? 'diblokir' : 'aktif';
        $user->update(['status_akun' => $nextStatus]);

        return back()->with('success', "Status akun {$user->nama} diubah menjadi ".strtoupper($nextStatus).'.');
    }
}
