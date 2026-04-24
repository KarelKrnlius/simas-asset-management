<!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SIMAS — Premium Asset Management</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
            body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fcfcfc; }
            .sidebar-active { background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); color: white !important; }
            .sidebar-transition { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
            .pattern-bg { background-image: radial-gradient(#e5e7eb 0.5px, transparent 0.5px); background-size: 20px 20px; }
        </style>
    </head>
    <body class="pattern-bg text-slate-900" x-data="{ sidebarOpen: true }">

        <aside 
            :class="sidebarOpen ? 'w-80' : 'w-24'" 
            class="sidebar-transition bg-white fixed inset-y-0 left-0 z-50 border-r border-slate-200 flex flex-col shadow-2xl">
            
            <div class="h-28 flex items-center px-10">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-red-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-red-200 rotate-3">
                        <i class="fas fa-database text-lg"></i>
                    </div>
                    <div x-show="sidebarOpen" x-transition class="leading-none">
                        <h1 class="font-extrabold text-2xl tracking-tighter uppercase italic">SIMAS<span class="text-red-600">.</span></h1>
                        <p class="text-[8px] font-black text-slate-400 tracking-[.3em] uppercase">Asset Excellence</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-6 space-y-3 pt-4 overflow-y-auto">
                <div>
                    <p x-show="sidebarOpen" class="text-[10px] font-black text-slate-400 uppercase px-4 mb-4 tracking-[.3em]">Menu Utama</p>
                    <a href="/dashboard" class="flex items-center gap-4 p-4 rounded-2xl {{ Request::is('dashboard*') ? 'sidebar-active shadow-lg' : 'text-slate-500 hover:bg-red-50 hover:text-red-600' }} transition-all group">
                        <div class="min-w-[20px] text-center"><i class="fas fa-shapes"></i></div>
                        <span x-show="sidebarOpen" class="font-bold text-xs uppercase tracking-widest">Dashboard</span>
                    </a>
                </div>

                @if(Auth::user()->role_id == 1)
                <div class="pt-4">
                    <p x-show="sidebarOpen" class="text-[10px] font-black text-slate-400 uppercase px-4 mb-4 tracking-[.3em]">Administrator</p>
                    <a href="/aset" class="flex items-center gap-4 p-4 rounded-2xl {{ Request::is('aset*') ? 'sidebar-active' : 'text-slate-500 hover:bg-red-50 hover:text-red-600' }} transition-all">
                        <div class="min-w-[20px] text-center"><i class="fas fa-box-archive"></i></div>
                        <span x-show="sidebarOpen" class="font-bold text-xs uppercase tracking-widest text-nowrap">Manajemen Aset</span>
                    </a>
                    <a href="/users" class="flex items-center gap-4 p-4 rounded-2xl {{ Request::is('users*') ? 'sidebar-active' : 'text-slate-500 hover:bg-red-50 hover:text-red-600' }} transition-all mt-2">
                        <div class="min-w-[20px] text-center"><i class="fas fa-users-gear"></i></div>
                        <span x-show="sidebarOpen" class="font-bold text-xs uppercase tracking-widest text-nowrap">Data Pengguna</span>
                    </a>
                </div>
                @endif

                <div class="pt-4">
                    <p x-show="sidebarOpen" class="text-[10px] font-black text-slate-400 uppercase px-4 mb-4 tracking-[.3em]">Logistik</p>
                    <a href="/peminjaman" class="flex items-center gap-4 p-4 rounded-2xl {{ Request::is('peminjaman*') ? 'sidebar-active' : 'text-slate-500 hover:bg-red-50 hover:text-red-600' }} transition-all">
                        <div class="min-w-[20px] text-center"><i class="fas fa-clipboard-list"></i></div>
                        <span x-show="sidebarOpen" class="font-bold text-xs uppercase tracking-widest text-nowrap">Riwayat Pinjam</span>
                    </a>

                    {{-- MENU PEMINJAMAN SAYA UNTUK STAFF --}}
                    @if(Auth::user()->role_id != 1)
                    <a href="/dashboard#peminjaman-section" class="flex items-center justify-between p-4 rounded-2xl bg-slate-100 border border-slate-200 transition-all mt-2 group hover:border-red-600">
                        <div class="flex items-center gap-4">
                            <div class="min-w-[20px] text-center"><i class="fas fa-box-open text-red-600"></i></div>
                            <span x-show="sidebarOpen" class="font-black text-[10px] text-slate-900 uppercase tracking-widest text-nowrap">Peminjaman Saya</span>
                        </div>
                        @php
                            $loanCount = 0;
                            // Cek apakah model Peminjaman ada untuk menghindari error "Not Found"
                            if (class_exists('App\Models\Peminjaman')) {
                                $loanCount = \App\Models\Peminjaman::where('user_id', Auth::id())->where('status', 'dipinjam')->count();
                            }
                        @endphp
                        @if($loanCount > 0 && $sidebarOpen)
                        <span class="bg-red-600 text-white text-[9px] font-black px-2 py-0.5 rounded-full shadow-lg shadow-red-200">
                            {{ $loanCount }}
                        </span>
                        @endif
                    </a>
                    @endif
                </div>
            </nav>

            <div class="p-6">
                <div :class="sidebarOpen ? 'bg-slate-900' : 'bg-transparent'" class="p-4 rounded-[2rem] transition-all duration-500 relative overflow-hidden group">
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="w-10 h-10 rounded-xl bg-red-600 flex items-center justify-center text-white font-bold shrink-0 shadow-lg shadow-red-900/50">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div x-show="sidebarOpen" class="overflow-hidden text-nowrap">
                            <p class="text-[10px] font-black text-white uppercase italic truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-tighter">System Access</p>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="mt-4 relative z-10">
                        @csrf
                        <button class="w-full text-[10px] font-black uppercase text-red-500 hover:text-red-400 flex items-center gap-2 px-1">
                            <i class="fas fa-power-off"></i> <span x-show="sidebarOpen">Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <main :class="sidebarOpen ? 'ml-80' : 'ml-24'" class="sidebar-transition min-h-screen">
            <header class="h-28 px-12 flex items-center justify-between sticky top-0 z-40 bg-white/60 backdrop-blur-xl border-b border-slate-100">
                <div class="flex items-center gap-6">
                    <button @click="sidebarOpen = !sidebarOpen" class="w-10 h-10 flex items-center justify-center text-slate-400 border border-slate-200 rounded-xl hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                        <i class="fas fa-outdent" :class="!sidebarOpen && 'rotate-180'"></i>
                    </button>
                    <div>
                        <h2 class="text-xs font-black text-slate-900 uppercase tracking-widest italic">Terminal <span class="text-red-600">v.1.2</span></h2>
                        <p class="text-[9px] text-slate-400 font-bold uppercase">Status: <span class="text-green-500 font-black">Online</span></p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-[1px] h-8 bg-slate-200 mx-2"></div>
                    <div class="flex flex-col text-right">
                        <span class="text-[10px] font-black text-slate-900">{{ date('l, d F Y') }}</span>
                        <span class="text-[9px] text-red-600 font-bold uppercase tracking-widest">Active Session</span>
                    </div>
                </div>
            </header>

            <div class="p-12">
                @yield('content')
            </div>
        </main>
    </body>
    </html>