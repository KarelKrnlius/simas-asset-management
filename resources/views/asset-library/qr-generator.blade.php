@extends('layouts.app')

@section('title', 'QR Code Generator - Asset Library')

@section('content')
<div class="container mx-auto px-4 lg:px-8 max-w-7xl">
    
    {{-- Header --}}
    <div class="text-center mb-8">
        <h1 class="text-3xl font-extrabold text-red-600 uppercase tracking-wider mb-2">QR Code Generator</h1>
        <p class="text-slate-500 text-sm uppercase tracking-[0.2em]">Generate QR codes untuk semua assets di database</p>
    </div>

    {{-- Instructions --}}
    <div class="bg-red-50 border border-red-200 rounded-2xl p-6 mb-8">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center text-white flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-red-900 mb-2">Cara Menggunakan:</h3>
                <ol class="text-sm text-red-700 space-y-1 list-decimal list-inside">
                    <li>QR codes ini otomatis di-generate dari database assets</li>
                    <li>Setiap asset baru akan otomatis muncul di halaman ini</li>
                    <li>Download QR codes dan print untuk ditempel pada aset fisik</li>
                    <li>Gunakan QR scanner untuk scan aset dan langsung ke detail</li>
                </ol>
            </div>
        </div>
    </div>

    {{-- Assets Grid --}}
    @if($assets->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($assets as $asset)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    {{-- Asset Info --}}
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-black text-slate-900 uppercase">{{ $asset->name }}</h3>
                            <span class="bg-red-600 text-white text-[10px] font-black px-3 py-1 rounded-full">
                                {{ $asset->category ? $asset->category->name : 'Uncategorized' }}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Kode</p>
                                <p class="font-black text-slate-900">{{ $asset->code }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">QR Code</p>
                                <p class="font-black text-slate-900 text-xs">{{ $asset->qr_code ?? 'N/A' }}</p>
                            </div>
                            @if($asset->brand)
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Merek</p>
                                <p class="font-black text-slate-900">{{ $asset->brand }}</p>
                            </div>
                            @endif
                            @if($asset->model)
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Model</p>
                                <p class="font-black text-slate-900">{{ $asset->model }}</p>
                            </div>
                            @endif
                            @if($asset->location)
                            <div class="col-span-2">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Lokasi</p>
                                <p class="font-black text-slate-900">{{ $asset->location }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    {{-- QR Code --}}
                    <div class="bg-slate-50 p-6 text-center border-t border-slate-200">
                        <div class="mb-4">
                            <div id="qrcode-{{ $asset->id }}" class="inline-block bg-white p-4 rounded-xl border-2 border-red-600"></div>
                        </div>
                        <button onclick="downloadQR('{{ $asset->id }}', '{{ $asset->qr_code ?? $asset->code }}')" 
                                class="bg-red-600 text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-700 transition-colors duration-200">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l4 4"></path>
                            </svg>
                            Download QR
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16 bg-white rounded-2xl border-2 border-dashed border-slate-300">
            <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <h3 class="text-xl font-black text-slate-600 uppercase mb-2">Belum Ada Assets</h3>
            <p class="text-slate-400">Tambahkan assets terlebih dahulu untuk generate QR codes</p>
            <a href="{{ route('assets.index') }}" 
               class="inline-flex items-center bg-red-600 text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-700 transition-colors duration-200 mt-4">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Asset
            </a>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
// Generate QR codes for all assets from database
document.addEventListener('DOMContentLoaded', function() {
    const assets = @json($assets->toArray());
    
    assets.forEach(asset => {
        const qrText = asset.qr_code || asset.code;
        
        try {
            new QRCode(document.getElementById(`qrcode-${asset.id}`), {
                text: qrText,
                width: 150,
                height: 150,
                colorDark: "#dc2626",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        } catch (error) {
            console.error('Error generating QR code for asset', asset.id, error);
        }
    });
});

function downloadQR(assetId, filename) {
    const canvas = document.querySelector(`#qrcode-${assetId} canvas`);
    if (canvas) {
        const link = document.createElement('a');
        link.download = `${filename}.png`;
        link.href = canvas.toDataURL();
        link.click();
    }
}
</script>
@endsection
