@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')

@section('content')
<div class="min-h-screen pt-1 items-start w-full">
    <div class="container mx-auto px-4 py-8">
        {{-- HEADER --}}
        <div class="bg-white rounded-[2rem] shadow-xl p-8 mb-8">
            <div class="mb-6">
                <h2 class="text-3xl font-black text-red-600 uppercase tracking-tighter mb-2">
                    Riwayat Peminjaman
                </h2>
                <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">
                    Semua data peminjaman asset (aktif dan sudah dikembalikan)
                </p>
            </div>

            {{-- INFO --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Informasi:</strong> Halaman ini menampilkan <span class="font-bold">SEMUA</span> peminjaman (status: dipinjam dan dikembalikan). Klik tombol "Lihat Detail" untuk melihat barang-barang yang dipinjam.
                </p>
            </div>
        </div>

        {{-- LOANS TABLE --}}
        <div class="bg-white rounded-[2rem] shadow-xl p-6">
            <!-- Controls Bar -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <!-- Total Count -->
                <div class="flex items-center gap-2">
                    <i class="fas fa-history text-red-primary"></i>
                    <span class="font-black text-slate-900">
                        Total Peminjaman: <span class="text-red-primary">{{ $loans->total() }}</span> transaksi
                    </span>
                    <span class="text-sm text-slate-500">
                        (Menampilkan {{ $loans->firstItem() }}-{{ $loans->lastItem() }})
                    </span>
                </div>
                
                <!-- Search, Clear, Sort, and Hapus Terpilih -->
                <div class="flex gap-3">
                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <input type="text"
                               id="searchInput"
                               placeholder="Cari nama, email, kode peminjaman, tanggal, atau status..."
                               value="{{ request('search') }}"
                               class="w-full px-4 py-2 pr-10 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-red-primary focus:border-transparent"
                               onkeypress="handleKeyPress(event)">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" onclick="performSearchFromInput()">
                            <i class="fas fa-search text-slate-400 hover:text-red-600"></i>
                        </div>
                    </div>
                    
                    <!-- Clear Button -->
                    <button onclick="performRefresh()"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-semibold transition-colors flex items-center gap-2">
                        <i class="fas fa-sync-alt"></i>
                        Clear
                    </button>
                    
                    <!-- Sort Dropdown -->
                    <div class="relative">
                        <select id="sortSelect"
                                onchange="performSort(this.value)"
                                class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-red-primary focus:border-transparent cursor-pointer">
                            <option value="terbaru" {{ $sort == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                            <option value="terlama" {{ $sort == 'terlama' ? 'selected' : '' }}>Terlama</option>
                        </select>
                    </div>
                    
                    <!-- Bulk Delete Button -->
                    <button onclick="showBulkDeleteModal()" id="bulkDeleteBtn" disabled
                        class="px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-trash mr-1"></i> Hapus Terpilih
                    </button>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full" id="loansTable">
                    <thead>
                        <tr class="border-b-2 border-slate-200">
                            <th class="text-center py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()" 
                                    class="w-4 h-4 text-red-primary border-slate-300 rounded focus:ring-red-primary focus:ring-2">
                            </th>
                            <th class="text-center py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">NO</th>
                            <th class="text-left py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Kode Peminjaman</th>
                            <th class="text-left py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Nama</th>
                            <th class="text-left py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Tanggal Peminjaman</th>
                            <th class="text-center py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Status</th>
                            <th class="text-center py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $index => $loan)
                            <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                <td class="py-4 px-4 text-center">
                                    <input type="checkbox" class="loan-checkbox" value="{{ $loan->id }}" onchange="updateBulkDeleteButton()"
                                        class="w-4 h-4 text-red-primary border-slate-300 rounded focus:ring-red-primary focus:ring-2">
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="inline-block bg-slate-100 text-slate-700 px-3 py-1 rounded-lg font-bold text-sm">
                                        {{ ($loans->currentPage() - 1) * $loans->perPage() + $loop->index + 1 }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-block bg-slate-100 text-slate-700 px-3 py-1 rounded-lg font-bold text-sm font-mono">
                                        {{ $loan->loan_code }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-900">{{ $loan->user->name }}</div>
                                    <div class="text-sm text-slate-500 mt-1">{{ $loan->user->email }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-calendar-alt text-slate-400"></i>
                                        <span class="font-semibold text-slate-700">
                                            {{ \Carbon\Carbon::parse($loan->borrow_date)->format('d') }} {{ ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][\Carbon\Carbon::parse($loan->borrow_date)->format('n') - 1] }} {{ \Carbon\Carbon::parse($loan->borrow_date)->format('Y') }}
                                        </span>
                                    </div>
                                    @if($loan->return_date)
                                        <div class="flex items-center gap-2 mt-1">
                                            <i class="fas fa-calendar-check text-blue-500 text-xs"></i>
                                            <span class="text-xs text-slate-500">
                                                Rencana: {{ \Carbon\Carbon::parse($loan->return_date)->format('d') }} {{ ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][\Carbon\Carbon::parse($loan->return_date)->format('n') - 1] }} {{ \Carbon\Carbon::parse($loan->return_date)->format('Y') }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-center">
                                    @if($loan->status === 'dipinjam')
                                        <span class="inline-block bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider">
                                            <i class="fas fa-clock mr-1"></i>Aktif
                                        </span>
                                    @else
                                        <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider">
                                            <i class="fas fa-check-circle mr-1"></i>Dikembalikan
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex justify-center">
                                        <button onclick="showLoanDetail({{ $loan->id }})" 
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors flex items-center gap-2">
                                            <i class="fas fa-eye"></i>
                                            Lihat Detail
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-history text-4xl text-slate-300 mb-4"></i>
                                        <h3 class="text-lg font-bold text-slate-900 mb-2">Belum Ada Riwayat Peminjaman</h3>
                                        <p class="text-sm text-slate-500">Tidak ada data peminjaman</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Controls -->
            @if($loans->hasPages())
                <div class="flex justify-between items-center mt-6 pt-6 border-t border-slate-200">
                    <div class="text-sm text-slate-600">
                        Menampilkan {{ $loans->firstItem() }}-{{ $loans->lastItem() }} dari {{ $loans->total() }} peminjaman
                    </div>
                    
                    <div class="flex gap-2">
                        {{-- Previous Button --}}
                        @if($loans->onFirstPage())
                            <button class="px-4 py-2 bg-slate-100 text-slate-400 rounded-lg font-semibold cursor-not-allowed" disabled>
                                <i class="fas fa-chevron-left mr-2"></i>Sebelumnya
                            </button>
                        @else
                            <a href="{{ $loans->previousPageUrl() }}" 
                               class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg font-semibold hover:bg-slate-50 transition-colors">
                                <i class="fas fa-chevron-left mr-2"></i>Sebelumnya
                            </a>
                        @endif
                        
                        {{-- Page Numbers --}}
                        @php
                            $currentPage = $loans->currentPage();
                            $lastPage = $loans->lastPage();
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $currentPage + 2);
                        @endphp
                        
                        @if($start > 1)
                            <a href="{{ $loans->url(1) }}" 
                               class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg font-semibold hover:bg-slate-50 transition-colors">
                                1
                            </a>
                            @if($start > 2)
                                <span class="px-4 py-2 text-slate-500">...</span>
                            @endif
                        @endif
                        
                        @for($i = $start; $i <= $end; $i++)
                            @if($i == $currentPage)
                                <span class="px-4 py-2 bg-red-primary text-white rounded-lg font-semibold">
                                    {{ $i }}
                                </span>
                            @else
                                <a href="{{ $loans->url($i) }}" 
                                   class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg font-semibold hover:bg-slate-50 transition-colors">
                                    {{ $i }}
                                </a>
                            @endif
                        @endfor
                        
                        @if($end < $lastPage)
                            @if($end < $lastPage - 1)
                                <span class="px-4 py-2 text-slate-500">...</span>
                            @endif
                            <a href="{{ $loans->url($lastPage) }}" 
                               class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg font-semibold hover:bg-slate-50 transition-colors">
                                {{ $lastPage }}
                            </a>
                        @endif
                        
                        {{-- Next Button --}}
                        @if($loans->hasMorePages())
                            <a href="{{ $loans->nextPageUrl() }}" 
                               class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg font-semibold hover:bg-slate-50 transition-colors">
                                Selanjutnya<i class="fas fa-chevron-right ml-2"></i>
                            </a>
                        @else
                            <button class="px-4 py-2 bg-slate-100 text-slate-400 rounded-lg font-semibold cursor-not-allowed" disabled>
                                Selanjutnya<i class="fas fa-chevron-right ml-2"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- DETAIL MODAL --}}
<div id="loanDetailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-[2rem] p-8 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-2xl font-black text-slate-900">Detail Peminjaman</h3>
                <p class="text-sm text-slate-600 mt-1">Barang-barang yang dipinjam</p>
            </div>
            <button onclick="closeLoanDetailModal()" 
                class="w-10 h-10 bg-red-100 hover:bg-red-200 text-red-600 rounded-full flex items-center justify-center transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        {{-- USER INFO --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase mb-1">Kode Peminjaman</p>
                    <p class="font-bold text-red-600" id="detailLoanCode">-</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase mb-1">Tanggal Pinjam</p>
                    <p class="font-semibold text-slate-700" id="detailBorrowDate">-</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase mb-1">Nama Peminjam</p>
                    <p class="font-bold text-slate-900" id="detailUserName">-</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase mb-1">Email</p>
                    <p class="font-semibold text-slate-700" id="detailUserEmail">-</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase mb-1">Status</p>
                    <p class="font-semibold text-slate-700" id="detailStatus">-</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase mb-1">Tanggal Kembali</p>
                    <p class="font-semibold text-slate-700" id="detailReturnDate">-</p>
                    <p class="text-xs text-blue-600 mt-1">(Rencana pengembalian)</p>
                </div>
            </div>
        </div>
        
        {{-- ASSETS TABLE --}}
        <div class="border border-slate-200 rounded-xl overflow-hidden">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left py-3 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">No</th>
                        <th class="text-left py-3 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Kode Asset</th>
                        <th class="text-left py-3 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Nama Asset</th>
                        <th class="text-left py-3 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Kategori</th>
                        <th class="text-center py-3 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Jumlah</th>
                        <th class="text-center py-3 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Kondisi</th>
                        <th class="text-center py-3 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Status</th>
                    </tr>
                </thead>
                <tbody id="detailAssetsTable">
                    <tr>
                        <td colspan="7" class="text-center py-8 text-slate-400">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p>Memuat data...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        {{-- CLOSE BUTTON --}}
        <div class="mt-6">
            <button onclick="closeLoanDetailModal()" 
                class="w-full bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 rounded-xl transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- JAVASCRIPT --}}
<script>
// Auto-open modal if loan_id parameter exists
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const loanId = urlParams.get('loan_id');
    
    if (loanId) {
        // Auto-open modal for the newly created loan
        setTimeout(() => {
            showLoanDetail(loanId);
        }, 500);
        
        // Remove loan_id from URL without reloading
        const newUrl = new URL(window.location);
        newUrl.searchParams.delete('loan_id');
        window.history.replaceState({}, '', newUrl);
    }
});

// Search functionality - Submit on Enter key
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const sortSelect = document.getElementById('sortSelect');
    if (searchInput) {
        // Restore search value from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const searchParam = urlParams.get('search');
        if (searchParam) {
            searchInput.value = searchParam;
        }
    }
    if (sortSelect) {
        // Restore sort value from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const sortParam = urlParams.get('sort');
        if (sortParam) {
            sortSelect.value = sortParam;
        }
    }
    
    // Show success notification after bulk delete
    if (sessionStorage.getItem('bulkDeleteSuccess') === 'true') {
        const message = sessionStorage.getItem('bulkDeleteMessage') || 'Peminjaman berhasil dihapus';
        showNotification(message, 'success');
        sessionStorage.removeItem('bulkDeleteSuccess');
        sessionStorage.removeItem('bulkDeleteMessage');
    }
});

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white font-semibold`;
    notification.innerHTML = `
        <div class="flex items-center gap-3">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} text-xl"></i>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function handleKeyPress(event) {
    if (event.key === 'Enter') {
        performSearchFromInput();
    }
}

