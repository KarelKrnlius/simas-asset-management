<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIMAS') — PT. Magang Jaya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fcfcfc; overflow-x: hidden; }
        .sidebar-active { background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); color: white !important; }
        .sidebar-transition { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>
<body x-data="{ sidebarOpen: true, showLogoutAllModal: false }">

    <aside :class="sidebarOpen ? 'w-72' : 'w-24'" class="sidebar-transition bg-white fixed inset-y-0 left-0 z-50 border-r border-slate-100 flex flex-col shadow-xl">
        <div class="h-24 flex items-center px-8">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center text-white shadow-lg rotate-3">
                    <i class="fas fa-box-open"></i>
                </div>
                <h1 x-show="sidebarOpen" class="font-extrabold text-xl tracking-tighter italic">SIMAS</h1>
            </div>
        </div>

        <nav class="flex-1 px-4 space-y-2 pt-4 overflow-y-auto">
            <div>
                <p x-show="sidebarOpen" class="text-[10px] font-black text-slate-400 uppercase px-4 mb-3 tracking-[.2em]">Main Menu</p>
                <a href="/dashboard" class="flex items-center gap-4 p-3.5 rounded-2xl {{ Request::is('dashboard*') ? 'sidebar-active shadow-md' : 'text-slate-500 hover:bg-red-50 hover:text-red-600' }} transition-all">
                    <div class="min-w-[20px] text-center"><i class="fas fa-th-large"></i></div>
                    <span x-show="sidebarOpen" class="font-bold text-xs uppercase tracking-widest">Dashboard</span>
                </a>
            </div>

            @if(Auth::user()->role_id == 1)
            <div class="pt-4">
                <p x-show="sidebarOpen" class="text-[10px] font-black text-slate-400 uppercase px-4 mb-3 tracking-[.2em]">Administrator</p>
                <a href="/users" class="flex items-center gap-4 p-3.5 rounded-2xl {{ Request::is('users*') ? 'sidebar-active' : 'text-slate-500 hover:bg-red-50 hover:text-red-600' }}">
                    <div class="min-w-[20px] text-center"><i class="fas fa-users"></i></div>
                    <span x-show="sidebarOpen" class="font-bold text-xs uppercase tracking-widest">Master User</span>
                </a>
                <a href="/assets" class="flex items-center gap-4 p-3.5 rounded-2xl {{ Request::is('assets*') ? 'sidebar-active' : 'text-slate-500 hover:bg-red-50 hover:text-red-600' }} mt-1">
                    <div class="min-w-[20px] text-center"><i class="fas fa-boxes"></i></div>
                    <span x-show="sidebarOpen" class="font-bold text-xs uppercase tracking-widest">Master Asset</span>
                </a>
                <a href="/pengembalian" class="flex items-center gap-4 p-3.5 rounded-2xl {{ Request::is('pengembalian*') ? 'sidebar-active' : 'text-slate-500 hover:bg-red-50 hover:text-red-600' }} mt-1">
                    <div class="min-w-[20px] text-center"><i class="fas fa-undo"></i></div>
                    <span x-show="sidebarOpen" class="font-bold text-xs uppercase tracking-widest">Pengembalian</span>
                </a>
            </div>
            @endif

            <div class="pt-4">
                <p x-show="sidebarOpen" class="text-[10px] font-black text-slate-400 uppercase px-4 mb-3 tracking-[.2em]">Transaction</p>
                <a href="/peminjaman" class="flex items-center gap-4 p-3.5 rounded-2xl {{ Request::is('peminjaman*') ? 'sidebar-active' : 'text-slate-500 hover:bg-red-50 hover:text-red-600' }}">
                    <div class="min-w-[20px] text-center"><i class="fas fa-hand-holding"></i></div>
                    <span x-show="sidebarOpen" class="font-bold text-xs uppercase tracking-widest">Peminjaman</span>
                </a>
                <a href="/riwayat" class="flex items-center gap-4 p-3.5 rounded-2xl {{ Request::is('riwayat*') ? 'sidebar-active' : 'text-slate-500 hover:bg-red-50 hover:text-red-600' }} mt-1">
                    <div class="min-w-[20px] text-center"><i class="fas fa-history"></i></div>
                    <span x-show="sidebarOpen" class="font-bold text-xs uppercase tracking-widest">Riwayat Asset</span>
                </a>
            </div>
        </nav>

        <div class="p-4 border-t border-slate-50">
            <a href="{{ route('profile') }}" class="flex items-center gap-4 p-3.5 text-slate-500 hover:bg-slate-50 rounded-2xl mb-1">
                <div class="min-w-[20px] text-center"><i class="fas fa-user-circle text-lg"></i></div>
                <span x-show="sidebarOpen" class="font-bold text-xs uppercase tracking-widest">Profile</span>
            </a>
            <div x-data="{ logoutDropdown: false }" class="relative">
                <button @click="logoutDropdown = !logoutDropdown" class="w-full flex items-center gap-4 p-3.5 text-red-400 hover:bg-red-50 rounded-2xl">
                    <div class="min-w-[20px] text-center"><i class="fas fa-sign-out-alt"></i></div>
                    <span x-show="sidebarOpen" class="font-bold text-xs uppercase tracking-widest">Logout</span>
                    <i x-show="sidebarOpen" class="fas fa-chevron-down text-xs ml-auto"></i>
                </button>
                
                <div x-show="logoutDropdown" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     @click.away="logoutDropdown = false"
                     class="absolute bottom-full left-0 right-0 mb-2 bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden">
                    
                    <!-- Logout Current Device -->
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 p-3 text-slate-600 hover:bg-slate-50 text-left">
                            <i class="fas fa-sign-out-alt text-sm"></i>
                            <div>
                                <div class="text-xs font-semibold">Logout Perangkat Ini</div>
                                <div class="text-[9px] text-slate-400">Keluar dari perangkat saat ini</div>
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <main :class="sidebarOpen ? 'ml-72' : 'ml-24'" class="sidebar-transition min-h-screen flex flex-col">
        <header class="h-24 px-8 flex items-center justify-between sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-100 flex-shrink-0">
            <button @click="sidebarOpen = !sidebarOpen" class="w-10 h-10 flex items-center justify-center text-slate-400 border border-slate-200 rounded-xl hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                <i class="fas fa-outdent" :class="!sidebarOpen && 'rotate-180'"></i>
            </button>

            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block leading-tight">
                    <p class="text-[10px] font-black uppercase text-slate-900">{{ Auth::user()->name }}</p>
                    <p class="text-[9px] font-bold text-red-500 uppercase">{{ Auth::user()->role_id == 1 ? 'Administrator' : 'Staff Internal' }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center font-bold text-slate-700 border border-slate-200 uppercase">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
            </div>
        </header>

        <div class="flex-1 p-8 flex items-center justify-center">
            @yield('content')
        </div>
    </main>

<!-- Security: Prevent back button access to login when authenticated -->
<script>
    // Immediate check - runs before page load
    (function() {
        @if(auth()->check())
            // If authenticated and trying to access login, redirect immediately
            if (window.location.pathname === '/login' || window.location.pathname === '/') {
                window.location.replace('/dashboard');
                return;
            }
        @endif
    })();
    
    // Check authentication on page load
    window.addEventListener('load', function() {
        // If authenticated and on login page, redirect to dashboard
        @if(auth()->check())
            if (window.location.pathname === '/login' || window.location.pathname === '/') {
                window.location.replace('/dashboard');
            }
        @endif
    });
    
    // Prevent back button cache for authenticated users
    window.addEventListener('pageshow', function(event) {
        // If page is loaded from cache (back button)
        if (event.persisted) {
            @if(auth()->check())
                // If authenticated and somehow back to login, redirect to dashboard
                if (window.location.pathname === '/login' || window.location.pathname === '/') {
                    window.location.replace('/dashboard');
                }
            @endif
        }
    });
    
    // Handle browser history navigation
    window.addEventListener('popstate', function(event) {
        @if(auth()->check())
            // If authenticated and trying to go back to login, prevent and stay on current page
            if (window.location.pathname === '/login' || window.location.pathname === '/') {
                event.preventDefault();
                window.location.replace('/dashboard');
            }
        @endif
    });
    
    // Continuous monitoring - check every 100ms
    setInterval(function() {
        @if(auth()->check())
            if (window.location.pathname === '/login' || window.location.pathname === '/') {
                window.location.replace('/dashboard');
            }
        @endif
    }, 100);
</script>
<!-- Success Toast Notification -->
<div x-data="{ 
    showToast: false,
    message: '',
    showNotification(msg) {
        this.message = msg;
        this.showToast = true;
        setTimeout(() => {
            this.showToast = false;
        }, 3000);
    }
}" x-init="
    // Check for success message on page load
    @if(session('success'))
        showNotification('{{ session('success') }}');
    @endif
">
    <div x-show="showToast" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-4"
         class="fixed top-4 right-4 bg-emerald-500 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-2 z-50">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <path d="m22 4-10 10.01L7 9.01"/>
        </svg>
        <span class="font-medium" x-text="message"></span>
    </div>
</div>

@if(Auth::check())
    <script>
        // Prevent back button to login page for authenticated users
        window.addEventListener('popstate', function(event) {
            // Check if we're trying to go back to login page
            if (window.location.pathname === '/login' || document.referrer.includes('/login')) {
                // Redirect to dashboard immediately
                window.location.href = '{{ route("dashboard") }}';
            }
        });
        
        // Also handle browser back button
        window.addEventListener('beforeunload', function(event) {
            // Store that user is authenticated
            sessionStorage.setItem('user_authenticated', 'true');
        });
        
        // Check on page load if user was authenticated and trying to access login
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.pathname === '/login') {
                // If user is authenticated, redirect to dashboard
                window.location.href = '{{ route("dashboard") }}';
            }
        });
    </script>
    @endif
</body>
</html>