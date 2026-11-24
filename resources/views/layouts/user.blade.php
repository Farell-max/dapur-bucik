<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen flex flex-col bg-[#FAF4E8] text-[#1B362D] antialiased">
    @php
        $authUser = auth()->user();
        $cartItemCount = 0;
        if ($authUser) {
            $cartItemCount = (int) \App\Models\ItemKeranjang::whereHas('keranjang', function ($query) use ($authUser): void {
                $query->where('user_id', $authUser->id);
            })->sum('qty');
        }

        // Di landing page publik (/), menu, dll: hanya tampilkan Beranda, Menu, dan Riwayat.
        // Item Profil hanya tampil di halaman dashboard/profil pengguna (bukan di landing page publik).
        $isDashboardPage = request()->routeIs('user.dashboard') || request()->routeIs('user.profil.*');

        $navLinks = [
            ['label' => 'Beranda', 'route' => 'user.home', 'active' => request()->routeIs('user.home')],
            ['label' => 'Menu', 'route' => 'user.menu.index', 'active' => request()->routeIs('user.menu.*')],
            ['label' => 'Riwayat', 'route' => 'user.riwayat.index', 'active' => request()->routeIs('user.riwayat.*') || request()->routeIs('user.pesanan.*')],
        ];

        if ($isDashboardPage && $authUser) {
            $navLinks[] = ['label' => 'Profil', 'route' => 'user.dashboard', 'active' => $isDashboardPage];
        }
    @endphp

    <header class="no-print sticky top-0 z-40 bg-[#FAF4E8]/95 backdrop-blur border-b border-dashed border-[#1B362D]/20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">
                <a href="{{ route('user.home') }}" class="font-serif text-2xl font-bold tracking-tight text-[#1B362D] shrink-0">
                    Dapur Bucik
                </a>

                <div class="flex items-center gap-6">
                    <nav class="hidden md:flex items-center gap-6" aria-label="Navigasi Utama">
                        @foreach ($navLinks as $item)
                            <a href="{{ route($item['route']) }}"
                               class="py-1 text-sm transition {{ $item['active'] ? 'font-bold text-[#1B362D] border-b-2 border-[#E29E31]' : 'font-semibold text-[#1B362D]/75 hover:text-[#1B362D]' }}">
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('user.keranjang.index') }}"
                           class="relative inline-flex items-center justify-center w-9 h-9 bucik-btn text-[#1B362D] hover:bg-[#1B362D]/5 transition"
                           aria-label="Keranjang Belanja">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6h13M10 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm7 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"/>
                            </svg>
                            @if ($cartItemCount > 0)
                                <span id="navbar-cart-count" class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-4 h-4 px-1 rounded-full bg-[#E29E31] text-[#1B362D] text-[10px] font-extrabold">
                                    {{ $cartItemCount }}
                                </span>
                            @endif
                        </a>

                        @auth
                            @if ($authUser->role !== 'admin' && ! $isDashboardPage)
                                <a href="{{ route('user.dashboard') }}"
                                   class="hidden sm:inline-flex items-center px-3.5 py-1.5 bucik-btn bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] border border-[#1B362D]/35 shadow-[0_2px_0_#1B362D] text-xs font-bold transition">
                                    Dashboard
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}"
                               class="hidden sm:inline-flex items-center px-4 py-1.5 bucik-btn bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] border border-[#1B362D]/35 shadow-[0_2px_0_#1B362D] text-xs font-bold transition">
                                Masuk
                            </a>
                        @endauth

                        <button type="button"
                                id="user-mobile-menu-btn"
                                class="md:hidden inline-flex items-center justify-center w-9 h-9 bucik-btn border border-[#1B362D]/15 text-[#1B362D]"
                                aria-label="Buka Menu">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div id="user-mobile-menu" class="hidden md:hidden pb-4 pt-2 border-t border-dashed border-[#1B362D]/15 space-y-1">
                @foreach ($navLinks as $item)
                    <a href="{{ route($item['route']) }}"
                       class="block px-3.5 py-2 bucik-btn text-sm font-semibold {{ $item['active'] ? 'bg-[#E29E31]/20 border-l-4 border-[#E29E31] text-[#1B362D] font-bold' : 'text-[#1B362D] hover:bg-[#1B362D]/5' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach

                @if ($authUser)
                    @if ($authUser->role !== 'admin' && ! $isDashboardPage)
                        <a href="{{ route('user.dashboard') }}"
                           class="block px-3.5 py-2 bucik-btn text-sm font-bold bg-[#E29E31] text-[#1B362D] border border-[#1B362D]/35 text-center mt-2">
                            Dashboard Pelanggan
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="pt-2">
                        @csrf
                        <button type="submit" class="w-full px-3.5 py-2 bucik-btn text-sm font-bold text-red-600 text-left">
                            Keluar Dapur ({{ $authUser->nama }})
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="block px-3.5 py-2 bucik-btn text-sm font-bold bg-[#E29E31] text-[#1B362D] border border-[#1B362D]/35 text-center mt-2">
                        Masuk / Daftar
                    </a>
                @endif
            </div>
        </div>
    </header>

    @include('partials.floating-alerts')

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="no-print bg-[#1B362D] text-[#FAF4E8] mt-16 border-t-4 border-[#E29E31]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h2 class="font-serif text-xl font-bold text-[#FAF4E8]">Dapur Bucik</h2>
                    <p class="mt-2 text-sm text-[#FAF4E8]/80 leading-relaxed">
                        Menyajikan masakan rumahan hangat dengan resep bumbu rempah pilihan. Tersedia layanan Dine-in scan meja, Pickup, dan Kurir Mandiri.
                    </p>
                </div>
                <div>
                    <h3 class="font-serif text-base font-bold text-[#E29E31]">Jam Operasional &amp; Dapur</h3>
                    <p class="mt-2 text-sm text-[#FAF4E8]/80">Senin â€“ Minggu: 09.00 â€“ 21.00 WIB</p>
                    <p class="mt-1 text-sm text-[#FAF4E8]/80">Jl. Melati Kusuma No. 24, Jakarta Selatan</p>
                    <p class="mt-1 text-sm text-[#FAF4E8]/80">WhatsApp Dapur: 0812-3456-7890</p>
                </div>
                <div>
                    <h3 class="font-serif text-base font-bold text-[#E29E31]">Navigasi Cepat</h3>
                    <ul class="mt-2 space-y-1.5 text-sm text-[#FAF4E8]/80">
                        <li><a href="{{ route('user.menu.index') }}" class="hover:text-[#E29E31] transition">Katalog Menu Lengkap</a></li>
                        <li><a href="{{ route('user.keranjang.index') }}" class="hover:text-[#E29E31] transition">Keranjang Belanja</a></li>
                        <li><a href="{{ route('user.riwayat.index') }}" class="hover:text-[#E29E31] transition">Riwayat &amp; Lacak Pesanan</a></li>
                        <li><a href="{{ route('admin.login') }}" class="hover:text-[#E29E31] transition">Portal Admin Dapur</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-6 border-t border-[#FAF4E8]/15 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-[#FAF4E8]/65">
                <span>&copy; 2026 Dapur Bucik. Seluruh hak cipta dilindungi.</span>
                <span>Aplikasi Pemesanan Kuliner Dapur Bucik</span>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('user-mobile-menu-btn')?.addEventListener('click', function () {
            document.getElementById('user-mobile-menu')?.classList.toggle('hidden');
        });
    </script>
    @stack('scripts')
</body>
</html>
