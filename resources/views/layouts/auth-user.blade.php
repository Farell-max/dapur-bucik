<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-[#FAF4E8] text-[#1B362D] flex flex-col justify-center items-center px-4 py-10 antialiased">
    @include('partials.floating-alerts')

    <div class="w-full max-w-md">
        <a href="{{ route('user.home') }}" class="flex flex-col items-center text-center mb-6 group">
            <span class="w-12 h-12 bucik-card bg-[#1B362D] text-[#E29E31] flex items-center justify-center shadow-sm group-hover:scale-105 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 11h16M5 11a7 7 0 0 0 14 0M9 7V5m3 2V4m3 3V5M7 19h10"/>
                </svg>
            </span>
            <span class="mt-3 font-serif text-2xl font-bold text-[#1B362D]">Dapur Bucik</span>
            <span class="text-xs font-medium text-[#1B362D]/65">Masakan Rumahan Hangat Setiap Hari</span>
        </a>

        <div class="bg-white bucik-card shadow-sm border border-[#1B362D]/15 border-l-4 border-l-[#E29E31] p-6 sm:p-8">
            @yield('auth_content')
        </div>

        <p class="mt-6 text-center text-xs text-[#1B362D]/65">
            <a href="{{ route('user.home') }}" class="hover:text-[#1B362D] font-semibold underline underline-offset-4">
                &larr; Kembali ke Beranda Dapur Bucik
            </a>
        </p>
    </div>

    @stack('scripts')
</body>
</html>
