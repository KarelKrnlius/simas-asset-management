@extends('layouts.app')

@section('title', 'Pengecek Peminjaman')

@section('content')
<div class="min-h-screen pt-1 items-start w-full">
    <div class="container mx-auto px-4 py-8">
        {{-- HEADER --}}
        <div class="bg-white rounded-[2rem] shadow-xl p-8 mb-8">
            <div class="mb-6">
                <h2 class="text-3xl font-black text-red-600 uppercase tracking-tighter mb-2">
                    Pengecek Peminjaman
                </h2>
                <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">
                    Cek dan monitor semua peminjaman asset
                </p>
            </div>

            {{-- INFO --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Informasi:</strong> Halaman ini menampilkan peminjaman yang <span class="font-bold">sedang aktif</span> (status: dipinjam). Klik tombol "Lihat Detail" untuk melihat barang-barang yang dipinjam. Peminjaman yang sudah dikembalikan tidak akan muncul di list ini.
                </p>
            </div>
        </div>

        {{-- LOANS TABLE --}}
        <div class="bg-white rounded-[2rem] shadow-xl p-6">
            <!-- Controls Bar -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <!-- Total Count -->
                <div class="flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-red-primary"></i>
                    <span class="font-black text-slate-900">
                        Peminjaman Aktif: <span class="text-red-primary">{{ $loans->total() }}</span> transaksi
                    </span>
                    <span class="text-sm text-slate-500">
                        (Menampilkan {{ $loans->firstItem() }}-{{ $loans->lastItem() }})
                    </span>
                </div>
                
                <!-- Search and Clear -->
                <div class="flex gap-3">
                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <input type="text"
                               id="searchInput"
                               placeholder="Cari nama atau email..."
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
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full" id="loansTable">
                    <thead>
                        <tr class="border-b-2 border-slate-200">
                            <th class="text-center py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">NO</th>
                            <th class="text-left py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Kode Peminjaman</th>
                            <th class="text-left py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Nama</th>
                            <th class="text-left py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Tanggal Peminjaman</th>
                            <th class="text-center py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $index => $loan)
                            <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
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
                                <td colspan="5" class="text-center py-12">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-inbox text-4xl text-slate-300 mb-4"></i>
                                        <h3 class="text-lg font-bold text-slate-900 mb-2">Belum Ada Peminjaman Aktif</h3>
                                        <p class="text-sm text-slate-500">Tidak ada peminjaman yang sedang berjalan</p>
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
                    <p class="text-xs font-bold text-slate-500 uppercase mb-1">Nama Peminjam</p>
                    <p class="font-bold text-slate-900" id="detailUserName">-</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase mb-1">Email</p>
                    <p class="font-semibold text-slate-700" id="detailUserEmail">-</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase mb-1">Tanggal Pinjam</p>
                    <p class="font-semibold text-slate-700" id="detailBorrowDate">-</p>
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
                    </tr>
                </thead>
                <tbody id="detailAssetsTable">
                    <tr>
                        <td colspan="5" class="text-center py-8 text-slate-400">
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
});

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

function performSort(sortValue) {
    const url = new URL(window.location);
    
    // Remove page parameter to always start from page 1 when sorting
    url.searchParams.delete('page');
    
    // Set sort parameter
    url.searchParams.set('sort', sortValue);
    
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
    fetch(`/pengecek-peminjaman/${loanId}`, {
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
            
            // Update assets table
            if (loan.assets && loan.assets.length > 0) {
                let html = '';
                loan.assets.forEach((asset, index) => {
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
                        </tr>
                    `;
                });
                assetsTable.innerHTML = html;
            } else {
                assetsTable.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-8 text-slate-400">
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

<style>
.bg-red-primary { background-color: #E11D48 !important; }
</style>

@endsection
