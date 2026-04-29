@extends('layouts.app')

@section('title', 'Tambah User - SIMAS')

@section('content')
<div class="min-h-screen flex flex-col items-start pt-4 px-6">
    
    {{-- HEADER --}}
    <div class="w-full max-w-7xl mx-auto mb-6">
        <div>
            <h1 class="text-3xl font-black text-red-600 uppercase tracking-tighter">
                Tambah User Baru
            </h1>
            <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider mt-1">
                Form pendaftaran pengguna baru sistem SIMAS
            </p>
        </div>
    </div>

    {{-- MAIN CONTAINER --}}
    <div class="w-full max-w-7xl mx-auto">
        <div class="bg-white rounded-[2.5rem] shadow-sm p-8">
            
            {{-- ERROR MESSAGES --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-2 border-red-200 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-circle text-red-600 mt-1"></i>
                        <div>
                            <h3 class="font-black text-red-800 mb-2">Error Submit</h3>
                            <div class="space-y-1">
                                @foreach ($errors->messages() as $field => $messages)
                                    @foreach($messages as $message)
                                        <p class="text-red-700 text-sm">- {{ $message }}</p>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- SESSION ERROR --}}
            @if(session('error'))
                <div class="mb-6 bg-red-50 border-2 border-red-200 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-circle text-red-600 mt-1"></i>
                        <div>
                            <h3 class="font-black text-red-800 mb-2">Error</h3>
                            <p class="text-red-700 text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- FORM --}}
            <form action="{{ route('users.store') }}" method="POST" id="createUserForm" autocomplete="off">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    {{-- LEFT COLUMN --}}
                    <div class="space-y-6">
                        
                        {{-- NAMA LENGKAP --}}
                        <div>
                            <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                                Nama Lengkap
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                    name="name" 
                                    value="{{ old('name') }}"
                                    required
                                    class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-semibold text-slate-700 focus:border-red-500 focus:outline-none transition-colors pl-12">
                                <i class="fas fa-user absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                            </div>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- EMAIL ADDRESS --}}
                        <div>
                            <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                                Email Address
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="email" 
                                    name="email" 
                                    value="{{ old('email') }}"
                                    required
                                    autocomplete="off"
                                    class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-semibold text-slate-700 focus:border-red-500 focus:outline-none transition-colors pl-12">
                                <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                            </div>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ROLE USER --}}
                        <div>
                            <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                                Role User
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="role_id" 
                                    required
                                    class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-semibold text-slate-700 focus:border-red-500 focus:outline-none transition-colors pl-12 appearance-none">
                                    <option value="">-- Pilih Role --</option>
                                    <option value="1" {{ old('role_id') == '1' ? 'selected' : '' }}>Admin</option>
                                    <option value="2" {{ old('role_id') == '2' ? 'selected' : '' }}>Staff</option>
                                </select>
                                <i class="fas fa-user-shield absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                                <i class="fas fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                            </div>
                            @error('role_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- RIGHT COLUMN --}}
                    <div class="space-y-6">
                        
                        {{-- PASSWORD --}}
                        <div>
                            <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                                Password
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" 
                                    id="password"
                                    name="password" 
                                    required
                                    autocomplete="new-password"
                                    class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-semibold text-slate-700 focus:border-red-500 focus:outline-none transition-colors pl-12 pr-12">
                                <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                                <button type="button" 
                                    onclick="togglePassword('password', 'passwordToggle')"
                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                                    <i id="passwordToggle" class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-slate-500 mt-1">Minimal 8 karakter</p>
                        </div>

                        {{-- KONFIRMASI PASSWORD --}}
                        <div>
                            <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                                Konfirmasi Password
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" 
                                    id="password_confirmation"
                                    name="password_confirmation" 
                                    required
                                    autocomplete="new-password"
                                    class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-semibold text-slate-700 focus:border-red-500 focus:outline-none transition-colors pl-12 pr-12">
                                <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                                <button type="button" 
                                    onclick="togglePassword('password_confirmation', 'passwordConfirmToggle')"
                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                                    <i id="passwordConfirmToggle" class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- STATUS AKUN --}}
                        <div>
                            <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                                Status Akun
                            </label>
                            <div class="relative">
                                <select name="is_active" 
                                    class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-semibold text-slate-700 focus:border-red-500 focus:outline-none transition-colors pl-12 appearance-none">
                                    <option value="1" selected>Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                                <i class="fas fa-toggle-on absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                                <i class="fas fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- FORM ACTIONS --}}
                <div class="flex justify-between items-center mt-8 pt-8 border-t-2 border-slate-200">
                    <a href="{{ route('users.index') }}" 
                        class="px-6 py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl font-bold transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                    
                    <button type="submit" 
                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
                        <i class="fas fa-save mr-2"></i> Simpan User
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- JavaScript --}}
<script>
// Toggle password visibility
function togglePassword(inputId, toggleId) {
    const input = document.getElementById(inputId);
    const toggle = document.getElementById(toggleId);
    
    if (input.type === 'password') {
        input.type = 'text';
        toggle.classList.remove('fa-eye');
        toggle.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        toggle.classList.remove('fa-eye-slash');
        toggle.classList.add('fa-eye');
    }
}

// Form validation
document.getElementById('createUserForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const passwordConfirmation = document.getElementById('password_confirmation').value;
    
    // Check password match
    if (password !== passwordConfirmation) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Password Tidak Cocok',
            text: 'Password dan konfirmasi password harus sama',
            confirmButtonColor: '#E11D48'
        });
        return;
    }
    
    // Check password length
    if (password.length < 8) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Password Terlalu Pendek',
            text: 'Password minimal 8 karakter',
            confirmButtonColor: '#E11D48'
        });
        return;
    }
    
    // Show loading
    Swal.fire({
        title: 'Menyimpan...',
        text: 'Sedang menyimpan data user',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
});

// Clear form and prevent auto-fill on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check if there are validation errors
    const errorContainer = document.querySelector('.bg-red-50');
    const form = document.getElementById('createUserForm');
    
    if (errorContainer) {
        // Check specific errors and only clear fields that have errors
        const errorText = errorContainer.textContent.toLowerCase();
        const nameInput = document.querySelector('input[name="name"]');
        const emailInput = document.querySelector('input[name="email"]');
        
        // Clear name field only if there's a name error
        if (errorText.includes('nama') || errorText.includes('name')) {
            if (nameInput) nameInput.value = '';
            if (nameInput) nameInput.focus();
        }
        
        // Clear email field only if there's an email error
        if (errorText.includes('email')) {
            if (emailInput) emailInput.value = '';
            // Focus on name if email error but no name error
            if (!errorText.includes('nama') && !errorText.includes('name')) {
                if (nameInput) nameInput.focus();
            }
        }
        
        // Keep password, role, and status fields intact
    } else {
        // Normal page load - just prevent auto-fill
        form.reset();
        
        const inputs = form.querySelectorAll('input');
        inputs.forEach(input => {
            if (input.type === 'email' || input.type === 'password') {
                input.value = '';
            }
        });
        
        document.querySelector('input[name="name"]').focus();
    }
});
</script>

@endsection
