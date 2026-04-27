@extends('layouts.app')

@section('title', 'Form Peminjaman Asset')

@section('content')

{{-- Container Utama: Form card untuk peminjaman --}}
<div class="w-full max-w-2xl bg-white rounded-[2.5rem] shadow-2xl p-10 h-auto -mt-4">

        {{-- JUDUL --}}
        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-red-600 uppercase tracking-tighter mb-2">
                PEMINJAMAN ASSET
            </h2>
            <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">
                Isi data dengan teliti dan benar
            </p>
        </div>

        <form action="{{ route('peminjaman.store') }}" method="POST">
            @csrf

            {{-- NAMA PEMINJAM --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Nama Peminjam
                </label>
                <input type="text"
                    value="{{ auth()->user()->name ?? '' }}"
                    readonly
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700">
            </div>

            {{-- ASSET SECTION --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Asset (Maksimal 5)
                </label>
                <div id="asset-wrapper" class="space-y-3 mb-4">
                    {{-- Row asset akan muncul di sini --}}
                </div>

                <button type="button" onclick="addAsset()" 
                    class="bg-slate-900 hover:bg-slate-800 text-white font-black text-xs uppercase tracking-wider px-4 py-3 rounded-xl transition-all duration-300 hover:shadow-lg">
                    <i class="fas fa-plus mr-2"></i> Tambah Asset
                </button>

                <div class="flex gap-4 mt-3 text-xs font-bold text-slate-400">
                    <span>🟢 Tersedia</span>
                    <span>🔴 Sedang dipinjam</span>
                </div>
            </div>

            {{-- TANGGAL --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div>
                    <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                        Tgl Pinjam
                    </label>
                    <input type="date" name="borrow_date" required 
                        class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-bold text-slate-700 focus:border-red-500 focus:outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                        Tgl Kembali
                    </label>
                    <input type="date" name="return_date" required 
                        class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-bold text-slate-700 focus:border-red-500 focus:outline-none transition-colors">
                </div>
            </div>

            {{-- TOMBOL KIRIM --}}
            <button type="submit" 
                class="w-full bg-red-600 hover:bg-red-700 text-white font-black text-sm uppercase tracking-wider py-4 rounded-xl transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1">
                KIRIM PERMINTAAN
            </button>
        </form>

    </div>
</div>

<div class="w-full max-w-2xl mt-8">
    <div class="bg-white rounded-[2.5rem] shadow-2xl p-8 border border-slate-200">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter">Daftar Peminjaman</h3>
                <p class="text-sm text-slate-500 mt-2">Data peminjaman terbaru dari database.</p>
            </div>
            <span class="px-4 py-2 text-[10px] font-black uppercase tracking-[0.25em] text-slate-500 bg-slate-100 rounded-full">Total: {{ $peminjaman->count() }}</span>
        </div>

        @if($peminjaman->isEmpty())
            <div class="text-center py-16 text-slate-400 font-bold uppercase tracking-[0.2em]">Belum ada data peminjaman</div>
        @else
            <div class="space-y-4">
                @foreach($peminjaman as $loan)
                    <div class="p-5 border border-slate-200 rounded-[2rem] hover:shadow-sm transition-all">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                            <div>
                                <p class="text-[10px] uppercase tracking-[0.3em] font-black text-slate-400">ID Peminjaman</p>
                                <p class="text-lg font-black text-slate-900">#{{ $loan->id }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] uppercase tracking-[0.3em] font-black text-slate-400">Peminjam</p>
                                <p class="text-sm font-bold text-slate-900">{{ $loan->user->name ?? 'Tidak diketahui' }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-slate-600">
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-[10px] uppercase tracking-[0.25em] font-black text-slate-400">Tgl Pinjam</p>
                                <p class="font-semibold">{{ \Carbon\Carbon::parse($loan->borrow_date)->translatedFormat('d M Y') }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-[10px] uppercase tracking-[0.25em] font-black text-slate-400">Tgl Kembali</p>
                                <p class="font-semibold">{{ \Carbon\Carbon::parse($loan->return_date)->translatedFormat('d M Y') }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-[10px] uppercase tracking-[0.25em] font-black text-slate-400">Status</p>
                                <p class="font-semibold uppercase">{{ $loan->status }}</p>
                            </div>
                        </div>

                        <div class="mt-6 space-y-3">
                            @foreach($loan->assets as $asset)
                                <div class="flex items-center justify-between gap-3 p-4 bg-slate-50 rounded-3xl">
                                    <div>
                                        <p class="font-black text-slate-900">{{ $asset->name }}</p>
                                        <p class="text-[10px] text-slate-400">Qty: {{ $asset->pivot->quantity }}</p>
                                    </div>
                                    <span class="text-[10px] uppercase tracking-[0.2em] font-black text-slate-500">{{ $asset->status }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- JS UNTUK DYNAMIC ASSET --}}
<script>
let maxAsset = 5;
let count = 0;
const assets = @json($assets);

function addAsset() {
    if (count >= maxAsset) {
        alert("Maksimal 5 asset per peminjaman!");
        return;
    }

    let wrapper = document.getElementById('asset-wrapper');
    let div = document.createElement('div');
    div.className = "flex gap-3 items-center animate-fadeIn";

    let select = `<select name="asset_id[]" class="flex-1 px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-bold text-slate-700 focus:border-red-500 focus:outline-none transition-colors" required>
                    <option value="">-- Pilih Asset --</option>`;

    assets.forEach(a => {
        let isAvailable = a.status === 'tersedia';
        let label = isAvailable ? `🟢 ${a.name}` : `🔴 ${a.name}`;
        let disabled = isAvailable ? '' : 'disabled';
        let colorClass = isAvailable ? 'text-slate-700' : 'text-slate-400';
        select += `<option value="${a.id}" ${disabled} class="${colorClass}">${label}</option>`;
    });

    select += `</select>`;

    div.innerHTML = select + `
        <button type="button" onclick="this.parentElement.remove(); count--" 
            class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 w-12 h-12 rounded-xl font-bold transition-all duration-200 hover:shadow-md">
            <i class="fas fa-trash"></i>
        </button>`;

    wrapper.appendChild(div);
    count++;
}

// Auto add first asset row on load
window.onload = addAsset;
</script>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
}
</style>

@endsection