@section('title', 'Login Admin — Dapur Bucik')
<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-[#1B362D] text-[#FAF4E8] flex flex-col justify-center items-center px-4 py-10 antialiased">
    @include('partials.floating-alerts')

    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <span class="w-14 h-14 mx-auto bucik-card bg-[#E29E31] text-[#1B362D] flex items-center justify-center font-serif font-bold text-2xl shadow-md">
                DB
            </span>
            <h1 class="mt-4 font-serif text-3xl font-bold text-[#FAF4E8]">Dapur Bucik</h1>
            <p class="text-xs font-semibold uppercase tracking-widest text-[#E29E31] mt-1">Panel Pengelola &amp; Dapur</p>
        </div>

        <div class="bg-white text-[#1B362D] bucik-card p-6 sm:p-8 shadow-xl border-t-4 border-[#E29E31]">
            <h2 class="font-serif text-2xl font-bold text-[#1B362D]">Masuk Admin</h2>
            <p class="mt-1 text-sm text-[#1B362D]/70">Masuk ke ruang kendali operasional, pesanan, dan menu Dapur Bucik.</p>

            <form method="POST" action="{{ route('admin.login.attempt') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="admin-email" class="block text-xs font-bold uppercase tracking-wide text-[#1B362D]/80 mb-1.5">Email Admin</label>
                    <input id="admin-email" name="email" type="email" value="{{ old('email') }}" required autofocus
                           placeholder="admindapurbucik@gmail.com"
                           class="w-full px-4 py-2.5 rounded-xl border border-[#1B362D]/20 bg-[#FAF4E8]/50 text-sm focus:outline-none focus:border-[#E29E31] focus:ring-2 focus:ring-[#E29E31]/30">
                </div>

                <div>
                    <label for="admin-password" class="block text-xs font-bold uppercase tracking-wide text-[#1B362D]/80 mb-1.5">Kata Sandi</label>
                    <input id="admin-password" name="password" type="password" required
                           placeholder="••••••••"
                           class="w-full px-4 py-2.5 rounded-xl border border-[#1B362D]/20 bg-[#FAF4E8]/50 text-sm focus:outline-none focus:border-[#E29E31] focus:ring-2 focus:ring-[#E29E31]/30">
                </div>

                <button type="submit"
                        class="w-full py-3 bucik-btn bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] border border-[#1B362D]/40 shadow-[0_2px_0_#1B362D] font-bold text-sm transition cursor-pointer">
                    Masuk ke Dashboard
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-[#FAF4E8]/75">
            <a href="{{ route('user.home') }}" class="hover:text-[#E29E31] underline underline-offset-4">
                &larr; Kembali ke Halaman Pelanggan
            </a>
        </p>
    </div>
</body>
</html>
