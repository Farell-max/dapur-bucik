<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-[#FAF4E8] text-[#1B362D] antialiased">
    @php
        $adminMenus = [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => request()->routeIs('admin.dashboard'), 'icon' => 'M4 5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5Zm10 0a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1V5ZM4 15a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-4Zm10 0a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1v-4Z'],
            ['label' => 'Pesanan', 'route' => 'admin.pesanan.index', 'active' => request()->routeIs('admin.pesanan.*'), 'icon' => 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9h6m-6 4h4'],
            ['label' => 'Menu Dapur', 'route' => 'admin.menu.index', 'active' => request()->routeIs('admin.menu.*'), 'icon' => 'M4 6h16M4 12h16M4 18h10'],
            ['label' => 'Meja & QR', 'route' => 'admin.meja.index', 'active' => request()->routeIs('admin.meja.*'), 'icon' => 'M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 3h6m-3-3v6'],
            ['label' => 'Ongkos Kirim', 'route' => 'admin.ongkir.index', 'active' => request()->routeIs('admin.ongkir.*'), 'icon' => 'M13 16V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h1m8-1a1 1 0 0 1-1 1H9m4-1V8a1 1 0 0 1 1-1h2.586a1 1 0 0 1 .707.293l3.414 3.414a1 1 0 0 1 .293.707V16a1 1 0 0 1-1 1h-1m-6 0a2 2 0 1 0 4 0m-4 0a2 2 0 1 1 4 0m6 0a2 2 0 1 0 4 0m-4 0a2 2 0 1 1 4 0'],
            ['label' => 'Pelanggan', 'route' => 'admin.pelanggan.index', 'active' => request()->routeIs('admin.pelanggan.*'), 'icon' => 'M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7Z'],
            ['label' => 'Laporan', 'route' => 'admin.laporan.index', 'active' => request()->routeIs('admin.laporan.*'), 'icon' => 'M9 19v-6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2Zm0 0V9a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v10m-6 0a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2m0 0V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2Z'],
        ];
    @endphp

    <div class="min-h-screen flex">
        <div id="admin-sidebar-backdrop" class="no-print fixed inset-0 z-40 bg-black/45 hidden lg:hidden"></div>

        <aside id="admin-sidebar"
               class="no-print fixed lg:sticky top-0 left-0 z-50 h-screen w-60 shrink-0 bg-[#1B362D] text-[#FAF4E8] flex flex-col justify-between transform -translate-x-full lg:translate-x-0 transition-transform duration-200">
            <div>
                <div class="px-6 pt-7 pb-6 border-b border-[#FAF4E8]/10 text-center relative">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex flex-col items-center">
                        <svg class="w-7 h-7 text-[#E29E31] mb-2" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 4v16M6 8h3M6 8H3m9-4v16m0-6h3m-3 0H9m9-10v16m0-11h3m-3 0h-3"/>
                        </svg>
                        <span class="font-serif text-xl font-bold text-[#E29E31] tracking-tight block leading-tight">Dapur Bucik</span>
                        <span class="mt-0.5 text-[10px] tracking-[0.22em] uppercase text-[#FAF4E8]/60 font-semibold">Admin Panel</span>
                    </a>
                    <button type="button" id="admin-sidebar-close" class="lg:hidden absolute top-4 right-4 text-[#FAF4E8]/75 hover:text-white" aria-label="Tutup Sidebar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <nav class="p-4 space-y-1.5" aria-label="Navigasi Admin">
                    @foreach ($adminMenus as $item)
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm transition {{ $item['active'] ? 'bg-[#E29E31]/18 border-l-4 border-[#E29E31] text-[#E29E31] font-bold rounded-r-xl' : 'text-[#FAF4E8]/80 hover:bg-[#FAF4E8]/8 hover:text-white font-medium rounded-xl' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                            </svg>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </nav>
            </div>

            <div class="p-4 border-t border-[#FAF4E8]/10">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-2.5 w-full px-3 py-2 bucik-btn text-xs font-semibold text-red-400 hover:text-red-300 hover:bg-white/5 transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1"/>
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 min-w-0 flex flex-col">
            <div class="no-print lg:hidden sticky top-0 z-30 bg-[#FAF4E8]/95 backdrop-blur border-b border-[#1B362D]/10 px-4 h-14 flex items-center justify-between">
                <button type="button" id="admin-sidebar-open"
                        class="inline-flex items-center justify-center w-9 h-9 bucik-btn border border-[#1B362D]/15 text-[#1B362D]"
                        aria-label="Buka Navigasi Admin">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <span class="font-serif font-bold text-base text-[#1B362D]">Dapur Bucik Admin</span>
                <span class="w-9"></span>
            </div>

            <main class="flex-1 p-5 sm:p-8 lg:p-10 max-w-6xl w-full">
                @yield('content')
            </main>
        </div>
    </div>

    @include('partials.floating-alerts')

    <script>
        (function () {
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('admin-sidebar-backdrop');
            const openBtn = document.getElementById('admin-sidebar-open');
            const closeBtn = document.getElementById('admin-sidebar-close');

            function toggleSidebar(show) {
                if (!sidebar || !backdrop) return;
                sidebar.classList.toggle('-translate-x-full', !show);
                backdrop.classList.toggle('hidden', !show);
            }

            openBtn?.addEventListener('click', () => toggleSidebar(true));
            closeBtn?.addEventListener('click', () => toggleSidebar(false));
            backdrop?.addEventListener('click', () => toggleSidebar(false));
        })();
    </script>
    @stack('scripts')
</body>
</html>
