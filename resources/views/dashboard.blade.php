@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 lg:px-8">
    
    {{-- BANNER SELAMAT DATANG (Warna Abu-abu Terang bg-slate-100) --}}
    <div class="relative bg-slate-100 rounded-[3rem] p-12 overflow-hidden mb-12 shadow-sm border border-slate-200 group">
        <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-red-600/10 to-transparent"></div>
        <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-red-600/5 rounded-full blur-3xl group-hover:bg-red-600/10 transition-all duration-700"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="text-center md:text-left">
                <span class="bg-red-600 text-white text-[10px] font-black px-4 py-2 rounded-full uppercase tracking-[0.2em]">Operational Report</span>
                <h2 class="text-5xl font-black text-slate-900 italic tracking-tighter uppercase mt-6 mb-2">
                    Selamat Datang, <span class="text-red-600">{{ Auth::user()->name }}</span>
                </h2>
                <p class="text-slate-500 font-bold text-xs uppercase tracking-[0.3em]">Sistem pemantauan aset dalam kendali penuh.</p>
            </div>
            <div class="hidden lg:block bg-slate-200/50 backdrop-blur-md border border-slate-300 p-6 rounded-3xl">
                <p class="text-[10px] font-black text-red-600 uppercase italic">Staff Access</p>
                <p class="text-xs font-bold text-slate-600 opacity-60">ID: {{ Auth::user()->id }}</p>
            </div>
        </div>
    </div>

    @if(Auth::user()->role_id == 1)
        {{-- BAGIAN ADMIN --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12 max-w-7xl mx-auto">
            @foreach([
                ['label' => 'Total Aset', 'val' => $stats['total'], 'icon' => 'fa-cubes', 'color' => 'red'],
                ['label' => 'Aset Tersedia', 'val' => $stats['available'], 'icon' => 'fa-check-double', 'color' => 'slate'],
                ['label' => 'Dalam Peminjaman', 'val' => $stats['loaned'], 'icon' => 'fa-hand-holding-box', 'color' => 'red'],
                ['label' => 'Perlu Perbaikan', 'val' => $stats['maintenance'], 'icon' => 'fa-screwdriver-wrench', 'color' => 'slate'],
            ] as $item)
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm hover:shadow-xl transition-all group overflow-hidden relative">
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-8">
                        <div class="w-12 h-12 {{ $item['color'] == 'red' ? 'bg-red-600 text-white shadow-red-200 shadow-lg' : 'bg-slate-100 text-slate-900' }} rounded-2xl flex items-center justify-center">
                            <i class="fas {{ $item['icon'] }} text-lg"></i>
                        </div>
                        <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic">Live Status</span>
                    </div>
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ $item['label'] }}</h4>
                    <p class="text-4xl font-black text-slate-900 italic tracking-tighter">{{ $item['val'] }}</p>
                </div>
                <div class="absolute bottom-0 right-0 w-16 h-1 bg-{{ $item['color'] == 'red' ? 'red-600' : 'slate-900' }}"></div>
            </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
            <div class="lg:col-span-2 bg-white p-10 rounded-[3rem] border border-slate-200 shadow-sm h-full">
                <div class="flex items-center justify-between mb-10">
                    <h3 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter border-l-4 border-red-600 pl-4">Statistik Inventaris</h3>
                </div>
                <div class="relative w-full h-[350px] overflow-hidden rounded-xl">
    <div id="chartAnalytics" class="w-full h-full"></div>
