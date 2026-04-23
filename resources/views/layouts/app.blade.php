<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIMAS') — PT. Magang Jaya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#F3F4F6]">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-white border-r border-gray-100 hidden lg:flex flex-col sticky top-0 h-screen">
            <div class="p-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-red-200">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <span class="font-black italic text-xl tracking-tighter">SIMAS</span>
                </div>
            </div>

            <nav class="flex-1 px-4 space-y-2">
                <p class="text-[10px] font-black text-gray-400 uppercase px-4 mb-2 tracking-widest">Main Menu</p>
                <a href="/dashboard" class="flex items-center gap-3 p-4 {{ Request::routeIs('dashboard') ? 'bg-red-50 text-red-600' : 'text-gray-500 hover:bg-gray-50' }} rounded-2xl font-black text-xs uppercase tracking-wider transition-all">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>

                @if(Auth::user()->role_id == 1) {{-- Menu Khusus Admin --}}
                <p class="text-[10px] font-black text-gray-400 uppercase px-4 mt-6 mb-2 tracking-widest">Administrator</p>
                <a href="#" class="flex items-center gap-3 p-4 text-gray-500 hover:bg-gray-50 rounded-2xl font-bold text-xs uppercase tracking-wider transition-all">
                    <i class="fas fa-users"></i> Master User
                </a>
                <a href="/aset" class="flex items-center gap-3 p-4 text-gray-500 hover:bg-gray-50 rounded-2xl font-bold text-xs uppercase tracking-wider transition-all">
                    <i class="fas fa-boxes"></i> Master Asset
                </a>
                @endif

                <p class="text-[10px] font-black text-gray-400 uppercase px-4 mt-6 mb-2 tracking-widest">Transaction</p>
                <a href="/peminjaman" class="flex items-center gap-3 p-4 {{ Request::routeIs('peminjaman') ? 'bg-red-50 text-red-600' : 'text-gray-500 hover:bg-gray-50' }} rounded-2xl font-bold text-xs uppercase tracking-wider transition-all">
                    <i class="fas fa-hand-holding"></i> Peminjaman
                </a>
                <a href="/riwayat" class="flex items-center gap-3 p-4 {{ Request::routeIs('riwayat') ? 'bg-red-50 text-red-600' : 'text-gray-500 hover:bg-gray-50' }} rounded-2xl font-bold text-xs uppercase tracking-wider transition-all">
                    <i class="fas fa-history"></i> Riwayat Asset
                </a>
            </nav>

            <div class="p-4 border-t border-gray-50">
                <a href="{{ route('profile') }}" class="flex items-center gap-3 p-4 {{ Request::routeIs('profile') ? 'bg-red-50 text-red-600' : 'text-gray-500 hover:bg-gray-50' }} rounded-2xl font-bold text-xs uppercase tracking-wider transition-all mb-2">
                    <i class="fas fa-user-circle"></i> Profile
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 p-4 text-red-400 hover:bg-red-50 rounded-2xl font-bold text-xs uppercase tracking-wider transition-all">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto">
            @yield('content')
        </main>
    </div>
</body>
</html> 