@extends('layouts.admin')

@section('title', 'Dashboard Admin — Dapur Bucik')

@section('content')
    <div class="pb-4 mb-6 border-b border-dashed border-[#1B362D]/20">
        <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#1B362D]">Ringkasan Dapur Hari Ini</h1>
    </div>

    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5">
        <article class="bg-white bucik-card p-5 border border-[#1B362D]/15 border-l-4 border-l-[#E29E31] shadow-xs">
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-[#1B362D]/60">Pesanan Masuk</span>
            <p class="mt-2 font-serif text-3xl font-bold text-[#1B362D]">{{ $kpi['pesanan_hari_ini'] }}</p>
            <p class="mt-2 text-[11px] font-bold text-emerald-700">&uarr; Hari ini</p>
        </article>

        <article class="bg-white bucik-card p-5 border border-[#1B362D]/15 border-l-4 border-l-[#1B362D] shadow-xs">
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-[#1B362D]/60">Pendapatan (Rp)</span>
            <p class="mt-2 font-serif text-2xl sm:text-3xl font-bold text-[#1B362D]">{{ number_format($kpi['total_pendapatan'], 0, ',', '.') }}</p>
            <p class="mt-2 text-[11px] font-bold text-emerald-700">&uarr; Terverifikasi lunas / selesai</p>
        </article>

        <article class="bg-white bucik-card p-5 border border-[#1B362D]/15 border-l-4 border-l-[#C84B31] shadow-xs">
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-[#1B362D]/60">Pesanan Aktif</span>
            <p class="mt-2 font-serif text-3xl font-bold text-[#C84B31]">{{ $kpi['pesanan_aktif'] }}</p>
            <p class="mt-2 text-[11px] text-[#1B362D]/60">Sedang diproses dapur</p>
        </article>

        <article class="bg-white bucik-card p-5 border border-[#1B362D]/15 border-l-4 border-l-stone-300 shadow-xs">
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-[#1B362D]/60">Menu Habis</span>
            <p class="mt-2 font-serif text-3xl font-bold text-stone-500">{{ $kpi['menu_habis'] }}</p>
            <p class="mt-2 text-[11px] font-bold text-[#C84B31]">Perlu update stok</p>
        </article>
    </section>

    <section class="mt-6 grid grid-cols-1 xl:grid-cols-12 gap-6 items-stretch">
        <div class="xl:col-span-8 bg-white bucik-card p-6 border border-[#1B362D]/15 shadow-xs flex flex-col justify-between">
            <h2 class="font-serif text-lg font-bold text-[#1B362D] mb-4">Grafik Pendapatan Mingguan</h2>

            <div class="p-4 rounded-xl bg-[#FAF4E8]/60 border border-[#1B362D]/10 h-64 relative">
                <canvas id="weeklyRevenueChart" aria-label="Grafik Pendapatan Mingguan"></canvas>
            </div>
        </div>

        <div class="xl:col-span-4 bg-white bucik-card p-6 border border-[#1B362D]/15 shadow-xs">
            <h2 class="font-serif text-lg font-bold text-[#1B362D] mb-5">Menu Paling Dicari</h2>

            <div class="divide-y divide-dashed divide-[#1B362D]/15">
                @forelse ($topMenus as $index => $menu)
                    @php
                        $soldQty = (int) ($menu->total_terjual ?? 0);
                        $words = preg_split('/\s+/', trim((string) $menu->nama)) ?: [];
                        $initials = implode('', array_map(fn (string $w): string => mb_strtoupper(mb_substr($w, 0, 1)), array_slice($words, 0, 2))) ?: 'MN';
                    @endphp
                    <div class="py-3.5 first:pt-0 last:pb-0 flex items-center gap-3.5">
                        <span class="font-serif text-lg font-bold text-[#1B362D]/30 w-4 text-center">{{ $index + 1 }}</span>
                        <span class="w-10 h-10 rounded-lg bg-[#1B362D] text-[#FAF4E8] font-bold text-xs flex items-center justify-center shrink-0">
                            {{ $initials }}
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-[#1B362D] truncate">{{ $menu->nama }}</p>
                            <p class="text-xs text-[#1B362D]/60">{{ $soldQty }} porsi terjual</p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-[#1B362D]/65">Belum ada data penjualan menu.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const canvas = document.getElementById('weeklyRevenueChart');
        if (!canvas || typeof Chart === 'undefined') return;

        const values = @json($weeklyChart['pendapatan']);
        const maxVal = Math.max(...values, 0);
        const bgColors = values.map((v) => (v === maxVal && maxVal > 0 ? '#1B362D' : '#E29E31'));

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: @json($weeklyChart['labels']),
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: values,
                    backgroundColor: bgColors,
                    borderRadius: 6,
                    maxBarThickness: 34,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => 'Rp ' + Number(value).toLocaleString('id-ID')
                        }
                    }
                }
            }
        });
    })();
</script>
@endpush
