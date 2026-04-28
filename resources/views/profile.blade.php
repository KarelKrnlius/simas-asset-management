@extends('layouts.app')

@section('title', 'Profil | Asset System')

@section('content')
<style>
    /* Definisi warna utama agar konsisten */
    .bg-red-primary { background-color: #E11D48 !important; }
    .text-red-primary { color: #E11D48 !important; }
    .border-red-primary { border-color: #E11D48 !important; }
</style>

<div class="max-w-4xl w-full mx-auto -mt-4">
        <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
            
            <div class="h-32 bg-red-primary relative"></div>

            <div class="px-6 md:px-12 pb-12">
                <div class="relative flex flex-col md:flex-row justify-between items-center md:items-end mt-6 mb-12 gap-6">
                    <div class="flex flex-col md:flex-row items-center md:items-end gap-6 text-center md:text-left">
                        <div class="w-28 h-28 bg-white rounded-[2rem] shadow-2xl border border-slate-50 flex items-center justify-center text-red-primary -mt-20 relative z-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        
                        <div class="pb-1">
                            <h1 class="text-4xl font-black text-slate-800 tracking-tight leading-none">Profil</h1>
                            <p class="text-sm text-slate-400 font-medium mt-3">Kelola informasi akun Anda</p>
                        </div>
                    </div>

                    <button id="editToggle" class="flex items-center justify-center gap-2 px-8 py-4 bg-red-primary text-white rounded-2xl font-bold text-sm hover:scale-105 transition-all shadow-lg shadow-red-200 w-fit">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                        <span>Ubah Profil</span>
                    </button>
                </div>

                <form id="profileForm" action="{{ route('profile.update') }}" method="POST" class="space-y-10">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-wrap justify-center md:justify-start gap-4">
                        <div class="bg-slate-900 px-8 py-2.5 rounded-2xl flex items-center justify-center min-w-[120px] text-white shadow-sm">
                            <span class="text-xs font-bold uppercase tracking-[0.15em] text-center w-full">
                                {{ Auth::user()->role_id == 1 ? 'Administrator' : 'Staff' }}
                            </span>
                        </div>

                        @if(Auth::user()->is_active)
                            <div class="bg-emerald-50 px-6 py-2.5 rounded-2xl flex items-center justify-center border border-emerald-100 text-emerald-600 shadow-sm">
                                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse mr-2.5"></div>
                                <span class="text-xs font-bold uppercase tracking-[0.1em]">Status: Aktif</span>
                            </div>
                        @else
                            <div class="bg-status-inactive px-6 py-2.5 rounded-2xl flex items-center justify-center border shadow-sm">
                                <div class="w-2 h-2 bg-slate-400 rounded-full mr-2.5"></div>
                                <span class="text-xs font-bold uppercase tracking-[0.1em]">Status: Non-Aktif</span>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Nama Lengkap</label>
                            <div id="view-name" class="text-lg font-bold text-slate-700 px-6 py-4 bg-slate-50/50 border border-slate-100 rounded-2xl">
                                {{ Auth::user()->name }}
                            </div>
                            <input type="text" id="input-name" name="name" value="{{ Auth::user()->name }}" class="hidden w-full px-6 py-4 bg-white border-2 border-slate-200 rounded-2xl focus:border-red-primary focus:outline-none transition-all font-bold">
                            @error('name')
                                <span class="text-red-500 text-xs block mt-1 font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="space-y-3">
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Alamat Email</label>
                            <div id="view-email" class="text-lg font-bold text-slate-700 px-6 py-4 bg-slate-50/50 border border-slate-100 rounded-2xl">
                                {{ Auth::user()->email }}
                            </div>
                            <input type="email" id="input-email" name="email" value="{{ Auth::user()->email }}" class="hidden w-full px-6 py-4 bg-white border-2 border-slate-200 rounded-2xl focus:border-red-primary focus:outline-none transition-all font-bold">
                            @error('email')
                                <span class="text-red-500 text-xs block mt-1 font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div id="passwordSection" class="hidden animate-in fade-in slide-in-from-top-4 duration-500">
                        <div class="pt-8 border-t border-slate-100 space-y-8">
                            <h3 class="text-xs font-black uppercase text-red-primary tracking-[0.25em] flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                Perbarui Kata Sandi
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-3">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase ml-1">Kata Sandi Baru</label>
                                    <div class="relative">
                                        <input type="password" id="password" name="password" placeholder="••••••••" class="w-full px-6 py-4 pr-12 bg-white border-2 border-slate-200 rounded-2xl focus:border-red-primary focus:outline-none font-bold transition-all">
                                        <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                            <svg id="password-eye" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </button>
                                    </div>
                                    @error('password')
                                        <span class="text-red-500 text-xs block mt-1 font-medium">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="space-y-3">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase ml-1">Konfirmasi Password</label>
                                    <div class="relative">
                                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" class="w-full px-6 py-4 pr-12 bg-white border-2 border-slate-200 rounded-2xl focus:border-red-primary focus:outline-none font-bold transition-all">
                                        <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                            <svg id="password_confirmation-eye" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </button>
                                    </div>
                                    @error('password_confirmation')
                                        <span class="text-red-500 text-xs block mt-1 font-medium">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-10 text-center border-t border-slate-50">
                        <div class="inline-flex items-center gap-2 px-6 py-2 bg-slate-100 rounded-full text-[10px] text-slate-500 font-bold uppercase tracking-widest">
                            Terdaftar: {{ Auth::user()->created_at->locale('id')->format('d F Y') }} | Diperbarui: {{ Auth::user()->updated_at->locale('id')->diffForHumans() }}
                        </div>
                    </div>

                    
                    <div id="actionButtons" class="hidden flex flex-col md:flex-row justify-between items-center gap-4">
                        <button type="button" onclick="logoutAllDevices()" class="px-6 py-3 text-sm font-bold text-red-600 hover:text-red-700 hover:bg-red-50 rounded-2xl transition-all border border-red-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Keluar Semua Perangkat
                        </button>
                        <div class="flex gap-4">
                            <button type="button" id="cancelBtn" class="px-8 py-4 text-sm font-bold text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-2xl transition-all order-2 md:order-1">Batal</button>
                            <button type="submit" class="px-12 py-4 bg-slate-900 text-white rounded-2xl font-bold text-sm hover:bg-black hover:shadow-xl transition-all order-1 md:order-2 transform active:scale-95">Simpan Perubahan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editBtn = document.getElementById('editToggle');
        const cancelBtn = document.getElementById('cancelBtn');
        const profileForm = document.getElementById('profileForm');

        profileForm.onsubmit = function(e) {
            e.preventDefault();
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;
            const passwordConfirmField = document.getElementById('password_confirmation');
            
            // Reset border color
            passwordConfirmField.classList.remove('border-red-500');
            passwordConfirmField.classList.add('border-slate-200');
            
            if ((password || passwordConfirmation) && password !== passwordConfirmation) {
                // Add red border
                passwordConfirmField.classList.remove('border-slate-200');
                passwordConfirmField.classList.add('border-red-500');
                
                // Show 3-second notification
                showNotification('Konfirmasi password tidak sama dengan password baru!', 'error');
                return false;
            }
            
            if (password && password.length < 8) {
                // Add red border to password field
                const passwordField = document.getElementById('password');
                passwordField.classList.remove('border-slate-200');
                passwordField.classList.add('border-red-500');
                
                // Show 3-second notification
                showNotification('Password harus minimal 8 karakter!', 'error');
                return false;
            }
            
            profileForm.submit();
        };

        editBtn.onclick = function() {
            document.querySelectorAll('[id^="view-"]').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('[id^="input-"]').forEach(el => el.classList.remove('hidden'));
            document.getElementById('passwordSection').classList.remove('hidden');
            document.getElementById('actionButtons').classList.remove('hidden');
            editBtn.classList.add('hidden');
            
            // Clear password fields when entering edit mode
            document.getElementById('password').value = '';
            document.getElementById('password_confirmation').value = '';
        };

        cancelBtn.onclick = function() {
            if(confirm('Batalkan perubahan?')) {
                window.location.reload();
            }
        };

        // Logout all devices function
        window.logoutAllDevices = function() {
            if (confirm('Apakah Anda yakin ingin keluar dari semua perangkat? Ini akan mengeluarkan Anda dari semua sesi aktif.')) {
                fetch('/profile/logout-all-devices', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '/login';
                    } else {
                        alert('Terjadi kesalahan saat logout dari semua perangkat');
                    }
                })
                .catch(error => {
                    // Fallback to direct form submission
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/profile/logout-all-devices';
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    form.appendChild(csrfToken);
                    document.body.appendChild(form);
                    form.submit();
                });
            }
        };
    });

    function togglePassword(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const eyeIcon = document.getElementById(fieldId + '-eye');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
        } else {
            passwordField.type = 'password';
            eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
        }
    }

    function showNotification(message, type = 'error') {
        // Remove existing notification
        const existing = document.querySelector('.notification-popup');
        if (existing) {
            existing.remove();
        }

        // Create notification
        const notification = document.createElement('div');
        notification.className = `notification-popup fixed bottom-4 right-4 px-6 py-3 rounded-xl shadow-lg flex items-center gap-2 z-50 animate-in fade-in slide-in-from-bottom-4 duration-300 ${
            type === 'error' ? 'bg-red-500 text-white' : 'bg-emerald-500 text-white'
        }`;
        
        notification.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                ${type === 'error' 
                    ? '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>'
                    : '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m22 4-10 10.01L7 9.01"/>'
                }
            </svg>
            <span class="font-medium">${message}</span>
        `;

        document.body.appendChild(notification);

        // Auto-remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('opacity-0', 'translate-y-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }, 3000);
    }

    // Real-time password validation
    document.addEventListener('DOMContentLoaded', function() {
        const passwordField = document.getElementById('password');
        const passwordConfirmField = document.getElementById('password_confirmation');

        function validatePasswords() {
            const password = passwordField.value;
            const passwordConfirmation = passwordConfirmField.value;

            // Reset borders
            passwordField.classList.remove('border-red-500');
            passwordConfirmField.classList.remove('border-red-500');
            passwordField.classList.add('border-slate-200');
            passwordConfirmField.classList.add('border-slate-200');

            // Check if both fields have value
            if (password || passwordConfirmation) {
                if (password.length > 0 && password.length < 8) {
                    passwordField.classList.remove('border-slate-200');
                    passwordField.classList.add('border-red-500');
                }

                if (passwordConfirmation && password !== passwordConfirmation) {
                    passwordConfirmField.classList.remove('border-slate-200');
                    passwordConfirmField.classList.add('border-red-500');
                }

                if (password.length >= 8 && password === passwordConfirmation) {
                    passwordField.classList.remove('border-red-500');
                    passwordField.classList.add('border-emerald-500');
                    passwordConfirmField.classList.remove('border-red-500');
                    passwordConfirmField.classList.add('border-emerald-500');
                }
            }
        }

        // Add event listeners
        if (passwordField) passwordField.addEventListener('input', validatePasswords);
        if (passwordConfirmField) passwordConfirmField.addEventListener('input', validatePasswords);
    });
</script>
@endsection