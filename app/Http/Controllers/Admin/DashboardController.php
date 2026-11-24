<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Pesanan;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

/**
 * Controller dashboard ringkasan operasional admin berbasis MySQL Eloquent.
 */
class DashboardController extends Controller
{
    /**
     * Menampilkan KPI real, grafik pendapatan 7 hari terakhir, dan 3 menu terlaris.
     */
    public function index(): View
    {
        $qualifyingQuery = Pesanan::query()->where(function ($query): void {
            $query->where('status', 'selesai')
                ->orWhereHas('pembayaran', function ($paymentQuery): void {
                    $paymentQuery->where('status_pembayaran', 'lunas');
                });
        });

        $kpi = [
            'pesanan_hari_ini' => Pesanan::whereDate('created_at', today())->count(),
            'total_pendapatan' => (float) (clone $qualifyingQuery)->sum('total_harga'),
            'pesanan_aktif' => Pesanan::whereIn('status', [
                'menunggu_konfirmasi',
                'dimasak',
                'siap_diambil',
                'menunggu_driver',
            ])->count(),
            'menu_habis' => Menu::where('status_ketersediaan', 'habis')->count(),
        ];

        $startDate = today()->subDays(6)->startOfDay();
        $weeklyOrders = (clone $qualifyingQuery)
            ->where('created_at', '>=', $startDate)
            ->get(['created_at', 'total_harga']);

        $groupedByDay = $weeklyOrders->groupBy(
            fn (Pesanan $order): string => Carbon::parse($order->created_at)->toDateString()
        );

        $labels = [];
        $pendapatanSeries = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = today()->subDays($i);
            $dateKey = $day->toDateString();
            $labels[] = $day->translatedFormat('d M');
            $pendapatanSeries[] = isset($groupedByDay[$dateKey])
                ? (float) $groupedByDay[$dateKey]->sum('total_harga')
                : 0.0;
        }

        $topMenus = Menu::with('kategori')
            ->withSum('detailPesanan as total_terjual', 'qty')
            ->orderByDesc('total_terjual')
            ->orderByDesc('rating')
            ->take(3)
            ->get();

        $recentOrders = Pesanan::with(['user', 'pembayaran'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'kpi' => $kpi,
            'weeklyChart' => [
                'labels' => $labels,
                'pendapatan' => $pendapatanSeries,
            ],
            'topMenus' => $topMenus,
            'recentOrders' => $recentOrders,
        ]);
    }
}