</div>
            </div>

            <div class="space-y-8">
                <div class="bg-red-600 p-10 rounded-[3rem] text-white shadow-2xl">
                    <h3 class="text-lg font-black uppercase italic mb-6">Aksi Cepat</h3>
                    <div class="space-y-3">
                        <a href="/assets?open_modal=add" class="block w-full bg-white text-red-600 py-5 rounded-2xl text-[10px] font-black uppercase text-center tracking-widest hover:scale-105 transition-all">
                            <i class="fas fa-plus mr-2"></i> Input Aset Baru
                        </a>
                    </div>
                </div>

                <div class="bg-white p-10 rounded-[3rem] border border-slate-200 shadow-sm">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-8 italic border-b border-slate-100 pb-4">Validasi Antrean</h3>
                    <div class="space-y-6">
                        @forelse($pendingLoans ?? [] as $q)
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-red-600 border border-slate-100">
                                <i class="fas fa-clock-rotate-left"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-[11px] font-black text-slate-900 uppercase italic leading-none">{{ $q->asset_name ?? 'Asset' }}</p>
                                <p class="text-[9px] text-slate-400 font-bold uppercase mt-1">Oleh: {{ $q->user_name ?? 'Staff' }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-[10px] font-bold text-slate-400 uppercase text-center">Tidak ada antrean</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    @else
        {{-- FLOW STAFF --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-10 max-w-7xl mx-auto">
            
            <div class="lg:col-span-3 space-y-10">
                <div class="bg-white p-10 rounded-[4rem] border border-slate-200 shadow-sm">
                    <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
                        <div>
                            <h3 class="text-4xl font-black text-slate-900 uppercase italic tracking-tighter">Katalog Aset <span class="text-red-600">Tersedia</span></h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">Pilih aset yang ingin dipinjam hari ini.</p>
                        </div>
                        <a href="/peminjaman" class="text-[10px] font-black text-red-600 uppercase tracking-widest border-b-2 border-red-600 pb-1">Lihat Semua Aset</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                        @forelse($recentAssets as $asset)
                        <div class="group">
                            <div class="bg-slate-50 rounded-[3rem] aspect-square flex flex-col items-center justify-center mb-6 relative overflow-hidden border border-slate-100 transition-all duration-500 group-hover:border-red-600 shadow-sm">
                                <div class="absolute top-6 right-6">
                                    <span class="bg-green-100 text-green-600 text-[8px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Ready</span>
                                </div>
                                
                                <i class="fas fa-laptop text-7xl text-slate-200 group-hover:text-red-600/20 group-hover:scale-110 transition-all duration-700"></i>
                                
                                <div class="absolute bottom-0 left-0 w-full p-8 translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                                    <a href="/peminjaman/create?asset_id={{ $asset->id }}" class="block w-full bg-red-600 text-white py-4 rounded-2xl text-[10px] font-black uppercase text-center tracking-widest shadow-xl shadow-red-900/20">Pinjam Aset</a>
                                </div>
                            </div>
                            <div class="px-4 text-center">
                                <h4 class="text-xl font-black text-slate-900 uppercase italic truncate">{{ $asset->name }}</h4>
                                <p class="text-[10px] font-bold text-slate-400 uppercase mt-2 italic tracking-widest">KODE: {{ $asset->code }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-3 text-center py-20 bg-slate-50 rounded-[3rem] border-2 border-dashed border-slate-200">
                            <i class="fas fa-box-open text-5xl text-slate-200 mb-4"></i>
                            <p class="text-slate-400 font-black uppercase italic tracking-widest">Belum ada aset tersedia untuk dipinjam</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                {{-- KOTAK PEMINJAMAN SAYA (Warna Abu-abu Terang bg-slate-100) --}}
                <div class="bg-slate-100 p-10 rounded-[3rem] border border-slate-200 shadow-sm relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="text-xs font-black uppercase tracking-widest text-red-600 mb-8 italic">Peminjaman Saya</h3>
                        <div class="space-y-6">
                            @php
                                $myLoans = [];
                                if (class_exists('\App\Models\Loan')) {
                                    $myLoans = \App\Models\Loan::where('user_id', Auth::id())
                                                ->where('status', 'dipinjam')
                                                ->get();
                                }
                            @endphp
                            
                            @forelse($myLoans as $loan)
                            <div class="flex items-center gap-4 border-b border-slate-200 pb-4 last:border-0">
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center border border-slate-200 shadow-sm">
                                    <i class="fas fa-clock text-red-600"></i>
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-slate-900 uppercase italic">ID Peminjaman #{{ $loan->id }}</p>
                                    <p class="text-[9px] text-slate-400 font-bold uppercase mt-1">Pinjam: {{ \Carbon\Carbon::parse($loan->borrow_date)->format('d M Y') }}</p>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">Kamu belum meminjam aset</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="bg-white p-10 rounded-[3rem] border border-slate-200 shadow-sm">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 italic border-b border-slate-100 pb-4 text-center">Panduan Staff</h3>
                    <div class="space-y-4">
                        <div class="flex gap-4">
                            <span class="w-6 h-6 bg-red-50 text-red-600 rounded-lg flex items-center justify-center text-[10px] font-black">1</span>
                            <p class="text-[10px] font-bold text-slate-600 uppercase leading-tight">Pilih aset dari katalog tersedia</p>
                        </div>
                        <div class="flex gap-4">
                            <span class="w-6 h-6 bg-red-50 text-red-600 rounded-lg flex items-center justify-center text-[10px] font-black">2</span>
                            <p class="text-[10px] font-bold text-slate-600 uppercase leading-tight">Klik pinjam dan tunggu validasi admin</p>
                        </div>
                        <div class="flex gap-4">
                            <span class="w-6 h-6 bg-red-50 text-red-600 rounded-lg flex items-center justify-center text-[10px] font-black">3</span>
                            <p class="text-[10px] font-bold text-slate-600 uppercase leading-tight">Kembalikan aset tepat waktu sesuai sistem</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>
<script>
    @if(Auth::user()->role_id == 1)
    document.addEventListener('DOMContentLoaded', function() {
        var options = {
            series: [{ name: 'Aktivitas', data: [44, 55, 41, 67, 22, 43, 21] }],
            chart: { 
                type: 'bar', 
                height: 350, 
                toolbar: {show: false}, 
                fontFamily: 'Plus Jakarta Sans',
                responsive: true,
                maintainAspectRatio: false
            },
            plotOptions: { 
                bar: { 
                    borderRadius: 12, 
                    columnWidth: '40%', 
                    distributed: true 
                } 
            },
            colors: ['#ef4444', '#0f172a', '#ef4444', '#0f172a', '#ef4444', '#0f172a', '#ef4444'],
            dataLabels: { enabled: false },
            xaxis: { 
                categories: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                labels: { 
                    style: { 
                        fontWeight: 800, 
                        fontSize: '10px',
                        colors: '#64748b'
                    } 
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: {
                        fontSize: '10px',
                        colors: '#64748b'
                    }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            grid: { 
                borderColor: '#f1f5f9',
                strokeDashArray: 3,
                padding: { top: 0, right: 0, bottom: 0, left: 0 }
            },
            tooltip: {
                theme: 'light',
                style: {
                    fontSize: '12px',
                    fontFamily: 'Plus Jakarta Sans'
                }
            }
        };
        
        var chart = new ApexCharts(document.querySelector("#chartAnalytics"), options);
        chart.render();
        
        // Handle window resize
        window.addEventListener('resize', function() {
            chart.updateOptions({
                chart: {
                    width: document.querySelector("#chartAnalytics").offsetWidth
                }
            });
        });
    });
    @endif
</script>
@endsection