@extends('layouts.app')

@section('title', 'Master Asset')

@section('content')
<div class="min-h-screen pt-1 items-start">
    <div class="container mx-auto px-4 py-8">
        {{-- HEADER, BUTTONS, AND CATATAN CONTAINER --}}
        <div class="bg-white rounded-[2rem] shadow-xl p-8 mb-8">
            {{-- HEADER --}}
            <div class="mb-6">
                <h2 class="text-3xl font-black text-red-600 uppercase tracking-tighter mb-2">
                    Master Asset
                </h2>
                <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">
                    Kelola semua Asset dalam sistem
                </p>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="flex flex-col sm:flex-row gap-4 mb-6">
                <button onclick="openAddAssetModal()" 
                    class="bg-red-primary hover:bg-red-700 text-white font-black text-sm uppercase tracking-wider px-6 py-3 rounded-xl transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-plus mr-2"></i> Tambah Asset
                </button>
                                <button onclick="openCategoryListModal()" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-black text-sm uppercase tracking-wider px-6 py-3 rounded-xl transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-edit mr-2"></i> Manajemen Kategori
                </button>
            </div>

            {{-- CATATAN/KETERANGAN --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Catatan:</strong> Gunakan tombol "Manajemen Kategori" untuk melihat, mengelola, dan menambah kategori baru. Setiap Asset baru akan otomatis memiliki status "Tersedia" dan kondisi "Baik".
                </p>
            </div>

            
        </div>

        {{-- ASSETS TABLE --}}
        <div class="bg-white rounded-[2rem] shadow-xl p-6">
            <!-- Controls Bar -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <!-- Total Asset Count -->
                <div class="flex items-center gap-2">
                    <i class="fas fa-boxes text-red-primary"></i>
                    <span class="font-black text-slate-900">
                        Total Asset: <span class="text-red-primary">{{ $assets->total() }}</span> item
                    </span>
                    <span class="text-sm text-slate-500">
                        (Menampilkan {{ $assets->firstItem() }}-{{ $assets->lastItem() }})
                    </span>
                </div>
                
                <!-- Sort and Bulk Actions -->
                <div class="flex flex-col lg:flex-row gap-3">
                    <!-- Export Excel Button -->
                    <a href="{{ route('assets.export') }}" 
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold transition-colors flex items-center gap-2">
                        <i class="fas fa-file-excel"></i>
                        Export Excel
                    </a>
                    
                    <!-- Search Input -->
                    <div class="flex-1">
                        <div class="relative flex-1">
                            <input type="text"
                                   id="searchInput"
                                   placeholder="Cari kode asset..."
                                   value="{{ request('search') }}"
                                   class="w-full px-4 py-2 pr-10 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-red-primary focus:border-transparent"
                                   onkeypress="handleKeyPress(event)">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" onclick="performSearchFromInput()">
                                <i class="fas fa-search text-slate-400 hover:text-red-600"></i>
                            </div>
                        </div>
                    </div>
                    <button onclick="performRefresh()"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-semibold transition-colors flex items-center gap-2">
                        <i class="fas fa-sync-alt"></i>
                        Clear
                    </button>
                    
                    <!-- Sort Dropdown -->
                    <div class="relative">
                        <select id="sortSelect" onchange="applySorting()" 
                            class="appearance-none bg-white border border-slate-200 rounded-lg px-4 py-2 pr-8 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-red-primary focus:border-transparent">
                            <option value="default" {{ !request('sort_by') || request('sort_by') == 'code' ? 'selected' : '' }}>Default (Urut Kode)</option>
                            <option value="latest" {{ request('sort_by') == 'latest' || request('sort_by') == 'created_at' && request('order') == 'desc' ? 'selected' : '' }}>Terbaru</option>
                            <option value="oldest" {{ request('sort_by') == 'created_at' && request('order') == 'asc' ? 'selected' : '' }}>Terlama</option>
                            <option value="name_asc" {{ request('sort_by') == 'name' && request('order') == 'asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                            <option value="name_desc" {{ request('sort_by') == 'name' && request('order') == 'desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                            @if($categories->count() > 0)
                                <optgroup label="Filter Kategori">
                                    @foreach($categories as $category)
                                        <option value="category_{{ $category->id }}" 
                                            {{ request('sort_by') == 'category_' . $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-700">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                    
                    <!-- Bulk Actions -->
                    <div class="flex gap-2">
                        <button onclick="showBulkDeleteModal()" id="bulkDeleteBtn" disabled
                            class="px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-trash mr-1"></i> Hapus Terpilih
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full" id="assetsTable">
                    <thead>
                        <tr class="border-b-2 border-slate-200">
                            <th class="text-center py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()" 
                                    class="w-4 h-4 text-red-primary border-slate-300 rounded focus:ring-red-primary focus:ring-2">
                            </th>
                            <th class="text-center py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">NO</th>
                            <th class="text-left py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Kode</th>
                            <th class="text-left py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Nama</th>
                            <th class="text-left py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Kategori</th>
                            <th class="text-center py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Foto</th>
                            <th class="text-left py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Stok</th>
                            <th class="text-left py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Kondisi</th>
                            <th class="text-left py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Status</th>
                            <th class="text-center py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">QR</th>
                            <th class="text-center py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $index => $asset)
                            <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                <td class="py-4 px-4 text-center">
                                    <input type="checkbox" class="asset-checkbox" value="{{ $asset->id }}" onchange="updateBulkDeleteButton()"
                                        class="w-4 h-4 text-red-primary border-slate-300 rounded focus:ring-red-primary focus:ring-2">
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="inline-block bg-slate-100 text-slate-700 px-3 py-1 rounded-lg font-bold text-sm">
                                        {{ ($assets->currentPage() - 1) * $assets->perPage() + $loop->index + 1 }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-block bg-slate-100 text-slate-700 px-3 py-1 rounded-lg font-bold text-sm font-mono">
                                        {{ $asset->code }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-900">{{ $asset->name }}</div>
                                    @if($asset->description)
                                        <div class="text-sm text-slate-500 mt-1">{{ $asset->description }}</div>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-lg text-sm font-semibold">
                                        {{ $asset->category->name }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    @if($asset->photo)
                                        @php
                                            $photoUrl = \App\Helpers\AssetHelper::getPhotoUrl($asset->photo);
                                        @endphp
                                        @if($photoUrl)
                                            <div class="flex justify-center items-center">
                                                <button onclick="showPhotoModal('{{ $photoUrl }}', '{{ $asset->name }}')" 
                                                    class="inline-flex items-center justify-center w-10 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors shadow-md hover:shadow-lg"
                                                    title="Lihat Foto">
                                                    <i class="fas fa-image text-sm"></i>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-slate-300">-</span>
                                        @endif
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-block px-3 py-1 rounded-lg font-bold text-sm
                                        @if($asset->stock == 0) bg-red-100 text-red-700
                                        @else bg-green-100 text-green-700 @endif">
                                        {{ $asset->stock }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-block px-3 py-1 rounded-lg text-sm font-semibold
                                        @if($asset->condition == 'baik') bg-green-100 text-green-700
                                        @elseif($asset->condition == 'rusak') bg-yellow-100 text-yellow-700
                                        @else bg-red-100 text-red-700 @endif">
                                        {{ ucfirst($asset->condition) }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-block px-3 py-1 rounded-lg text-sm font-semibold
                                        @if($asset->status == 'tersedia') bg-green-100 text-green-700
                                        @elseif($asset->status == 'dipinjam') bg-blue-100 text-blue-700
                                        @elseif($asset->status == 'perlu_perbaikan') bg-yellow-100 text-yellow-700
                                        @else bg-red-100 text-red-700 @endif">
                                        {{ str_replace('_', ' ', ucfirst($asset->status)) }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <button onclick="generateQR('{{ $asset->id }}', 'https://magnifier-sinner-unsettled.ngrok-free.dev/asset/{{ $asset->code }}', '{{ $asset->code }}')" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm font-bold transition-colors"
                                            title="Generate QR Code">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex justify-center gap-2">
                                        <button onclick="editAsset({{ $asset->id }})" 
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm font-bold transition-colors"
                                            title="Edit Asset">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteAsset({{ $asset->id }})" 
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm font-bold transition-colors"
                                            title="Hapus Asset">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-12">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-inbox text-4xl text-slate-300 mb-4"></i>
                                        <h3 class="text-lg font-bold text-slate-900 mb-2">Belum Ada Data Asset</h3>
                                        <p class="text-sm text-slate-500">Mulai dengan menambah Asset pertama Anda</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Controls -->
            @if($assets->hasPages())
                <div class="flex justify-between items-center mt-6 pt-6 border-t border-slate-200">
                    <div class="text-sm text-slate-600">
                        Menampilkan {{ $assets->firstItem() }}-{{ $assets->lastItem() }} dari {{ $assets->total() }} Asset
                    </div>
                    
                    <div class="flex gap-2">
                        {{-- Previous Button --}}
                        @if($assets->onFirstPage())
                            <button class="px-4 py-2 bg-slate-100 text-slate-400 rounded-lg font-semibold cursor-not-allowed" disabled>
                                <i class="fas fa-chevron-left mr-2"></i>Sebelumnya
                            </button>
                        @else
                            <a href="{{ $assets->previousPageUrl() }}" 
                               class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg font-semibold hover:bg-slate-50 transition-colors">
                                <i class="fas fa-chevron-left mr-2"></i>Sebelumnya
                            </a>
                        @endif
                        
                        {{-- Page Numbers --}}
                        @foreach($assets->links() as $link)
                            @if($link['label'] == '...')
                                <span class="px-4 py-2 text-slate-500">...</span>
                            @elseif($link['active'])
                                <span class="px-4 py-2 bg-red-primary text-white rounded-lg font-semibold">
                                    {{ $link['label'] }}
                                </span>
                            @else
                                <a href="{{ $link['url'] }}" 
                                   class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg font-semibold hover:bg-slate-50 transition-colors">
                                    {{ $link['label'] }}
                                </a>
                            @endif
                        @endforeach
                        
                        {{-- Next Button --}}
                        @if($assets->hasMorePages())
                            <a href="{{ $assets->nextPageUrl() }}" 
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

            {{-- TOTAL INFO --}}
            <div class="mt-6 text-center text-sm text-slate-500">
                Menampilkan {{ $assets->firstItem() }} - {{ $assets->lastItem() }} dari {{ $assets->total() }} Asset
            </div>
        </div>
    </div>
</div>

{{-- INCLUDE ASSET --}}
@include('assets.tambah-asset')
@include('assets.tambah-kategori')
@include('assets.category-list')
@include('assets.edit-kategori')
@include('assets.edit-asset')
@include('assets.delete-asset')
@include('assets.foto-asset')

{{-- DELETE ASSET --}}
{{-- JAVASCRIPT DATA --}}
<script>
    // Pass existing categories to JavaScript
    window.existingCategories = @json($categories->pluck('name')->toArray());

    // Pass category highest codes for instant code generation
    window.categoryHighestCodes = @json($categoryHighestCodes);
    
    // Pass flash message if exists
    window.flashMessage = @json(session('success'));
</script>

{{-- SORTING AND BULK DELETE JAVASCRIPT --}}
<script>
// Search functionality - Submit on Enter key
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        // Restore search value from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const searchParam = urlParams.get('search');
        if (searchParam) {
            searchInput.value = searchParam;
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

function performRefresh() {
    const url = new URL(window.location);
    const currentSortBy = url.searchParams.get('sort_by');
    const currentOrder = url.searchParams.get('order');
    const currentCategoryId = url.searchParams.get('category_id');
    
    // Clear all parameters
    url.searchParams.delete('search');
    url.searchParams.delete('page');
    
    // Preserve sort by parameters
    if (currentSortBy) {
        url.searchParams.set('sort_by', currentSortBy);
    }
    if (currentOrder) {
        url.searchParams.set('order', currentOrder);
    }
    if (currentCategoryId) {
        url.searchParams.set('category_id', currentCategoryId);
    }
    
    // Reload page with clean URL
    window.location.href = url.toString();
}

// Sorting functionality
function applySorting() {
    const sortValue = document.getElementById('sortSelect').value;
    const url = new URL(window.location);
    
    // Parse sort value
    let sortBy, order, categoryId;
    switch(sortValue) {
        case 'default':
            sortBy = 'code';
            order = 'asc';
            break;
        case 'latest':
            sortBy = 'created_at';
            order = 'desc';
            break;
        case 'oldest':
            sortBy = 'created_at';
            order = 'asc';
            break;
        case 'name_asc':
            sortBy = 'name';
            order = 'asc';
            break;
        case 'name_desc':
            sortBy = 'name';
            order = 'desc';
            break;
        default:
            // Handle dynamic category sorting (category_1, category_2, etc.)
            if (sortValue.startsWith('category_')) {
                sortBy = sortValue; // Keep the full value for backend parsing
                categoryId = sortValue.replace('category_', '');
                order = 'desc';
            } else {
                sortBy = 'code';
                order = 'asc';
            }
            break;
    }
    
    // Clear existing parameters
    url.searchParams.delete('sort_by');
    url.searchParams.delete('order');
    url.searchParams.delete('category_id');
    url.searchParams.delete('page'); // Reset to page 1
    
    // Update URL parameters
    url.searchParams.set('sort_by', sortBy);
    if (order) {
        url.searchParams.set('order', order);
    }
    if (categoryId) {
        url.searchParams.set('category_id', categoryId);
    }
    
    // Reload page with new sorting (will be page 1)
    window.location.href = url.toString();
}

// Bulk delete functionality

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.asset-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateBulkDeleteButton();
}

function updateBulkDeleteButton() {
    const checkboxes = document.querySelectorAll('.asset-checkbox:checked');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const totalCheckboxes = document.querySelectorAll('.asset-checkbox');
    
    // Update button and checkbox states
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
    // Langsung panggil confirmBulkDelete (yang sudah pakai SweetAlert2)
    confirmBulkDelete();
}

function confirmBulkDelete() {
    const checkboxes = document.querySelectorAll('.asset-checkbox:checked');
    const assetIds = Array.from(checkboxes).map(cb => cb.value);
    
    if (assetIds.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak Ada Asset Dipilih',
            text: 'Pilih minimal satu asset untuk dihapus',
            confirmButtonColor: '#E11D48',
            customClass: {
                popup: 'animated-shake'
            }
        });
        return;
    }
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'CSRF token tidak ditemukan',
            confirmButtonColor: '#E11D48',
            customClass: {
                popup: 'animated-shake'
            }
        });
        return;
    }
    
    // Show confirmation with SweetAlert2
    Swal.fire({
        title: 'Hapus Asset?',
        html: `Apakah Anda yakin ingin menghapus <strong>${assetIds.length}</strong> asset yang dipilih?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#E11D48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            // Store current sorting parameters before deletion
            const currentUrl = new URL(window.location);
            sessionStorage.setItem('currentSort', currentUrl.searchParams.toString());
            
            // Create form data for bulk delete
            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('asset_ids', JSON.stringify(assetIds));
            
            // Send bulk delete request
            return fetch('/assets/bulk-delete', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Bulk delete response:', data); // Debug log
                if (!data.success) {
                    // Return error object instead of throwing
                    return { error: true, message: data.message || 'Unknown error' };
                }
                return data;
            })
            .catch(error => {
                console.log('Bulk delete error:', error); // Debug log
                // Return error object instead of showing validation message
                return { error: true, message: error.message };
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            // Check if there's an error in the result
            if (result.value && result.value.error) {
                // Show error dialog with X icon
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    html: result.value.message || 'Beberapa asset tidak bisa dihapus',
                    confirmButtonColor: '#E11D48',
                    confirmButtonText: 'OK'
                });
            } else if (result.value && result.value.success) {
                // Show success and reload
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.value.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload(true);
                });
            }
        }
    });
}

// Check for bulk delete success on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check for open_modal parameter to auto-open add asset modal
    const urlParams = new URLSearchParams(window.location.search);
    const openModal = urlParams.get('open_modal');
    
    if (openModal === 'add') {
        // Remove the parameter from URL
        const cleanUrl = window.location.pathname;
        window.history.replaceState({}, document.title, cleanUrl);
        
        // Open add asset modal
        setTimeout(() => {
            openAddAssetModal();
        }, 500);
    }
    
    // Restore sorting state if exists
    const currentSort = sessionStorage.getItem('currentSort');
    if (currentSort && !window.location.search) {
        // If current page has no search params but we have saved sort, redirect to restore
        const currentUrl = new URL(window.location);
        currentUrl.search = currentSort;
        window.location.replace(currentUrl.toString());
        return;
    }
    
    // Clear sort state if we're already on the correct URL
    if (window.location.search) {
        sessionStorage.removeItem('currentSort');
    }
    
    // Initialize bulk delete button state
    updateBulkDeleteButton();
});
</script>

{{-- ADVANCED JAVASCRIPT FOR MODULAR BLADE SYSTEM --}}
<script>
// Data Integration - Use variables from Controller
const categoryHighestCodes = @json($categoryHighestCodes);
const categories = @json($categories);

// Modal Functions
function openAddAssetModal() {
    document.getElementById('addAssetModal').style.display = 'flex';
}

function closeAddAssetModal() {
    document.getElementById('addAssetModal').style.display = 'none';
    const form = document.getElementById('addAssetForm');
    if (form) {
        form.reset();
        // Reset submission state
        form.dataset.isSubmitting = 'false';
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.innerHTML = 'Simpan Asset';
        }
        // Reset code preview
        const codePreview = document.getElementById('assetCodePreview');
        if (codePreview) {
            codePreview.value = 'Pilih kategori untuk melihat kode';
            codePreview.className = 'w-full px-4 py-3 bg-slate-100 border-2 border-slate-200 rounded-xl font-bold text-slate-500';
        }
    }
}

function openAddCategoryModal() {
    document.getElementById('addCategoryModal').style.display = 'flex';
}

function closeAddCategoryModal() {
    document.getElementById('addCategoryModal').style.display = 'none';
    const form = document.getElementById('form-tambah-kategori');
    if (form) form.reset();
}

function editAsset(id) {
    // Fetch asset data for edit modal
    fetch(`/assets/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const asset = data.data;
                const photoUrl = data.photo_url;
                
                document.getElementById('editAssetId').value = asset.id;
                document.getElementById('editCategory').value = asset.category_id;
                document.getElementById('editCategoryHidden').value = asset.category_id;
                document.getElementById('editName').value = asset.name;
                document.getElementById('editCode').value = asset.code;
                document.getElementById('editDescription').value = asset.description || '';
                document.getElementById('editStock').value = asset.stock;
                document.getElementById('editCondition').value = asset.condition;
                document.getElementById('editStatus').value = asset.status;
                
                // Handle current photo - support both local and RustFS
                const currentPhotoContainer = document.getElementById('editCurrentPhotoContainer');
                const currentPhotoPreview = document.getElementById('editCurrentPhotoPreview');
                const currentPhotoInput = document.getElementById('editCurrentPhoto');
                const photoButtonText = document.getElementById('editPhotoButtonText');
                
                if (photoUrl) {
                    currentPhotoPreview.src = photoUrl;
                    currentPhotoContainer.classList.remove('hidden');
                    currentPhotoInput.value = asset.photo;
                    photoButtonText.textContent = 'Pilih Foto Baru';
                } else {
                    currentPhotoContainer.classList.add('hidden');
                    currentPhotoPreview.src = '';
                    currentPhotoInput.value = '';
                    photoButtonText.textContent = 'Pilih File';
                }
                
                // Reset new photo preview
                document.getElementById('editPhotoPreviewContainer').classList.add('hidden');
                document.getElementById('editPhotoPreview').src = '';
                document.getElementById('editAssetPhoto').value = '';
                
                document.getElementById('editAssetModal').style.display = 'flex';
            }
        });
}

function closeEditAssetModal() {
    document.getElementById('editAssetModal').style.display = 'none';
}

function deleteAsset(id) {
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!csrfToken) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'CSRF token tidak ditemukan',
            confirmButtonColor: '#E11D48',
            customClass: {
                popup: 'animated-shake'
            }
        });
        return;
    }
    
    // Show confirmation with SweetAlert2
    Swal.fire({
        title: 'Hapus Asset?',
        html: 'Apakah Anda yakin ingin menghapus asset ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#E11D48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            // Store current sorting parameters before deletion
            const currentUrl = new URL(window.location);
            sessionStorage.setItem('currentSort', currentUrl.searchParams.toString());
            
            // Send delete request
            return fetch(`/assets/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Individual delete response:', data); // Debug log
                if (!data.success) {
                    // Return error object instead of throwing
                    return { error: true, message: data.message || 'Unknown error' };
                }
                return data;
            })
            .catch(error => {
                console.log('Individual delete error:', error); // Debug log
                // Return error object instead of showing validation message
                return { error: true, message: error.message };
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            // Check if there's an error in the result
            if (result.value && result.value.error) {
                // Show error dialog with X icon
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    html: result.value.message || 'Asset ini tidak bisa dihapus',
                    confirmButtonColor: '#E11D48',
                    confirmButtonText: 'OK'
                });
            } else if (result.value && result.value.success) {
                // Show success and reload
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.value.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload(true);
                });
            }
        }
    });
}

function closeDeleteModal() {
    document.getElementById('deleteAsset').style.display = 'none';
}

// Real-time category name validation
function validateCategoryName(categoryName) {
    const categoryInput = document.getElementById('categoryName');
    const categoryError = document.getElementById('categoryError');
    
    if (!categoryName || !categoryInput || !categoryError) return;
    
    // Remove all spaces and convert to lowercase for case-insensitive and space-insensitive comparison
    const normalizedName = categoryName.replace(/\s+/g, '').toLowerCase();
    
    // Check against existing categories (also remove spaces)
    const existingCategories = window.existingCategories || [];
    const isDuplicate = existingCategories.some(cat => 
        cat.replace(/\s+/g, '').toLowerCase() === normalizedName
    );
    
    if (isDuplicate && normalizedName !== '') {
        // Show error
        categoryInput.classList.remove('border-slate-200');
        categoryInput.classList.add('border-red-600');
        categoryError.textContent = 'Kategori ini sudah terdaftar!';
        categoryError.classList.remove('hidden');
    } else {
        // Hide error
        categoryInput.classList.remove('border-red-600');
        categoryInput.classList.add('border-slate-200');
        categoryError.classList.add('hidden');
    }
}

// Success Notification Function
function showSuccessNotification(message) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 24px;
        background: #10b981;
        color: white;
        border-radius: 12px;
        font-weight: 600;
        z-index: 9999;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto disappear after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// AJAX Submission for #form-tambah-asset with strong spam protection
function handleAssetFormSubmit(e) {
    const form = e.target;
    const submitButton = form.querySelector('button[type="submit"]');
    const formData = new FormData(form);
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    
    // Strong spam protection - check if already submitting
    if (form.dataset.isSubmitting === 'true') {
        console.log('Form already submitting - ignoring spam click');
        return;
    }
    
    // Mark as submitting immediately
    form.dataset.isSubmitting = 'true';
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
    
    fetch('/assets', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : '',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success notification
            showSuccessNotification('Berhasil menambahkan Asset!');
            
            // Close modal immediately
            closeAddAssetModal();
            
            // Fast refresh without delay
            setTimeout(() => {
                location.reload();
            }, 500);
        } else {
            alert('Gagal: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    })
    .finally(() => {
        // Always reset form state
        form.dataset.isSubmitting = 'false';
        submitButton.disabled = false;
        submitButton.innerHTML = 'Simpan Aset';
    });
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Hide all modals on load
    const modals = ['addAssetModal', 'addCategoryModal', 'categoryListModal', 'editCategoryModal', 'editAssetModal', 'deleteAsset', 'deleteCategoryConfirmModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) modal.style.display = 'none';
    });
    
    // Event Delegation Logic - Global event listener for modal inputs
    document.addEventListener('input', function(e) {
        // Only listen for input changes on #addStock (not #addCategory)
        if (e.target.id === 'addStock') {
            // Code is now auto-generated, no preview needed
        }
        
        // Real-time validation for category name
        if (e.target.id === 'categoryName') {
            validateCategoryName(e.target.value);
        }
    });
    
    // Event Delegation for form submission
    document.addEventListener('submit', function(e) {
        // Handle form submission for #addAssetForm
        if (e.target.id === 'addAssetForm') {
            e.preventDefault();
            handleAssetFormSubmit(e);
        }
    });
    
    // Handle category form submission with spam protection
    document.addEventListener('submit', function(e) {
        if (e.target.id === 'addCategoryForm') {
            e.preventDefault();
            
            const form = e.target;
            const submitButton = form.querySelector('button[type="submit"]');
            const formData = new FormData(form);
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const categoryInput = document.getElementById('categoryName');
            const categoryError = document.getElementById('categoryError');
            
            // Strong spam protection - check if already submitting
            if (form.dataset.isSubmitting === 'true') {
                console.log('Category form already submitting - ignoring spam click');
                return;
            }
            
            // Mark as submitting immediately
            form.dataset.isSubmitting = 'true';
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
            
            // Reset error state
            categoryInput.classList.remove('border-red-600');
            categoryInput.classList.add('border-slate-200');
            categoryError.classList.add('hidden');
            
            fetch('/categories', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : '',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessNotification('Kategori berhasil ditambahkan!');
                    closeAddCategoryModal();
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                } else {
                    alert('Gagal: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            })
            .finally(() => {
                // Always reset form state
                form.dataset.isSubmitting = 'false';
                submitButton.disabled = false;
                submitButton.innerHTML = 'Simpan Kategori';
            });
        }
    });
    
    // Handle edit form submission
    document.addEventListener('submit', function(e) {
        if (e.target.id === 'editAssetForm') {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const assetId = formData.get('asset_id');
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            
            fetch(`/assets/${assetId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : '',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessNotification('Berhasil');
                    closeEditAssetModal();
                    location.reload();
                } else {
                    alert('Gagal: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            });
        }
    });
    
    // Handle delete form submission
    document.addEventListener('submit', function(e) {
        if (e.target.id === 'deleteForm') {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const url = e.target.action;
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : '',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessNotification('Berhasil');
                    closeDeleteModal();
                    location.reload();
                } else {
                    alert('Gagal: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            });
        }
    });
});

// QR Code Generator Function
function generateQR(assetId, qrUrl, assetCode) {
    // Create modal if not exists
    if (!document.getElementById('qrModal')) {
        const modal = document.createElement('div');
        modal.id = 'qrModal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center';
        modal.innerHTML = `
            <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-black text-slate-900 uppercase">QR Code Asset</h3>
                    <button onclick="closeQRModal()" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="text-center">
                    <div id="qrCodeContainer" class="inline-block bg-white p-4 rounded-xl border-2 border-red-600 mb-4"></div>
                    <div class="mb-4">
                        <p class="text-sm font-black text-slate-600 mb-1">Kode Asset:</p>
                        <p class="text-xs text-slate-400 mb-2" id="qrCodeText"></p>
                        <p class="text-sm font-black text-slate-600 mb-1">Link URL:</p>
                        <p class="text-xs text-blue-500 break-all" id="qrUrlText"></p>
                    </div>
                    <button onclick="downloadQR()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-colors duration-200">
                        <i class="fas fa-download mr-2"></i>Download QR
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }

    // Show modal
    document.getElementById('qrModal').classList.remove('hidden');
    document.getElementById('qrModal').classList.add('flex');

    // Generate QR code with full URL
    document.getElementById('qrCodeText').textContent = assetCode;
    document.getElementById('qrUrlText').textContent = qrUrl;
    document.getElementById('qrCodeContainer').innerHTML = '';

    // Load QRCode library if not loaded
    if (typeof QRCode === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js';
        script.onload = function() {
            generateQRCode(qrUrl);
        };
        document.head.appendChild(script);
    } else {
        generateQRCode(qrUrl);
    }

    // Store current asset code for download filename
    window.currentQRCode = assetCode;
}

function generateQRCode(qrText) {
    try {
        new QRCode(document.getElementById('qrCodeContainer'), {
            text: qrText,
            width: 200,
            height: 200,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    } catch (error) {
        console.error('Error generating QR code:', error);
        document.getElementById('qrCodeContainer').innerHTML = '<p class="text-red-500">Gagal generate QR code</p>';
    }
}

function closeQRModal() {
    const modal = document.getElementById('qrModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function downloadQR() {
    const canvas = document.querySelector('#qrCodeContainer canvas');
    if (canvas && window.currentQRCode) {
        const link = document.createElement('a');
        link.download = `${window.currentQRCode}.png`;
        link.href = canvas.toDataURL();
        link.click();
    }
}
</script>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- CSS for Shake Animation --}}
<style>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
    20%, 40%, 60%, 80% { transform: translateX(10px); }
}

.animated-shake {
    animation: shake 0.5s;
}

/* Custom SweetAlert2 styling for error */
.swal2-icon.swal2-error {
    border-color: #dc2626 !important;
    color: #dc2626 !important;
}

.swal2-icon.swal2-error [class^='swal2-x-mark-line'] {
    background-color: #dc2626 !important;
}
</style>

@endsection
            