@extends('layouts.app')

@section('content')
<div class="w-full">

    {{-- WELCOME BANNER --}}
    <div class="relative bg-slate-100 rounded-2xl lg:rounded-[3rem] p-6 lg:p-12 overflow-hidden mb-6 lg:mb-12 shadow-sm border border-slate-200 group">
        <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-red-600/10 to-transparent"></div>
        <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-red-600/5 rounded-full blur-3xl group-hover:bg-red-600/10 transition-all duration-700"></div>
        <div class="relative z-10">
            <span class="bg-red-600 text-white text-[10px] font-black px-3 py-1.5 rounded-full uppercase tracking-[0.2em]">Operational Report</span>
            <h2 class="text-2xl sm:text-3xl lg:text-5xl font-black text-slate-900 italic tracking-tighter uppercase mt-4 mb-2 break-words">
                Selamat Datang, <span class="text-red-600">{{ Auth::user()->name }}</span>
            </h2>
            <p class="text-slate-500 font-bold text-xs uppercase tracking-[0.2em]">Sistem pemantauan aset dalam kendali penuh.</p>
        </div>
    </div>

    @if(Auth::user()->role && Auth::user()->role->isAdmin())
        {{-- STATS GRID — 2 kolom di mobile, 3 di tablet, 5 di desktop --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 lg:gap-6 mb-6 lg:mb-12">
            @foreach([
                ['label' => 'Total Asset',      'val' => $stats['total'],            'icon' => 'fa-cubes',               'color' => 'red'],
                ['label' => 'Asset Tersedia',   'val' => $stats['available'],        'icon' => 'fa-check-double',        'color' => 'slate'],
                ['label' => 'Dalam Peminjaman', 'val' => $stats['loaned'],           'icon' => 'fa-hand-holding',        'color' => 'red'],
                ['label' => 'Perlu Perbaikan',  'val' => $stats['maintenance'],      'icon' => 'fa-screwdriver-wrench',  'color' => 'slate'],
                ['label' => 'Asset Hilang',     'val' => $stats['missing'] ?? 0,     'icon' => 'fa-exclamation-triangle','color' => 'red']
            ] as $item)
            <div class="bg-white p-5 lg:p-8 rounded-2xl lg:rounded-[2.5rem] border border-slate-200 shadow-sm hover:shadow-xl transition-all group overflow-hidden relative">
                <div class="relative z-10">
                    <div class="w-10 h-10 lg:w-12 lg:h-12 {{ $item['color'] == 'red' ? 'bg-red-600 text-white shadow-red-200 shadow-lg' : 'bg-slate-100 text-slate-900' }} rounded-xl lg:rounded-2xl flex items-center justify-center mb-4 lg:mb-8">
                        <i class="fas {{ $item['icon'] }} text-base lg:text-lg"></i>
                    </div>
                    <h4 class="text-[9px] lg:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-tight">{{ $item['label'] }}</h4>
                    <p class="text-2xl lg:text-4xl font-black text-slate-900 italic tracking-tighter">{{ $item['val'] }}</p>
                </div>
                <div class="absolute bottom-0 right-0 w-12 lg:w-16 h-1 bg-{{ $item['color'] == 'red' ? 'red-600' : 'slate-900' }}"></div>
            </div>
            @endforeach
        </div>

        {{-- CHART + SIDEBAR --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
            <div class="lg:col-span-2 bg-white p-6 lg:p-10 rounded-2xl lg:rounded-[3rem] border border-slate-200 shadow-sm">
                <h3 class="text-base lg:text-xl font-black text-slate-900 uppercase italic tracking-tighter border-l-4 border-red-600 pl-4 mb-6 lg:mb-10">Statistik Inventaris</h3>
                <div class="relative w-full h-[250px] lg:h-[350px] overflow-hidden rounded-xl">
                    <div id="chartAnalytics" class="w-full h-full"></div>
                </div>
            </div>

            <div class="space-y-4 lg:space-y-8">
                <div class="bg-red-600 p-6 lg:p-10 rounded-2xl lg:rounded-[3rem] text-white shadow-2xl">
                    <h3 class="text-base font-black uppercase italic mb-4 lg:mb-6">Aksi Cepat</h3>
                    <a href="/assets?open_modal=add" class="block w-full bg-white text-red-600 py-4 rounded-2xl text-[10px] font-black uppercase text-center tracking-widest hover:scale-105 transition-all">
                        <i class="fas fa-plus mr-2"></i> Tambah Asset Baru
                    </a>
                </div>

                <div class="bg-white p-6 lg:p-10 rounded-2xl lg:rounded-[3rem] border border-slate-200 shadow-sm">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 italic border-b border-slate-100 pb-4">Validasi Antrean</h3>
                    <div class="space-y-4">
                        @forelse($pendingLoans ?? [] as $q)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-red-600 border border-slate-100 flex-shrink-0">
                                <i class="fas fa-clock-rotate-left text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] font-black text-slate-900 uppercase italic leading-none truncate">{{ $q->asset_name ?? 'Asset' }}</p>
                                <p class="text-[9px] text-slate-400 font-bold uppercase mt-1">Oleh: {{ $q->user_name ?? 'User' }}</p>
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
        {{-- NON-ADMIN --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 lg:gap-10">

            <div class="lg:col-span-3 space-y-6 lg:space-y-10">
                <div class="bg-white p-6 lg:p-10 rounded-2xl lg:rounded-[4rem] border border-slate-200 shadow-sm">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-8 lg:mb-12 gap-4">
                        <div>
                            <h3 class="text-2xl lg:text-4xl font-black text-slate-900 uppercase italic tracking-tighter">Katalog Aset <span class="text-red-600">Tersedia</span></h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">Pilih aset yang ingin dipinjam hari ini.</p>
                        </div>
                        <div class="flex gap-4 flex-shrink-0">
                            <a href="/peminjaman" class="text-[10px] font-black text-red-600 uppercase tracking-widest border-b-2 border-red-600 pb-1">Lihat Semua</a>
                            <a href="/asset-library" class="text-[10px] font-black text-slate-600 uppercase tracking-widest border-b-2 border-slate-600 pb-1">Library</a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-10">
                        @forelse($recentAssets as $asset)
                        <div class="group">
                            <div class="bg-slate-50 rounded-2xl lg:rounded-[3rem] aspect-square flex flex-col items-center justify-center mb-4 relative overflow-hidden border border-slate-100 transition-all duration-500 group-hover:border-red-600 shadow-sm">
                                <div class="absolute top-4 right-4">
                                    <span class="bg-green-100 text-green-600 text-[8px] font-black px-2 py-1 rounded-full uppercase">Ready</span>
                                </div>
                                <i class="fas fa-box text-5xl lg:text-7xl text-slate-200 group-hover:text-red-600/20 group-hover:scale-110 transition-all duration-700"></i>
                                <div class="absolute bottom-0 left-0 w-full p-4 lg:p-8 translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                                    <a href="/peminjaman" class="block w-full bg-red-600 text-white py-3 rounded-xl text-[10px] font-black uppercase text-center tracking-widest">Pinjam Aset</a>
                                </div>
                            </div>
                            <div class="px-2 text-center">
                                <h4 class="text-base lg:text-xl font-black text-slate-900 uppercase italic truncate">{{ $asset->name }}</h4>
                                <p class="text-[10px] font-bold text-slate-400 uppercase mt-1 italic tracking-widest">{{ $asset->code }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full text-center py-12 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                            <i class="fas fa-box-open text-4xl text-slate-200 mb-3"></i>
                            <p class="text-slate-400 font-black uppercase italic tracking-widest text-sm">Belum ada aset tersedia</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="space-y-4 lg:space-y-8">
                <div class="bg-slate-100 p-6 lg:p-10 rounded-2xl lg:rounded-[3rem] border border-slate-200 shadow-sm">
                    <h3 class="text-xs font-black uppercase tracking-widest text-red-600 mb-6 italic">Peminjaman Saya</h3>
                    <div class="space-y-4">
                        @php
                            $myLoans = \App\Models\Loan::where('user_id', Auth::id())->where('status', 'dipinjam')->get();
                        @endphp
                        @forelse($myLoans as $loan)
                        <div class="flex items-center gap-3 border-b border-slate-200 pb-3 last:border-0">
                            <div class="w-9 h-9 bg-white rounded-xl flex items-center justify-center border border-slate-200 shadow-sm flex-shrink-0">
                                <i class="fas fa-hand-holding text-red-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] font-black text-slate-900 uppercase italic truncate">PIN #{{ $loan->id }}</p>
                                <p class="text-[9px] text-slate-400 font-bold uppercase mt-0.5">{{ \Carbon\Carbon::parse($loan->borrow_date)->formatId() }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic text-center py-4">Belum ada peminjaman</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white p-6 lg:p-10 rounded-2xl lg:rounded-[3rem] border border-slate-200 shadow-sm">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic border-b border-slate-100 pb-3 text-center">Panduan</h3>
                    <div class="space-y-3">
                        @foreach(['Pilih aset dari katalog tersedia', 'Klik pinjam dan tunggu validasi', 'Kembalikan aset tepat waktu'] as $i => $step)
                        <div class="flex gap-3 items-start">
                            <span class="w-6 h-6 bg-red-50 text-red-600 rounded-lg flex items-center justify-center text-[10px] font-black flex-shrink-0">{{ $i+1 }}</span>
                            <p class="text-[10px] font-bold text-slate-600 uppercase leading-tight">{{ $step }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>
<script>
    @if(Auth::user()->role && Auth::user()->role->isAdmin())
    document.addEventListener('DOMContentLoaded', function() {
        var options = {
            series: [{ name: 'Aktivitas', data: [44, 55, 41, 67, 22, 43, 21] }],
            chart: { type: 'bar', height: '100%', toolbar: {show: false}, fontFamily: 'Plus Jakarta Sans' },
            plotOptions: { bar: { borderRadius: 10, columnWidth: '40%', distributed: true } },
            colors: ['#ef4444', '#0f172a', '#ef4444', '#0f172a', '#ef4444', '#0f172a', '#ef4444'],
            dataLabels: { enabled: false },
            xaxis: {
                categories: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                labels: { style: { fontWeight: 800, fontSize: '10px', colors: '#64748b' } },
                axisBorder: { show: false }, axisTicks: { show: false }
            },
            yaxis: { labels: { style: { fontSize: '10px', colors: '#64748b' } }, axisBorder: { show: false }, axisTicks: { show: false } },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 3 },
            tooltip: { theme: 'light', style: { fontSize: '12px', fontFamily: 'Plus Jakarta Sans' } }
        };
        new ApexCharts(document.querySelector("#chartAnalytics"), options).render();
    });
    @endif
</script>
@endsection
