@extends('layouts.app')

@section('title', 'Form Peminjaman Asset')

@section('content')

<div class="w-full max-w-2xl bg-white rounded-[2.5rem] shadow-2xl p-10 h-auto -mt-4">

<div class="text-center mb-8">
    <h2 class="text-2xl font-black text-red-600 uppercase tracking-tighter mb-2">
        PEMINJAMAN ASSET
    </h2>
    <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">
        Isi data dengan teliti dan benar
    </p>
</div>

<form action="{{ route('peminjaman.store') }}" method="POST" onsubmit="return validateForm()">
    @csrf

    {{-- NAMA --}}
    <div class="mb-6">
        <label class="block text-xs font-black text-slate-600 mb-2">
            Nama Peminjam
        </label>
        <input type="text"
            value="{{ auth()->user()->name ?? '' }}"
            readonly
            class="w-full px-4 py-3 bg-slate-50 border rounded-xl font-bold">
    </div>

    {{-- ASSET --}}
    <div class="mb-6">
        <label class="block text-xs font-black text-slate-600 mb-2">
            Asset (Max 5)
        </label>

        <div id="asset-wrapper" class="space-y-3 mb-4"></div>

        <button type="button" onclick="addAsset()"
            class="bg-slate-900 text-white px-4 py-3 rounded-xl text-xs font-bold">
            + Tambah Asset
        </button>

        <div class="flex gap-4 mt-3 text-xs font-bold text-slate-400">
            <span>🟢 Tersedia</span>
            <span>🔴 Dipinjam</span>
            <span>🟡 Perlu Perbaikan</span>
            <span>⚫ Tidak Tersedia</span>
        </div>
    </div>

    {{-- TANGGAL --}}
    <div class="grid grid-cols-2 gap-4 mb-6">
        <input type="date" name="borrow_date" required class="border p-3 rounded-xl">
        <input type="date" name="return_date" required class="border p-3 rounded-xl">
    </div>

    <button class="w-full bg-red-600 text-white py-3 rounded-xl font-bold">
        KIRIM
    </button>

</form>


</div>

<script>
let maxAsset = 5;
let count = 0;

const assets = @json($assets);
const categories = @json($categories);

// ambil asset yg sudah dipilih
function getSelectedAssets() {
    return Array.from(document.querySelectorAll('input[name="asset_id[]"]'))
        .map(i => i.value);
}

// tambah row
function addAsset() {
    if (count >= maxAsset) return alert("Max 5!");

    let wrapper = document.getElementById('asset-wrapper');

    let div = document.createElement('div');
    div.className = "p-3 border rounded-xl space-y-2";

    div.innerHTML = `
        <select onchange="resetRow(this)" class="w-full border p-2 rounded-xl">
            <option value="">Pilih Kategori</option>
            ${categories.map(c => `<option value="${c.id}">${c.name}</option>`).join('')}
        </select>

        <div class="relative">
            <input type="text" placeholder="Cari asset..."
                onkeyup="searchAsset(this)"
                class="w-full border p-2 rounded-xl">

            <div class="asset-list absolute w-full bg-white border mt-1 rounded-xl shadow max-h-40 overflow-auto hidden"></div>
        </div>

        <input type="hidden" name="asset_id[]">

        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-600 font-semibold">KODE ASSET:</span>
                <span id="asset-code-display" class="text-xs font-bold text-red-600">-</span>
            </div>
            <button type="button" onclick="removeRow(this)" class="text-red-500 text-xs">
                Hapus
            </button>
        </div>
    `;

    wrapper.appendChild(div);
    count++;
}

// hapus
function removeRow(btn) {
    btn.closest('.p-3').remove();
    count--;
}

// reset
function resetRow(select) {
    let parent = select.parentElement;
    parent.querySelector('.asset-list').innerHTML = '';
    parent.querySelector('input[type="text"]').value = '';
}

// search
function searchAsset(input) {
    let keyword = input.value.toLowerCase();
    let parent = input.closest('.p-3');

    let categoryId = parent.querySelector('select').value;
    let list = parent.querySelector('.asset-list');

    list.innerHTML = '';

    if (!keyword) {
        list.classList.add('hidden');
        return;
    }

    if (!categoryId) {
        list.innerHTML = `<div class="p-2 text-red-500">Pilih kategori dulu</div>`;
        list.classList.remove('hidden');
        return;
    }

    let selected = getSelectedAssets();

    let filtered = assets.filter(a =>
        a.category_id == categoryId &&
        a.name.toLowerCase().includes(keyword)
    );

    filtered.forEach(a => {
        let isAvailable = a.status === 'tersedia';
        let isBorrowed = a.status === 'dipinjam';
        let isRepair = a.status === 'perlu_perbaiki';
        let isNotAvailable = a.status === 'tidak_tersedia';
        let isSelected = selected.includes(a.id.toString());

        let item = document.createElement('div');
        item.className = "p-2 flex justify-between";

        item.innerHTML = `
            <span>${a.name}</span>
            <span class="text-xs">${a.code}</span>
        `;

        if (!isAvailable || isBorrowed || isRepair || isNotAvailable || isSelected) {
            item.classList.add('opacity-50');
        } else {
            item.classList.add('cursor-pointer', 'hover:bg-slate-100');
            item.onclick = () => selectAsset(parent, input, a);
        }

        list.appendChild(item);
    });

    list.classList.remove('hidden');
}

// pilih
function selectAsset(parent, input, asset) {
    let list = parent.querySelector('.asset-list');
    let codeDisplay = parent.querySelector('#asset-code-display');

    input.value = asset.name;
    codeDisplay.textContent = asset.code;

    list.innerHTML = `
        <div class="bg-green-100 p-2 rounded flex justify-between">
            <span>✔ ${asset.name}</span>
            <span>${asset.code}</span>
        </div>
    `;

    parent.querySelector('input[name="asset_id[]"]').value = asset.id;
    list.classList.remove('hidden');
}

// klik luar close
document.addEventListener('click', function(e) {
    document.querySelectorAll('.asset-list').forEach(list => {
        if (!list.contains(e.target)) {
            list.classList.add('hidden');
        }
    });
});

// validate form
function validateForm() {
    const selectedAssets = document.querySelectorAll('input[name="asset_id[]"]');
    let hasSelectedAsset = false;
    
    selectedAssets.forEach(input => {
        if (input.value && input.value !== '') {
            hasSelectedAsset = true;
        }
    });
    
    if (!hasSelectedAsset) {
        alert('Silakan pilih minimal satu asset terlebih dahulu!');
        return false;
    }
    
    return true;
}

// auto first
window.onload = addAsset;
</script>

@endsection
