@extends('layouts.user')

@section('title', 'Lacak Pesanan #DB-' . str_pad((string) $order->id, 4, '0', STR_PAD_LEFT) . ' â€” Dapur Bucik')

@section('content')
    @php
        $kodePesanan = '#DB-' . str_pad((string) $order->id, 4, '0', STR_PAD_LEFT);
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

        $steps = [
            [
                'key' => 'menunggu_konfirmasi',
                'title' => 'Menunggu Konfirmasi',
                'desc' => 'Pesanan masuk dan menunggu verifikasi pembayaran serta penerimaan oleh dapur.',
            ],
            [
                'key' => 'dimasak',
                'title' => 'Dimasak',
                'desc' => 'Koki Dapur Bucik sedang memasak dan menyiapkan hidangan hangat Anda.',
            ],
            [
                'key' => 'siap',
                'title' => 'Siap Diambil / Menunggu Driver',
                'desc' => 'Pesanan telah selesai dikemas, siap disajikan atau diantar kurir mandiri.',
            ],
            [
                'key' => 'selesai',
                'title' => 'Selesai',
                'desc' => 'Pesanan telah diterima dengan baik. Selamat menikmati masakan Dapur Bucik!',
            ],
        ];

        $statusToStepIndex = match ($order->status) {
            'menunggu_konfirmasi' => 0,
            'dimasak' => 1,
            'siap_diambil', 'menunggu_driver' => 2,
            'selesai' => 3,
            default => -1,
        };
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div>
                <a href="{{ route('user.riwayat.index') }}" class="text-xs font-bold text-[#CC8A22] hover:underline">&larr; Kembali ke Riwayat Pesanan</a>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#1B362D] mt-1">Pelacakan Pesanan {{ $kodePesanan }}</h1>
                <p class="text-sm text-[#1B362D]/70">Dipesan pada {{ $order->created_at?->format('d M Y, H:i') }} WIB</p>
            </div>
            <x-order-status-badge :status="$order->status" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <section class="lg:col-span-7 bg-white bucik-card border border-[#1B362D]/15 border-l-4 border-l-[#E29E31] p-6 sm:p-8 shadow-xs">
                <h2 class="font-serif text-xl font-bold text-[#1B362D]">Status Proses Dapur</h2>
                @if ($order->estimasi_waktu_ambil)
                    <p class="mt-1 text-xs sm:text-sm text-[#1B362D]/75">
                        Estimasi siap: <strong class="text-[#1B362D]">{{ $order->estimasi_waktu_ambil->format('d M Y, H:i') }} WIB</strong>
                    </p>
                @endif

                @if ($order->status === 'ditolak')
                    <div class="mt-6 p-5 rounded-2xl bg-red-50 border border-red-200 text-red-900">
                        <h3 class="font-serif text-lg font-bold">Pesanan Ditolak</h3>
                        <p class="text-sm mt-1">Alasan penolakan: {{ $order->alasan_tolak ?? 'Pesanan tidak dapat diproses saat ini.' }}</p>
                    </div>
                @endif

                <ol class="mt-7 space-y-6">
                    @foreach ($steps as $idx => $step)
                        @php
                            $isDone = $statusToStepIndex > $idx;
                            $isCurrent = $statusToStepIndex === $idx;
                        @endphp
                        <li class="relative flex gap-4">
                            @if (! $loop->last)
                                <span class="absolute left-4 top-9 -bottom-6 w-0.5 {{ $isDone ? 'bg-[#1B362D]' : 'bg-[#1B362D]/15' }}" aria-hidden="true"></span>
                            @endif

                            <span class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center text-xs font-extrabold shrink-0 {{ $isDone ? 'bg-[#1B362D] text-[#FAF4E8]' : ($isCurrent ? 'bg-[#E29E31] text-[#1B362D] ring-4 ring-[#E29E31]/25' : 'bg-[#FAF4E8] border border-[#1B362D]/20 text-[#1B362D]/50') }}">
                                @if ($isDone)
                                    &#10003;
                                @else
                                    {{ $idx + 1 }}
                                @endif
                            </span>

                            <div class="pb-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-serif text-base sm:text-lg font-bold {{ $isCurrent || $isDone ? 'text-[#1B362D]' : 'text-[#1B362D]/45' }}">
                                        {{ $step['title'] }}
                                    </h3>
                                    @if ($isCurrent)
                                        <span class="bucik-stamp border-[#E29E31] bg-[#FAF4E8] text-[#CC8A22] text-[10px]">
                                            Tahap Saat Ini
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs sm:text-sm mt-1 {{ $isCurrent || $isDone ? 'text-[#1B362D]/75' : 'text-[#1B362D]/45' }}">
                                    {{ $step['desc'] }}
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </section>

            <aside class="lg:col-span-5 bg-[#1B362D] text-[#FAF4E8] bucik-card p-6 shadow-md border-t-4 border-[#E29E31]">
                <h2 class="font-serif text-xl font-bold">Info Penerimaan &amp; Daftar Nota</h2>

                <div class="mt-4 p-4 rounded-xl bg-[#12261F] border border-[#FAF4E8]/10 space-y-2 text-xs sm:text-sm">
                    <div class="flex justify-between gap-2">
                        <span class="text-[#FAF4E8]/70">Pemesan</span>
                        <span class="font-semibold">{{ $order->user?->nama }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-[#FAF4E8]/70">Metode Penerimaan</span>
                        <span class="font-bold text-[#E29E31]">{{ $receptionLabels[$order->metode_penerimaan] ?? $order->metode_penerimaan }}</span>
                    </div>
                    @if ($order->meja)
                        <div class="flex justify-between gap-2">
                            <span class="text-[#FAF4E8]/70">Nomor Meja</span>
                            <span class="font-semibold text-right">{{ $order->meja->nomor_meja }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between gap-2 pt-1 border-t border-[#FAF4E8]/10">
                        <span class="text-[#FAF4E8]/70">Pembayaran</span>
                        <span class="font-semibold">
                            {{ $paymentLabels[$order->metode_pembayaran] ?? $order->metode_pembayaran }}
                            ({{ ucfirst($order->pembayaran?->status_pembayaran ?? 'menunggu') }})
                        </span>
                    </div>
                </div>

                <div class="mt-5 divide-y divide-[#FAF4E8]/15">
                    @foreach ($order->detailPesanan as $detail)
                        <div class="py-3 flex items-start justify-between gap-3 text-sm">
                            <div>
                                <p class="font-semibold">{{ $detail->qty }}x {{ $detail->menu?->nama ?? 'Menu' }}</p>
                                @if (! empty($detail->catatan))
                                    <p class="text-[11px] text-[#FAF4E8]/65">Catatan: {{ $detail->catatan }}</p>
                                @endif
                            </div>
                            <span class="font-semibold shrink-0">Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-4 border-t border-[#FAF4E8]/20 flex items-baseline justify-between">
                    <span class="text-sm font-semibold">Total Pembayaran</span>
                    <span class="font-serif text-2xl font-bold text-[#E29E31]">
                        Rp {{ number_format((float) $order->total_harga, 0, ',', '.') }}
                    </span>
                </div>

                @if (($order->pembayaran?->status_pembayaran ?? 'menunggu') === 'menunggu' && $order->status !== 'ditolak')
                    <a href="{{ route('user.pesanan.konfirmasi', $order->id) }}"
                       class="mt-5 w-full inline-flex items-center justify-center py-3 px-4 rounded-xl bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] text-xs font-bold transition">
                        Lihat Instruksi / Konfirmasi Pembayaran
                    </a>
                @endif
            </aside>
        </div>
    </div>
@endsection
