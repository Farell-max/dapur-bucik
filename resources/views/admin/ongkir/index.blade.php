@extends('layouts.admin')

@section('title', 'Pengaturan Ongkos Kirim — Dapur Bucik')

@section('content')
    <div class="pb-4 mb-6 border-b border-dashed border-[#1B362D]/20">
        <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#1B362D]">Pengaturan Ongkos Kirim</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-5xl items-start">
        {{-- Pengaturan ongkir saat ini bersifat informatif di sisi antarmuka; TODO(BE): simpan ke tabel konfigurasi jika skema diperluas --}}
        <form onsubmit="event.preventDefault(); window.showBucikToast && window.showBucikToast('Pengaturan tarif radius diperbarui untuk sesi ini.', 'info');"
              class="bg-white bucik-card border border-[#1B362D]/15 border-l-4 border-l-[#E29E31] p-6 shadow-xs space-y-4">
            <div>
                <h2 class="font-serif text-lg font-bold text-[#1B362D]">Zona Radius (Kurir Mandiri)</h2>
                <p class="text-xs text-[#1B362D]/60 mt-0.5">Atur tarif berdasarkan jarak pelanggan dari dapur.</p>
            </div>

            <div id="radius-zones-list" class="space-y-3 pt-1">
                <div class="radius-item p-3.5 rounded-xl bg-[#FAF4E8]/70 border border-[#1B362D]/10">
                    <label class="block text-[11px] font-bold text-[#1B362D] mb-1.5">Jarak 0 - 2 KM</label>
                    <div class="flex items-center gap-2.5">
                        <input type="text" value="10.000"
                               class="flex-1 px-3.5 py-2 rounded-lg border border-[#1B362D]/15 bg-white text-sm font-medium focus:outline-none focus:border-[#E29E31]">
                        <button type="button" onclick="this.closest('.radius-item')?.remove()"
                                class="text-red-500 hover:text-red-700 p-1.5 cursor-pointer" aria-label="Hapus Zona">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="radius-item p-3.5 rounded-xl bg-[#FAF4E8]/70 border border-[#1B362D]/10">
                    <label class="block text-[11px] font-bold text-[#1B362D] mb-1.5">Jarak 2.1 - 5 KM</label>
                    <div class="flex items-center gap-2.5">
                        <input type="text" value="15.000"
                               class="flex-1 px-3.5 py-2 rounded-lg border border-[#1B362D]/15 bg-white text-sm font-medium focus:outline-none focus:border-[#E29E31]">
                        <button type="button" onclick="this.closest('.radius-item')?.remove()"
                                class="text-red-500 hover:text-red-700 p-1.5 cursor-pointer" aria-label="Hapus Zona">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <button type="button" id="btn-add-radius"
                    class="text-xs font-bold text-[#1B362D] hover:text-[#CC8A22] inline-block cursor-pointer">
                + Tambah Radius Baru
            </button>

            <div class="pt-2">
                <button type="submit"
                        class="w-full py-2.5 px-5 bucik-btn bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] text-xs font-bold border border-[#1B362D]/40 shadow-[0_2px_0_#1B362D] transition cursor-pointer">
                    Simpan Tarif
                </button>
            </div>
        </form>

        <form onsubmit="event.preventDefault(); window.showBucikToast && window.showBucikToast('Catatan kurir mandiri diperbarui untuk sesi ini.', 'info');"
              class="bg-white bucik-card border border-[#1B362D]/15 border-l-4 border-l-[#1B362D] p-6 shadow-xs space-y-4">
            <div>
                <h2 class="font-serif text-lg font-bold text-[#1B362D]">Informasi Tambahan</h2>
                <p class="text-xs text-[#1B362D]/60 mt-0.5">Catatan ini akan tampil di halaman checkout pengguna.</p>
            </div>

            <div class="pt-1">
                <label for="ongkir-catatan" class="block text-[11px] font-bold text-[#1B362D] mb-1.5">Catatan Kurir Mandiri</label>
                <textarea id="ongkir-catatan" rows="4"
                          class="w-full px-3.5 py-2.5 rounded-xl border border-[#1B362D]/15 bg-white text-xs sm:text-sm text-[#1B362D]/85 leading-relaxed focus:outline-none focus:border-[#E29E31]">Pengiriman dilakukan oleh tim kurir Dapur Bucik (Bukan ojek online). Harap maklum jika ada sedikit keterlambatan di jam sibuk.</textarea>
            </div>

            <label class="flex items-start gap-2.5 cursor-pointer pt-1">
                <input type="checkbox" checked class="mt-0.5 w-4 h-4 accent-[#1B362D] rounded">
                <span>
                    <strong class="block text-xs font-bold text-[#1B362D]">Aktifkan Kurir Mandiri</strong>
                    <span class="block text-[11px] text-[#1B362D]/55">Matikan jika kurir sedang tidak tersedia hari ini.</span>
                </span>
            </label>

            <div class="pt-2">
                <button type="submit"
                        class="w-full py-2.5 px-5 bucik-btn bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] text-xs font-bold border border-[#1B362D]/40 shadow-[0_2px_0_#1B362D] transition cursor-pointer">
                    Simpan Info
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('btn-add-radius')?.addEventListener('click', function () {
        const list = document.getElementById('radius-zones-list');
        if (!list) return;
        const wrapper = document.createElement('div');
        wrapper.className = 'radius-item p-3.5 rounded-xl bg-[#FAF4E8]/70 border border-[#1B362D]/10';
        wrapper.innerHTML = `
            <label class="block text-[11px] font-bold text-[#1B362D] mb-1.5">Jarak 5.1 - 8 KM</label>
            <div class="flex items-center gap-2.5">
                <input type="text" value="20.000" class="flex-1 px-3.5 py-2 rounded-lg border border-[#1B362D]/15 bg-white text-sm font-medium focus:outline-none focus:border-[#E29E31]">
                <button type="button" onclick="this.closest('.radius-item')?.remove()" class="text-red-500 hover:text-red-700 p-1.5 cursor-pointer" aria-label="Hapus Zona">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        `;
        list.appendChild(wrapper);
    });
</script>
@endpush
