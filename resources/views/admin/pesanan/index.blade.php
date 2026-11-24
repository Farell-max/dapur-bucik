@extends('layouts.admin')

@section('title', 'Manajemen Pesanan â€” Dapur Bucik')

@section('content')
    <div class="pb-4 mb-5 border-b border-dashed border-[#1B362D]/20">
        <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#1B362D]">Pesanan Masuk</h1>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.pesanan.index', ['tab' => 'perlu_diproses', 'q' => $filters['q']]) }}"
               class="px-4 py-2 rounded-lg text-xs transition {{ $filters['tab'] === 'perlu_diproses' && empty($filters['status']) ? 'font-bold bg-[#1B362D] text-[#FAF4E8]' : 'font-semibold bg-[#EDE5D5] text-[#1B362D] hover:bg-[#E2D8C3]' }}">
                Perlu Diproses ({{ $pendingCount }})
            </a>
            <a href="{{ route('admin.pesanan.index', ['tab' => 'semua', 'q' => $filters['q']]) }}"
               class="px-4 py-2 rounded-lg text-xs transition {{ $filters['tab'] === 'semua' && empty($filters['status']) ? 'font-bold bg-[#1B362D] text-[#FAF4E8]' : 'font-semibold bg-[#EDE5D5] text-[#1B362D] hover:bg-[#E2D8C3]' }}">
                Semua Pesanan
            </a>
        </div>

        <form method="GET" action="{{ route('admin.pesanan.index') }}" class="flex flex-wrap items-center gap-2">
            <input type="hidden" name="tab" value="semua">
            <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Cari pelanggan / ID..."
                   class="px-3.5 py-1.5 rounded-lg border border-[#1B362D]/15 bg-white text-xs">
            <select name="status" onchange="this.form.submit()"
                    class="px-3 py-1.5 rounded-lg border border-[#1B362D]/15 bg-white text-xs font-medium">
                <option value="">Semua Status</option>
                @foreach (['menunggu_konfirmasi', 'dimasak', 'siap_diambil', 'menunggu_driver', 'selesai', 'ditolak'] as $st)
                    <option value="{{ $st }}" {{ $filters['status'] === $st ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $st)) }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        @forelse ($orders as $order)
            @php
                $kode = '#INV-' . str_pad((string) $order->id, 4, '0', STR_PAD_LEFT);
                $borderColor = match ($order->status) {
                    'menunggu_konfirmasi' => 'border-l-[#E29E31]',
                    'ditolak' => 'border-l-red-600',
                    default => 'border-l-[#1B362D]',
                };
                $statusBayar = $order->pembayaran?->status_pembayaran ?? 'menunggu';
                $readyStatus = $order->metode_penerimaan === 'kurir_mandiri' ? 'menunggu_driver' : 'siap_diambil';
                $readyLabel = $order->metode_penerimaan === 'kurir_mandiri' ? 'Menunggu Driver' : 'Tandai Siap';
                $receptionBadge = match ($order->metode_penerimaan) {
                    'dine-in' => 'Dine-in' . ($order->meja ? ' (' . preg_replace('/\s*\(.*\)$/', '', $order->meja->nomor_meja) . ')' : ''),
                    'pickup' => 'Pickup',
                    default => 'Kurir Mandiri',
                };
            @endphp

            <article class="bg-white bucik-card border border-[#1B362D]/15 border-l-4 {{ $borderColor }} p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-3 pb-3.5 border-b border-dashed border-[#1B362D]/15">
                        <div>
                            <div class="flex flex-wrap items-center gap-2.5">
                                <span class="font-bold text-base text-[#1B362D]">{{ $kode }}</span>
                                <x-order-status-badge :status="$order->status" />
                            </div>
                            <p class="text-[11px] text-[#1B362D]/60 mt-1">
                                {{ $order->user?->nama ?? 'Pelanggan' }} ({{ $order->user?->no_hp ?? '-' }}) &bull; {{ $order->created_at?->format('H:i') }} WIB
                            </p>
                        </div>

                        <span class="bucik-stamp border-[#1B362D]/60 bg-[#FAF4E8] text-[#1B362D] text-[10px] shrink-0">
                            {{ $receptionBadge }}
                        </span>
                    </div>

                    <div class="py-3.5 space-y-2.5 text-xs sm:text-sm">
                        @foreach ($order->detailPesanan as $detail)
                            <div>
                                <div class="flex items-baseline justify-between gap-3">
                                    <span class="font-bold text-[#1B362D]">{{ $detail->qty }}x {{ $detail->menu?->nama ?? 'Menu' }}</span>
                                    <span class="font-bold text-[#1B362D] shrink-0">Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}</span>
                                </div>
                                @if (! empty($detail->catatan))
                                    <div class="mt-1 px-2.5 py-1 rounded bg-[#FAF4E8] text-[11px] text-[#1B362D]/70 inline-block">
                                        Catatan: {{ $detail->catatan }}
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        @if (! empty($order->alasan_tolak))
                            <p class="pt-1 text-xs text-red-700 font-semibold">
                                Alasan tolak: {{ $order->alasan_tolak }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="pt-3.5 border-t border-dashed border-[#1B362D]/15 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <span class="font-serif text-lg font-extrabold text-[#CC8A22]">
                            Rp {{ number_format((float) $order->total_harga, 0, ',', '.') }}
                        </span>
                        @if ($statusBayar !== 'lunas')
                            <span class="block text-[10px] font-bold text-amber-800 uppercase">Bayar: {{ $statusBayar }}</span>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @if ($statusBayar !== 'lunas' && $order->status !== 'ditolak')
                            <form method="POST" action="{{ route('admin.pesanan.konfirmasi-bayar', $order->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="px-2.5 py-1.5 rounded-lg border border-emerald-600 text-emerald-700 hover:bg-emerald-50 text-xs font-bold cursor-pointer">
                                    Verifikasi Bayar
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('admin.pesanan.struk', $order->id) }}" target="_blank"
                           title="Cetak Struk Dapur"
                           aria-label="Cetak Struk"
                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[#1B362D]/25 text-[#1B362D] hover:bg-[#FAF4E8] transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 9V4h12v5M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v6H6v-6Z"/>
                            </svg>
                        </a>

                        @if (! in_array($order->status, ['selesai', 'ditolak'], true))
                            @if ($order->status === 'menunggu_konfirmasi')
                                <button type="button"
                                        class="btn-reject-order px-3 py-1.5 bucik-btn border border-[#1B362D]/35 bg-white hover:bg-red-50 hover:text-red-700 text-xs font-bold text-[#1B362D] cursor-pointer"
                                        data-kode="{{ $kode }}"
                                        data-action="{{ route('admin.pesanan.update-status', $order->id) }}">
                                    Tolak
                                </button>

                                <form method="POST" action="{{ route('admin.pesanan.update-status', $order->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="dimasak">
                                    <button type="submit"
                                            class="px-3.5 py-1.5 bucik-btn bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] text-xs font-bold border border-[#1B362D]/40 shadow-[0_2px_0_#1B362D] cursor-pointer">
                                        Mulai Masak
                                    </button>
                                </form>
                            @elseif ($order->status === 'dimasak')
                                <form method="POST" action="{{ route('admin.pesanan.update-status', $order->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $readyStatus }}">
                                    <button type="submit"
                                            class="px-3.5 py-1.5 bucik-btn bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] text-xs font-bold border border-[#1B362D]/40 shadow-[0_2px_0_#1B362D] cursor-pointer">
                                        {{ $readyLabel }}
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.pesanan.update-status', $order->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="selesai">
                                    <button type="submit"
                                            class="px-3.5 py-1.5 bucik-btn bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold cursor-pointer">
                                        Selesai
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-[#1B362D]/10 p-10 text-center">
                <p class="font-serif text-xl font-bold text-[#1B362D]">Tidak Ada Pesanan</p>
                <p class="text-sm text-[#1B362D]/70 mt-1">Belum ada pesanan yang sesuai dengan filter saat ini.</p>
            </div>
        @endforelse
    </div>

    <div id="reject-modal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
        <form id="reject-form" method="POST" action="" class="bg-white rounded-2xl w-full max-w-md border-t-4 border-red-600 p-6 shadow-xl">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="ditolak">
            <h2 class="font-serif text-xl font-bold text-[#1B362D]">Tolak Pesanan <span id="reject-kode"></span></h2>
            <p class="text-xs text-[#1B362D]/70 mt-1">Masukkan alasan penolakan yang akan tampil kepada pelanggan.</p>
            <textarea name="alasan_tolak" rows="3" required placeholder="Contoh: Stok menu habis atau dapur sudah tutup."
                      class="mt-4 w-full px-3.5 py-2 rounded-xl border border-[#1B362D]/20 bg-[#FAF4E8]/40 text-sm"></textarea>
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" id="cancel-reject" class="px-4 py-2 rounded-xl border border-[#1B362D]/20 text-xs font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 text-white text-xs font-bold cursor-pointer">Konfirmasi Tolak</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const rejectModal = document.getElementById('reject-modal');
        const rejectForm = document.getElementById('reject-form');
        const rejectKode = document.getElementById('reject-kode');

        document.querySelectorAll('.btn-reject-order').forEach((btn) => {
            btn.addEventListener('click', function () {
                if (rejectForm) rejectForm.action = this.dataset.action || '';
                if (rejectKode) rejectKode.textContent = this.dataset.kode || '';
                rejectModal?.classList.remove('hidden');
            });
        });

        document.getElementById('cancel-reject')?.addEventListener('click', () => rejectModal?.classList.add('hidden'));
    })();
</script>
@endpush
