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
        <div>
            <label class="block text-xs font-black text-slate-600 mb-2">
                Tanggal Peminjaman
            </label>
            <input type="date" name="borrow_date" required class="w-full border p-3 rounded-xl">
        </div>
        <div>
            <label class="block text-xs font-black text-slate-600 mb-2">
                Tanggal Pengembalian
            </label>
            <input type="date" name="return_date" required class="w-full border p-3 rounded-xl">
        </div>
    </div>

    <button class="w-full bg-red-600 text-white py-3 rounded-xl font-bold">
        KIRIM
    </button>

</form>


</div>

{{-- JS UNTUK DYNAMIC ASSET --}}
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
        <select onchange="loadAssetsByCategory(this)" class="w-full border p-2 rounded-xl">
            <option value="">Pilih Kategori</option>
            ${categories.map(c => `<option value="${c.id}">${c.name}</option>`).join('')}
        </select>

        <div class="relative">
            <input type="text" placeholder="Pilih asset dari list..."
                onfocus="showAssetList(this)"
                onkeyup="filterAssetList(this)"
                class="w-full border p-2 rounded-xl">

            <div class="asset-list absolute w-full bg-white border mt-1 rounded-xl shadow max-h-60 overflow-auto hidden z-10"></div>
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
    parent.querySelector('.asset-list').classList.add('hidden');
    parent.querySelector('input[type="text"]').value = '';
    parent.querySelector('input[name="asset_id[]"]').value = '';
    parent.querySelector('#asset-code-display').textContent = '-';
}

// load assets saat kategori dipilih
function loadAssetsByCategory(select) {
    let categoryId = select.value;
    let parent = select.parentElement;
    let list = parent.querySelector('.asset-list');
    let input = parent.querySelector('input[type="text"]');

    // reset dulu
    list.innerHTML = '';
    input.value = '';
    parent.querySelector('input[name="asset_id[]"]').value = '';
    parent.querySelector('#asset-code-display').textContent = '-';

    if (!categoryId) {
        list.classList.add('hidden');
        return;
    }

    // filter asset berdasarkan kategori
    let selected = getSelectedAssets();
    let filtered = assets.filter(a => a.category_id == categoryId);

    if (filtered.length === 0) {
        list.innerHTML = `<div class="p-2 text-slate-500 text-sm">Tidak ada asset di kategori ini</div>`;
        list.classList.remove('hidden');
        return;
    }

    // sorting: asset tersedia di atas
    filtered.sort((a, b) => {
        // prioritas status: tersedia > dipinjam > perlu_perbaikan > tidak_tersedia
        const statusPriority = {
            'tersedia': 1,
            'dipinjam': 2,
            'perlu_perbaikan': 3,
            'tidak_tersedia': 4
        };
        
        const priorityA = statusPriority[a.status] || 5;
        const priorityB = statusPriority[b.status] || 5;
        
        // jika prioritas berbeda, urutkan berdasarkan prioritas
        if (priorityA !== priorityB) {
            return priorityA - priorityB;
        }
        
        // jika prioritas sama, urutkan berdasarkan nama
        return a.name.localeCompare(b.name);
    });

    // tampilkan semua asset dari kategori
    filtered.forEach(a => {
        let statusIcon = '';
        let statusClass = '';
        let isClickable = false;

        if (a.status === 'tersedia') {
            statusIcon = '🟢';
            statusClass = 'text-green-600';
            isClickable = true;
        } else if (a.status === 'dipinjam') {
            statusIcon = '🔴';
            statusClass = 'text-red-600';
        } else if (a.status === 'perlu_perbaikan') {
            statusIcon = '🟡';
            statusClass = 'text-yellow-600';
        } else if (a.status === 'tidak_tersedia') {
            statusIcon = '⚫';
            statusClass = 'text-slate-600';
        } else {
            // fallback untuk status lain
            statusIcon = '⚫';
            statusClass = 'text-slate-600';
        }

        let isSelected = selected.includes(a.id.toString());

        let item = document.createElement('div');
        item.className = "p-2 flex justify-between items-center border-b last:border-b-0";

        item.innerHTML = `
            <div class="flex items-center gap-2">
                <span class="text-lg">${statusIcon}</span>
                <span class="text-sm ${statusClass}">${a.name}</span>
            </div>
            <span class="text-xs text-slate-500">${a.code}</span>
        `;

        if (!isClickable || isSelected) {
            item.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            item.classList.add('cursor-pointer', 'hover:bg-slate-100');
            item.onclick = () => selectAsset(parent, input, a);
        }

        list.appendChild(item);
    });

    list.classList.remove('hidden');
}

// show list saat input di-focus
function showAssetList(input) {
    let parent = input.closest('.p-3');
    let categoryId = parent.querySelector('select').value;
    let list = parent.querySelector('.asset-list');

    if (!categoryId) {
        list.innerHTML = `<div class="p-2 text-red-500 text-sm">Pilih kategori terlebih dahulu</div>`;
        list.classList.remove('hidden');
        return;
    }

    // jika list kosong, load ulang
    if (list.children.length === 0) {
        loadAssetsByCategory(parent.querySelector('select'));
    } else {
        list.classList.remove('hidden');
    }
}

// filter list saat mengetik
function filterAssetList(input) {
    let keyword = input.value.toLowerCase();
    let parent = input.closest('.p-3');
    let list = parent.querySelector('.asset-list');
    let items = list.querySelectorAll('.p-2');

    if (!keyword) {
        items.forEach(item => item.style.display = 'flex');
        return;
    }

    items.forEach(item => {
        let text = item.textContent.toLowerCase();
        if (text.includes(keyword)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}

// search (fungsi lama - tidak dipakai lagi)
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

    parent.querySelector('input[name="asset_id[]"]').value = asset.id;
    
    // tutup list setelah dipilih
    list.classList.add('hidden');
}

// klik luar close
document.addEventListener('click', function(e) {
    document.querySelectorAll('.asset-list').forEach(list => {
        let parent = list.closest('.p-3');
        if (parent && !parent.contains(e.target)) {
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
