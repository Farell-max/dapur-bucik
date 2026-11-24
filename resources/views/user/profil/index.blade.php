@extends('layouts.user')

@section('title', 'Profil Pengguna â€” Dapur Bucik')

@section('content')
    @php
        $resolvedTab = $activeTab;
        if ($errors->has('current_password') || $errors->has('password')) {
            $resolvedTab = 'ubah-sandi';
        } elseif ($errors->has('label') || $errors->has('detail_alamat')) {
            $resolvedTab = 'buku-alamat';
        }
        $firstLetter = mb_strtoupper(mb_substr(trim((string) $profile->nama), 0, 1)) ?: 'B';
    @endphp

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <aside class="lg:col-span-4 space-y-5">
                <div class="bg-[#1B362D] text-[#FAF4E8] bucik-card p-6 text-center shadow-sm">
                    @if (! empty($profile->foto_profil))
                        <img src="{{ asset('storage/' . $profile->foto_profil) }}"
                             alt="{{ $profile->nama }}"
                             class="w-16 h-16 mx-auto rounded-full object-cover border-2 border-[#E29E31]">
                    @else
                        <div class="w-16 h-16 mx-auto rounded-full border-2 border-[#E29E31] flex items-center justify-center text-2xl font-bold text-[#FAF4E8]">
                            {{ $firstLetter }}
                        </div>
                    @endif

                    <h2 class="mt-3 font-serif text-lg font-bold text-[#FAF4E8] truncate">{{ $profile->nama }}</h2>
                    <p class="text-xs text-[#FAF4E8]/65 truncate mt-0.5">{{ $profile->email }}</p>
                </div>

                <nav class="space-y-1.5" aria-label="Menu Profil">
                    <button type="button" data-profil-tab="data-diri"
                            class="profil-tab-btn w-full text-left px-4 py-3 text-sm cursor-pointer transition {{ $resolvedTab === 'data-diri' ? 'bg-[#E29E31]/20 border-l-4 border-[#E29E31] rounded-r-xl font-bold text-[#1B362D]' : 'rounded-xl font-semibold text-[#1B362D]/80 hover:bg-[#1B362D]/5' }}">
                        Data Diri
                    </button>
                    <button type="button" data-profil-tab="buku-alamat"
                            class="profil-tab-btn w-full text-left px-4 py-3 text-sm cursor-pointer transition {{ $resolvedTab === 'buku-alamat' ? 'bg-[#E29E31]/20 border-l-4 border-[#E29E31] rounded-r-xl font-bold text-[#1B362D]' : 'rounded-xl font-semibold text-[#1B362D]/80 hover:bg-[#1B362D]/5' }}">
                        Buku Alamat
                    </button>
                    <button type="button" data-profil-tab="ubah-sandi"
                            class="profil-tab-btn w-full text-left px-4 py-3 text-sm cursor-pointer transition {{ $resolvedTab === 'ubah-sandi' ? 'bg-[#E29E31]/20 border-l-4 border-[#E29E31] rounded-r-xl font-bold text-[#1B362D]' : 'rounded-xl font-semibold text-[#1B362D]/80 hover:bg-[#1B362D]/5' }}">
                        Ubah Sandi
                    </button>
                </nav>

                <div class="pt-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 text-xs font-bold text-red-600 hover:text-red-800 hover:underline cursor-pointer">
                            Keluar Dapur
                        </button>
                    </form>
                </div>
            </aside>

            <div class="lg:col-span-8">
                <section id="tab-data-diri" class="profil-panel {{ $resolvedTab === 'data-diri' ? '' : 'hidden' }} bg-white bucik-card border border-[#1B362D]/15 border-l-4 border-l-[#E29E31] p-6 sm:p-8 shadow-xs">
                    <div class="pb-4 mb-6 border-b border-dashed border-[#1B362D]/15">
                        <h1 class="font-serif text-2xl font-bold text-[#1B362D]">Ubah Data Diri</h1>
                    </div>

                    <form method="POST" action="{{ route('user.profil.update') }}" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="profil-nama" class="block text-[11px] font-extrabold uppercase tracking-wider text-[#1B362D]/80 mb-2">
                                    Nama Panggilan
                                </label>
                                <input id="profil-nama" name="nama" type="text" value="{{ old('nama', $profile->nama) }}" required
                                       class="w-full px-4 py-2.5 rounded-xl border border-[#1B362D]/15 bg-white text-sm text-[#1B362D] focus:outline-none focus:border-[#E29E31]">
                            </div>
                            <div>
                                <label for="profil-hp" class="block text-[11px] font-extrabold uppercase tracking-wider text-[#1B362D]/80 mb-2">
                                    Nomor WhatsApp
                                </label>
                                <input id="profil-hp" name="no_hp" type="tel" value="{{ old('no_hp', $profile->no_hp) }}" required
                                       class="w-full px-4 py-2.5 rounded-xl border border-[#1B362D]/15 bg-white text-sm text-[#1B362D] focus:outline-none focus:border-[#E29E31]">
                            </div>
                        </div>

                        <div>
                            <label for="profil-email" class="block text-[11px] font-extrabold uppercase tracking-wider text-[#1B362D]/80 mb-2">
                                Email
                            </label>
                            <input id="profil-email" name="email" type="email" value="{{ old('email', $profile->email) }}" readonly
                                   class="w-full px-4 py-2.5 rounded-xl border border-[#1B362D]/10 bg-[#FAF4E8]/45 text-sm text-[#1B362D]/60 cursor-not-allowed">
                            <span class="block mt-1.5 text-[11px] text-[#1B362D]/45">Email tidak dapat diubah.</span>
                        </div>

                        <div>
                            <label for="profil-foto" class="block text-[11px] font-extrabold uppercase tracking-wider text-[#1B362D]/80 mb-1.5">
                                Foto Profil (Opsional)
                            </label>
                            <input id="profil-foto" name="foto_profil" type="file" accept="image/*"
                                   class="w-full text-xs text-[#1B362D]/70 file:mr-3 file:py-1.5 file:px-3.5 file:rounded-lg file:border file:border-[#1B362D]/15 file:text-xs file:font-bold file:bg-[#FAF4E8] file:text-[#1B362D]">
                        </div>

                        <div class="pt-4 flex items-center justify-end gap-3">
                            <a href="{{ route('user.profil.index') }}"
                               class="px-5 py-2.5 bucik-btn border border-[#1B362D]/35 bg-white hover:bg-[#FAF4E8] text-xs font-bold text-[#1B362D] transition">
                                Batal
                            </a>
                            <button type="submit"
                                    class="px-6 py-2.5 bucik-btn bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] text-xs font-bold border border-[#1B362D]/40 shadow-[0_2px_0_#1B362D] transition cursor-pointer">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </section>

                <section id="tab-buku-alamat" class="profil-panel {{ $resolvedTab === 'buku-alamat' ? '' : 'hidden' }} bg-white bucik-card border border-[#1B362D]/15 border-l-4 border-l-[#E29E31] p-6 sm:p-8 shadow-xs space-y-5">
                    <div class="pb-4 border-b border-dashed border-[#1B362D]/15 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h2 class="font-serif text-2xl font-bold text-[#1B362D]">Buku Alamat</h2>
                            <p class="text-xs text-[#1B362D]/65 mt-0.5">Alamat tersimpan untuk pengiriman Kurir Mandiri.</p>
                        </div>
                        <button type="button" id="toggle-add-address"
                                class="px-4 py-2 rounded-xl bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] text-xs font-bold border border-[#1B362D]/40 shadow-[0_2px_0_#1B362D] transition cursor-pointer">
                            + Tambah Alamat
                        </button>
                    </div>

                    <form id="form-add-address" method="POST" action="{{ route('user.profil.alamat.store') }}"
                          class="hidden p-4 rounded-xl bg-[#FAF4E8] border border-[#1B362D]/15 space-y-3">
                        @csrf
                        <div>
                            <label for="new-address-label" class="block text-xs font-bold uppercase text-[#1B362D]/80 mb-1">Label Alamat</label>
                            <input id="new-address-label" name="label" type="text" required placeholder="Contoh: Rumah / Kantor"
                                   class="w-full px-3.5 py-2 rounded-xl border border-[#1B362D]/20 bg-white text-sm">
                        </div>
                        <div>
                            <label for="new-address-detail" class="block text-xs font-bold uppercase text-[#1B362D]/80 mb-1">Detail Alamat Lengkap</label>
                            <textarea id="new-address-detail" name="detail_alamat" rows="2" required placeholder="Nama jalan, nomor rumah, RT/RW, dan patokan..."
                                      class="w-full px-3.5 py-2 rounded-xl border border-[#1B362D]/20 bg-white text-sm"></textarea>
                        </div>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-[#1B362D] text-[#FAF4E8] text-xs font-bold cursor-pointer">
                            Simpan Alamat
                        </button>
                    </form>

                    <div class="space-y-3">
                        @forelse ($addresses as $address)
                            <div class="p-4 rounded-xl bg-[#FAF4E8]/60 border border-[#1B362D]/15 space-y-3">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                    <div>
                                        <span class="bucik-stamp border-[#E29E31] bg-[#1B362D] text-[#E29E31] text-[10px]">
                                            {{ $address->label }}
                                        </span>
                                        <p class="mt-2 text-sm text-[#1B362D]/85">{{ $address->detail_alamat }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <button type="button"
                                                onclick="document.getElementById('edit-alamat-{{ $address->id }}')?.classList.toggle('hidden')"
                                                class="px-3 py-1.5 rounded-lg border border-[#1B362D]/20 bg-white text-xs font-semibold hover:border-[#E29E31] cursor-pointer">
                                            Ubah
                                        </button>
                                        <form method="POST" action="{{ route('user.profil.alamat.destroy', $address->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg bg-red-50 text-red-700 text-xs font-semibold hover:bg-red-100 cursor-pointer">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <form id="edit-alamat-{{ $address->id }}" method="POST" action="{{ route('user.profil.alamat.update', $address->id) }}"
                                      class="hidden pt-3 border-t border-[#1B362D]/10 space-y-2.5">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="label" value="{{ $address->label }}" required
                                           class="w-full px-3 py-2 rounded-xl border border-[#1B362D]/20 bg-white text-sm">
                                    <textarea name="detail_alamat" rows="2" required
                                              class="w-full px-3 py-2 rounded-xl border border-[#1B362D]/20 bg-white text-sm">{{ $address->detail_alamat }}</textarea>
                                    <button type="submit" class="px-4 py-1.5 rounded-lg bg-[#E29E31] text-[#1B362D] text-xs font-bold cursor-pointer">
                                        Simpan Perubahan
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="p-6 rounded-xl bg-[#FAF4E8]/50 border border-[#1B362D]/10 text-center text-sm text-[#1B362D]/70">
                                Belum ada alamat tersimpan. Klik <strong>+ Tambah Alamat</strong> untuk menambahkan alamat baru.
                            </div>
                        @endforelse
                    </div>
                </section>

                <section id="tab-ubah-sandi" class="profil-panel {{ $resolvedTab === 'ubah-sandi' ? '' : 'hidden' }} bg-white bucik-card border border-[#1B362D]/15 border-l-4 border-l-[#E29E31] p-6 sm:p-8 shadow-xs">
                    <div class="pb-4 mb-6 border-b border-dashed border-[#1B362D]/15">
                        <h2 class="font-serif text-2xl font-bold text-[#1B362D]">Ubah Kata Sandi</h2>
                    </div>

                    <form method="POST" action="{{ route('user.profil.password') }}" class="space-y-4 max-w-md">
                        @csrf
                        @method('PUT')
                        <div>
                            <label for="old-pass" class="block text-[11px] font-extrabold uppercase tracking-wider text-[#1B362D]/80 mb-1.5">Sandi Saat Ini</label>
                            <input id="old-pass" name="current_password" type="password" required placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢"
                                   class="w-full px-4 py-2.5 rounded-xl border border-[#1B362D]/15 bg-white text-sm focus:outline-none focus:border-[#E29E31]">
                        </div>
                        <div>
                            <label for="new-pass" class="block text-[11px] font-extrabold uppercase tracking-wider text-[#1B362D]/80 mb-1.5">Sandi Baru</label>
                            <input id="new-pass" name="password" type="password" required placeholder="Minimal 8 karakter"
                                   class="w-full px-4 py-2.5 rounded-xl border border-[#1B362D]/15 bg-white text-sm focus:outline-none focus:border-[#E29E31]">
                        </div>
                        <div>
                            <label for="confirm-pass" class="block text-[11px] font-extrabold uppercase tracking-wider text-[#1B362D]/80 mb-1.5">Konfirmasi Sandi Baru</label>
                            <input id="confirm-pass" name="password_confirmation" type="password" required placeholder="Ulangi sandi baru"
                                   class="w-full px-4 py-2.5 rounded-xl border border-[#1B362D]/15 bg-white text-sm focus:outline-none focus:border-[#E29E31]">
                        </div>
                        <div class="pt-2">
                            <button type="submit"
                                    class="px-6 py-2.5 rounded-xl bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] text-xs font-bold border border-[#1B362D]/40 shadow-[0_2px_0_#1B362D] transition cursor-pointer">
                                Perbarui Kata Sandi
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const tabBtns = document.querySelectorAll('.profil-tab-btn');
        const panels = document.querySelectorAll('.profil-panel');

        tabBtns.forEach((btn) => {
            btn.addEventListener('click', function () {
                const target = this.dataset.profilTab;

                tabBtns.forEach((b) => {
                    b.className = 'profil-tab-btn w-full text-left px-4 py-3 text-sm cursor-pointer transition rounded-xl font-semibold text-[#1B362D]/80 hover:bg-[#1B362D]/5';
                });
                this.className = 'profil-tab-btn w-full text-left px-4 py-3 text-sm cursor-pointer transition bg-[#E29E31]/20 border-l-4 border-[#E29E31] rounded-r-xl font-bold text-[#1B362D]';

                panels.forEach((panel) => {
                    panel.classList.toggle('hidden', panel.id !== 'tab-' + target);
                });
            });
        });

        document.getElementById('toggle-add-address')?.addEventListener('click', function () {
            document.getElementById('form-add-address')?.classList.toggle('hidden');
        });
    })();
</script>
@endpush
