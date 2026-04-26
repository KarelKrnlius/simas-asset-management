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

        <form action="{{ route('peminjaman') }}" method="POST">
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