function performSearchFromInput() {
    const searchInput = document.getElementById('searchInput');
    const searchTerm = searchInput.value.trim();
    
    if (searchTerm === '') {
        // Reset to default view
        performRefresh();
    } else {
        // Submit search to server
        performSearch(searchTerm);
    }
}

function performSearch(searchTerm) {
    const url = new URL(window.location);
    
    // Remove page parameter to always start from page 1 when searching
    url.searchParams.delete('page');
    
    // Set search parameter
    url.searchParams.set('search', searchTerm);
    
    // Navigate to new URL
    window.location.href = url.toString();
}

function performRefresh() {
    const url = new URL(window.location);
    
    // Clear all parameters
    url.searchParams.delete('search');
    url.searchParams.delete('page');
    url.searchParams.delete('sort');
    
    // Reload page with clean URL
    window.location.href = url.toString();
}

function performSort(sortValue) {
    const url = new URL(window.location);
    
    // Remove page parameter to always start from page 1 when sorting
    url.searchParams.delete('page');
    
    // Set sort parameter
    url.searchParams.set('sort', sortValue);
    
    // Navigate to new URL
    window.location.href = url.toString();
}

function showLoanDetail(loanId) {
    const modal = document.getElementById('loanDetailModal');
    const assetsTable = document.getElementById('detailAssetsTable');
    
    // Show modal
    modal.classList.remove('hidden');
    
    // Reset table
    assetsTable.innerHTML = `
        <tr>
            <td colspan="5" class="text-center py-8 text-slate-400">
                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                <p>Memuat data...</p>
            </td>
        </tr>
    `;
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Fetch loan details
    fetch(`/riwayat-peminjaman/${loanId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const loan = data.data;
            
            // Update user info
            document.getElementById('detailLoanCode').textContent = loan.loan_code;
            document.getElementById('detailUserName').textContent = loan.user.name;
            document.getElementById('detailUserEmail').textContent = loan.user.email;
            document.getElementById('detailBorrowDate').textContent = formatDate(loan.borrow_date);
            document.getElementById('detailReturnDate').textContent = loan.return_date ? formatDate(loan.return_date) : 'Tidak ada rencana';
            
            // Update status
            const statusElement = document.getElementById('detailStatus');
            if (loan.status === 'dipinjam') {
                statusElement.innerHTML = '<span class="inline-block bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider"><i class="fas fa-clock mr-1"></i>Aktif</span>';
            } else {
                statusElement.innerHTML = '<span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider"><i class="fas fa-check-circle mr-1"></i>Dikembalikan</span>';
            }
            
            // Update assets table
            if (loan.assets && loan.assets.length > 0) {
                let html = '';
                loan.assets.forEach((asset, index) => {
                    // Determine condition badge color
                    let conditionBadge = '';
                    if (asset.display_condition === 'baik') {
                        conditionBadge = '<span class="inline-block bg-green-100 text-green-700 px-2 py-1 rounded-lg text-xs font-semibold uppercase">Baik</span>';
                    } else if (asset.display_condition === 'rusak') {
                        conditionBadge = '<span class="inline-block bg-yellow-100 text-yellow-700 px-2 py-1 rounded-lg text-xs font-semibold uppercase">Rusak</span>';
                    } else if (asset.display_condition === 'hilang') {
                        conditionBadge = '<span class="inline-block bg-red-100 text-red-700 px-2 py-1 rounded-lg text-xs font-semibold uppercase">Hilang</span>';
                    } else {
                        conditionBadge = '<span class="inline-block bg-slate-100 text-slate-700 px-2 py-1 rounded-lg text-xs font-semibold uppercase">-</span>';
                    }

                    // Determine status badge color
                    let statusBadge = '';
                    if (asset.return_status === 'dikembalikan') {
                        statusBadge = '<span class="inline-block bg-green-100 text-green-700 px-2 py-1 rounded-lg text-xs font-semibold uppercase">Dikembalikan</span>';
                    } else if (asset.return_status === 'tidak ditemukan') {
                        statusBadge = '<span class="inline-block bg-red-100 text-red-700 px-2 py-1 rounded-lg text-xs font-semibold uppercase">Tidak Ditemukan</span>';
                    } else {
                        statusBadge = '<span class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded-lg text-xs font-semibold uppercase">Dipinjam</span>';
                    }

                    html += `
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="py-3 px-4">
                                <span class="inline-block bg-slate-100 text-slate-700 px-2 py-1 rounded-lg font-bold text-sm">
                                    ${index + 1}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-block bg-slate-100 text-slate-700 px-2 py-1 rounded-lg font-bold text-sm">
                                    ${asset.code}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">${asset.name}</div>
                                ${asset.description ? `<div class="text-xs text-slate-500 mt-1">${asset.description}</div>` : ''}
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded-lg text-xs font-semibold">
                                    ${asset.category.name}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-lg font-bold text-sm">
                                    ${asset.pivot.quantity}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                ${conditionBadge}
                            </td>
                            <td class="py-3 px-4 text-center">
                                ${statusBadge}
                            </td>
                        </tr>
                    `;
                });
                assetsTable.innerHTML = html;
            } else {
                assetsTable.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-8 text-slate-400">
                            <i class="fas fa-inbox text-2xl mb-2"></i>
                            <p>Tidak ada asset yang dipinjam</p>
                        </td>
                    </tr>
                `;
            }
        } else {
            alert('Gagal memuat detail peminjaman');
            closeLoanDetailModal();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat data');
        closeLoanDetailModal();
    });
}

