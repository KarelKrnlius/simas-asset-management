<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <link rel="icon" type="image/png" href="{{ asset('images/logo/logo.png') }}">
    <title>@yield('title', 'Perusahaan') — PT. Magang Jaya</title>

    <style>
        /* RESET */
        * {
            box-sizing: border-box;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        /* ================= NAVBAR ================= */

        .navbar {
            position: sticky;
            top: 0;
            z-index: 999;

            display: flex;
            align-items: center;
            justify-content: space-between;

            height: 70px;
            padding: 0 30px;

            background: #d6d4d0; 
            
        }

        /* LOGO */
        .logo {
            height: 60px;
            width: auto;
        }

        /* MENU DESKTOP */
        .nav-menu {
            list-style: none;
            display: flex;
            gap: 30px;
            margin: 0;
            padding: 0;
        }

        .nav-menu li a {
            color: rgb(0, 0, 0);    
            text-decoration: none;
            font-weight: bold;
            position: relative;
        }

        /* HOVER EFFECT */
        .nav-menu li a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0%;
            height: 2px;
            background: rgb(67, 9, 9);
            transition: 0.3s;
        }

        .nav-menu li a:hover::after {
            width: 100%;
        }

        /* HAMBURGER */
        .menu-toggle {
            /* Layout Utama */
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;

            /* Ukuran Wadah */
            width: 50px; /* Sedikit diperbesar agar lingkaran terlihat bagus */
            height: 50px;
            cursor: pointer;

            /* EFEK GLASSES (GLASSMORPHISM) */
            background: rgba(255, 255, 255, 0.1); /* Warna putih transparan */
            backdrop-filter: blur(10px); /* Efek blur di belakang lingkaran */
            -webkit-backdrop-filter: blur(10px); /* Dukungan untuk Safari */
            border-radius: 50%; /* Membuat bentuk lingkaran sempurna */
            border: 1px solid rgba(255, 255, 255, 0.2); /* Garis tepi tipis agar efek kaca tegas */
            
            /* Bayangan halus */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            
            /* Animasi saat hover */
            transition: all 0.3s ease;
        }

        /* Efek saat mouse menempel */
        .menu-toggle:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }
        .menu-toggle span {
            width: 24px;
            height: 3px;
            background: white;
            margin: 3px 0;
            border-radius: 2px;
            transition: 0.3s ease;
        }

        /* ANIMASI X */
        .menu-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(7px, 8px);
        }

        .menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }

        .menu-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(5px, -5px);
        }

        /* ================= RESPONSIVE ================= */

        @media (max-width: 768px) {

            .menu-toggle {
                display: flex;
            }

            .nav-menu {
                position: absolute;
                top: 70px;
                left: 0;
                width: 100%;

                flex-direction: column;
                padding: 20px 30px;

                display: none;

                background: rgba(156, 151, 151, 0.7); /* dari #9c9797 jadi transparan */
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            }

            .nav-menu li {
                margin-bottom: 10px;
            }

            .nav-menu.active {
                display: flex;
                animation: slideDown 0.3s ease;
            }
        }

        /* ANIMASI */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

    footer {
        text-align: center;
        padding: 5px;
        background: #37474F;
        color: #aaa;
        font-size: 13px;
        margin-top: auto; 
    }
    </style>

    @stack('styles')
</head>
<body>
    
<!-- NAVBAR -->
<nav class="navbar">
    <img src="{{ asset('images/logo/logi.png') }}" class="logo">

    <div class="menu-toggle" id="menu-toggle">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <ul class="nav-menu" id="nav-menu">
        <li><a href="/">Beranda</a></li>   
        <li><a href="/layanan">Layanan</a></li>
        <li><a href="/aset">Aset</a></li>
        <li><a href="/peminjaman">Peminjaman</a></li>
        <li><a href="/riwayat">Riwayat</a></li>
        <li><a href="/kontak">Kontak</a></li>
    </ul>

    <!-- SCRIPT -->
    <script>
        const toggle = document.getElementById('menu-toggle');
        const menu = document.getElementById('nav-menu');

        toggle.addEventListener('click', () => {
            toggle.classList.toggle('active');
            menu.classList.toggle('active');
        });
    </script>
</nav>

<!-- CONTENT -->
<div class="container">
    @yield('content')
</div>

<!-- FOOTER -->
<footer>
    <p>© {{ date('Y') }} PT. Magang Jaya. All Rights Reserved.</p>
</footer>

@stack('scripts')
</body>
</html>
=======
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
                <a href="/dashboard" class="flex items-center gap-3 p-4 bg-red-50 text-red-600 rounded-2xl font-black text-xs uppercase tracking-wider transition-all">
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
                <a href="/peminjaman" class="flex items-center gap-3 p-4 text-gray-500 hover:bg-gray-50 rounded-2xl font-bold text-xs uppercase tracking-wider transition-all">
                    <i class="fas fa-hand-holding"></i> Peminjaman
                </a>
                <a href="/riwayat" class="flex items-center gap-3 p-4 text-gray-500 hover:bg-gray-50 rounded-2xl font-bold text-xs uppercase tracking-wider transition-all">
                    <i class="fas fa-history"></i> Riwayat Asset
                </a>
            </nav>

            <div class="p-4 border-t border-gray-50">
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
>>>>>>> origin/feature/dashboard-utama
