@extends('layouts.user')

@section('title', 'Riwayat Pesanan â€” Dapur Bucik')

@section('content')
    @php
        $receptionLabels = [
            'dine-in' => 'Dine-in',
            'pickup' => 'Pickup',
            'kurir_mandiri' => 'Kurir Mandiri',
        ];
        $paymentLabels = [
            'qris' => 'QRIS',
            'transfer_bank' => 'Transfer Bank',
            'bayar_di_tempat' => 'Bayar Tunai',
        ];
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#1B362D]">Riwayat Pesanan Saya</h1>
                <p class="text-sm text-[#1B362D]/70 mt-1">Pantau transaksi aktif maupun riwayat pesanan yang telah selesai.</p>
            </div>
            <a href="{{ route('user.menu.index') }}"
               class="inline-flex items-center justify-center px-5 py-2.5 bucik-btn bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] border border-[#1B362D]/40 shadow-[0_2px_0_#1B362D] text-xs sm:text-sm font-bold transition">
                + Pesan Menu Baru
            </a>
        </div>

        <div class="flex flex-wrap gap-2 mb-6" id="riwayat-tabs">
            <button type="button" data-filter="all"
                    class="riwayat-tab px-4 py-2 rounded-full text-xs sm:text-sm font-bold bg-[#1B362D] text-[#FAF4E8] cursor-pointer">
                Semua
            </button>
            <button type="button" data-filter="proses"
                    class="riwayat-tab px-4 py-2 rounded-full text-xs sm:text-sm font-semibold bg-white border border-[#1B362D]/15 text-[#1B362D] cursor-pointer">
                Sedang Proses
            </button>
            <button type="button" data-filter="selesai"
                    class="riwayat-tab px-4 py-2 rounded-full text-xs sm:text-sm font-semibold bg-white border border-[#1B362D]/15 text-[#1B362D] cursor-pointer">
                Selesai
            </button>
            <button type="button" data-filter="ditolak"
                    class="riwayat-tab px-4 py-2 rounded-full text-xs sm:text-sm font-semibold bg-white border border-[#1B362D]/15 text-[#1B362D] cursor-pointer">
                Dibatalkan
            </button>
        </div>

        <div class="space-y-4" id="riwayat-list">
            @forelse ($orders as $order)
                @php
                    $kodePesanan = '#DB-' . str_pad((string) $order->id, 4, '0', STR_PAD_LEFT);
                    $group = match ($order->status) {
                        'selesai' => 'selesai',
                        'ditolak' => 'ditolak',
                        default => 'proses',
                    };
                    $accent = match ($group) {
                        'selesai' => 'border-l-[#1B362D]',
                        'ditolak' => 'border-l-red-600',
                        default => 'border-l-[#E29E31]',
                    };
                    $statusBayar = $order->pembayaran?->status_pembayaran ?? 'menunggu';
                @endphp

                <article class="riwayat-card bg-white bucik-card border border-[#1B362D]/15 border-l-4 {{ $accent }} p-5 sm:p-6 shadow-xs"
                         data-group="{{ $group }}">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-[#1B362D]/10">
                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="font-serif text-lg font-bold text-[#1B362D]">{{ $kodePesanan }}</span>
                                <span class="bucik-stamp border-[#1B362D]/60 bg-[#FAF4E8] text-[#1B362D] text-[10px]">
                                    {{ $receptionLabels[$order->metode_penerimaan] ?? $order->metode_penerimaan }}
                                </span>
                                @if ($order->meja)
                                    <span class="bucik-stamp border-[#E29E31] bg-[#FAF4E8] text-[#CC8A22] text-[10px]">
                                        {{ $order->meja->nomor_meja }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-[#1B362D]/65 mt-1">
                                {{ $order->created_at?->format('d M Y, H:i') }} WIB &bull;
                                {{ $paymentLabels[$order->metode_pembayaran] ?? $order->metode_pembayaran }}
                                ({{ ucfirst($statusBayar) }})
                            </p>
                        </div>

                        <x-order-status-badge :status="$order->status" />
                    </div>

                    <div class="py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="space-y-1 text-sm text-[#1B362D]/85">
                            @foreach ($order->detailPesanan as $detail)
                                <p>&bull; <strong>{{ $detail->qty }}x</strong> {{ $detail->menu?->nama ?? 'Menu' }} (Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }})</p>
                            @endforeach

                            @if (! empty($order->alasan_tolak))
                                <p class="mt-2 text-xs text-red-700 font-medium">
                                    Alasan ditolak: {{ $order->alasan_tolak }}
                                </p>
                            @endif
                        </div>

                        <div class="flex flex-col sm:flex-row md:flex-col items-start sm:items-center md:items-end justify-between gap-3 shrink-0">
                            <div class="md:text-right">
                                <span class="text-xs text-[#1B362D]/60 block">Total Harga</span>
                                <span class="font-serif text-xl font-bold text-[#1B362D]">
                                    Rp {{ number_format((float) $order->total_harga, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center gap-2.5">
                                @if ($statusBayar === 'menunggu' && $order->status !== 'ditolak')
                                    <a href="{{ route('user.pesanan.konfirmasi', $order->id) }}"
                                       class="px-4 py-2 bucik-btn bg-[#1B362D] hover:bg-[#12261F] text-[#FAF4E8] text-xs font-bold transition">
                                        Bayar Sekarang
                                    </a>
                                @endif
                                <a href="{{ route('user.pesanan.lacak', $order->id) }}"
                                   class="px-4 py-2 bucik-btn border border-[#1B362D]/45 bg-white text-[#1B362D] text-xs font-bold hover:bg-[#FAF4E8] transition">
                                    Lihat Nota
                                </a>
                                <a href="{{ route('user.menu.index') }}"
                                   class="px-4 py-2 bucik-btn bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] border border-[#1B362D]/40 shadow-[0_2px_0_#1B362D] text-xs font-bold transition">
                                    Pesan Lagi
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="bg-white bucik-card border border-[#1B362D]/15 p-10 text-center">
                    <h2 class="font-serif text-xl font-bold text-[#1B362D]">Belum Ada Riwayat Pesanan</h2>
                    <p class="text-sm text-[#1B362D]/70 mt-1">Mulai pesan hidangan favorit Anda dari katalog Dapur Bucik.</p>
                    <a href="{{ route('user.menu.index') }}"
                       class="mt-5 inline-flex items-center px-5 py-2.5 bucik-btn bg-[#E29E31] text-[#1B362D] border border-[#1B362D]/40 shadow-[0_2px_0_#1B362D] text-sm font-bold">
                        Jelajahi Menu
                    </a>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const tabs = document.querySelectorAll('.riwayat-tab');
        const cards = document.querySelectorAll('.riwayat-card');

        tabs.forEach((tab) => {
            tab.addEventListener('click', function () {
                const filter = this.dataset.filter;

                tabs.forEach((t) => {
                    t.classList.remove('bg-[#1B362D]', 'text-[#FAF4E8]', 'font-bold');
                    t.classList.add('bg-white', 'border', 'border-[#1B362D]/15', 'text-[#1B362D]', 'font-semibold');
                });
                this.classList.add('bg-[#1B362D]', 'text-[#FAF4E8]', 'font-bold');
                this.classList.remove('bg-white', 'border', 'border-[#1B362D]/15', 'text-[#1B362D]', 'font-semibold');

                cards.forEach((card) => {
                    const show = filter === 'all' || card.dataset.group === filter;
                    card.classList.toggle('hidden', !show);
                });
            });
        });
    })();
</script>
@endpush
