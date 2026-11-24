@extends('layouts.admin')

@section('title', 'Manajemen Menu Dapur â€” Dapur Bucik')

@section('content')
    <div class="pb-4 mb-6 border-b border-dashed border-[#1B362D]/20 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#1B362D]">Manajemen Menu</h1>
            <span class="sr-only">Manajemen Menu Dapur</span>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <button type="button" id="btn-open-add-kategori"
                    class="px-3.5 py-2 bucik-btn border border-[#1B362D]/30 bg-white hover:bg-[#FAF4E8] text-[#1B362D] text-xs font-bold transition cursor-pointer">
                + Kategori
            </button>
            <button type="button" id="btn-open-add-menu"
                    class="px-5 py-2.5 bucik-btn bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] text-xs sm:text-sm font-bold border border-[#1B362D]/40 shadow-[0_2px_0_#1B362D] transition cursor-pointer">
                + Tambah Resep Baru
            </button>
        </div>
    </div>

    <div class="bg-white bucik-card border border-[#1B362D]/15 shadow-xs overflow-hidden">
        <form method="GET" action="{{ route('admin.menu.index') }}" class="p-4 sm:p-5 bg-[#FAF4E8]/45 border-b border-[#1B362D]/10 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <input name="q" type="search" value="{{ $filters['q'] }}" placeholder="Cari menu..."
                   class="flex-1 px-4 py-2.5 rounded-xl border border-[#1B362D]/15 bg-white text-sm focus:outline-none focus:border-[#E29E31]">
            <select name="kategori_id" onchange="this.form.submit()"
                    class="px-4 py-2.5 rounded-xl border border-[#1B362D]/15 bg-white text-sm font-medium focus:outline-none focus:border-[#E29E31]">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ (string) $filters['kategori_id'] === (string) $category->id ? 'selected' : '' }}>
                        {{ $category->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-[#1B362D]/10 text-[11px] font-extrabold uppercase tracking-wider text-[#1B362D]/60">
                    <tr>
                        <th class="px-5 py-3.5">Menu</th>
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5">Harga</th>
                        <th class="px-5 py-3.5 text-center">Status/Promo</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1B362D]/10">
                    @forelse ($menus as $menu)
                        @php
                            $isAvailable = $menu->status_ketersediaan === 'tersedia';
                            $hasPromo = ! empty($menu->harga_promo);
                            $menuPayload = [
                                'id' => $menu->id,
                                'kategori_id' => $menu->kategori_id,
                                'nama' => $menu->nama,
                                'deskripsi' => $menu->deskripsi,
                                'harga' => (int) round((float) $menu->harga),
                                'harga_promo' => $menu->harga_promo ? (int) round((float) $menu->harga_promo) : '',
                                'status_ketersediaan' => $menu->status_ketersediaan,
                                'update_url' => route('admin.menu.update', $menu->id),
                            ];
                        @endphp
                        <tr class="hover:bg-[#FAF4E8]/35 transition">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3.5">
                                    <img src="{{ $menu->fotoUrl() }}" alt="{{ $menu->nama }}"
                                         width="88"
                                         height="88"
                                         loading="lazy"
                                         decoding="async"
                                         class="w-11 h-11 rounded-lg object-cover bg-[#1B362D] shrink-0 {{ $isAvailable ? '' : 'opacity-50 grayscale' }}">
                                    <div>
                                        <p class="font-bold {{ $isAvailable ? 'text-[#1B362D]' : 'text-[#1B362D]/45' }}">{{ $menu->nama }}</p>
                                        @if ($hasPromo)
                                            <span class="mt-1 bucik-stamp border-[#E29E31] bg-[#1B362D] text-[#E29E31] text-[9px] px-2 py-0.5">
                                                Promo
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-xs sm:text-sm text-[#1B362D]/80">
                                {{ $menu->kategori?->nama_kategori ?? 'Umum' }}
                            </td>
                            <td class="px-5 py-4">
                                @if ($hasPromo)
                                    <span class="block text-[11px] text-[#1B362D]/40 line-through">
                                        Rp {{ number_format((float) $menu->harga, 0, ',', '.') }}
                                    </span>
                                    <span class="font-bold text-[#1B362D]">
                                        Rp {{ number_format((float) $menu->harga_promo, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="font-bold text-[#1B362D]">
                                        Rp {{ number_format((float) $menu->harga, 0, ',', '.') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <form method="POST" action="{{ route('admin.menu.toggle', $menu->id) }}" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    @if ($isAvailable)
                                        <button type="submit"
                                                title="Klik untuk tandai Habis"
                                                aria-label="Status Tersedia"
                                                class="w-11 h-6 rounded-full bg-[#1B362D] p-1 inline-flex items-center justify-end transition cursor-pointer">
                                            <span class="w-4 h-4 rounded-full bg-white block"></span>
                                        </button>
                                    @else
                                        <button type="submit"
                                                title="Klik untuk aktifkan Tersedia"
                                                class="bucik-stamp border-red-500 text-red-600 bg-white hover:bg-red-50 transition cursor-pointer">
                                            HABIS
                                        </button>
                                    @endif
                                </form>
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-3">
                                    <button type="button"
                                            class="btn-edit-menu text-xs font-bold text-[#1B362D] underline underline-offset-4 hover:text-[#CC8A22] cursor-pointer"
                                            data-menu="{{ json_encode($menuPayload) }}">
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('admin.menu.destroy', $menu->id) }}"
                                          onsubmit="return confirm('Yakin ingin menghapus menu {{ addslashes($menu->nama) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-xs font-semibold text-red-600 hover:underline cursor-pointer">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-sm text-[#1B362D]/65">Belum ada menu ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="menu-modal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg border-t-4 border-[#E29E31] shadow-xl overflow-hidden">
            <div class="px-6 py-4 bg-[#1B362D] text-[#FAF4E8] flex items-center justify-between">
                <h2 id="menu-modal-title" class="font-serif text-xl font-bold">Tambah Resep Baru</h2>
                <button type="button" id="close-menu-modal" class="text-[#FAF4E8]/80 hover:text-white text-xl leading-none cursor-pointer">&times;</button>
            </div>

            <form id="menu-modal-form" method="POST" action="{{ route('admin.menu.store') }}" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="menu-form-method" value="POST">

                <div>
                    <label for="form-menu-nama" class="block text-xs font-bold uppercase text-[#1B362D]/80 mb-1">Nama Menu</label>
                    <input id="form-menu-nama" name="nama" type="text" required placeholder="Contoh: Ayam Goreng Lengkuas"
                           class="w-full px-3.5 py-2 rounded-xl border border-[#1B362D]/20 bg-[#FAF4E8]/40 text-sm">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="form-menu-kategori" class="block text-xs font-bold uppercase text-[#1B362D]/80 mb-1">Kategori</label>
                        <select id="form-menu-kategori" name="kategori_id" class="w-full px-3.5 py-2 rounded-xl border border-[#1B362D]/20 bg-[#FAF4E8]/40 text-sm">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="form-menu-status" class="block text-xs font-bold uppercase text-[#1B362D]/80 mb-1">Ketersediaan</label>
                        <select id="form-menu-status" name="status_ketersediaan" class="w-full px-3.5 py-2 rounded-xl border border-[#1B362D]/20 bg-[#FAF4E8]/40 text-sm">
                            <option value="tersedia">Tersedia</option>
                            <option value="habis">Habis</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="form-menu-harga" class="block text-xs font-bold uppercase text-[#1B362D]/80 mb-1">Harga Normal (Rp)</label>
                        <input id="form-menu-harga" name="harga" type="number" min="0" required placeholder="25000"
                               class="w-full px-3.5 py-2 rounded-xl border border-[#1B362D]/20 bg-[#FAF4E8]/40 text-sm">
                    </div>
                    <div>
                        <label for="form-menu-promo" class="block text-xs font-bold uppercase text-[#1B362D]/80 mb-1">Harga Promo (Opsional)</label>
                        <input id="form-menu-promo" name="harga_promo" type="number" min="0" placeholder="15000"
                               class="w-full px-3.5 py-2 rounded-xl border border-[#1B362D]/20 bg-[#FAF4E8]/40 text-sm">
                    </div>
                </div>

                <div>
                    <label for="form-menu-deskripsi" class="block text-xs font-bold uppercase text-[#1B362D]/80 mb-1">Deskripsi</label>
                    <textarea id="form-menu-deskripsi" name="deskripsi" rows="3" required placeholder="Deskripsi komposisi dan cita rasa menu..."
                              class="w-full px-3.5 py-2 rounded-xl border border-[#1B362D]/20 bg-[#FAF4E8]/40 text-sm"></textarea>
                </div>

                <div>
                    <label for="form-menu-foto" class="block text-xs font-bold uppercase text-[#1B362D]/80 mb-1">Foto Menu (Otomatis dikonversi ke WebP)</label>
                    <input id="form-menu-foto" name="foto" type="file" accept="image/*" class="w-full text-xs text-[#1B362D]/75">
                </div>

                <div class="pt-2 flex justify-end gap-2.5">
                    <button type="button" id="cancel-menu-modal"
                            class="px-4 py-2.5 rounded-xl border border-[#1B362D]/20 text-xs font-bold cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] text-xs font-bold cursor-pointer">
                        Simpan Menu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="kategori-modal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-sm border-t-4 border-[#1B362D] shadow-xl overflow-hidden">
            <div class="px-6 py-4 bg-[#1B362D] text-[#FAF4E8] flex items-center justify-between">
                <h2 class="font-serif text-lg font-bold">Tambah Kategori Baru</h2>
                <button type="button" id="close-kategori-modal" class="text-xl leading-none cursor-pointer">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.kategori.store') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label for="nama_kategori" class="block text-xs font-bold uppercase text-[#1B362D]/80 mb-1">Nama Kategori</label>
                    <input id="nama_kategori" name="nama_kategori" type="text" required placeholder="Contoh: Paket Hemat"
                           class="w-full px-3.5 py-2 rounded-xl border border-[#1B362D]/20 bg-[#FAF4E8]/40 text-sm">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" id="cancel-kategori-modal" class="px-4 py-2 rounded-xl border border-[#1B362D]/20 text-xs font-bold cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-[#E29E31] text-[#1B362D] text-xs font-bold cursor-pointer">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const modal = document.getElementById('menu-modal');
        const form = document.getElementById('menu-modal-form');
        const methodInput = document.getElementById('menu-form-method');
        const modalTitle = document.getElementById('menu-modal-title');
        const namaInput = document.getElementById('form-menu-nama');
        const katSelect = document.getElementById('form-menu-kategori');
        const statusSelect = document.getElementById('form-menu-status');
        const hargaInput = document.getElementById('form-menu-harga');
        const promoInput = document.getElementById('form-menu-promo');
        const deskInput = document.getElementById('form-menu-deskripsi');

        document.getElementById('btn-open-add-menu')?.addEventListener('click', () => {
            if (form) form.action = '{{ route('admin.menu.store') }}';
            if (methodInput) methodInput.value = 'POST';
            if (modalTitle) modalTitle.textContent = 'Tambah Resep Baru';
            if (namaInput) namaInput.value = '';
            if (hargaInput) hargaInput.value = '';
            if (promoInput) promoInput.value = '';
            if (deskInput) deskInput.value = '';
            if (statusSelect) statusSelect.value = 'tersedia';
            modal?.classList.remove('hidden');
        });

        document.querySelectorAll('.btn-edit-menu').forEach((btn) => {
            btn.addEventListener('click', function () {
                const menu = JSON.parse(this.dataset.menu || '{}');
                if (form && menu.update_url) form.action = menu.update_url;
                if (methodInput) methodInput.value = 'PUT';
                if (modalTitle) modalTitle.textContent = 'Edit Menu: ' + (menu.nama || '');
                if (namaInput) namaInput.value = menu.nama || '';
                if (katSelect) katSelect.value = String(menu.kategori_id || '');
                if (statusSelect) statusSelect.value = menu.status_ketersediaan || 'tersedia';
                if (hargaInput) hargaInput.value = menu.harga || '';
                if (promoInput) promoInput.value = menu.harga_promo ?? '';
                if (deskInput) deskInput.value = menu.deskripsi || '';
                modal?.classList.remove('hidden');
            });
        });

        document.getElementById('close-menu-modal')?.addEventListener('click', () => modal?.classList.add('hidden'));
        document.getElementById('cancel-menu-modal')?.addEventListener('click', () => modal?.classList.add('hidden'));

        const katModal = document.getElementById('kategori-modal');
        document.getElementById('btn-open-add-kategori')?.addEventListener('click', () => katModal?.classList.remove('hidden'));
        document.getElementById('close-kategori-modal')?.addEventListener('click', () => katModal?.classList.add('hidden'));
        document.getElementById('cancel-kategori-modal')?.addEventListener('click', () => katModal?.classList.add('hidden'));
    })();
</script>
@endpush