function closeLoanDetailModal() {
    document.getElementById('loanDetailModal').classList.add('hidden');
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Oct', 'Nov', 'Des'];
    return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
}

// Close modal with ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeLoanDetailModal();
    }
});
</script>

{{-- BULK DELETE MODAL --}}
<div id="bulkDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-md w-full mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-900">Hapus Peminjaman Terpilih</h3>
                <p class="text-sm text-slate-600">Aksi ini tidak dapat dibatalkan</p>
            </div>
        </div>
        
        <div class="mb-6">
            <p class="text-slate-700 mb-2">
                Apakah Anda yakin ingin menghapus <span id="selectedCount" class="font-bold text-red-600">0</span> peminjaman terpilih?
            </p>
            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                <p class="text-sm text-red-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Semua peminjaman yang dipilih akan dihapus dan status asset akan dikembalikan.
                </p>
            </div>
        </div>
        
        <div class="flex gap-3">
            <button onclick="closeBulkDeleteModal()" 
                class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 rounded-xl transition-colors">
                Batal
            </button>
            <button onclick="confirmBulkDelete()" 
                class="flex-1 bg-red-600 hover:bg-red-700 text-white font-black py-3 rounded-xl transition-all duration-300 hover:shadow-xl">
                <i class="fas fa-trash mr-2"></i> Hapus
            </button>
        </div>
    </div>
