<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MejaRequest;
use App\Models\Meja;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MejaController extends Controller
{
    public function index(): View
    {
        $tables = Meja::withCount('pesanan')->orderBy('id')->get();

        return view('admin.meja.index', [
            'tables' => $tables,
        ]);
    }

    /**
     * Membuat meja baru dan menghasilkan URL QR Code menuju katalog parameter meja.
     */
    public function store(MejaRequest $request): RedirectResponse
    {
        $meja = DB::transaction(function () use ($request): Meja {
            $meja = Meja::create([
                'nomor_meja' => $request->validated('nomor_meja'),
                'qr_code' => '',
            ]);

            $meja->update([
                'qr_code' => url('/menu?meja='.$meja->id),
            ]);

            return $meja;
        });

        return redirect()
            ->route('admin.meja.index')
            ->with('success', "Meja {$meja->nomor_meja} dan QR Code berhasil dibuat.");
    }

    /**
     * Memperbarui nama/nomor meja dan memastikan qr_code mengarah ke parameter meja.
     */
    public function update(MejaRequest $request, Meja $meja): RedirectResponse
    {
        $meja->update([
            'nomor_meja' => $request->validated('nomor_meja'),
            'qr_code' => url('/menu?meja='.$meja->id),
        ]);

        return redirect()
            ->route('admin.meja.index')
            ->with('success', "Data {$meja->nomor_meja} berhasil diperbarui.");
    }

    /**
     * Menghapus meja dari daftar.
     */
    public function destroy(Meja $meja): RedirectResponse
    {
        $nama = $meja->nomor_meja;
        $meja->delete();

        return redirect()
            ->route('admin.meja.index')
            ->with('success', "{$nama} berhasil dihapus.");
    }
}
