@extends('layouts.user')

@section('title', $menu->nama . ' â€” Dapur Bucik')

@section('content')
    @php
        $isHabis = $menu->status_ketersediaan === 'habis';
        $hasPromo = ! empty($menu->harga_promo);
        $activePrice = $menu->effectivePrice();
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        <nav class="text-xs sm:text-sm text-[#1B362D]/70 mb-6 flex flex-wrap items-center gap-2" aria-label="Breadcrumb">
            <a href="{{ route('user.home') }}" class="hover:text-[#1B362D]">Beranda</a>
            <span>/</span>
            <a href="{{ route('user.menu.index') }}" class="hover:text-[#1B362D]">Menu</a>
            <span>/</span>
            <span class="font-semibold text-[#1B362D]">{{ $menu->nama }}</span>
        </nav>

        <div class="bg-white bucik-card border border-[#1B362D]/15 border-l-4 border-l-[#E29E31] p-5 sm:p-8 shadow-xs">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <div class="lg:col-span-6 relative rounded-2xl overflow-hidden bg-[#F3E9D6] border border-[#1B362D]/10 aspect-[4/3]">
                    <img src="{{ $menu->fotoUrl() }}"
                         alt="{{ $menu->nama }}"
                         width="800"
                         height="600"
                         fetchpriority="high"
                         decoding="async"
                         class="w-full h-full object-cover {{ $isHabis ? 'opacity-55 grayscale' : '' }}">

                    @if ($hasPromo && ! $isHabis)
                        <span class="absolute top-4 left-4 bucik-stamp border-[#E29E31] bg-[#1B362D]/95 text-[#E29E31] text-xs shadow">
                            HARGA PROMO
                        </span>
                    @endif

                    @if ($isHabis)
                        <div class="absolute inset-0 bg-[#1B362D]/45 flex items-center justify-center">
                            <span class="bucik-stamp border-red-400 bg-white/95 text-red-700 text-sm px-5 py-2 shadow">
                                STOK HABIS
                            </span>
                        </div>
                    @endif
                </div>

                <div class="lg:col-span-6 space-y-5">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="bucik-stamp border-[#1B362D]/70 bg-[#FAF4E8] text-[#1B362D] text-[11px]">
                            {{ $menu->kategori?->nama_kategori ?? 'Umum' }}
                        </span>
                        <span class="bucik-stamp border-[#E29E31] bg-[#FAF4E8] text-[#1B362D] text-[11px]">
                            <span class="text-[#E29E31]">&#9733;</span>
                            {{ number_format((float) $menu->rating, 1) }} ({{ (int) ($menu->total_terjual ?? 0) }} terjual)
                        </span>
                        <span class="bucik-stamp text-[11px] {{ $isHabis ? 'border-red-500 bg-red-50 text-red-700' : 'border-emerald-700 bg-emerald-50 text-emerald-800' }}">
                            {{ $isHabis ? 'Habis' : 'Tersedia' }}
                        </span>
                    </div>

                    <div>
                        <h1 class="font-serif text-3xl sm:text-4xl font-bold text-[#1B362D]">{{ $menu->nama }}</h1>
                        <div class="mt-3 flex items-baseline gap-3">
                            <span class="text-2xl sm:text-3xl font-extrabold text-[#1B362D]">
                                Rp {{ number_format($activePrice, 0, ',', '.') }}
                            </span>
                            @if ($hasPromo)
                                <span class="text-base text-[#1B362D]/45 line-through">
                                    Rp {{ number_format((float) $menu->harga, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <p class="text-sm sm:text-base text-[#1B362D]/80 leading-relaxed">
                        {{ $menu->deskripsi }}
                    </p>

                    <form method="POST" action="{{ route('user.keranjang.store') }}" class="pt-2 border-t border-[#1B362D]/10 space-y-4">
                        @csrf
                        <input type="hidden" name="menu_id" value="{{ $menu->id }}">

                        <div>
                            <label for="catatan-khusus" class="block text-xs font-bold uppercase tracking-wide text-[#1B362D]/80 mb-1.5">
                                Catatan Khusus (Opsional)
                            </label>
                            <textarea id="catatan-khusus" name="catatan" rows="3"
                                      placeholder="Contoh: sambal terasi dipisah, tanpa bawang goreng, nasi setengah porsi..."
                                      class="w-full px-4 py-2.5 rounded-xl border border-[#1B362D]/20 bg-[#FAF4E8]/40 text-sm focus:outline-none focus:border-[#E29E31] focus:ring-2 focus:ring-[#E29E31]/30">{{ old('catatan') }}</textarea>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <div>
                                <span class="block text-xs font-bold uppercase tracking-wide text-[#1B362D]/80 mb-1.5">Jumlah</span>
                                <div class="inline-flex items-center rounded-xl border border-[#1B362D]/20 bg-[#FAF4E8]/50">
                                    <button type="button" id="qty-minus"
                                            class="w-10 h-11 font-bold text-lg text-[#1B362D] hover:bg-[#1B362D]/10 rounded-l-xl cursor-pointer"
                                            aria-label="Kurangi jumlah">&minus;</button>
                                    <input id="detail-qty" name="qty" type="number" min="1" max="99" value="{{ old('qty', 1) }}" readonly
                                           class="w-12 text-center font-bold text-sm bg-transparent focus:outline-none">
                                    <button type="button" id="qty-plus"
                                            class="w-10 h-11 font-bold text-lg text-[#1B362D] hover:bg-[#1B362D]/10 rounded-r-xl cursor-pointer"
                                            aria-label="Tambah jumlah">+</button>
                                </div>
                            </div>

                            <div class="flex-1 sm:pt-5">
                                @if ($isHabis)
                                    <button type="button" disabled
                                            class="w-full py-3.5 px-6 rounded-xl bg-stone-200 text-stone-500 font-bold text-sm cursor-not-allowed">
                                        Stok Sedang Habis
                                    </button>
                                @else
                                    <button type="submit"
                                            class="w-full py-3.5 px-6 rounded-xl bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] font-bold text-sm transition shadow-sm cursor-pointer">
                                        Tambah ke Keranjang &bull; Rp <span id="detail-subtotal">{{ number_format($activePrice, 0, ',', '.') }}</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if ($relatedMenus->isNotEmpty())
            <section class="mt-12">
                <h2 class="font-serif text-2xl font-bold text-[#1B362D] mb-5">Menu Lainnya dari Dapur Bucik</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($relatedMenus as $index => $item)
                        <x-menu-card :menu="$item" :accent="$index % 2 === 0 ? 'green' : 'gold'" class="h-full" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const unitPrice = {{ (int) round($activePrice) }};
        const qtyInput = document.getElementById('detail-qty');
        const subtotalEl = document.getElementById('detail-subtotal');
        const minusBtn = document.getElementById('qty-minus');
        const plusBtn = document.getElementById('qty-plus');

        function updateTotal(nextQty) {
            const clamped = Math.min(99, Math.max(1, nextQty));
            if (qtyInput) qtyInput.value = clamped;
            if (subtotalEl) subtotalEl.textContent = (clamped * unitPrice).toLocaleString('id-ID');
        }

        minusBtn?.addEventListener('click', () => updateTotal(Number(qtyInput?.value || 1) - 1));
        plusBtn?.addEventListener('click', () => updateTotal(Number(qtyInput?.value || 1) + 1));
    })();
</script>
@endpush
