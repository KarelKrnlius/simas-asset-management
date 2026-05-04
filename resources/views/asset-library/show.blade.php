@extends('layouts.app')

@section('title', 'Asset Detail - ' . $asset->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100 px-8 py-6">

    <!-- HEADER -->
    <div class="mb-10 flex items-center gap-4">
        <div class="w-4 h-4 bg-gradient-to-r from-red-500 to-red-600 rounded-full shadow-lg shadow-red-500/30"></div>
        <div>
            <h1 class="text-4xl font-normal text-slate-900 tracking-tight mb-1">
                {{ $asset->name }}
            </h1>
            <p class="text-sm font-medium text-slate-500 uppercase tracking-widest">{{ $asset->code }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- LEFT - CORE SPECIFICATIONS -->
        <div>
            <div class="bg-white rounded-3xl p-8 border border-slate-200/60 shadow-xl shadow-slate-200/50">
                <h2 class="text-sm font-normal text-slate-700 mb-8 uppercase tracking-widest flex items-center gap-3">
                    <span class="w-3 h-3 bg-gradient-to-r from-red-500 to-red-600 rounded-full shadow-lg shadow-red-500/30"></span>
                    Core Specifications
                </h2>

                <div class="space-y-6">

                    <div class="group">
                        <p class="text-xs font-normal text-slate-400 uppercase tracking-widest mb-2">Asset ID</p>
                        <div class="bg-gradient-to-r from-slate-50 to-slate-100 rounded-xl px-4 py-3 border border-slate-200/60">
                            <p class="text-lg font-normal text-slate-900">{{ $asset->code }}</p>
                        </div>
                    </div>

                    <div class="group">
                        <p class="text-xs font-normal text-slate-400 uppercase tracking-widest mb-2">Device Type</p>
                        <div class="bg-gradient-to-r from-slate-50 to-slate-100 rounded-xl px-4 py-3 border border-slate-200/60">
                            <p class="text-lg font-normal text-slate-900">{{ $asset->category->name ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="group">
                        <p class="text-xs font-normal text-slate-400 uppercase tracking-widest mb-2">Deployment Location</p>
                        <div class="bg-gradient-to-r from-slate-50 to-slate-100 rounded-xl px-4 py-3 border border-slate-200/60">
                            <p class="text-lg font-normal text-slate-900">{{ $asset->location ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="group">
                        <p class="text-xs font-normal text-slate-400 uppercase tracking-widest mb-3">Current Custodian</p>
                        <div class="bg-gradient-to-r from-red-50 to-red-100 rounded-xl p-4 border border-red-200/60">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-r from-red-500 to-red-600 flex items-center justify-center text-white font-normal text-lg shadow-lg shadow-red-500/30">
                                    {{ strtoupper(substr($asset->loans->first()?->user->name ?? 'U',0,1)) }}
                                </div>
                                <div>
                                    <p class="text-lg font-normal text-slate-900">
                                        {{ $asset->loans->first()?->user->name ?? 'No User Assigned' }}
                                    </p>
                                    <p class="text-xs font-normal text-red-600 uppercase tracking-widest">Active User</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    <div class="mt-8">
                        <div class="inline-flex items-center gap-3 px-6 py-3 rounded-2xl
                            @if($asset->status == 'tersedia') bg-gradient-to-r from-green-50 to-green-100 text-green-700 border border-green-200/60
                            @elseif($asset->status == 'dipinjam') bg-gradient-to-r from-red-50 to-red-100 text-red-700 border border-red-200/60
                            @else bg-gradient-to-r from-green-50 to-green-100 text-green-700 border border-green-200/60 @endif">
                            <div class="w-3 h-3 rounded-full shadow-lg 
                                @if($asset->status == 'tersedia') bg-green-500 shadow-green-500/50
                                @elseif($asset->status == 'dipinjam') bg-red-500 shadow-red-500/50
                                @else bg-green-500 shadow-green-500/50 @endif"></div>
                            <span class="text-sm font-normal uppercase tracking-widest">{{ ucfirst($asset->status) }}</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- RIGHT - IMAGE & HISTORY -->
        <div class="lg:col-span-2 space-y-8">

            <!-- IMAGE SECTION -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200/60 shadow-xl shadow-slate-200/50">
                <div class="relative">
                    <div class="h-80 w-full rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center relative overflow-hidden border-2 border-dashed border-slate-300/60">
                        <div class="absolute top-4 left-4">
                            <span class="w-3 h-3 bg-gradient-to-r from-red-500 to-red-600 rounded-full shadow-lg shadow-red-500/30 animate-pulse"></span>
                        </div>
                        <div class="text-center">
                            <svg class="w-20 h-20 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-slate-400 font-normal uppercase tracking-widest text-sm">Asset Image</p>
                            <p class="text-slate-300 text-xs mt-2">No image available</p>
                        </div>
                    </div>
                    
                    {{-- Additional Asset Info --}}
                    @if($asset->brand || $asset->model || $asset->year)
                    <div class="mt-6 grid grid-cols-3 gap-4">
                        @if($asset->brand)
                        <div class="text-center">
                            <p class="text-xs font-normal text-slate-400 uppercase tracking-widest mb-1">Brand</p>
                            <p class="text-sm font-normal text-slate-900">{{ $asset->brand }}</p>
                        </div>
                        @endif
                        @if($asset->model)
                        <div class="text-center">
                            <p class="text-xs font-normal text-slate-400 uppercase tracking-widest mb-1">Model</p>
                            <p class="text-sm font-normal text-slate-900">{{ $asset->model }}</p>
                        </div>
                        @endif
                        @if($asset->year)
                        <div class="text-center">
                            <p class="text-xs font-normal text-slate-400 uppercase tracking-widest mb-1">Year</p>
                            <p class="text-sm font-normal text-slate-900">{{ $asset->year }}</p>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- HISTORY SECTION -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200/60 shadow-xl shadow-slate-200/50">
                <h2 class="text-sm font-normal text-slate-700 mb-8 uppercase tracking-widest flex items-center gap-3">
                    <span class="w-3 h-3 bg-gradient-to-r from-red-500 to-red-600 rounded-full shadow-lg shadow-red-500/30"></span>
                    Usage History
                </h2>

                @forelse($asset->loans as $index => $loan)

                <div class="flex gap-6 mb-8">

                    <!-- DOT & LINE -->
                    <div class="flex flex-col items-center">
                        <div class="w-4 h-4 rounded-full {{ $index == 0 ? 'bg-gradient-to-r from-red-500 to-red-600 shadow-lg shadow-red-500/30' : 'bg-slate-300' }}"></div>
                        @if(!$loop->last)
                        <div class="w-[3px] h-full bg-gradient-to-b from-slate-200 to-transparent mt-2"></div>
                        @endif
                    </div>

                    <!-- CONTENT -->
                    <div class="flex-1">
                        <div class="bg-gradient-to-r {{ $index == 0 ? 'from-red-50 to-red-100 border-red-200/60' : 'from-slate-50 to-slate-100 border-slate-200/60' }} rounded-2xl p-6 border">
                            <div class="flex justify-between items-center mb-3">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-full {{ $index == 0 ? 'bg-gradient-to-r from-red-500 to-red-600 shadow-lg shadow-red-500/30' : 'bg-gradient-to-r from-slate-400 to-slate-500' }} flex items-center justify-center text-white font-normal text-lg">
                                        {{ strtoupper(substr($loan->user->name ?? 'U',0,1)) }}
                                    </div>
                                    <div>
                                        <p class="text-lg font-normal text-slate-900">
                                            {{ $loan->user->name ?? 'User tidak ditemukan' }}
                                        </p>
                                        <p class="text-xs font-normal {{ $index == 0 ? 'text-red-600' : 'text-slate-500' }} uppercase tracking-widest">
                                            {{ $index == 0 ? 'CURRENT CUSTODIAN' : 'PREVIOUS USER' }}
                                        </p>
                                    </div>
                                </div>

                                <span class="text-xs font-normal px-4 py-2 rounded-full
                                    {{ $index == 0 ? 'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/30' : 'bg-gradient-to-r from-slate-200 to-slate-300 text-slate-600' }}">
                                    {{ $index == 0 ? 'ACTIVE' : 'COMPLETED' }}
                                </span>
                            </div>

                            <div class="flex items-center gap-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-slate-600 font-medium">
                                        {{ \Carbon\Carbon::parse($loan->created_at)->format('d M Y') }}
                                    </span>
                                </div>
                                
                                @if($index == 0)
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-red-600 font-medium">Present</span>
                                </div>
                                @else
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-green-600 font-medium">Returned</span>
                                </div>
                                @endif
                            </div>

                            @if($index == 0)
                            <div class="mt-4 pt-4 border-t border-red-200/60">
                                <p class="text-sm text-slate-600 italic">
                                    <span class="text-red-600 font-normal">●</span> Currently assigned and in use
                                </p>
                            </div>
                            @endif

                        </div>
                    </div>

                </div>

                @empty
                    <div class="text-center py-16">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-slate-400 font-normal uppercase tracking-widest text-sm">No Usage History</p>
                        <p class="text-slate-300 text-xs mt-2">This asset hasn't been assigned yet</p>
                    </div>
                @endforelse

            </div>

        </div>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="mt-12">
        <div class="bg-white rounded-3xl p-8 border border-slate-200/60 shadow-xl shadow-slate-200/50">
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/asset-library/scan" 
                   class="bg-gradient-to-r from-slate-600 to-slate-700 hover:from-slate-700 hover:to-slate-800 text-white px-8 py-4 rounded-2xl text-sm font-normal uppercase tracking-widest transition-all duration-300 hover:shadow-xl hover:shadow-slate-600/30 transform hover:-translate-y-1">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Scan Another
                </a>
            </div>
        </div>
    </div>

</div>
@endsection