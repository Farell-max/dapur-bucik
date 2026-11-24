@props([
    'menu',
    'accent' => 'gold',
])

@php
    $isHabis = $menu->status_ketersediaan === 'habis';
    $hasPromo = ! empty($menu->harga_promo);
    $activePrice = $menu->effectivePrice();
    $borderAccent = $accent === 'green' ? 'border-l-[#1B362D]' : 'border-l-[#E29E31]';
    $kategoriNama = $menu->kategori?->nama_kategori ?? 'Umum';
    $totalTerjual = (int) ($menu->total_terjual ?? 0);
@endphp

<article {{ $attributes->merge([
    'class' => "bg-white bucik-card border border-[#1B362D]/15 border-l-4 {$borderAccent} shadow-xs hover:shadow-md transition overflow-hidden flex flex-col justify-between"
]) }}>
    <div>
        <a href="{{ route('user.menu.show', $menu->id) }}" class="block relative aspect-[4/3] bg-[#F3E9D6] overflow-hidden group">
            <img src="{{ $menu->fotoUrl() }}"
                 alt="{{ $menu->nama }}"
                 width="600"
                 height="450"
                 loading="lazy"
                 decoding="async"
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300 {{ $isHabis ? 'opacity-55 grayscale' : '' }}">

            <div class="absolute top-3 left-3 flex flex-wrap gap-2">
                <span class="bucik-stamp border-[#1B362D] bg-[#FAF4E8]/95 text-[#1B362D] text-[10px] shadow-xs">
                    {{ $kategoriNama }}
                </span>
                @if ($hasPromo && ! $isHabis)
                    <span class="bucik-stamp border-[#E29E31] bg-[#1B362D]/95 text-[#E29E31] text-[10px] shadow-xs">
                        PROMO
                    </span>
                @endif
            </div>

            @if ($isHabis)
                <div class="absolute inset-0 bg-[#1B362D]/45 flex items-center justify-center">
                    <span class="bucik-stamp border-red-400 bg-white/95 text-red-700 shadow">
                        HABIS
                    </span>
                </div>
            @endif
        </a>

        <div class="p-4 sm:p-5">
            <div class="flex items-center justify-between gap-2 text-xs text-[#1B362D]/70 mb-1.5">
                <span class="inline-flex items-center gap-1 font-semibold text-[#1B362D]">
                    <span class="text-[#E29E31]">&#9733;</span>
                    {{ number_format((float) $menu->rating, 1) }}
                </span>
                <span>{{ $totalTerjual }} terjual</span>
            </div>

            <a href="{{ route('user.menu.show', $menu->id) }}" class="block">
                <h3 class="font-serif text-lg font-bold text-[#1B362D] hover:text-[#CC8A22] transition line-clamp-1">
                    {{ $menu->nama }}
                </h3>
            </a>
            <p class="mt-1.5 text-xs sm:text-sm text-[#1B362D]/70 line-clamp-2 leading-relaxed">
                {{ $menu->deskripsi }}
            </p>
        </div>
    </div>

    <div class="px-4 sm:px-5 pb-4 sm:pb-5 pt-2 flex items-center justify-between gap-3 border-t border-[#1B362D]/5">
        <div>
            @if ($hasPromo)
                <span class="block text-xs text-[#1B362D]/45 line-through">
                    Rp {{ number_format((float) $menu->harga, 0, ',', '.') }}
                </span>
            @endif
            <span class="text-base sm:text-lg font-extrabold text-[#1B362D]">
                Rp {{ number_format($activePrice, 0, ',', '.') }}
            </span>
        </div>

        @if ($isHabis)
            <button type="button"
                    disabled
                    class="px-3.5 py-2 bucik-btn bg-stone-200 text-stone-500 text-xs font-bold cursor-not-allowed">
                Stok Habis
            </button>
        @else
            <form method="POST" action="{{ route('user.keranjang.store') }}">
                @csrf
                <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                <input type="hidden" name="qty" value="1">
                <button type="submit"
                        class="px-3.5 py-2 bucik-btn bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] border border-[#1B362D]/35 shadow-[0_2px_0_#1B362D] text-xs font-bold transition cursor-pointer">
                    + Keranjang
                </button>
            </form>
        @endif
    </div>
</article>
