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
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border-b-8 border-red-600">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Assets</p>
                <p class="text-3xl font-black italic text-gray-900">{{ $total }} <span class="text-xs text-gray-300 not-italic">Items</span></p>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border-b-8 border-green-500">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Available</p>
                <p class="text-3xl font-black italic text-gray-900">{{ $tersedia }} <span class="text-xs text-gray-300 not-italic">Items</span></p>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border-b-8 border-yellow-500">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Maintenance</p>
                <p class="text-3xl font-black italic text-gray-900">{{ $maintenance }} <span class="text-xs text-gray-300 not-italic">Items</span></p>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border-b-8 border-black">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Broken</p>
                <p class="text-3xl font-black italic text-gray-900">{{ $rusak }} <span class="text-xs text-gray-300 not-italic">Items</span></p>
            </div>
        </div>

        <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-gray-50">
            <div class="flex justify-between items-center mb-10">
                <h3 class="text-xl font-black uppercase italic border-l-8 border-red-600 pl-6">Recent Inventory Monitoring</h3>
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
                            <td class="py-6 text-sm font-black text-gray-700 capitalize">{{ $asset->name }}</td>

                            <td class="py-6 text-center">
                                <span class="text-[9px] font-black px-5 py-2 rounded-xl uppercase
                                    {{ strtolower($asset->status) == 'tersedia' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                    {{ $asset->status }}
                                </span>
                            </td>

                            <td class="py-6 text-right text-[10px] font-bold text-gray-400">
                                {{ optional($asset->updated_at)->diffForHumans() ?? '-' }}
                            </td>
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
        <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-gray-100 flex flex-col justify-between">

            <div class="mb-6">
                <h4 class="text-xl font-black text-gray-800 mb-2">{{ $asset->name }}</h4>
                <p class="text-xs text-gray-400 mb-4">{{ Str::limit($asset->description, 50) }}</p>

                <span class="text-[10px] font-black uppercase
                    {{ strtolower($asset->status) == 'tersedia' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $asset->status }}
                </span>
            </div>

            @if(strtolower($asset->status) == 'tersedia')
            <button class="w-full bg-black text-white py-3 rounded-xl text-xs font-black">
                Ajukan Peminjaman
            </button>
            @else
            <button disabled class="w-full bg-gray-200 text-gray-400 py-3 rounded-xl text-xs font-black">
                Tidak Tersedia
            </button>
            @endif

        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection