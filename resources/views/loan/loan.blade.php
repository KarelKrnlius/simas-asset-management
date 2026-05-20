@extends('layouts.app')

@section('title', 'Peminjaman')

@section('content')

<div class="w-full max-w-2xl lg:max-w-4xl bg-white rounded-[2.5rem] shadow-2xl p-6 sm:p-8 lg:p-10 h-auto -mt-4 mx-4 sm:mx-auto">

<div class="text-center mb-8">
    <h2 class="text-2xl font-black text-red-600 uppercase tracking-tighter mb-2">
        PEMINJAMAN ASSET
    </h2>
    <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">
        Isi data dengan teliti dan benar
    </p>
</div>

<form method="POST" action="{{ route('peminjaman.store') }}" onsubmit="return validateForm()" id="loanForm" data-submitted="false">
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
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
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

    <button type="submit" id="submitBtn" class="w-full bg-red-600 text-white py-3 rounded-xl font-bold flex items-center justify-center">
        <span id="submitText">KIRIM</span>
        <svg id="loadingSpinner" class="animate-spin h-5 w-5 ml-2 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </button>

</form>


</div>

{{-- JS UNTUK DYNAMIC ASSET --}}
<script>
let maxAsset = 5;
let count = 0;

const assets = @json($assets);
const categories = @json($categories);
const activeLoansCount = {{ $activeLoansCount ?? 0 }};

// ambil asset yg sudah dipilih
function getSelectedAssets() {
    return Array.from(document.querySelectorAll('input[name="asset_id[]"]'))
        .map(i => i.value);
}

