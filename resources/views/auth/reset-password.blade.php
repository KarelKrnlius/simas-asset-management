<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMAS - Ubah Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-pattern {
            background-color: #f8fafc;
            background-image: radial-gradient(#bc1c1c 0.5px, transparent 0.5px);
            background-size: 24px 24px;
            background-opacity: 0.05;
        }
        /* Custom Outer Glow (Tebal) untuk Logo */
        .white-shadow-glow {
            filter: drop-shadow(0 0 15px rgba(255, 255, 255, 0.9))
                    drop-shadow(0 0 2px rgba(255, 255, 255, 1));
        }
    </style>
</head>
<body class="bg-pattern flex items-center justify-center min-h-screen p-6">

    <div class="flex w-full max-w-5xl bg-white rounded-[30px] shadow-[0_20px_50px_rgba(0,0,0,0.1)] overflow-hidden min-h-[600px]">
        
        <div class="hidden lg:flex w-[45%] bg-[#bc1c1c] relative flex-col items-center justify-center text-white overflow-hidden">
            <div class="absolute top-[-10%] left-[-10%] w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-64 h-64 bg-black/20 rounded-full blur-3xl"></div>

            <div class="relative z-10 text-center px-12">
                <div class="mb-10 inline-block transition-transform hover:scale-105 duration-500">
                    <img src="{{ asset('images/logo-brin.png') }}" 
                         alt="Logo BRIN" 
                         class="w-36 white-shadow-glow">
                </div>
                
                <h1 class="text-5xl font-extrabold tracking-tighter mb-4">SIMAS</h1>
                <div class="h-1.5 w-12 bg-white/40 mx-auto mb-6 rounded-full"></div>
                <p class="text-sm font-light opacity-80 leading-relaxed uppercase tracking-[0.3em]">
                    Asset Management <br> <span class="font-bold">Excellence</span>
                </p>
            </div>
            
            <p class="absolute bottom-8 text-[10px] opacity-50 tracking-widest uppercase text-center w-full">
                © 2026 Badan Riset dan Inovasi Nasional
            </p>
        </div>

        <div class="w-full lg:w-[55%] p-12 lg:p-20 flex flex-col justify-center relative bg-white">
            
            <div class="lg:hidden flex justify-center mb-8 bg-gray-100 p-4 rounded-3xl shadow-inner">
                <img src="{{ asset('images/logo-brin.png') }}" 
                     alt="Logo BRIN" 
                     class="w-24 drop-shadow-[0_4px_10px_rgba(0,0,0,0.2)]">
            </div>

            <div class="mb-10 text-center lg:text-left">
                <h2 class="text-3xl font-800 text-gray-900 mb-2 font-bold uppercase tracking-tight">Ubah Password</h2>
                <p class="text-gray-400 text-sm italic">Keamanan akun Anda adalah prioritas kami.</p>
            </div>

            <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="token" value="{{ $token ?? '' }}">
                <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

                <div class="group">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2 ml-1">Password Baru</label>
                    <div class="relative transition-all duration-300 group-focus-within:transform group-focus-within:-translate-y-1">
                        <input type="password" name="password" placeholder="••••••••" required
                            class="w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 focus:border-[#bc1c1c] focus:bg-white rounded-2xl transition-all duration-300 outline-none text-sm shadow-sm @error('password') border-red-500 @enderror">
                    </div>
                    @error('password')
                        <span class="text-red-500 text-[10px] mt-2 block ml-1 uppercase font-bold">{{ $message }}</span>
                    @enderror
                </div>

                <div class="group">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2 ml-1">Konfirmasi Password Baru</label>
                    <div class="relative transition-all duration-300 group-focus-within:transform group-focus-within:-translate-y-1">
                        <input type="password" name="password_confirmation" placeholder="••••••••" required
                            class="w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 focus:border-[#bc1c1c] focus:bg-white rounded-2xl transition-all duration-300 outline-none text-sm shadow-sm">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" 
                        class="w-full bg-gray-900 hover:bg-[#bc1c1c] text-white font-bold py-4 rounded-2xl shadow-[0_10px_20px_rgba(0,0,0,0.1)] hover:shadow-[#bc1c1c]/30 transition-all duration-500 transform active:scale-95 uppercase tracking-widest text-xs">
                        Perbarui Password
                    </button>
                </div>
            </form>

            <div class="mt-12 flex items-center justify-center space-x-4">
                <div class="h-[1px] w-8 bg-gray-200"></div>
                <a href="{{ url('/login') }}" class="text-[11px] font-bold text-gray-400 hover:text-[#bc1c1c] transition-colors uppercase tracking-widest">
                    Kembali ke Login
                </a>
                <div class="h-[1px] w-8 bg-gray-200"></div>
            </div>
        </div>
    </div>

</body>
</html>