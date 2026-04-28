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
                    <img src="{{ asset('images/logo/logo.png') }}" 
                         alt="Logo BRIN" 
                         class="w-36 white-shadow-glow">
                </div>
                
                <h1 class="text-5xl font-extrabold tracking-tighter mb-4">SIMAS</h1>
                <div class="h-1.5 w-12 bg-white/40 mx-auto mb-6 rounded-full"></div>
                <p class="text-sm font-light opacity-80 leading-relaxed uppercase tracking-[0.3em]">
                    Sistem Manajemen <br> <span class="font-bold">Asset</span>
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
                        <input type="password" name="password" id="password" placeholder="••••••••" required minlength="8"
                            class="w-full px-5 py-4 pr-12 bg-gray-50 border-2 border-gray-100 focus:border-[#bc1c1c] focus:bg-white rounded-2xl transition-all duration-300 outline-none text-sm shadow-sm @error('password') border-red-500 @enderror">
                        <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i id="password-toggle" class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="mt-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] ml-1 uppercase font-bold text-gray-400" id="password-strength-text">Minimal 8 karakter</span>
                            <span class="text-[10px] uppercase font-bold text-gray-400" id="password-length">0/8</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                            <div id="password-strength" class="h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <div class="mt-1">
                            <span id="password-requirement" class="text-[9px] text-gray-500">• Password harus minimal 8 karakter</span>
                        </div>
                    </div>
                    @error('password')
                        <span class="text-red-500 text-[10px] mt-2 block ml-1 uppercase font-bold">{{ $message }}</span>
                    @enderror
                </div>

                <div class="group">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2 ml-1">Konfirmasi Password Baru</label>
                    <div class="relative transition-all duration-300 group-focus-within:transform group-focus-within:-translate-y-1">
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="••••••••" required minlength="8"
                            class="w-full px-5 py-4 pr-12 bg-gray-50 border-2 border-gray-100 focus:border-[#bc1c1c] focus:bg-white rounded-2xl transition-all duration-300 outline-none text-sm shadow-sm">
                        <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i id="password_confirmation-toggle" class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="mt-2">
                        <div class="flex items-center">
                            <i id="match-icon" class="fas fa-times-circle text-gray-400 mr-2"></i>
                            <span id="match-text" class="text-[10px] uppercase font-bold text-gray-400">Password tidak cocok</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" 
                        class="w-full bg-gray-900 hover:bg-[#bc1c1c] text-white font-bold py-4 rounded-2xl shadow-[0_10px_20px_rgba(0,0,0,0.1)] hover:shadow-[#bc1c1c]/30 transition-all duration-500 transform active:scale-95 uppercase tracking-widest text-xs">
                        Perbarui Password
                    </button>
                </div>
            </form>

                    </div>
    </div>

<script>
// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const toggle = document.getElementById(fieldId + '-toggle');
    
    if (field.type === 'password') {
        field.type = 'text';
        toggle.classList.remove('fa-eye');
        toggle.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        toggle.classList.remove('fa-eye-slash');
        toggle.classList.add('fa-eye');
    }
}

// Password strength and validation
document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    const confirmField = document.getElementById('password_confirmation');
    const strengthBar = document.getElementById('password-strength');
    const strengthText = document.getElementById('password-strength-text');
    const lengthText = document.getElementById('password-length');
    const matchIcon = document.getElementById('match-icon');
    const matchText = document.getElementById('match-text');
    
    // Password strength checker
    passwordField.addEventListener('input', function() {
        const password = this.value;
        const length = password.length;
        const requirementText = document.getElementById('password-requirement');
        
        // Update length counter
        lengthText.textContent = `${length}/8`;
        lengthText.className = length >= 8 ? 'text-[10px] uppercase font-bold text-green-500' : 'text-[10px] uppercase font-bold text-red-500';
        
        // Update strength bar and text
        if (length === 0) {
            strengthBar.style.width = '0%';
            strengthBar.className = 'h-2 rounded-full transition-all duration-300';
            strengthText.textContent = 'Minimal 8 karakter';
            strengthText.className = 'text-[10px] ml-1 uppercase font-bold text-gray-400';
            requirementText.innerHTML = '• Password harus minimal 8 karakter';
            requirementText.className = 'text-[9px] text-gray-500';
        } else if (length < 8) {
            strengthBar.style.width = '50%';
            strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-red-500';
            strengthText.textContent = 'Terlalu pendek';
            strengthText.className = 'text-[10px] ml-1 uppercase font-bold text-red-500';
            requirementText.innerHTML = '• Password harus minimal 8 karakter (<span class="text-red-500 font-bold">' + (8 - length) + ' karakter lagi</span>)';
            requirementText.className = 'text-[9px] text-red-500';
        } else {
            strengthBar.style.width = '100%';
            strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-green-500';
            strengthText.textContent = 'Kuat';
            strengthText.className = 'text-[10px] ml-1 uppercase font-bold text-green-500';
            requirementText.innerHTML = '✓ Password memenuhi syarat minimal 8 karakter';
            requirementText.className = 'text-[9px] text-green-500';
        }
        
        // Check match with confirmation
        checkPasswordMatch();
    });
    
    // Password confirmation checker
    confirmField.addEventListener('input', checkPasswordMatch);
    
    function checkPasswordMatch() {
        const password = passwordField.value;
        const confirmation = confirmField.value;
        
        if (confirmation.length === 0) {
            matchIcon.className = 'fas fa-times-circle text-gray-400 mr-2';
            matchText.textContent = 'Password tidak cocok';
            matchText.className = 'text-[10px] uppercase font-bold text-gray-400';
        } else if (password === confirmation) {
            matchIcon.className = 'fas fa-check-circle text-green-500 mr-2';
            matchText.textContent = 'Password cocok';
            matchText.className = 'text-[10px] uppercase font-bold text-green-500';
        } else {
            matchIcon.className = 'fas fa-times-circle text-red-500 mr-2';
            matchText.textContent = 'Password tidak cocok';
            matchText.className = 'text-[10px] uppercase font-bold text-red-500';
        }
    }
});
</script>
</body>
</html>