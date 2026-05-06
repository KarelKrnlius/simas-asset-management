@extends('layouts.app')

@section('content')
<div class="w-full min-h-screen bg-slate-100 px-10 py-10">

    <div class="w-full space-y-10">

        <!-- HEADER -->
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-slate-800">
                Detail Aset
            </h1>

            <a href="/asset-library/scan"
               class="bg-slate-900 text-white px-6 py-2.5 rounded-xl hover:bg-slate-800 hover:scale-105 hover:shadow-lg active:scale-95 active:bg-slate-700 transition-all duration-200 transform cursor-pointer">
                <i class="fas fa-qrcode mr-2"></i>Scan Lagi
            </a>
        </div>

        <!-- GRID -->
        <div class="grid grid-cols-3 gap-10">

            <!-- LEFT CARD -->
            <div class="col-span-1 bg-white p-8 rounded-3xl shadow-sm hover:shadow-md transition">

                <h2 class="text-lg font-semibold text-slate-700 mb-6">
                    Spesifikasi Aset
                </h2>

                <div class="space-y-5">

                    <div>
                        <p class="text-gray-400 text-sm">Kode Asset</p>
                        <p class="text-xl font-semibold text-slate-800">
                            {{ $asset->code }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-400 text-sm">Nama Asset</p>
                        <p class="text-xl font-semibold text-slate-800">
                            {{ $asset->name }}
                        </p>
                    </div>

                    <!-- STATUS -->
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Status</p>
                        <span class="px-4 py-1.5 text-sm rounded-full font-medium uppercase
                            @if(strtolower($asset->status) == 'tersedia')
                                bg-green-100 text-green-600
                            @elseif(strtolower($asset->status) == 'dipinjam')
                                bg-blue-100 text-blue-600
                            @elseif(strtolower($asset->status) == 'maintenance')
                                bg-yellow-100 text-yellow-600
                            @else
                                bg-red-100 text-red-600
                            @endif">
                            {{ $asset->status }}
                        </span>
                    </div>

                    <!-- KONDISI -->
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Kondisi</p>
                        <span class="px-4 py-1.5 text-sm rounded-full font-medium uppercase
                            @if(strtolower($asset->condition) == 'baik')
                                bg-green-100 text-green-600
                            @elseif(strtolower($asset->condition) == 'rusak')
                                bg-yellow-100 text-yellow-600
                            @elseif(strtolower($asset->condition) == 'hilang')
                                bg-red-100 text-red-600
                            @else
                                bg-gray-100 text-gray-600
                            @endif">
                            {{ $asset->condition }}
                        </span>
                    </div>

                </div>

            </div>

            <!-- IMAGE CARD -->
            <div class="col-span-2 bg-white rounded-3xl p-10 flex items-center justify-center shadow-sm hover:shadow-md transition">

                @php
                    $photoUrl = \App\Helpers\AssetHelper::getPhotoUrl($asset->photo) ?? asset('images/no-image.png');
                @endphp

                <img src="{{ $photoUrl }}"
                     class="max-h-[420px] object-contain transition duration-300 hover:scale-105">

            </div>

        </div>

        <!-- HISTORY -->
        <div class="bg-white rounded-3xl p-8 shadow-sm hover:shadow-md transition">

            <h2 class="text-lg font-semibold text-slate-700 mb-8">
                Riwayat Penggunaan
            </h2>

            <div class="space-y-8">

                @forelse($asset->loans as $loan)

                    <div class="flex gap-6 items-start">

                        <!-- DOT -->
                        <div class="mt-2">
                            <div class="w-3 h-3 rounded-full
                                {{ $loan->status == 'returned' ? 'bg-green-500' : 'bg-blue-500' }}">
                            </div>
                        </div>

                        <!-- CONTENT -->
                        <div class="flex-1 border-l border-gray-200 pl-6">

                            <div class="flex justify-between items-center">
                                <p class="font-semibold text-slate-800 text-lg">
                                    {{ $loan->user->name }}
                                </p>

                                <span class="text-xs px-3 py-1 rounded-full
                                    {{ $loan->status == 'returned' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600' }}">
                                    {{ $loan->status }}
                                </span>
                            </div>

                            <p class="text-sm text-gray-500 mt-2">
                                {{ $loan->borrow_date }} - {{ $loan->return_date ?? 'Sekarang' }}
                            </p>

                        </div>

                    </div>

                @empty

                    <p class="text-gray-400 text-sm">
                        Belum ada riwayat penggunaan.
                    </p>

                @endforelse

            </div>

        </div>

    </div>

</div>
@endsection