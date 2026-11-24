<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailPesanan;
use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Controller laporan keuangan, penjualan harian, menu terlaris, dan export PDF/CSV.
 */
class LaporanController extends Controller
{
    /**
     * Menampilkan laporan penjualan dan menu terlaris berdasarkan rentang tanggal.
     */
    public function index(Request $request): View
    {
        return view('admin.laporan.index', $this->buildReportData($request));
    }

    /**
     * Mengunduh laporan penjualan dalam format PDF menggunakan DomPDF.
     */
    public function exportPdf(Request $request): Response
    {
        $data = $this->buildReportData($request);
        $pdf = Pdf::loadView('admin.laporan.pdf', $data)->setPaper('a4', 'portrait');

        $filename = "laporan-dapur-bucik-{$data['startDate']}-{$data['endDate']}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Mengunduh laporan penjualan dalam format CSV kompatibel Microsoft Excel.
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $data = $this->buildReportData($request);
        $filename = "laporan-dapur-bucik-{$data['startDate']}-{$data['endDate']}.csv";

        return response()->streamDownload(function () use ($data): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            // BOM UTF-8 agar Excel membaca karakter dan format dengan benar
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['LAPORAN PENJUALAN DAPUR BUCIK']);
            fputcsv($handle, ['Periode', $data['startDate'].' s/d '.$data['endDate']]);
            fputcsv($handle, ['Total Pendapatan (Rp)', $data['totalPendapatan']]);
            fputcsv($handle, ['Total Transaksi', $data['totalTransaksi']]);
            fputcsv($handle, []);

            fputcsv($handle, ['RINGKASAN PENJUALAN HARIAN']);
            fputcsv($handle, ['Tanggal', 'Jumlah Pesanan', 'Metode Dominan', 'Pendapatan (Rp)']);
            foreach ($data['dailySales'] as $row) {
                fputcsv($handle, [
                    $row['tanggal'],
                    $row['jumlah_pesanan'],
                    $row['metode_dominan'],
                    $row['pendapatan'],
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['MENU TERLARIS']);
            fputcsv($handle, ['Peringkat', 'Nama Menu', 'Kategori', 'Jumlah Terjual', 'Total Omzet (Rp)']);
            foreach ($data['topMenus'] as $idx => $menu) {
                fputcsv($handle, [
                    $idx + 1,
                    $menu['nama'],
                    $menu['kategori'],
                    $menu['total_qty'],
                    $menu['total_omzet'],
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Menyusun dataset laporan teragregasi berdasarkan filter tanggal.
     *
     * @return array<string, mixed>
     */
    private function buildReportData(Request $request): array
    {
        $rawStart = (string) $request->query('start_date', '');
        $rawEnd = (string) $request->query('end_date', '');

        try {
            $startCarbon = $rawStart !== '' ? Carbon::parse($rawStart)->startOfDay() : today()->subDays(6)->startOfDay();
        } catch (\Throwable) {
            $startCarbon = today()->subDays(6)->startOfDay();
        }

        try {
            $endCarbon = $rawEnd !== '' ? Carbon::parse($rawEnd)->endOfDay() : today()->endOfDay();
        } catch (\Throwable) {
            $endCarbon = today()->endOfDay();
        }

        if ($startCarbon->gt($endCarbon)) {
            [$startCarbon, $endCarbon] = [$endCarbon->copy()->startOfDay(), $startCarbon->copy()->endOfDay()];
        }

        $startDate = $startCarbon->toDateString();
        $endDate = $endCarbon->toDateString();

        $orders = Pesanan::with(['pembayaran'])
            ->whereBetween('created_at', [$startCarbon, $endCarbon])
            ->where(function ($query): void {
                $query->where('status', 'selesai')
                    ->orWhereHas('pembayaran', function ($paymentQuery): void {
                        $paymentQuery->where('status_pembayaran', 'lunas');
                    });
            })
            ->orderByDesc('created_at')
            ->get();

        $totalPendapatan = (float) $orders->sum('total_harga');
        $totalTransaksi = $orders->count();
        $rataRata = $totalTransaksi > 0 ? round($totalPendapatan / $totalTransaksi) : 0.0;

        $paymentLabels = [
            'qris' => 'QRIS',
            'transfer_bank' => 'Transfer Bank',
            'bayar_di_tempat' => 'Bayar Tunai',
        ];

        $dailySales = $orders
            ->groupBy(fn (Pesanan $order): string => Carbon::parse($order->created_at)->toDateString())
            ->map(function (Collection $dayOrders, string $dateKey) use ($paymentLabels): array {
                $dominantKey = (string) ($dayOrders->countBy('metode_pembayaran')->sortDesc()->keys()->first() ?? 'qris');

                return [
                    'date_key' => $dateKey,
                    'tanggal' => Carbon::parse($dateKey)->translatedFormat('d M Y'),
                    'jumlah_pesanan' => $dayOrders->count(),
                    'metode_dominan' => $paymentLabels[$dominantKey] ?? $dominantKey,
                    'pendapatan' => (float) $dayOrders->sum('total_harga'),
                ];
            })
            ->values();

        // Data grafik berurutan kronologis untuk tiap tanggal yang memiliki transaksi atau rentang 7 hari
        $chartLabels = [];
        $chartValues = [];
        $indexedDaily = $dailySales->keyBy('date_key');

        $cursor = $startCarbon->copy()->startOfDay();
        $daySpan = min(31, (int) $startCarbon->diffInDays($endCarbon) + 1);
        for ($i = 0; $i < $daySpan; $i++) {
            $key = $cursor->toDateString();
            $chartLabels[] = $cursor->translatedFormat('d M');
            $chartValues[] = isset($indexedDaily[$key]) ? $indexedDaily[$key]['pendapatan'] : 0.0;
            $cursor->addDay();
        }

        $orderIds = $orders->pluck('id');
        $topMenus = collect();

        if ($orderIds->isNotEmpty()) {
            $topMenus = DetailPesanan::with('menu.kategori')
                ->whereIn('pesanan_id', $orderIds)
                ->selectRaw('menu_id, SUM(qty) as total_qty, SUM(subtotal) as total_omzet')
                ->groupBy('menu_id')
                ->orderByDesc('total_qty')
                ->get()
                ->map(fn (DetailPesanan $row): array => [
                    'nama' => $row->menu?->nama ?? 'Menu',
                    'kategori' => $row->menu?->kategori?->nama_kategori ?? 'Umum',
                    'total_qty' => (int) $row->total_qty,
                    'total_omzet' => (float) $row->total_omzet,
                ]);
        }

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalPendapatan' => $totalPendapatan,
            'totalTransaksi' => $totalTransaksi,
            'rataRata' => $rataRata,
            'dailySales' => $dailySales,
            'topMenus' => $topMenus,
            'chartData' => [
                'labels' => $chartLabels,
                'pendapatan' => $chartValues,
            ],
        ];
    }
}
