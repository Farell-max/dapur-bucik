@extends('layouts.user')

@section('title', 'Checkout Pesanan â€” Dapur Bucik')

@section('content')
    @php
        $defaultReception = old('metode_penerimaan', 'dine-in');
        $defaultPayment = old('metode_pembayaran', 'qris');
        $defaultMejaId = old('meja_id', $selectedMejaId ?? ($tables->first()?->id));
        $defaultAlamatId = old('alamat_id', $addresses->first()?->id);
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        <div class="mb-6">
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#1B362D]">Checkout Pesanan</h1>
            <p class="text-sm text-[#1B362D]/70 mt-1">Pilih metode penerimaan pesanan serta cara pembayaran yang paling sesuai.</p>
        </div>

        <form method="POST" action="{{ route('user.checkout.store') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            @csrf

            <div class="lg:col-span-8 space-y-6">
                <section class="bg-white bucik-card border border-[#1B362D]/15 border-l-4 border-l-[#1B362D] p-5 sm:p-6 shadow-xs">
                    <h2 class="font-serif text-xl font-bold text-[#1B362D]">1. Metode Penerimaan</h2>
                    <p class="text-xs sm:text-sm text-[#1B362D]/70 mt-1">Tentukan bagaimana pesanan Anda akan diterima.</p>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="reception-card relative flex flex-col p-4 rounded-xl border border-[#1B362D]/20 bg-white cursor-pointer transition">
                            <input type="radio" name="metode_penerimaan" value="dine-in" {{ $defaultReception === 'dine-in' ? 'checked' : '' }} class="sr-only">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#CC8A22]">Makan di Tempat</span>
                            <span class="font-serif text-lg font-bold text-[#1B362D] mt-1">Dine-in</span>
                            <span class="text-xs text-[#1B362D]/70 mt-1">Disajikan hangat langsung ke meja pilihan Anda.</span>
                        </label>

                        <label class="reception-card relative flex flex-col p-4 rounded-xl border border-[#1B362D]/20 bg-white hover:border-[#E29E31] cursor-pointer transition">
                            <input type="radio" name="metode_penerimaan" value="pickup" {{ $defaultReception === 'pickup' ? 'checked' : '' }} class="sr-only">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#CC8A22]">Ambil Sendiri</span>
                            <span class="font-serif text-lg font-bold text-[#1B362D] mt-1">Pickup</span>
                            <span class="text-xs text-[#1B362D]/70 mt-1">Ambil langsung di kasir dapur saat pesanan siap.</span>
                        </label>

                        <label class="reception-card relative flex flex-col p-4 rounded-xl border border-[#1B362D]/20 bg-white hover:border-[#E29E31] cursor-pointer transition">
                            <input type="radio" name="metode_penerimaan" value="kurir_mandiri" {{ $defaultReception === 'kurir_mandiri' ? 'checked' : '' }} class="sr-only">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#CC8A22]">Antar Dapur</span>
                            <span class="font-serif text-lg font-bold text-[#1B362D] mt-1">Kurir Mandiri</span>
                            <span class="text-xs text-[#1B362D]/70 mt-1">Diantar kurir internal Dapur Bucik ke alamat Anda.</span>
                        </label>
                    </div>

                    <div id="panel-dine-in" class="mt-5 p-4 rounded-xl bg-[#FAF4E8] border border-[#1B362D]/15">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <label for="meja_id" class="block text-xs font-bold uppercase tracking-wide text-[#1B362D]">
                                Pilih Nomor Meja
                            </label>
                            @if ($selectedMejaId)
                                <span class="bucik-stamp border-[#E29E31] bg-[#1B362D] text-[#E29E31] text-[10px]">
                                    Terpilih dari Scan QR Meja
                                </span>
                            @endif
                        </div>
                        <select id="meja_id" name="meja_id"
                                class="w-full px-4 py-2.5 rounded-xl border border-[#1B362D]/20 bg-white text-sm font-medium focus:outline-none focus:border-[#E29E31]">
                            @foreach ($tables as $table)
                                <option value="{{ $table->id }}" {{ (string) $defaultMejaId === (string) $table->id ? 'selected' : '' }}>
                                    {{ $table->nomor_meja }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="panel-pickup" class="hidden mt-5 p-4 rounded-xl bg-[#FAF4E8] border border-[#1B362D]/15 text-xs sm:text-sm text-[#1B362D]/80">
                        <strong class="font-bold text-[#1B362D] block mb-1">Lokasi Pengambilan Pesanan:</strong>
                        Dapur Bucik Pusat â€” Jl. Melati Kusuma No. 24, Jakarta Selatan (Estimasi siap 15â€“20 menit setelah dikonfirmasi).
                    </div>

                    <div id="panel-kurir" class="hidden mt-5 p-4 rounded-xl bg-[#FAF4E8] border border-[#1B362D]/15 space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <label for="alamat_id" class="block text-xs font-bold uppercase tracking-wide text-[#1B362D]">
                                Pilih Alamat Pengiriman Tersimpan
                            </label>
                            <a href="{{ route('user.profil.index', ['tab' => 'buku-alamat']) }}"
                               class="text-xs font-bold text-[#CC8A22] hover:underline">
                                + Kelola Alamat
                            </a>
                        </div>

                        @if ($addresses->isNotEmpty())
                            <select id="alamat_id" name="alamat_id"
                                    class="w-full px-4 py-2.5 rounded-xl border border-[#1B362D]/20 bg-white text-sm font-medium focus:outline-none focus:border-[#E29E31]">
                                @foreach ($addresses as $address)
                                    <option value="{{ $address->id }}" {{ (string) $defaultAlamatId === (string) $address->id ? 'selected' : '' }}>
                                        {{ $address->label }} â€” {{ $address->detail_alamat }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900">
                                Anda belum memiliki alamat tersimpan. Silakan tambahkan alamat di
                                <a href="{{ route('user.profil.index', ['tab' => 'buku-alamat']) }}" class="font-bold underline">Buku Alamat Profil</a>.
                            </div>
                        @endif

                        <p class="text-xs text-[#1B362D]/70">
                            Catatan Kurir Mandiri: Pengantaran area sekitar dapur tanpa biaya tambahan pada nota pesanan. Pembayaran wajib non-tunai (QRIS / Transfer Bank).
                        </p>
                    </div>
                </section>

                <section class="bg-white bucik-card border border-[#1B362D]/15 border-l-4 border-l-[#E29E31] p-5 sm:p-6 shadow-xs">
                    <h2 class="font-serif text-xl font-bold text-[#1B362D]">2. Cara Pembayaran</h2>
                    <p class="text-xs sm:text-sm text-[#1B362D]/70 mt-1">Pilih metode pembayaran yang tersedia untuk pesanan Anda.</p>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="payment-card relative flex flex-col p-4 rounded-xl border border-[#1B362D]/20 bg-white cursor-pointer transition">
                            <input type="radio" name="metode_pembayaran" value="qris" {{ $defaultPayment === 'qris' ? 'checked' : '' }} class="sr-only">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#CC8A22]">Instan</span>
                            <span class="font-serif text-lg font-bold text-[#1B362D] mt-1">QRIS</span>
                            <span class="text-xs text-[#1B362D]/70 mt-1">Scan kode QR dari semua e-wallet &amp; m-banking.</span>
                        </label>

                        <label class="payment-card relative flex flex-col p-4 rounded-xl border border-[#1B362D]/20 bg-white hover:border-[#E29E31] cursor-pointer transition">
                            <input type="radio" name="metode_pembayaran" value="transfer_bank" {{ $defaultPayment === 'transfer_bank' ? 'checked' : '' }} class="sr-only">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#CC8A22]">Manual / VA</span>
                            <span class="font-serif text-lg font-bold text-[#1B362D] mt-1">Transfer Bank</span>
                            <span class="text-xs text-[#1B362D]/70 mt-1">Transfer ke rekening resmi BCA / Bank Mandiri.</span>
                        </label>

                        <label id="card-bayar-tunai"
                               class="payment-card relative flex flex-col p-4 rounded-xl border border-[#1B362D]/20 bg-white hover:border-[#E29E31] cursor-pointer transition">
                            <input id="radio-bayar-tunai" type="radio" name="metode_pembayaran" value="bayar_di_tempat" {{ $defaultPayment === 'bayar_di_tempat' ? 'checked' : '' }} class="sr-only">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#CC8A22]">Kasir</span>
                            <span class="font-serif text-lg font-bold text-[#1B362D] mt-1">Bayar Tunai</span>
                            <span id="tunai-helper" class="text-xs text-[#1B362D]/70 mt-1">Aktif khusus untuk Dine-in &amp; Pickup.</span>
                        </label>
                    </div>
                </section>
            </div>

            <aside class="lg:col-span-4 bg-[#1B362D] text-[#FAF4E8] bucik-card p-6 shadow-md border-t-4 border-[#E29E31] sticky top-24">
                <h2 class="font-serif text-xl font-bold">Nota Pesanan</h2>
                <p class="text-xs text-[#FAF4E8]/70 mt-1">Ringkasan akhir dihitung murni dari subtotal item.</p>

                <div class="mt-4 divide-y divide-[#FAF4E8]/15 max-h-72 overflow-y-auto pr-1">
                    @foreach ($items as $item)
                        <div class="py-3 flex items-start justify-between gap-3 text-sm">
                            <div class="min-w-0">
                                <p class="font-semibold text-[#FAF4E8]">{{ $item->qty }}x {{ $item->menu?->nama }}</p>
                                @if (! empty($item->catatan))
                                    <p class="text-[11px] text-[#FAF4E8]/65 mt-0.5">Catatan: {{ $item->catatan }}</p>
                                @endif
                            </div>
                            <span class="font-semibold text-[#FAF4E8] shrink-0">
                                Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-4 border-t border-[#FAF4E8]/20 space-y-2 text-xs text-[#FAF4E8]/80">
                    <div class="flex justify-between">
                        <span>Metode Penerimaan</span>
                        <span id="checkout-summary-reception" class="font-bold text-[#FAF4E8]">Dine-in</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Cara Bayar</span>
                        <span id="checkout-summary-payment" class="font-bold text-[#FAF4E8]">QRIS</span>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-[#FAF4E8]/20 flex items-baseline justify-between">
                    <span class="text-sm font-semibold">Total Harga</span>
                    <span class="font-serif text-2xl font-bold text-[#E29E31]">
                        Rp {{ number_format($totalHarga, 0, ',', '.') }}
                    </span>
                </div>

                <button type="submit"
                        class="mt-6 w-full inline-flex items-center justify-center gap-2 py-3.5 px-5 rounded-xl bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] font-bold text-sm transition shadow-sm cursor-pointer">
                    Buat Pesanan Sekarang &rarr;
                </button>
            </aside>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const receptionRadios = document.querySelectorAll('input[name="metode_penerimaan"]');
        const paymentRadios = document.querySelectorAll('input[name="metode_pembayaran"]');
        const panelDineIn = document.getElementById('panel-dine-in');
        const panelPickup = document.getElementById('panel-pickup');
        const panelKurir = document.getElementById('panel-kurir');
        const cardTunai = document.getElementById('card-bayar-tunai');
        const radioTunai = document.getElementById('radio-bayar-tunai');
        const tunaiHelper = document.getElementById('tunai-helper');
        const summaryReception = document.getElementById('checkout-summary-reception');
        const summaryPayment = document.getElementById('checkout-summary-payment');

        const receptionLabels = {
            'dine-in': 'Dine-in',
            'pickup': 'Pickup',
            'kurir_mandiri': 'Kurir Mandiri',
        };
        const paymentLabels = {
            'qris': 'QRIS',
            'transfer_bank': 'Transfer Bank',
            'bayar_di_tempat': 'Bayar Tunai',
        };

        function syncSelectionStyles() {
            document.querySelectorAll('.reception-card').forEach((card) => {
                const checked = card.querySelector('input')?.checked;
                card.classList.toggle('border-2', Boolean(checked));
                card.classList.toggle('border-[#1B362D]', Boolean(checked));
                card.classList.toggle('bg-[#FAF4E8]/70', Boolean(checked));
                card.classList.toggle('border', !checked);
                card.classList.toggle('border-[#1B362D]/20', !checked);
                card.classList.toggle('bg-white', !checked);
            });

            document.querySelectorAll('.payment-card').forEach((card) => {
                const input = card.querySelector('input');
                const checked = input?.checked;
                const disabled = input?.disabled;
                card.classList.toggle('border-2', Boolean(checked && !disabled));
                card.classList.toggle('border-[#1B362D]', Boolean(checked && !disabled));
                card.classList.toggle('bg-[#FAF4E8]/70', Boolean(checked && !disabled));
                card.classList.toggle('border', !(checked && !disabled));
                card.classList.toggle('border-[#1B362D]/20', !(checked && !disabled));
                card.classList.toggle('bg-white', !(checked && !disabled));
            });
        }

        function handleReceptionChange() {
            const val = document.querySelector('input[name="metode_penerimaan"]:checked')?.value || 'dine-in';

            panelDineIn?.classList.toggle('hidden', val !== 'dine-in');
            panelPickup?.classList.toggle('hidden', val !== 'pickup');
            panelKurir?.classList.toggle('hidden', val !== 'kurir_mandiri');

            const allowCash = val === 'dine-in' || val === 'pickup';
            if (radioTunai) {
                radioTunai.disabled = !allowCash;
                if (!allowCash && radioTunai.checked) {
                    const qrisRadio = document.querySelector('input[name="metode_pembayaran"][value="qris"]');
                    if (qrisRadio) qrisRadio.checked = true;
                }
            }

            if (cardTunai) {
                cardTunai.classList.toggle('opacity-50', !allowCash);
                cardTunai.classList.toggle('cursor-not-allowed', !allowCash);
                cardTunai.classList.toggle('cursor-pointer', allowCash);
            }

            if (tunaiHelper) {
                tunaiHelper.textContent = allowCash
                    ? 'Aktif khusus untuk Dine-in & Pickup.'
                    : 'Tidak tersedia untuk Kurir Mandiri.';
            }

            if (summaryReception) summaryReception.textContent = receptionLabels[val] || val;
            const activePay = document.querySelector('input[name="metode_pembayaran"]:checked')?.value || 'qris';
            if (summaryPayment) summaryPayment.textContent = paymentLabels[activePay] || activePay;

            syncSelectionStyles();
        }

        receptionRadios.forEach((r) => r.addEventListener('change', handleReceptionChange));
        paymentRadios.forEach((r) => r.addEventListener('change', () => {
            const activePay = document.querySelector('input[name="metode_pembayaran"]:checked')?.value || 'qris';
            if (summaryPayment) summaryPayment.textContent = paymentLabels[activePay] || activePay;
            syncSelectionStyles();
        }));

        handleReceptionChange();
    })();
</script>
@endpush
