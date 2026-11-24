@extends('layouts.user')

@section('title', 'Konfirmasi Pembayaran â€” Dapur Bucik')

@section('content')
    @php
        $kodePesanan = '#DB-' . str_pad((string) $order->id, 4, '0', STR_PAD_LEFT);
        $statusBayar = $order->pembayaran?->status_pembayaran ?? 'menunggu';
        $defaultTab = $order->metode_pembayaran === 'transfer_bank' ? 'transfer' : 'qris';
        $initMinutes = str_pad((string) intdiv($remainingSeconds, 60), 2, '0', STR_PAD_LEFT);
        $initSecs = str_pad((string) ($remainingSeconds % 60), 2, '0', STR_PAD_LEFT);
    @endphp

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        <div class="bg-[#1B362D] text-[#FAF4E8] bucik-card p-6 sm:p-8 shadow-md border-b-4 border-[#E29E31]">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <span class="bucik-stamp border-[#E29E31] text-[#E29E31] bg-transparent text-xs px-3 py-1">
                        Pembayaran: {{ ucfirst($statusBayar) }}
                    </span>
                    <h1 class="font-serif text-2xl sm:text-3xl font-bold mt-3">Pesanan {{ $kodePesanan }} Dibuat</h1>
                    <p class="text-xs sm:text-sm text-[#FAF4E8]/80 mt-1">
                        @if ($order->metode_pembayaran === 'bayar_di_tempat')
                            Silakan lakukan pembayaran tunai di kasir Dapur Bucik saat mengambil pesanan atau di meja.
                        @else
                            Selesaikan pembayaran dalam batas waktu berikut agar dapur segera memproses pesanan Anda.
                        @endif
                    </p>
                </div>

                <div class="bg-[#12261F] border border-[#E29E31]/40 rounded-2xl px-5 py-4 text-center shrink-0">
                    <span class="text-[11px] uppercase tracking-wider text-[#FAF4E8]/70 block">Batas Waktu</span>
                    <span id="payment-countdown" class="font-serif text-3xl font-bold text-[#E29E31] tabular-nums">{{ $initMinutes }}:{{ $initSecs }}</span>
                </div>
            </div>
        </div>

        <div class="mt-6 bg-white bucik-card border border-[#1B362D]/15 border-l-4 border-l-[#E29E31] p-6 shadow-xs">
            <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-[#1B362D]/10">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wide text-[#1B362D]/60">Total yang Harus Dibayar</span>
                    <p class="font-serif text-2xl sm:text-3xl font-bold text-[#1B362D]">
                        Rp {{ number_format((float) $order->total_harga, 0, ',', '.') }}
                    </p>
                </div>

                <div class="inline-flex rounded-xl bg-[#FAF4E8] p-1 border border-[#1B362D]/10">
                    <button type="button" data-pay-tab="qris"
                            class="pay-tab-btn px-4 py-2 rounded-lg text-xs font-bold cursor-pointer {{ $defaultTab === 'qris' ? 'bg-[#1B362D] text-[#FAF4E8]' : 'text-[#1B362D]' }}">
                        Instruksi QRIS
                    </button>
                    <button type="button" data-pay-tab="transfer"
                            class="pay-tab-btn px-4 py-2 rounded-lg text-xs font-bold cursor-pointer {{ $defaultTab === 'transfer' ? 'bg-[#1B362D] text-[#FAF4E8]' : 'text-[#1B362D]' }}">
                        Transfer Bank
                    </button>
                </div>
            </div>

            <div id="pay-panel-qris" class="{{ $defaultTab === 'qris' ? '' : 'hidden' }} pt-6 grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                <div class="md:col-span-5 flex flex-col items-center justify-center p-5 rounded-2xl bg-[#FAF4E8] border border-[#1B362D]/15">
                    <svg class="w-44 h-44 text-[#1B362D]" viewBox="0 0 120 120" fill="none" aria-label="Kode QRIS Dapur Bucik">
                        <rect width="120" height="120" rx="8" fill="#FFFFFF"/>
                        <rect x="10" y="10" width="28" height="28" stroke="#1B362D" stroke-width="5"/>
                        <rect x="18" y="18" width="12" height="12" fill="#1B362D"/>
                        <rect x="82" y="10" width="28" height="28" stroke="#1B362D" stroke-width="5"/>
                        <rect x="90" y="18" width="12" height="12" fill="#1B362D"/>
                        <rect x="10" y="82" width="28" height="28" stroke="#1B362D" stroke-width="5"/>
                        <rect x="18" y="90" width="12" height="12" fill="#1B362D"/>
                        <rect x="46" y="12" width="8" height="8" fill="#1B362D"/>
                        <rect x="62" y="12" width="12" height="8" fill="#1B362D"/>
                        <rect x="48" y="26" width="10" height="10" fill="#1B362D"/>
                        <rect x="66" y="30" width="8" height="14" fill="#1B362D"/>
                        <rect x="12" y="48" width="10" height="12" fill="#1B362D"/>
                        <rect x="28" y="52" width="14" height="8" fill="#1B362D"/>
                        <rect x="50" y="50" width="20" height="20" rx="3" fill="#E29E31"/>
                        <rect x="78" y="50" width="12" height="10" fill="#1B362D"/>
                        <rect x="98" y="48" width="12" height="14" fill="#1B362D"/>
                        <rect x="48" y="78" width="12" height="10" fill="#1B362D"/>
                        <rect x="68" y="76" width="14" height="12" fill="#1B362D"/>
                        <rect x="90" y="78" width="18" height="10" fill="#1B362D"/>
                        <rect x="48" y="96" width="16" height="14" fill="#1B362D"/>
                        <rect x="74" y="96" width="10" height="14" fill="#1B362D"/>
                        <rect x="92" y="94" width="16" height="16" fill="#1B362D"/>
                    </svg>
                    <span class="mt-3 text-xs font-bold text-[#1B362D]">NMID: ID102026DAPURBUCIK</span>
                </div>

                <div class="md:col-span-7 space-y-3 text-sm text-[#1B362D]/80">
                    <h2 class="font-serif text-xl font-bold text-[#1B362D]">Cara Pembayaran QRIS</h2>
                    <ol class="list-decimal list-inside space-y-2">
                        <li>Buka aplikasi dompet digital (GoPay, OVO, DANA, ShopeePay) atau mobile banking Anda.</li>
                        <li>Pilih menu <strong>Scan / Bayar QRIS</strong> dan arahkan kamera ke kode QR di samping.</li>
                        <li>Pastikan nama merchant tertera <strong>Dapur Bucik</strong> dan nominal sesuai sebesar <strong>Rp {{ number_format((float) $order->total_harga, 0, ',', '.') }}</strong>.</li>
                        <li>Setelah membayar, klik tombol <strong>Saya Sudah Bayar</strong> di bawah ini.</li>
                    </ol>
                </div>
            </div>

            <div id="pay-panel-transfer" class="{{ $defaultTab === 'transfer' ? '' : 'hidden' }} pt-6 space-y-4">
                <h2 class="font-serif text-xl font-bold text-[#1B362D]">Rekening Resmi Dapur Bucik</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl bg-[#FAF4E8] border border-[#1B362D]/15 flex items-center justify-between gap-3">
                        <div>
                            <span class="text-xs font-bold uppercase text-[#CC8A22]">Bank BCA</span>
                            <p class="font-serif text-lg font-bold text-[#1B362D]">5420 8891 22</p>
                            <p class="text-xs text-[#1B362D]/70">a.n. Dapur Bucik Kuliner</p>
                        </div>
                        <button type="button"
                                onclick="navigator.clipboard?.writeText('5420889122'); window.showBucikToast && window.showBucikToast('Nomor rekening BCA disalin.');"
                                class="px-3 py-1.5 rounded-lg bg-white border border-[#1B362D]/20 text-xs font-bold hover:border-[#E29E31] cursor-pointer">
                            Salin
                        </button>
                    </div>

                    <div class="p-4 rounded-xl bg-[#FAF4E8] border border-[#1B362D]/15 flex items-center justify-between gap-3">
                        <div>
                            <span class="text-xs font-bold uppercase text-[#CC8A22]">Bank Mandiri</span>
                            <p class="font-serif text-lg font-bold text-[#1B362D]">1240 0091 8877 1</p>
                            <p class="text-xs text-[#1B362D]/70">a.n. Dapur Bucik Kuliner</p>
                        </div>
                        <button type="button"
                                onclick="navigator.clipboard?.writeText('1240009188771'); window.showBucikToast && window.showBucikToast('Nomor rekening Mandiri disalin.');"
                                class="px-3 py-1.5 rounded-lg bg-white border border-[#1B362D]/20 text-xs font-bold hover:border-[#E29E31] cursor-pointer">
                            Salin
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-[#1B362D]/10 flex flex-col sm:flex-row gap-3">
                @if ($statusBayar === 'menunggu')
                    <form method="POST" action="{{ route('user.pesanan.bayar', $order->id) }}" class="flex-1">
                        @csrf
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center py-3.5 px-5 rounded-xl bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] font-bold text-sm transition shadow-xs cursor-pointer">
                            Saya Sudah Bayar &bull; Lacak Pesanan &rarr;
                        </button>
                    </form>
                @else
                    <a href="{{ route('user.pesanan.lacak', $order->id) }}"
                       class="flex-1 inline-flex items-center justify-center py-3.5 px-5 rounded-xl bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] font-bold text-sm transition shadow-xs">
                        Lacak Status Pesanan &rarr;
                    </a>
                @endif

                <a href="{{ route('user.riwayat.index') }}"
                   class="inline-flex items-center justify-center py-3.5 px-5 rounded-xl border border-[#1B362D]/20 text-[#1B362D] font-semibold text-sm hover:bg-[#FAF4E8] transition">
                    Lihat Riwayat Pesanan
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        let remainingSeconds = {{ (int) $remainingSeconds }};
        const countdownEl = document.getElementById('payment-countdown');

        if (remainingSeconds > 0) {
            const interval = setInterval(() => {
                remainingSeconds--;
                if (remainingSeconds <= 0) {
                    clearInterval(interval);
                    if (countdownEl) countdownEl.textContent = '00:00';
                    return;
                }
                const m = String(Math.floor(remainingSeconds / 60)).padStart(2, '0');
                const s = String(remainingSeconds % 60).padStart(2, '0');
                if (countdownEl) countdownEl.textContent = m + ':' + s;
            }, 1000);
        }

        const tabBtns = document.querySelectorAll('.pay-tab-btn');
        const qrisPanel = document.getElementById('pay-panel-qris');
        const transferPanel = document.getElementById('pay-panel-transfer');

        tabBtns.forEach((btn) => {
            btn.addEventListener('click', function () {
                const tab = this.dataset.payTab;
                tabBtns.forEach((b) => {
                    b.classList.remove('bg-[#1B362D]', 'text-[#FAF4E8]');
                    b.classList.add('text-[#1B362D]');
                });
                this.classList.add('bg-[#1B362D]', 'text-[#FAF4E8]');
                this.classList.remove('text-[#1B362D]');

                qrisPanel?.classList.toggle('hidden', tab !== 'qris');
                transferPanel?.classList.toggle('hidden', tab !== 'transfer');
            });
        });
    })();
</script>
@endpush
