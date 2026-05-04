@extends('layouts.app')

@section('title', 'Asset Library')

@section('content')
<div class="container mx-auto px-4 lg:px-8">
    
    {{-- Header Banner --}}
    <div class="relative bg-slate-100 rounded-[3rem] p-12 overflow-hidden mb-12 shadow-sm border border-slate-200 group">
        <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-red-600/10 to-transparent"></div>
        <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-red-600/5 rounded-full blur-3xl group-hover:bg-red-600/10 transition-all duration-700"></div>
        
        <div class="relative z-10 text-center">
            <span class="bg-red-600 text-white text-[10px] font-black px-4 py-2 rounded-full uppercase tracking-[0.2em]">Asset Library</span>
            <h2 class="text-5xl font-black text-slate-900 italic tracking-tighter uppercase mt-6 mb-2">
                Pindai <span class="text-red-600">QR Code</span> Asset
            </h2>
            <p class="text-slate-500 font-bold text-xs uppercase tracking-[0.3em]">Akses cepat ke informasi asset melalui QR code scanning</p>
        </div>
    </div>

    {{-- Main Scanner Card --}}
    <div class="max-w-2xl mx-auto">
        <div class="bg-white p-12 rounded-[3rem] border border-slate-200 shadow-sm text-center">
            <div class="w-24 h-24 bg-red-600 rounded-3xl flex items-center justify-center text-white mx-auto mb-8 shadow-xl shadow-red-900/20 transform hover:scale-110 transition-all duration-300">
                <i class="fas fa-qrcode text-4xl"></i>
            </div>
            
            <h3 class="text-3xl font-black text-slate-900 uppercase italic mb-4">QR Code Scanner</h3>
            <p class="text-slate-500 font-bold text-xs uppercase tracking-widest mb-8">Klik tombol di bawah untuk memulai scanning QR code asset</p>
            
            <a href="/asset-library/scan" 
               class="inline-flex items-center gap-3 bg-red-600 text-white px-12 py-6 rounded-3xl text-sm font-black uppercase tracking-widest hover:scale-105 transition-all shadow-xl shadow-red-900/20">
                <i class="fas fa-camera"></i>
                Mulai Scanning
            </a>
        </div>

        {{-- Quick Manual Input --}}
        <div class="mt-8 bg-slate-50 p-8 rounded-[3rem] border border-slate-200">
            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 italic text-center">Input Manual</h4>
            <form action="{{ route('asset-library.search') }}" method="GET" class="flex gap-3">
                <input type="text" name="qr_code" placeholder="Masukkan kode asset atau QR code..." 
                       class="flex-1 bg-white border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-900 placeholder-slate-400 focus:outline-none focus:border-red-600 focus:bg-white transition-all">
                <button type="submit" 
                        class="bg-slate-900 text-white px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:scale-105 transition-all">
                    Cari Asset
                </button>
            </form>
        </div>

        {{-- Instructions --}}
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-8 rounded-[3rem] border border-slate-200 shadow-sm">
                <div class="w-12 h-12 bg-red-600 text-white rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-qrcode"></i>
                </div>
                <h4 class="text-sm font-black text-slate-900 uppercase mb-2">Scanning QR Code</h4>
                <p class="text-xs text-slate-600 font-medium">Arahkan kamera ke QR code asset untuk melihat detail lengkap</p>
            </div>
            
            <div class="bg-white p-8 rounded-[3rem] border border-slate-200 shadow-sm">
                <div class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-search"></i>
                </div>
                <h4 class="text-sm font-black text-slate-900 uppercase mb-2">Pencarian Manual</h4>
                <p class="text-xs text-slate-600 font-medium">Masukkan kode asset secara manual jika QR code tidak tersedia</p>
            </div>
        </div>
    </div>
</div>
@endsection