</div>

<style>
.bg-red-primary { background-color: #E11D48 !important; }
</style>

<script>
// Bulk delete functionality
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.loan-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateBulkDeleteButton();
}

function updateBulkDeleteButton() {
    const checkboxes = document.querySelectorAll('.loan-checkbox:checked');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const totalCheckboxes = document.querySelectorAll('.loan-checkbox');
    
    bulkDeleteBtn.disabled = checkboxes.length === 0;
    
    const checkedCount = checkboxes.length;
    const totalCount = totalCheckboxes.length;
    
    if (checkedCount === 0) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
    } else if (checkedCount === totalCount) {
        selectAllCheckbox.checked = true;
        selectAllCheckbox.indeterminate = false;
    } else {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = true;
    }
}

function showBulkDeleteModal() {
    const checkboxes = document.querySelectorAll('.loan-checkbox:checked');
    const selectedCount = document.getElementById('selectedCount');
    
    selectedCount.textContent = checkboxes.length;
    document.getElementById('bulkDeleteModal').style.display = 'flex';
}

function closeBulkDeleteModal() {
    document.getElementById('bulkDeleteModal').style.display = 'none';
}

function confirmBulkDelete() {
    const checkboxes = document.querySelectorAll('.loan-checkbox:checked');
    const loanIds = Array.from(checkboxes).map(cb => cb.value);
    
    if (loanIds.length === 0) {
        alert('Pilih minimal satu peminjaman untuk dihapus');
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        alert('CSRF token tidak ditemukan');
        return;
    }
    
    // Show loading on delete button
    const deleteBtn = event.target;
    const originalText = deleteBtn.innerHTML;
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menghapus...';
    deleteBtn.disabled = true;
    
    // Send delete request
    fetch('/riwayat-peminjaman/bulk-delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            loan_ids: loanIds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Store success message for post-refresh notification
            sessionStorage.setItem('bulkDeleteSuccess', 'true');
            sessionStorage.setItem('bulkDeleteMessage', data.message);
            
            closeBulkDeleteModal();
            setTimeout(() => {
                location.reload(true);
            }, 500);
        } else {
            // Restore button
            deleteBtn.innerHTML = originalText;
            deleteBtn.disabled = false;
            alert('Gagal menghapus: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Restore button
        deleteBtn.innerHTML = originalText;
        deleteBtn.disabled = false;
        alert('Terjadi kesalahan saat menghapus');
    });
}
</script>

@endsection
