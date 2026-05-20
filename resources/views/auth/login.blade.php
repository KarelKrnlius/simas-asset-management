<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        .logo-float { animation: float 3s ease-in-out infinite; filter: drop-shadow(0 0 8px rgba(255,255,255,0.8)) drop-shadow(0 0 15px rgba(255,255,255,0.6)); }
    </style>
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-4">

<div class="w-full max-w-4xl bg-white rounded-2xl overflow-hidden shadow-2xl flex flex-col md:flex-row min-h-[480px]">

    {{-- LEFT — branding --}}
    <div class="bg-red-700 text-white flex flex-col items-center justify-center p-8 md:p-12 md:w-2/5 flex-shrink-0">
        <img src="{{ asset('images/logo/logo.png') }}" alt="SIMAS Logo" class="logo-float w-24 md:w-32 mb-4">
        <h2 class="text-2xl md:text-3xl font-black tracking-tight">SIMAS</h2>
        <p class="text-sm text-red-200 mt-1 text-center">Sistem Manajemen Aset</p>
    </div>

    {{-- RIGHT — form --}}
    <div class="flex-1 flex flex-col items-center justify-center p-8 md:p-12">

        <div class="w-full max-w-sm">
            {{-- Title --}}
            <div class="text-center mb-8">
                <h1 class="text-3xl font-black text-red-700 tracking-tight">SIMAS</h1>
                <div class="w-12 h-1 bg-red-700 rounded mx-auto my-3"></div>
                <p class="text-xs text-slate-400 font-semibold uppercase tracking-widest">Sistem Manajemen Aset</p>
                <p class="text-lg font-bold text-slate-700 mt-2">Masuk ke Sistem</p>
            </div>

            {{-- Error --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-4">
                    {{ $errors->first() }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-4">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Form --}}
            <form id="loginForm" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <input type="email" name="email" placeholder="Email" required
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-red-600 focus:ring-2 focus:ring-red-100 transition">
                </div>

                <div class="mb-4 relative">
                    <input type="password" id="password" name="password" placeholder="Password" required
                        class="w-full px-4 py-3 pr-12 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-red-600 focus:ring-2 focus:ring-red-100 transition">
                    <button type="button" onclick="togglePassword()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs font-semibold">
                        <span id="toggleText">Show</span>
                    </button>
                </div>

                <div class="flex items-center justify-between mb-6 text-sm">
                    <label class="flex items-center gap-2 text-slate-500 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-red-600">
                        Remember Me
                    </label>
                    <a href="/forgot-password" class="text-red-600 hover:text-red-700 font-semibold">Lupa Password?</a>
                </div>

                <button type="submit" id="btnLogin"
                    class="w-full bg-red-700 hover:bg-red-800 text-white font-bold py-3 rounded-xl transition-all hover:shadow-lg active:scale-95">
                    Masuk
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const pass = document.getElementById('password');
    const text = document.getElementById('toggleText');
    pass.type = pass.type === 'password' ? 'text' : 'password';
    text.textContent = pass.type === 'password' ? 'Show' : 'Hide';
}

document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('btnLogin');
    btn.disabled = true;
    btn.textContent = 'Memuat...';
});

@if(auth()->check())
    window.location.replace('/dashboard');
@endif
</script>
</body>
</html>
