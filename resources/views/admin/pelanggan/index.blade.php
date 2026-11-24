@extends('layouts.admin')

@section('title', 'Manajemen Pelanggan — Dapur Bucik')

@section('content')
    <div class="pb-4 mb-6 border-b border-dashed border-[#1B362D]/20">
        <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#1B362D]">Buku Pelanggan</h1>
    </div>

    <div class="bg-white bucik-card border border-[#1B362D]/15 shadow-xs overflow-hidden">
        <form method="GET" action="{{ route('admin.pelanggan.index') }}" class="p-4 sm:p-5 bg-[#FAF4E8]/45 border-b border-[#1B362D]/10 flex flex-wrap items-center justify-between gap-3">
            <input name="q" type="search" value="{{ $filters['q'] }}" placeholder="Cari nama atau email..."
                   class="w-full sm:w-72 px-4 py-2 rounded-xl border border-[#1B362D]/15 bg-white text-sm focus:outline-none focus:border-[#E29E31]">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.pelanggan.index', ['q' => $filters['q']]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs {{ empty($filters['status']) ? 'font-bold bg-[#1B362D] text-[#FAF4E8]' : 'font-semibold text-[#1B362D]/70 hover:bg-white' }}">
                    Semua
                </a>
                <a href="{{ route('admin.pelanggan.index', ['q' => $filters['q'], 'status' => 'aktif']) }}"
                   class="px-3 py-1.5 rounded-lg text-xs {{ $filters['status'] === 'aktif' ? 'font-bold bg-[#1B362D] text-[#FAF4E8]' : 'font-semibold text-[#1B362D]/70 hover:bg-white' }}">
                    Aktif
                </a>
                <a href="{{ route('admin.pelanggan.index', ['q' => $filters['q'], 'status' => 'diblokir']) }}"
                   class="px-3 py-1.5 rounded-lg text-xs {{ $filters['status'] === 'diblokir' ? 'font-bold bg-[#1B362D] text-[#FAF4E8]' : 'font-semibold text-[#1B362D]/70 hover:bg-white' }}">
                    Diblokir
                </a>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-[#1B362D]/10 text-[11px] font-extrabold uppercase tracking-wider text-[#1B362D]/60">
                    <tr>
                        <th class="px-5 py-3.5">Nama Panggilan</th>
                        <th class="px-5 py-3.5">Kontak</th>
                        <th class="px-5 py-3.5">Total Transaksi</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1B362D]/10">
                    @forelse ($customers as $customer)
                        @php
                            $isActive = $customer->status_akun === 'aktif';
                        @endphp
                        <tr class="{{ $isActive ? 'hover:bg-[#FAF4E8]/35' : 'bg-red-50/40' }} transition">
                            <td class="px-5 py-4 font-bold text-[#1B362D]">
                                {{ $customer->nama }}
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-xs sm:text-sm text-[#1B362D]">{{ $customer->email }}</p>
                                <p class="text-[11px] text-[#1B362D]/55 mt-0.5">{{ $customer->no_hp }}</p>
                            </td>
                            <td class="px-5 py-4 font-bold text-[#1B362D]">
                                {{ $customer->pesanan_count }} Pesanan
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if ($isActive)
                                    <span class="bucik-stamp border-emerald-700 bg-emerald-50 text-emerald-800">
                                        AKTIF
                                    </span>
                                @else
                                    <span class="bucik-stamp border-red-500 text-red-600 bg-white">
                                        DIBLOKIR
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-3">
                                    @if ($isActive)
                                        <a href="{{ route('admin.pesanan.index', ['tab' => 'semua', 'q' => $customer->nama]) }}"
                                           class="text-xs font-bold text-[#1B362D] underline underline-offset-4 hover:text-[#CC8A22]">
                                            Riwayat
                                        </a>
                                    @endif
                                    <form method="POST" action="{{ route('admin.pelanggan.toggle-status', $customer->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="text-xs font-bold underline underline-offset-4 cursor-pointer {{ $isActive ? 'text-red-600 hover:text-red-800' : 'text-[#1B362D] hover:text-[#CC8A22]' }}">
                                            {{ $isActive ? 'Blokir' : 'Buka Blokir' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-sm text-[#1B362D]/65">Belum ada data pelanggan ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
