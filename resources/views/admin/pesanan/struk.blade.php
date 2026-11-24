@section('title', 'Struk Dapur #INV-' . str_pad((string) $order->id, 4, '0', STR_PAD_LEFT))
<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
</head>
<body class="bg-[#FAF4E8] text-[#1B362D] p-4 sm:p-8 antialiased">
    @php
        $kode = '#INV-' . str_pad((string) $order->id, 4, '0', STR_PAD_LEFT);
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

    <div class="max-w-sm mx-auto bg-white rounded-2xl border border-[#1B362D]/20 p-6 shadow-sm">
        <div class="text-center border-b border-dashed border-[#1B362D]/30 pb-4">
            <h1 class="font-serif text-2xl font-bold text-[#1B362D]">DAPUR BUCIK</h1>
            <p class="text-xs text-[#1B362D]/70 mt-0.5">Struk Pesanan Dapur</p>
            <p class="mt-2 text-sm font-extrabold">{{ $kode }}</p>
            <p class="text-xs text-[#1B362D]/70">{{ $order->created_at?->format('d M Y, H:i') }} WIB</p>
        </div>

        <div class="py-3 border-b border-dashed border-[#1B362D]/30 text-xs space-y-1">
            <div class="flex justify-between">
                <span class="text-[#1B362D]/70">Pelanggan</span>
                <span class="font-bold">{{ $order->user?->nama ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-[#1B362D]/70">Layanan</span>
                <span class="font-bold">{{ $receptionLabels[$order->metode_penerimaan] ?? $order->metode_penerimaan }}</span>
            </div>
            @if ($order->meja)
                <div class="flex justify-between">
                    <span class="text-[#1B362D]/70">Meja</span>
                    <span class="font-bold">{{ $order->meja->nomor_meja }}</span>
                </div>
            @endif
            <div class="flex justify-between">
                <span class="text-[#1B362D]/70">Pembayaran</span>
                <span class="font-bold">
                    {{ $paymentLabels[$order->metode_pembayaran] ?? $order->metode_pembayaran }}
                    ({{ strtoupper($order->pembayaran?->status_pembayaran ?? 'menunggu') }})
                </span>
            </div>
        </div>

        <div class="py-4 border-b border-dashed border-[#1B362D]/30 space-y-2.5 text-xs">
            @foreach ($order->detailPesanan as $detail)
                <div>
                    <div class="flex justify-between font-bold text-[#1B362D]">
                        <span>{{ $detail->qty }}x {{ $detail->menu?->nama ?? 'Menu' }}</span>
                        <span>Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if (! empty($detail->catatan))
                        <p class="text-[11px] text-[#CC8A22] mt-0.5">Catatan: {{ $detail->catatan }}</p>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="pt-4 flex justify-between items-baseline">
            <span class="text-xs font-bold uppercase tracking-wider">Total</span>
            <span class="font-serif text-xl font-bold text-[#1B362D]">
                Rp {{ number_format((float) $order->total_harga, 0, ',', '.') }}
            </span>
        </div>

        <div class="no-print mt-6 flex gap-2">
            <button type="button" onclick="window.print()"
                    class="flex-1 py-2.5 rounded-xl bg-[#E29E31] text-[#1B362D] text-xs font-bold cursor-pointer">
                Cetak Struk
            </button>
            <a href="{{ route('admin.pesanan.index') }}"
               class="px-4 py-2.5 rounded-xl border border-[#1B362D]/20 text-xs font-bold text-[#1B362D]">
                Kembali
            </a>
        </div>
    </div>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>