// tambah row dengan validasi dinamis
function addAsset() {
    // Hitung asset yang sudah dipilih di form ini
    let currentFormAssets = document.querySelectorAll('.asset-row').length;
    
    // Hitung total asset yang sudah dipinjam (form + aktif dari sesi sebelumnya)
    let totalAssets = currentFormAssets + activeLoansCount;
    
    // Debug: tampilkan jumlah asset
    console.log('Form assets:', currentFormAssets, 'Active loans:', activeLoansCount, 'Total:', totalAssets, 'Max assets:', maxAsset);
    
    // Cek batas maksimal (5)
    if (totalAssets >= maxAsset) {
        console.log('Showing limit notification');
        showAssetLimitNotification();
        return;
    }

    let wrapper = document.getElementById('asset-wrapper');

    let div = document.createElement('div');
    div.className = "p-3 border rounded-xl space-y-2 asset-row";

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

            <div class="asset-list absolute w-full bg-white border mt-1 rounded-xl shadow max-h-36 overflow-y-auto hidden z-10"></div>
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

    // sorting: asset tersedia di atas, lalu berurutan berdasarkan kode asset
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
        
        // jika prioritas sama, extract angka terakhir dari kode untuk sorting
        const extractNumber = (code) => {
            const match = code.match(/\d+$/);
            return match ? parseInt(match[0]) : 0;
        };
        
        const numA = extractNumber(a.code);
        const numB = extractNumber(b.code);
        
        return numA - numB;
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
            item.classList.add('opacity-50', 'cursor-pointer');
            item.onclick = () => showAssetUnavailableNotification(a);
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

// validate form with loading state
function validateForm() {
    const form = document.getElementById('loanForm');
    
    // Cek apakah form sudah disubmit
    if (form.dataset.submitted === 'true') {
        showFormNotification('error', 'Form sedang diproses. Mohon tunggu...');
        return false;
    }
    
    const selectedAssets = document.querySelectorAll('input[name="asset_id[]"]');
    let hasSelectedAsset = false;
    
    selectedAssets.forEach(input => {
        if (input.value && input.value !== '') {
            hasSelectedAsset = true;
        }
    });
    
    if (!hasSelectedAsset) {
        showFormNotification('error', 'Silakan pilih minimal satu asset terlebih dahulu!');
        return false;
    }
    
    // Show loading state and prevent double submit
    showLoading();
    
    // Add form submitted flag
    form.dataset.submitted = 'true';
    
    return true;
}

// show loading state
function showLoading() {
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingSpinner = document.getElementById('loadingSpinner');
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
    submitText.textContent = 'MENGIRIM...';
    loadingSpinner.classList.remove('hidden');
}

// reset loading state (untuk error handling)
function resetLoading() {
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingSpinner = document.getElementById('loadingSpinner');
    
    // Enable button and hide loading
    submitBtn.disabled = false;
    submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
    submitText.textContent = 'KIRIM';
    loadingSpinner.classList.add('hidden');
    
    // Reset form submitted flag
    document.getElementById('loanForm').dataset.submitted = 'false';
}

// show form notification
function showFormNotification(type, message) {
    // Hapus notifikasi lama
    const oldNotif = document.getElementById('formNotification');
    if (oldNotif) {
        oldNotif.remove();
    }
    
    // Buat notifikasi baru
    const notification = document.createElement('div');
    notification.id = 'formNotification';
    
    if (type === 'error') {
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            z-index: 9999;
            max-width: 400px;
        `;
        notification.innerHTML = `
            <div style="display: flex; align-items: center;">
                <span style="font-size: 24px; margin-right: 12px;">❌</span>
                <div>
                    <p style="font-size: 14px; font-weight: 600; color: #991b1b; margin: 0;">Error</p>
                    <p style="font-size: 14px; color: #b91c1c; margin: 4px 0 0 0;">${message}</p>
                </div>
                <button onclick="document.getElementById('formNotification').remove()" style="margin-left: auto; color: #ef4444; background: none; border: none; cursor: pointer;">
                    ✕
                </button>
            </div>
        `;
    } else if (type === 'success') {
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #f0fdf4;
            border-left: 4px solid #22c55e;
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            z-index: 9999;
            max-width: 400px;
        `;
        notification.innerHTML = `
            <div style="display: flex; align-items: center;">
                <span style="font-size: 24px; margin-right: 12px;">✅</span>
                <div>
                    <p style="font-size: 14px; font-weight: 600; color: #166534; margin: 0;">Berhasil</p>
                    <p style="font-size: 14px; color: #16a34a; margin: 4px 0 0 0;">${message}</p>
                </div>
                <button onclick="document.getElementById('formNotification').remove()" style="margin-left: auto; color: #22c55e; background: none; border: none; cursor: pointer;">
                    ✕
                </button>
            </div>
        `;
    }
    
    document.body.appendChild(notification);
    
    // Auto remove setelah 3 detik
    setTimeout(() => {
        const notif = document.getElementById('formNotification');
        if (notif) {
            notif.remove();
        }
    }, 3000);
}

// show notification for unavailable assets
function showAssetUnavailableNotification(asset) {
    let message = '';
    let icon = '';
    
    if (asset.status === 'dipinjam') {
        message = `Asset "${asset.name}" sedang dipinjam oleh user lain. Silakan pilih asset lain yang tersedia.`;
        icon = '🔴';
    } else if (asset.status === 'perlu_perbaikan') {
        message = `Asset "${asset.name}" sedang dalam perbaikan. Tidak dapat dipinjam saat ini.`;
        icon = '🟡';
    } else if (asset.status === 'tidak_tersedia') {
        message = `Asset "${asset.name}" tidak tersedia untuk dipinjam.`;
        icon = '⚫';
    } else {
        // Sudah dipilih di form ini
        message = `Asset "${asset.name}" sudah ditambahkan dalam daftar peminjaman ini.`;
        icon = '📋';
    }
    
    // Tampilkan notifikasi
    showNotification(icon, message);
}

// show notification function
function showNotification(icon, message) {
    // Cek apakah sudah ada notifikasi
    let existingNotif = document.getElementById('assetNotification');
    if (existingNotif) {
        existingNotif.remove();
    }
    
    // Buat notifikasi baru
    const notification = document.createElement('div');
    notification.id = 'assetNotification';
    notification.className = 'fixed top-4 right-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-lg z-50 max-w-md';
    notification.innerHTML = `
        <div class="flex items-center">
            <span class="text-2xl mr-3">${icon}</span>
            <div>
                <p class="text-sm font-medium text-red-800">Asset Tidak Tersedia</p>
                <p class="text-sm text-red-600 mt-1">${message}</p>
            </div>
            <button onclick="this.closest('#assetNotification').remove()" class="ml-auto text-red-400 hover:text-red-600">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    `;
    
    // Tambahkan ke body
    document.body.appendChild(notification);
    
    // Auto remove setelah 5 detik
    setTimeout(() => {
        if (document.getElementById('assetNotification')) {
            document.getElementById('assetNotification').remove();
        }
    }, 5000);
}

// show asset limit notification
function showAssetLimitNotification() {
    // Hapus notifikasi lama jika ada
    const oldNotif = document.getElementById('assetLimitNotification');
    if (oldNotif) {
        oldNotif.remove();
    }
    
    // Buat notifikasi baru
    const notification = document.createElement('div');
    notification.id = 'assetLimitNotification';
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #fef2f2;
        border-left: 4px solid #ef4444;
        padding: 16px;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 9999;
        max-width: 400px;
    `;
    
    // Buat pesan yang lebih spesifik
    let message = '';
    if (activeLoansCount > 0) {
        message = `Anda sudah meminjam ${activeLoansCount} asset dari sesi sebelumnya. Kembalikan minimal 1 asset untuk bisa menambahkan lagi.`;
    } else {
        message = `Maksimal 5 asset per peminjaman. Anda sudah meminjam ${currentFormAssets} asset di form ini.`;
    }
    
    notification.innerHTML = `
        <div style="display: flex; align-items: center;">
            <span style="font-size: 24px; margin-right: 12px;">⚠️</span>
            <div>
                <p style="font-size: 14px; font-weight: 600; color: #991b1b; margin: 0;">Sudah Melebihi Batas Peminjaman</p>
                <p style="font-size: 14px; color: #b91c1c; margin: 4px 0 0 0;">${message}</p>
            </div>
            <button onclick="document.getElementById('assetLimitNotification').remove()" style="margin-left: auto; color: #ef4444; background: none; border: none; cursor: pointer;">
                ✕
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Debug log
    console.log('Asset limit notification added');
    
    // Auto remove setelah 4 detik
    setTimeout(() => {
        const notif = document.getElementById('assetLimitNotification');
        if (notif) {
            notif.remove();
        }
    }, 4000);
}

// tidak auto addAsset saat halaman dimuat
window.onload = function() {
    // Tidak lakukan apa-apa saat halaman dimuat
    console.log('Halaman peminjaman dimuat');
};
</script>

@endsection
