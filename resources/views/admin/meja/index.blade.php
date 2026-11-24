@extends('layouts.admin')

@section('title', 'Manajemen Meja & QR â€” Dapur Bucik')

@section('content')
    <div class="pb-4 mb-6 border-b border-dashed border-[#1B362D]/20 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#1B362D]">Manajemen Meja Dine-in</h1>

        <button type="button" id="btn-add-meja"
                class="px-5 py-2.5 bucik-btn bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] text-xs sm:text-sm font-bold border border-[#1B362D]/40 shadow-[0_2px_0_#1B362D] transition cursor-pointer">
            + Tambah Meja Baru
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse ($tables as $index => $table)
            @php
                $targetUrl = url('/menu?meja=' . $table->id);
                $qrSvg = (string) \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                    ->size(116)
                    ->margin(1)
                    ->color(27, 54, 45)
                    ->generate($targetUrl);

                // Pisahkan judul utama (mis. "Meja 1") dan subjudul area (mis. "Area Dekat Jendela")
                $rawName = (string) $table->nomor_meja;
                $mainTitle = $rawName;
                $areaSubtitle = 'Area Dine-in';
                if (preg_match('/^([^(]+)\(([^)]+)\)/', $rawName, $matches)) {
                    $mainTitle = trim($matches[1]);
                    $areaSubtitle = trim($matches[2]);
                }

                $tablePayload = [
                    'id' => $table->id,
                    'nomor_meja' => $table->nomor_meja,
                    'target_url' => $targetUrl,
                    'update_url' => route('admin.meja.update', $table->id),
                    'delete_url' => route('admin.meja.destroy', $table->id),
                ];
            @endphp

            <article class="bg-white bucik-card border border-[#1B362D]/15 border-l-4 {{ $index % 2 === 0 ? 'border-l-[#E29E31]' : 'border-l-[#1B362D]' }} p-6 shadow-xs text-center flex flex-col justify-between">
                <div>
                    <h2 class="font-serif text-xl font-bold text-[#1B362D]">{{ $mainTitle }}</h2>
                    <p class="text-xs text-[#1B362D]/55 mt-0.5">{{ $areaSubtitle }}</p>
                    <span class="sr-only">{{ $table->nomor_meja }}</span>
                    <span class="sr-only">{{ $targetUrl }}</span>

                    <div class="my-6 inline-flex flex-col items-center justify-center p-2.5 rounded-lg bg-[#1B362D] border-2 border-dashed border-[#1B362D]/30 shadow-xs">
                        <div class="bg-white p-1.5 rounded qr-svg-wrapper">
                            {!! $qrSvg !!}
                        </div>
                        <span class="mt-1.5 text-[11px] font-bold tracking-wider text-[#FAF4E8]">QR {{ $table->id }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5 pt-1">
                    <button type="button"
                            class="btn-cetak-qr py-2 px-3 bucik-btn border border-[#1B362D]/30 bg-white hover:bg-[#FAF4E8] text-[#1B362D] text-xs font-bold transition cursor-pointer"
                            data-table="{{ json_encode($tablePayload) }}">
                        Cetak QR
                    </button>
                    <button type="button"
                            class="btn-edit-meja py-2 px-3 bucik-btn border border-[#1B362D]/30 bg-white hover:bg-[#FAF4E8] text-[#1B362D] text-xs font-bold transition cursor-pointer"
                            data-table="{{ json_encode($tablePayload) }}">
                        Edit
                    </button>
                </div>
            </article>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-[#1B362D]/10 p-10 text-center">
                <p class="font-serif text-xl font-bold text-[#1B362D]">Belum Ada Data Meja</p>
                <p class="text-sm text-[#1B362D]/70 mt-1">Klik tombol <strong>+ Tambah Meja Baru</strong> untuk membuat meja dan QR Code otomatis.</p>
            </div>
        @endforelse
    </div>

    <div id="meja-modal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md border-t-4 border-[#E29E31] overflow-hidden shadow-xl">
            <div class="px-6 py-4 bg-[#1B362D] text-[#FAF4E8] flex items-center justify-between">
                <h2 id="meja-modal-title" class="font-serif text-xl font-bold">Tambah Meja Baru</h2>
                <button type="button" id="close-meja-modal" class="text-xl leading-none cursor-pointer">&times;</button>
            </div>
            <div class="p-6 space-y-4">
                <form id="meja-modal-form" method="POST" action="{{ route('admin.meja.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="_method" id="meja-form-method" value="POST">
                    <div>
                        <label for="form-nomor-meja" class="block text-xs font-bold uppercase text-[#1B362D]/80 mb-1">Nomor / Nama Meja</label>
                        <input id="form-nomor-meja" name="nomor_meja" type="text" required
                               placeholder="Contoh: Meja 4 (Lantai Atas)"
                               class="w-full px-3.5 py-2 rounded-xl border border-[#1B362D]/20 bg-[#FAF4E8]/40 text-sm">
                        <p class="text-[11px] text-[#1B362D]/65 mt-1.5">
                            Kode QR otomatis mengarah ke <code>{{ url('/menu?meja=ID') }}</code>.
                        </p>
                    </div>
                    <div class="flex items-center justify-between gap-2 pt-2">
                        <button type="button" id="btn-trigger-delete-meja"
                                class="hidden px-3.5 py-2 rounded-xl bg-red-50 text-red-700 hover:bg-red-100 text-xs font-bold cursor-pointer">
                            Hapus Meja
                        </button>
                        <div class="flex items-center gap-2 ml-auto">
                            <button type="button" id="cancel-meja-modal" class="px-4 py-2 rounded-xl border border-[#1B362D]/20 text-xs font-bold cursor-pointer">Batal</button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-[#E29E31] text-[#1B362D] text-xs font-bold cursor-pointer">Simpan</button>
                        </div>
                    </div>
                </form>

                <form id="meja-delete-form" method="POST" action="" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>

    <div id="print-qr-modal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-sm p-6 text-center border-t-4 border-[#1B362D] shadow-xl">
            <span class="text-xs font-bold uppercase tracking-widest text-[#CC8A22]">Dapur Bucik &bull; Dine-in</span>
            <h2 id="print-qr-name" class="font-serif text-xl font-bold text-[#1B362D] mt-1">Meja</h2>
            <div class="my-4 p-5 rounded-2xl bg-[#FAF4E8] border border-[#1B362D]/15 inline-block">
                <div id="print-qr-svg" class="bg-white p-2 rounded-xl inline-block"></div>
                <p id="print-qr-url" class="mt-2 text-[11px] font-bold text-[#1B362D] break-all"></p>
            </div>
            <div class="no-print flex gap-2">
                <button type="button" onclick="window.print()" class="flex-1 py-2.5 rounded-xl bg-[#E29E31] text-[#1B362D] text-xs font-bold cursor-pointer">Print Kartu QR</button>
                <button type="button" id="close-print-qr" class="px-4 py-2.5 rounded-xl border border-[#1B362D]/20 text-xs font-bold cursor-pointer">Tutup</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const mejaModal = document.getElementById('meja-modal');
        const mejaForm = document.getElementById('meja-modal-form');
        const mejaMethod = document.getElementById('meja-form-method');
        const mejaTitle = document.getElementById('meja-modal-title');
        const nomorInput = document.getElementById('form-nomor-meja');
        const deleteBtn = document.getElementById('btn-trigger-delete-meja');
        const deleteForm = document.getElementById('meja-delete-form');

        document.getElementById('btn-add-meja')?.addEventListener('click', () => {
            if (mejaForm) mejaForm.action = '{{ route('admin.meja.store') }}';
            if (mejaMethod) mejaMethod.value = 'POST';
            if (mejaTitle) mejaTitle.textContent = 'Tambah Meja Baru';
            if (nomorInput) nomorInput.value = '';
            deleteBtn?.classList.add('hidden');
            mejaModal?.classList.remove('hidden');
        });

        document.querySelectorAll('.btn-edit-meja').forEach((btn) => {
            btn.addEventListener('click', function () {
                const table = JSON.parse(this.dataset.table || '{}');
                if (mejaForm && table.update_url) mejaForm.action = table.update_url;
                if (deleteForm && table.delete_url) deleteForm.action = table.delete_url;
                if (mejaMethod) mejaMethod.value = 'PUT';
                if (mejaTitle) mejaTitle.textContent = 'Edit Meja #' + (table.id || '');
                if (nomorInput) nomorInput.value = table.nomor_meja || '';
                deleteBtn?.classList.remove('hidden');
                mejaModal?.classList.remove('hidden');
            });
        });

        deleteBtn?.addEventListener('click', () => {
            if (confirm('Hapus meja ini?')) {
                deleteForm?.submit();
            }
        });

        document.getElementById('close-meja-modal')?.addEventListener('click', () => mejaModal?.classList.add('hidden'));
        document.getElementById('cancel-meja-modal')?.addEventListener('click', () => mejaModal?.classList.add('hidden'));

        const printModal = document.getElementById('print-qr-modal');
        const printName = document.getElementById('print-qr-name');
        const printUrl = document.getElementById('print-qr-url');
        const printSvgContainer = document.getElementById('print-qr-svg');

        document.querySelectorAll('.btn-cetak-qr').forEach((btn) => {
            btn.addEventListener('click', function () {
                const table = JSON.parse(this.dataset.table || '{}');
                const cardSvg = this.closest('article')?.querySelector('.qr-svg-wrapper')?.innerHTML || '';
                if (printName) printName.textContent = table.nomor_meja || '';
                if (printUrl) printUrl.textContent = table.target_url || '';
                if (printSvgContainer) printSvgContainer.innerHTML = cardSvg;
                printModal?.classList.remove('hidden');
            });
        });
        document.getElementById('close-print-qr')?.addEventListener('click', () => printModal?.classList.add('hidden'));
    })();
</script>
@endpush
