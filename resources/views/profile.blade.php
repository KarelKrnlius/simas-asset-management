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

                    <div id="actionButtons" class="hidden flex flex-col md:flex-row justify-end gap-4">
                        <button type="button" id="cancelBtn" class="px-8 py-4 text-sm font-bold text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-2xl transition-all order-2 md:order-1">Batal</button>
                        <button type="submit" class="px-12 py-4 bg-slate-900 text-white rounded-2xl font-bold text-sm hover:bg-black hover:shadow-xl transition-all order-1 md:order-2 transform active:scale-95">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="fixed bottom-4 right-4 bg-emerald-500 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-2 z-50 animate-in fade-in slide-in-from-bottom-4 duration-300">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m22 4-10 10.01L7 9.01"/></svg>
    <span class="font-medium">{{ session('success') }}</span>
</div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editBtn = document.getElementById('editToggle');
        const cancelBtn = document.getElementById('cancelBtn');
        const profileForm = document.getElementById('profileForm');

        profileForm.onsubmit = function(e) {
            e.preventDefault();
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;
            
            if ((password || passwordConfirmation) && password !== passwordConfirmation) {
                alert('Konfirmasi password tidak sama dengan password baru!');
                return false;
            }
            
            if (password && password.length < 8) {
                alert('Password harus minimal 8 karakter!');
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
</script>
@endsection