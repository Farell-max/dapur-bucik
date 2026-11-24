@php
    $flashItems = [];
    if (session('success')) {
        $flashItems[] = [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => (string) session('success'),
            'border' => 'border-l-[#1B362D]',
            'badge' => 'bg-emerald-100 text-emerald-800',
            'icon' => '&#10003;',
        ];
    }
    if (session('error')) {
        $flashItems[] = [
            'type' => 'error',
            'title' => 'Gagal / Ditolak',
            'message' => (string) session('error'),
            'border' => 'border-l-red-600',
            'badge' => 'bg-red-100 text-red-700',
            'icon' => '!',
        ];
    }
    if (session('info')) {
        $flashItems[] = [
            'type' => 'info',
            'title' => 'Informasi Dapur',
            'message' => (string) session('info'),
            'border' => 'border-l-[#E29E31]',
            'badge' => 'bg-[#E29E31]/25 text-[#1B362D]',
            'icon' => 'i',
        ];
    }
    if (isset($errors) && $errors->any()) {
        foreach ($errors->all() as $errMessage) {
            $flashItems[] = [
                'type' => 'error',
                'title' => 'Periksa Input Anda',
                'message' => (string) $errMessage,
                'border' => 'border-l-red-600',
                'badge' => 'bg-red-100 text-red-700',
                'icon' => '!',
            ];
        }
    }
@endphp

<div id="bucik-alert-stack"
     class="no-print fixed top-20 right-4 sm:right-6 z-50 w-full max-w-sm space-y-3 pointer-events-none"
     aria-live="polite">
    @foreach ($flashItems as $alert)
        <div data-bucik-alert
             role="alert"
             class="pointer-events-auto bucik-card bg-white text-[#1B362D] border border-[#1B362D]/20 border-l-4 {{ $alert['border'] }} shadow-[0_12px_30px_rgba(27,54,45,0.18)] p-4 flex items-start gap-3 transition-all duration-300">
            <span class="w-7 h-7 rounded-full {{ $alert['badge'] }} flex items-center justify-center shrink-0 text-xs font-extrabold mt-0.5">
                {!! $alert['icon'] !!}
            </span>
            <div class="flex-1 min-w-0">
                <p class="font-serif text-xs font-bold uppercase tracking-wider text-[#1B362D]/70">
                    {{ $alert['title'] }}
                </p>
                <p class="text-xs sm:text-sm font-semibold text-[#1B362D] mt-0.5 break-words">
                    {{ $alert['message'] }}
                </p>
            </div>
            <button type="button"
                    data-bucik-alert-close
                    aria-label="Tutup Notifikasi"
                    class="text-[#1B362D]/45 hover:text-[#1B362D] text-lg leading-none font-bold px-1 cursor-pointer">
                &times;
            </button>
        </div>
    @endforeach
</div>

<div id="bucik-toast"
     class="no-print fixed bottom-5 right-5 z-50 pointer-events-none opacity-0 translate-y-3 transition-all duration-200">
    <div class="bucik-card flex items-center gap-2.5 px-4 py-3 bg-[#1B362D] text-[#FAF4E8] shadow-lg border-l-4 border-[#E29E31] text-sm font-medium">
        <span class="w-2 h-2 rounded-full bg-[#E29E31]"></span>
        <span data-toast-message>Tersimpan.</span>
    </div>
</div>
