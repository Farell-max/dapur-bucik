@extends('layouts.user')

@section('title', 'Katalog Menu â€” Dapur Bucik')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        @if ($selectedMeja)
            <div class="mb-5 px-5 py-3.5 bucik-card bg-[#1B362D] text-[#FAF4E8] border-l-4 border-[#E29E31] flex flex-wrap items-center justify-between gap-3">
                <div class="text-xs sm:text-sm">
                    <span class="font-bold text-[#E29E31] uppercase tracking-wide mr-2">Mode Dine-in Aktif:</span>
                    Anda sedang memesan untuk <strong>{{ $selectedMeja->nomor_meja }}</strong> (otomatis terpilih saat Checkout).
                </div>
                <a href="{{ route('user.checkout.index') }}" class="text-xs font-bold text-[#E29E31] hover:underline">
                    Lanjut Checkout &rarr;
                </a>
            </div>
        @endif

        <form method="GET" action="{{ route('user.menu.index') }}" id="catalog-filter-form">
            <div class="bg-white bucik-card border border-[#1B362D]/15 border-l-4 border-l-[#1B362D] p-5 sm:p-6 shadow-xs">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#1B362D]">Katalog Menu Dapur Bucik</h1>
                        <p class="text-sm text-[#1B362D]/70 mt-1">Temukan lauk rumahan, cemilan tradisional, minuman segar, hingga menu khusus anak.</p>
                    </div>

                    <div class="w-full md:w-88 flex gap-2">
                        <div class="relative flex-1">
                            <label for="menu-search" class="sr-only">Cari Menu</label>
                            <input id="menu-search" name="q" type="search" value="{{ $filters['q'] }}"
                                   placeholder="Cari ayam goreng, nasi kuning..."
                                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#1B362D]/20 bg-[#FAF4E8]/50 text-sm focus:outline-none focus:border-[#E29E31] focus:ring-2 focus:ring-[#E29E31]/30">
                            <svg class="w-4 h-4 text-[#1B362D]/50 absolute left-3.5 top-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z"/>
                            </svg>
                        </div>
                        <button type="submit"
                                class="px-4 py-2.5 rounded-xl bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] text-xs font-bold cursor-pointer">
                            Cari
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-7 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <aside class="lg:col-span-3 bg-white bucik-card border border-[#1B362D]/15 border-l-4 border-l-[#E29E31] p-5 shadow-xs space-y-6">
                    <div>
                        <h2 class="font-serif text-lg font-bold text-[#1B362D]">Kategori</h2>
                        <div class="mt-3 space-y-2">
                            <label class="flex items-center gap-2.5 text-sm cursor-pointer">
                                <input type="radio" name="kategori_id" value=""
                                       {{ empty($filters['kategori_id']) ? 'checked' : '' }}
                                       onchange="this.form.submit()"
                                       class="accent-[#1B362D]">
                                <span class="font-medium">Semua Kategori</span>
                            </label>
                            @foreach ($categories as $category)
                                <label class="flex items-center gap-2.5 text-sm cursor-pointer">
                                    <input type="radio" name="kategori_id" value="{{ $category->id }}"
                                           {{ (string) $filters['kategori_id'] === (string) $category->id ? 'checked' : '' }}
                                           onchange="this.form.submit()"
                                           class="accent-[#1B362D]">
                                    <span>{{ $category->nama_kategori }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-5 border-t border-[#1B362D]/10">
                        <label for="menu-sort" class="font-serif text-lg font-bold text-[#1B362D] block">Urutkan</label>
                        <select id="menu-sort" name="urut" onchange="this.form.submit()"
                                class="mt-2.5 w-full px-3.5 py-2.5 rounded-xl border border-[#1B362D]/20 bg-[#FAF4E8]/40 text-sm font-medium focus:outline-none focus:border-[#E29E31]">
                            <option value="terlaris" {{ $filters['urut'] === 'terlaris' ? 'selected' : '' }}>Paling Terlaris</option>
                            <option value="harga_asc" {{ $filters['urut'] === 'harga_asc' ? 'selected' : '' }}>Harga Terendah</option>
                            <option value="harga_desc" {{ $filters['urut'] === 'harga_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                            <option value="rating_desc" {{ $filters['urut'] === 'rating_desc' ? 'selected' : '' }}>Rating Tertinggi</option>
                        </select>
                    </div>

                    <a href="{{ route('user.menu.index') }}"
                       class="block text-center w-full py-2 rounded-xl border border-[#1B362D]/20 text-xs font-bold text-[#1B362D] hover:bg-[#FAF4E8] transition">
                        Reset Filter
                    </a>
                </aside>

                <div class="lg:col-span-9">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-sm text-[#1B362D]/75">
                            Menampilkan <strong>{{ $menus->count() }}</strong> menu
                        </p>
                    </div>

                    @if ($menus->isNotEmpty())
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                            @foreach ($menus as $index => $menu)
                                <x-menu-card :menu="$menu" :accent="$index % 2 === 0 ? 'gold' : 'green'" class="h-full" />
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white rounded-2xl border border-[#1B362D]/10 p-10 text-center">
                            <p class="font-serif text-xl font-bold text-[#1B362D]">Menu tidak ditemukan</p>
                            <p class="text-sm text-[#1B362D]/70 mt-1">Coba ubah kata kunci pencarian atau pilih kategori lain.</p>
                            <a href="{{ route('user.menu.index') }}" class="mt-4 inline-block px-5 py-2 rounded-xl bg-[#E29E31] text-[#1B362D] text-xs font-bold">
                                Tampilkan Semua Menu
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
@endsection
