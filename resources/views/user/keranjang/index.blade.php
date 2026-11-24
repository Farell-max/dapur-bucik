@extends('layouts.user')

@section('title', 'Keranjang Belanja â€” Dapur Bucik')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        <div class="mb-6">
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#1B362D]">Keranjang Belanja</h1>
            <p class="text-sm text-[#1B362D]/70 mt-1">Periksa kembali pesanan dan catatan khusus Anda sebelum melanjutkan ke checkout.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-8 space-y-4">
                @forelse ($items as $item)
                    @php
                        $menu = $item->menu;
                        $unitPrice = $menu ? $menu->effectivePrice() : 0;
                    @endphp
                    <article class="bg-white bucik-card border border-[#1B362D]/15 border-l-4 border-l-[#E29E31] p-4 sm:p-5 shadow-xs">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <img src="{{ $menu ? $menu->fotoUrl() : asset('images/menu/ayam-goreng-lengkuas.webp') }}"
                                 alt="{{ $menu?->nama ?? 'Menu' }}"
                                 loading="lazy"
                                 decoding="async"
                                 class="w-full sm:w-28 h-36 sm:h-28 rounded-xl object-cover bg-[#F3E9D6] shrink-0">

                            <div class="flex-1 min-w-0 flex flex-col justify-between gap-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <span class="text-[11px] font-semibold uppercase tracking-wide text-[#CC8A22]">
                                            {{ $menu?->kategori?->nama_kategori ?? 'Umum' }}
                                        </span>
                                        <h2 class="font-serif text-lg font-bold text-[#1B362D]">
                                            <a href="{{ route('user.menu.show', $item->menu_id) }}" class="hover:text-[#CC8A22]">
                                                {{ $menu?->nama ?? 'Menu' }}
                                            </a>
                                        </h2>
                                        <p class="text-xs text-[#1B362D]/65 mt-0.5">
                                            Harga satuan: Rp {{ number_format($unitPrice, 0, ',', '.') }}
                                            @if ($menu && $menu->harga_promo)
                                                <span class="line-through text-[#1B362D]/40 ml-1">Rp {{ number_format((float) $menu->harga, 0, ',', '.') }}</span>
                                            @endif
                                        </p>
                                    </div>

                                    <form method="POST" action="{{ route('user.keranjang.destroy', $item->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-xs font-semibold text-red-700 hover:text-red-900 px-2.5 py-1 rounded-lg hover:bg-red-50 transition cursor-pointer">
                                            Hapus
                                        </button>
                                    </form>
                                </div>

                                @if (! empty($item->catatan))
                                    <div class="px-3 py-2 rounded-xl bg-[#FAF4E8] text-xs text-[#1B362D]/80">
                                        <strong class="font-semibold">Catatan:</strong> {{ $item->catatan }}
                                    </div>
                                @endif

                                <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                                    <div class="inline-flex items-center rounded-xl border border-[#1B362D]/20 bg-[#FAF4E8]/50">
                                        <form method="POST" action="{{ route('user.keranjang.update', $item->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="qty" value="{{ max(1, $item->qty - 1) }}">
                                            <button type="submit"
                                                    {{ $item->qty <= 1 ? 'disabled' : '' }}
                                                    class="w-9 h-9 font-bold text-[#1B362D] hover:bg-[#1B362D]/10 rounded-l-xl {{ $item->qty <= 1 ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer' }}"
                                                    aria-label="Kurangi">&minus;</button>
                                        </form>

                                        <span class="w-11 text-center text-sm font-bold">{{ $item->qty }}</span>

                                        <form method="POST" action="{{ route('user.keranjang.update', $item->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="qty" value="{{ min(99, $item->qty + 1) }}">
                                            <button type="submit"
                                                    class="w-9 h-9 font-bold text-[#1B362D] hover:bg-[#1B362D]/10 rounded-r-xl cursor-pointer"
                                                    aria-label="Tambah">+</button>
                                        </form>
                                    </div>

                                    <div class="text-right">
                                        <span class="block text-[11px] text-[#1B362D]/60">Subtotal</span>
                                        <span class="text-base sm:text-lg font-extrabold text-[#1B362D]">
                                            Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="bg-white rounded-2xl border border-[#1B362D]/10 p-10 text-center">
                        <h2 class="font-serif text-xl font-bold text-[#1B362D]">Keranjang Anda Masih Kosong</h2>
                        <p class="text-sm text-[#1B362D]/70 mt-1">Yuk pilih masakan rumahan hangat dari katalog menu Dapur Bucik.</p>
                        <a href="{{ route('user.menu.index') }}"
                           class="mt-5 inline-flex items-center px-5 py-2.5 rounded-xl bg-[#E29E31] text-[#1B362D] text-sm font-bold">
                            Lihat Katalog Menu
                        </a>
                    </div>
                @endforelse
            </div>

            <aside class="lg:col-span-4 bg-[#1B362D] text-[#FAF4E8] bucik-card p-6 shadow-md border-t-4 border-[#E29E31] sticky top-24">
                <h2 class="font-serif text-xl font-bold text-[#FAF4E8]">Ringkasan Nota</h2>
                <p class="text-xs text-[#FAF4E8]/70 mt-1">Total dihitung murni dari subtotal item keranjang Anda.</p>

                <div class="mt-5 pt-4 border-t border-[#FAF4E8]/15 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-[#FAF4E8]/80">Total Porsi</span>
                        <span class="font-bold">{{ $totalQty }} item</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[#FAF4E8]/80">Subtotal Menu</span>
                        <span class="font-semibold">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-[#FAF4E8]/20 flex items-baseline justify-between">
                    <span class="text-sm font-semibold text-[#FAF4E8]/90">Total Pembayaran</span>
                    <span class="font-serif text-2xl font-bold text-[#E29E31]">
                        Rp {{ number_format($totalHarga, 0, ',', '.') }}
                    </span>
                </div>

                @if ($items->isNotEmpty())
                    <a href="{{ route('user.checkout.index') }}"
                       class="mt-6 w-full inline-flex items-center justify-center gap-2 py-3.5 px-5 rounded-xl bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] font-bold text-sm transition shadow-sm">
                        <span>Lanjut ke Checkout</span>
                        <span>&rarr;</span>
                    </a>
                @else
                    <button type="button" disabled
                            class="mt-6 w-full py-3.5 px-5 rounded-xl bg-[#FAF4E8]/15 text-[#FAF4E8]/50 font-bold text-sm cursor-not-allowed">
                        Keranjang Kosong
                    </button>
                @endif

                <a href="{{ route('user.menu.index') }}"
                   class="mt-3 w-full inline-flex items-center justify-center py-2.5 px-4 rounded-xl border border-[#FAF4E8]/25 text-xs font-semibold text-[#FAF4E8]/85 hover:bg-[#FAF4E8]/10 transition">
                    + Tambah Menu Lain
                </a>
            </aside>
        </div>
    </div>
@endsection
