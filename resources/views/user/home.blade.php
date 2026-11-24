@extends('layouts.user')

@section('title', 'Beranda Dapur Bucik')

@section('content')
    @php
        $promoWords = $promoMenu ? (preg_split('/\s+/', trim((string) $promoMenu->nama)) ?: []) : [];
        $promoInitials = implode('', array_map(fn (string $w): string => mb_strtoupper(mb_substr($w, 0, 1)), array_slice($promoWords, 0, 2))) ?: 'NK';
    @endphp

    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-8">
        <div class="relative overflow-hidden bucik-card bg-[#1B362D] text-[#FAF4E8] px-7 py-10 sm:px-12 sm:py-14 shadow-lg">
            <div class="hidden md:flex items-center justify-center absolute -right-14 top-1/2 -translate-y-1/2 w-[23rem] h-[23rem] lg:w-[27rem] lg:h-[27rem] rounded-full bg-[#B5893C] overflow-hidden select-none"
                 aria-hidden="true">
                @if ($promoMenu)
                    <img src="{{ $promoMenu->fotoUrl() }}"
                         alt="{{ $promoMenu->nama }}"
                         width="600"
                         height="600"
                         fetchpriority="high"
                         decoding="async"
                         class="w-full h-full object-cover opacity-90 mix-blend-multiply">
                    <span class="sr-only">{{ $promoInitials }}</span>
                @else
                    <span class="font-sans font-extrabold text-[8.5rem] lg:text-[10rem] leading-none tracking-tight text-[#1B362D] pr-10">
                        {{ $promoInitials }}
                    </span>
                @endif
            </div>

            <div class="relative z-10 max-w-lg space-y-5">
                <span class="bucik-stamp border-[#E29E31] text-[#E29E31] bg-transparent text-[11px] px-3.5 py-1.5">
                    PROMO SPESIAL JUMAT
                </span>

                <h1 class="font-serif text-3xl sm:text-4xl lg:text-[2.75rem] font-bold leading-[1.15] text-[#FAF4E8]">
                    Nasi Kuning Bumbu Rempah Khas Bucik
                </h1>

                <p class="text-sm sm:text-[15px] text-[#FAF4E8]/85 max-w-md leading-relaxed">
                    Rasakan kehangatan rempah alami. Gratis teh hangat pandan untuk setiap pemesanan makan di tempat atau ambil sendiri (Pickup).
                </p>

                <div class="pt-1">
                    <a href="{{ $promoMenu ? route('user.menu.show', $promoMenu->id) : route('user.menu.index') }}"
                       class="inline-flex items-center justify-center px-7 py-3.5 bucik-btn bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] font-serif text-base font-bold transition shadow-sm">
                        Pesan Sekarang
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-7">
        <div class="flex flex-wrap gap-3 pb-7 border-b border-dashed border-[#1B362D]/20" id="home-category-pills">
            <button type="button"
                    data-category="all"
                    class="home-pill px-5 py-2.5 bucik-btn text-xs sm:text-sm font-bold bg-[#1B362D] text-[#E29E31] border-b-2 border-[#E29E31] transition cursor-pointer">
                Semua Menu
            </button>
            @foreach ($categories as $category)
                <button type="button"
                        data-category="{{ $category->id }}"
                        class="home-pill px-5 py-2.5 bucik-btn text-xs sm:text-sm font-semibold bg-[#FAF4E8] text-[#1B362D] border border-[#1B362D]/15 hover:border-[#E29E31] hover:bg-white transition cursor-pointer">
                    {{ $category->nama_kategori }}
                </button>
            @endforeach
        </div>

        <div class="mt-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-serif text-2xl sm:text-3xl font-bold text-[#1B362D]">Menu Terlaris Minggu Ini</h2>
            <a href="{{ route('user.menu.index') }}"
               class="text-xs sm:text-sm font-bold text-[#CC8A22] hover:text-[#1B362D] transition">
                Lihat Katalog Lengkap &rsaquo;
            </a>
        </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5" id="home-menu-grid">
            @foreach ($menus as $index => $menu)
                <div class="home-menu-item {{ $index < 4 ? '' : 'hidden' }}"
                     data-category="{{ $menu->kategori_id }}"
                     data-featured="{{ $index < 4 ? '1' : '0' }}">
                    <x-menu-card :menu="$menu" :accent="$index % 2 === 0 ? 'gold' : 'green'" class="h-full" />
                </div>
            @endforeach
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white bucik-card p-5 border border-[#1B362D]/15 border-l-4 border-l-[#1B362D]">
                <span class="text-xs font-bold uppercase tracking-wider text-[#CC8A22]">01. Dine-in</span>
                <h3 class="font-serif text-lg font-bold mt-1">Pesan dari Meja</h3>
                <p class="text-xs sm:text-sm text-[#1B362D]/70 mt-1">Scan QR meja untuk memilih meja otomatis saat checkout.</p>
            </div>
            <div class="bg-white bucik-card p-5 border border-[#1B362D]/15 border-l-4 border-l-[#E29E31]">
                <span class="text-xs font-bold uppercase tracking-wider text-[#CC8A22]">02. Pickup</span>
                <h3 class="font-serif text-lg font-bold mt-1">Ambil di Dapur</h3>
                <p class="text-xs sm:text-sm text-[#1B362D]/70 mt-1">Pesan terlebih dahulu, pantau estimasi matang, lalu ambil sesuai jadwal.</p>
            </div>
            <div class="bg-white bucik-card p-5 border border-[#1B362D]/15 border-l-4 border-l-[#1B362D]">
                <span class="text-xs font-bold uppercase tracking-wider text-[#CC8A22]">03. Kurir Mandiri</span>
                <h3 class="font-serif text-lg font-bold mt-1">Diantar Kurir Dapur</h3>
                <p class="text-xs sm:text-sm text-[#1B362D]/70 mt-1">Pengantaran langsung oleh kurir internal Dapur Bucik ke alamat tersimpan Anda.</p>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    (function () {
        const pills = document.querySelectorAll('.home-pill');
        const items = document.querySelectorAll('.home-menu-item');

        pills.forEach((pill) => {
            pill.addEventListener('click', function () {
                const selected = this.dataset.category;

                pills.forEach((p) => {
                    p.className = 'home-pill px-5 py-2.5 bucik-btn text-xs sm:text-sm font-semibold bg-[#FAF4E8] text-[#1B362D] border border-[#1B362D]/15 hover:border-[#E29E31] hover:bg-white transition cursor-pointer';
                });

                this.className = 'home-pill px-5 py-2.5 bucik-btn text-xs sm:text-sm font-bold bg-[#1B362D] text-[#E29E31] border-b-2 border-[#E29E31] transition cursor-pointer';

                items.forEach((item) => {
                    const cat = item.dataset.category;
                    const isFeatured = item.dataset.featured === '1';
                    const shouldShow = selected === 'all' ? isFeatured : cat === selected;
                    item.classList.toggle('hidden', !shouldShow);
                });
            });
        });
    })();
</script>
@endpush
