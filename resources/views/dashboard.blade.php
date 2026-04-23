@extends('layouts.app')

@section('content')
<div class="p-4 lg:p-10 container mx-auto">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-tight text-gray-900">
                @if(Auth::user()->role_id == 1) ADMIN PANEL @else STAFF DASHBOARD @endif
            </h1>
            <p class="text-sm text-gray-400 font-bold uppercase tracking-widest">
                Selamat datang kembali, <span class="text-red-600 underline cursor-pointer">{{ Auth::user()->name }}!</span>
            </p>
        </div>
        
        <div class="flex items-center gap-4 bg-white p-2 pr-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="w-10 h-10 bg-black rounded-xl flex items-center justify-center text-white font-black text-xs uppercase shadow-lg shadow-gray-200">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div>
                <p class="text-[10px] font-black leading-none">{{ Auth::user()->name }}</p>
                <p class="text-[8px] text-gray-400 font-black uppercase tracking-tighter mt-1">
                    {{ Auth::user()->role_id == 1 ? 'Super Administrator' : 'Staff Internal' }}
                </p>
            </div>
        </div>
    </div>

    @if(Auth::user()->role_id == 1)
    <div class="space-y-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border-b-8 border-red-600 hover:-translate-y-2 transition-all">
                <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-red-600 mb-6 text-xl shadow-inner"><i class="fas fa-box"></i></div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Assets</p>
                <p class="text-3xl font-black italic text-gray-900">{{ $total }} <span class="text-xs text-gray-300 not-italic">Items</span></p>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border-b-8 border-green-500 hover:-translate-y-2 transition-all">
                <div class="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center text-green-500 mb-6 text-xl shadow-inner"><i class="fas fa-check-circle"></i></div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Available</p>
                <p class="text-3xl font-black italic text-gray-900">{{ $tersedia }} <span class="text-xs text-gray-300 not-italic">Items</span></p>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border-b-8 border-yellow-500 hover:-translate-y-2 transition-all">
                <div class="w-12 h-12 bg-yellow-50 rounded-2xl flex items-center justify-center text-yellow-500 mb-6 text-xl shadow-inner"><i class="fas fa-wrench"></i></div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Maintenance</p>
                <p class="text-3xl font-black italic text-gray-900">{{ $maintenance }} <span class="text-xs text-gray-300 not-italic">Items</span></p>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border-b-8 border-black hover:-translate-y-2 transition-all">
                <div class="w-12 h-12 bg-red-50 rounded-2xl flex items-center justify-center text-red-600 mb-6 text-xl shadow-inner"><i class="fas fa-times-circle"></i></div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Broken</p>
                <p class="text-3xl font-black italic text-gray-900">{{ $rusak }} <span class="text-xs text-gray-300 not-italic">Items</span></p>
            </div>
        </div>

        <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-gray-50">
            <div class="flex justify-between items-center mb-10">
                <h3 class="text-xl font-black uppercase italic border-l-8 border-red-600 pl-6">Recent Inventory Monitoring</h3>
                <button class="bg-black text-white px-8 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-red-600 transition-all">View Full Report</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] border-b border-gray-50">
                            <th class="pb-6">Asset Name</th>
                            <th class="pb-6 text-center">Status</th>
                            <th class="pb-6 text-right">Last Update</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($assets as $asset)
                        <tr class="group hover:bg-gray-50/50 transition-colors">
                            <td class="py-6 text-sm font-black text-gray-700 tracking-tight capitalize">{{ $asset->name }}</td>
                            <td class="py-6 text-center">
                                <span class="bg-green-100 text-green-600 text-[9px] font-black px-5 py-2 rounded-xl uppercase shadow-sm border border-green-200">
                                    {{ $asset->status }}
                                </span>
                            </td>
                            <td class="py-6 text-right text-[10px] font-bold text-gray-400">{{ $asset->updated_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($assets as $asset)
        <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-gray-100 flex flex-col justify-between group hover:shadow-2xl hover:shadow-gray-200 transition-all duration-500">
            <div class="relative overflow-hidden rounded-[2rem] bg-gray-50 aspect-video flex items-center justify-center mb-6">
                <i class="fas fa-laptop text-5xl text-gray-200 group-hover:scale-110 group-hover:text-red-100 transition-transform duration-500"></i>
                <div class="absolute top-4 right-4 bg-white/80 backdrop-blur px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border border-white">
                    {{ $asset->code }}
                </div>
            </div>
            
            <div class="mb-6">
                <h4 class="text-xl font-black text-gray-800 tracking-tight mb-2">{{ $asset->name }}</h4>
                <p class="text-xs text-gray-400 font-medium mb-4">{{ Str::limit($asset->description, 50) }}</p>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full {{ $asset->status == 'Tersedia' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                    <span class="text-[10px] font-black uppercase tracking-widest {{ $asset->status == 'Tersedia' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $asset->status }}
                    </span>
                </div>
            </div>

            @if($asset->status == 'Tersedia')
            <button class="w-full bg-black text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-red-600 hover:shadow-xl hover:shadow-red-200 transition-all active:scale-95">
                <i class="fas fa-plus mr-2"></i> Ajukan Peminjaman
            </button>
            @else
            <button disabled class="w-full bg-gray-100 text-gray-400 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] cursor-not-allowed">
                Asset Tidak Tersedia
            </button>
            @endif
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection