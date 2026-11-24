@extends('layouts.auth-user')

@section('title', 'Masuk Pelanggan — Dapur Bucik')

@section('auth_content')
    <div class="mb-6">
        <h1 class="font-serif text-2xl font-bold text-[#1B362D]">Selamat Datang Kembali</h1>
        <p class="mt-1 text-sm text-[#1B362D]/70">Masuk untuk memesan hidangan favorit dan melacak pesanan Anda.</p>
    </div>

    <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wide text-[#1B362D]/80 mb-1.5">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   class="w-full px-4 py-2.5 rounded-xl border border-[#1B362D]/20 bg-[#FAF4E8]/40 text-sm focus:outline-none focus:border-[#E29E31] focus:ring-2 focus:ring-[#E29E31]/30"
                   placeholder="budisantoso@gmail.com">
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-bold uppercase tracking-wide text-[#1B362D]/80">Kata Sandi</label>
                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-[#CC8A22] hover:underline">Lupa kata sandi?</a>
            </div>
            <input id="password" name="password" type="password" required
                   class="w-full px-4 py-2.5 rounded-xl border border-[#1B362D]/20 bg-[#FAF4E8]/40 text-sm focus:outline-none focus:border-[#E29E31] focus:ring-2 focus:ring-[#E29E31]/30"
                   placeholder="••••••••">
        </div>

        <button type="submit"
                class="w-full py-3 rounded-xl bg-[#E29E31] hover:bg-[#CC8A22] text-[#1B362D] font-bold text-sm transition shadow-xs cursor-pointer">
            Masuk
        </button>
    </form>

    <div class="my-5 flex items-center gap-3">
        <div class="h-px flex-1 bg-[#1B362D]/15"></div>
        <span class="text-xs font-medium text-[#1B362D]/55">atau</span>
        <div class="h-px flex-1 bg-[#1B362D]/15"></div>
    </div>

    <button type="button"
            onclick="window.showBucikToast && window.showBucikToast('Login Google belum diaktifkan. Gunakan email dan kata sandi akun Anda.')"
            class="w-full py-2.5 px-4 rounded-xl border border-[#1B362D]/20 bg-white hover:bg-[#FAF4E8]/70 text-sm font-semibold text-[#1B362D] flex items-center justify-center gap-2.5 transition cursor-pointer">
        <svg class="w-4 h-4" viewBox="0 0 24 24" aria-hidden="true">
            <path fill="#EA4335" d="M12 10.2v3.9h5.4c-.2 1.3-1.5 3.9-5.4 3.9-3.2 0-5.9-2.7-5.9-6s2.7-6 5.9-6c1.9 0 3.1.8 3.8 1.5l2.6-2.5C16.8 3.5 14.6 2.5 12 2.5 6.8 2.5 2.5 6.8 2.5 12s4.3 9.5 9.5 9.5c5.5 0 9.1-3.9 9.1-9.3 0-.6-.1-1.1-.2-1.6H12Z"/>
            <path fill="#34A853" d="M3.6 7.5l3.2 2.3C7.7 7.7 9.7 6.3 12 6.3c1.9 0 3.1.8 3.8 1.5l2.6-2.5C16.8 3.5 14.6 2.5 12 2.5c-3.7 0-6.9 2.1-8.4 5Z"/>
            <path fill="#FBBC05" d="M12 21.5c2.5 0 4.7-.8 6.3-2.3l-2.9-2.4c-.8.6-1.9 1-3.4 1-2.6 0-4.9-1.8-5.7-4.2l-3.2 2.5c1.5 3.2 4.9 5.4 8.9 5.4Z"/>
            <path fill="#4285F4" d="M21.1 12.2c0-.6-.1-1.1-.2-1.6H12v3.9h5.4c-.3 1.3-1.1 2.4-2.1 3.1l2.9 2.4c1.7-1.6 2.9-4 2.9-7.8Z"/>
        </svg>
        <span>Masuk dengan Google</span>
    </button>

    <p class="mt-6 text-center text-sm text-[#1B362D]/75">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-bold text-[#1B362D] underline decoration-[#E29E31] decoration-2 underline-offset-4 hover:text-[#CC8A22]">
            Daftar Sekarang
        </a>
    </p>
@endsection
