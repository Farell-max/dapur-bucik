@extends('layouts.admin')

@section('title', 'Laporan Keuangan & Penjualan â€” Dapur Bucik')

@section('content')
    <div class="pb-4 mb-5 border-b border-dashed border-[#1B362D]/20 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#1B362D]">Laporan Keuangan &amp; Penjualan</h1>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.laporan.export-pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
               class="px-4 py-2 bucik-btn bg-[#1B362D] hover:bg-[#12261F] text-[#FAF4E8] border-b-2 border-[#E29E31] text-xs font-bold transition">
                Unduh PDF
            </a>
            <a href="{{ route('admin.laporan.export-excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
               class="px-4 py-2 bucik-btn bg-white border border-[#1B362D]/30 hover:bg-[#FAF4E8] text-[#1B362D] text-xs font-bold transition">
                Unduh Excel
            </a>
        </div>
    </div>

    <div class="flex items-center gap-6 mb-6">
        <button type="button" data-rep-tab="ringkasan"
                class="rep-tab-btn pb-2 text-xs sm:text-sm font-bold text-[#1B362D] border-b-2 border-[#E29E31] cursor-pointer">
            Ringkasan Penjualan
        </button>
        <button type="button" data-rep-tab="terlaris"
                class="rep-tab-btn pb-2 text-xs sm:text-sm font-semibold text-[#1B362D]/60 hover:text-[#1B362D] cursor-pointer">
            Laporan Menu Terlaris
        </button>
    </div>

    <div class="bg-white bucik-card border border-[#1B362D]/15 border-l-4 border-l-[#E29E31] p-5 sm:p-6 shadow-xs mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <form method="GET" action="{{ route('admin.laporan.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#1B362D]/60 mb-1.5">Periode Laporan</span>
                <div class="flex flex-wrap items-center gap-2">
                    <input name="start_date" type="date" value="{{ $startDate }}"
                           class="px-3 py-1.5 rounded-lg border border-[#1B362D]/20 bg-white text-xs font-semibold">
                    <span class="text-xs text-[#1B362D]/50">s/d</span>
                    <input name="end_date" type="date" value="{{ $endDate }}"
                           class="px-3 py-1.5 rounded-lg border border-[#1B362D]/20 bg-white text-xs font-semibold">
                    <button type="submit"
                            class="px-3.5 py-1.5 rounded-lg bg-[#1B362D] text-[#FAF4E8] text-xs font-bold cursor-pointer">
                        Terapkan
                    </button>
                </div>
            </div>
        </form>

        <div class="lg:text-right">
            <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#1B362D]/60">Total Pendapatan Bersih</span>
            <p class="font-serif text-2xl sm:text-3xl font-bold text-[#1B362D] mt-1">
                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <div id="rep-panel-ringkasan" class="space-y-6">
        <div class="bg-white bucik-card border border-[#1B362D]/15 p-6 shadow-xs">
            <div class="flex items-center justify-between gap-3 mb-4">
                <span class="text-xs font-bold text-[#1B362D]/60">Grafik Pendapatan Periode Terpilih</span>
                <div class="inline-flex rounded-lg bg-[#FAF4E8] p-1 border border-[#1B362D]/10">
                    <button type="button" data-chart-type="bar" class="chart-type-btn px-3 py-1 rounded text-xs font-bold bg-[#1B362D] text-[#FAF4E8] cursor-pointer">Batang</button>
                    <button type="button" data-chart-type="line" class="chart-type-btn px-3 py-1 rounded text-xs font-bold text-[#1B362D] cursor-pointer">Garis</button>
                </div>
            </div>
            <div class="h-72 relative">
                <canvas id="reportSalesChart" aria-label="Grafik Laporan Penjualan"></canvas>
            </div>
        </div>

        @if ($dailySales->isNotEmpty())
            <div class="bg-white bucik-card border border-[#1B362D]/15 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-[#FAF4E8]/60 border-b border-[#1B362D]/10 text-[11px] font-extrabold uppercase tracking-wider text-[#1B362D]/60">
                            <tr>
                                <th class="px-5 py-3.5">Tanggal</th>
                                <th class="px-5 py-3.5">Jumlah Pesanan</th>
                                <th class="px-5 py-3.5">Metode Dominan</th>
                                <th class="px-5 py-3.5 text-right">Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#1B362D]/10">
                            @foreach ($dailySales as $day)
                                <tr class="hover:bg-[#FAF4E8]/35">
                                    <td class="px-5 py-3.5 font-semibold">{{ $day['tanggal'] }}</td>
                                    <td class="px-5 py-3.5">{{ $day['jumlah_pesanan'] }} pesanan</td>
                                    <td class="px-5 py-3.5">
                                        <span class="bucik-stamp border-[#1B362D]/60 bg-[#FAF4E8] text-[#1B362D] text-[10px]">{{ $day['metode_dominan'] }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-bold text-[#1B362D]">Rp {{ number_format($day['pendapatan'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <div id="rep-panel-terlaris" class="hidden bg-white bucik-card border border-[#1B362D]/15 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#FAF4E8]/60 border-b border-[#1B362D]/10 text-[11px] font-extrabold uppercase tracking-wider text-[#1B362D]/60">
                    <tr>
                        <th class="px-5 py-3.5">Peringkat</th>
                        <th class="px-5 py-3.5">Nama Menu</th>
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5">Terjual</th>
                        <th class="px-5 py-3.5 text-right">Total Omzet</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1B362D]/10">
                    @forelse ($topMenus as $idx => $menu)
                        <tr class="hover:bg-[#FAF4E8]/35">
                            <td class="px-5 py-3.5 font-bold text-[#CC8A22]">#{{ $idx + 1 }}</td>
                            <td class="px-5 py-3.5 font-bold text-[#1B362D]">{{ $menu['nama'] }}</td>
                            <td class="px-5 py-3.5 text-xs">{{ $menu['kategori'] }}</td>
                            <td class="px-5 py-3.5 font-semibold">{{ $menu['total_qty'] }} porsi</td>
                            <td class="px-5 py-3.5 text-right font-bold text-[#1B362D]">Rp {{ number_format($menu['total_omzet'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-sm text-[#1B362D]/65">Belum ada penjualan menu pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const canvas = document.getElementById('reportSalesChart');
        let chartInstance = null;

        function renderReportChart(type) {
            if (!canvas || typeof Chart === 'undefined') return;
            if (chartInstance) chartInstance.destroy();

            chartInstance = new Chart(canvas, {
                type: type,
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: @json($chartData['pendapatan']),
                        backgroundColor: type === 'bar' ? '#E29E31' : 'rgba(226, 158, 49, 0.25)',
                        borderColor: '#1B362D',
                        borderWidth: 2,
                        borderRadius: type === 'bar' ? 6 : 0,
                        fill: type === 'line',
                        tension: 0.35
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (v) => 'Rp ' + Number(v).toLocaleString('id-ID')
                            }
                        }
                    }
                }
            });
        }

        renderReportChart('bar');

        document.querySelectorAll('.chart-type-btn').forEach((btn) => {
            btn.addEventListener('click', function () {
                const nextType = this.dataset.chartType || 'bar';
                document.querySelectorAll('.chart-type-btn').forEach((b) => {
                    b.classList.remove('bg-[#1B362D]', 'text-[#FAF4E8]');
                    b.classList.add('text-[#1B362D]');
                });
                this.classList.add('bg-[#1B362D]', 'text-[#FAF4E8]');
                this.classList.remove('text-[#1B362D]');
                renderReportChart(nextType);
            });
        });

        const repTabs = document.querySelectorAll('.rep-tab-btn');
        const panelRingkasan = document.getElementById('rep-panel-ringkasan');
        const panelTerlaris = document.getElementById('rep-panel-terlaris');

        repTabs.forEach((btn) => {
            btn.addEventListener('click', function () {
                const target = this.dataset.repTab;
                repTabs.forEach((b) => {
                    b.className = 'rep-tab-btn pb-2 text-xs sm:text-sm font-semibold text-[#1B362D]/60 hover:text-[#1B362D] cursor-pointer';
                });
                this.className = 'rep-tab-btn pb-2 text-xs sm:text-sm font-bold text-[#1B362D] border-b-2 border-[#E29E31] cursor-pointer';

                panelRingkasan?.classList.toggle('hidden', target !== 'ringkasan');
                panelTerlaris?.classList.toggle('hidden', target !== 'terlaris');
            });
        });
    })();
</script>
@endpush
