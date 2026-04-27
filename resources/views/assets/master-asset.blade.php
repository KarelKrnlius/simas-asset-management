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
                    Kelola semua aset dalam sistem
                </p>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="flex flex-col sm:flex-row gap-4 mb-6">
                <button onclick="openAddAssetModal()" 
                    class="bg-red-primary hover:bg-red-700 text-white font-black text-sm uppercase tracking-wider px-6 py-3 rounded-xl transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-plus mr-2"></i> Tambah Aset
                </button>
                                <button onclick="openCategoryListModal()" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-black text-sm uppercase tracking-wider px-6 py-3 rounded-xl transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-edit mr-2"></i> Edit Kategori
                </button>
            </div>

            {{-- CATATAN/KETERANGAN --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Catatan:</strong> Gunakan tombol "Edit Kategori" untuk melihat, mengelola, dan menambah kategori baru. Setiap aset baru akan otomatis memiliki status "Tersedia" dan kondisi "Baik".
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
                        Total Aset: <span class="text-red-primary">{{ $assets->total() }}</span> item
                    </span>
                    <span class="text-sm text-slate-500">
                        (Menampilkan {{ $assets->firstItem() }}-{{ $assets->lastItem() }})
                    </span>
                </div>
                
                <!-- Sort and Bulk Actions -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Sort Dropdown -->
                    <div class="relative">
                        <label class="text-sm font-semibold text-slate-700 mb-1 block text-center">Sort by</label>
                        <select id="sortSelect" onchange="applySorting()" 
                            class="appearance-none bg-white border border-slate-200 rounded-lg px-4 py-2 pr-8 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-red-primary focus:border-transparent">
                            <option value="latest" {{ request('sort_by') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="oldest" {{ request('sort_by') == 'created_at' && request('order') == 'asc' ? 'selected' : '' }}>Terlama</option>
                            @if($categories->count() > 0)
                                <optgroup label="kategori">
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
                <table class="w-full">
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
                            <th class="text-left py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Stok</th>
                            <th class="text-left py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Kondisi</th>
                            <th class="text-left py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">Status</th>
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
                                    <span class="inline-block bg-slate-100 text-slate-700 px-3 py-1 rounded-lg font-bold text-sm">
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
                                <td class="py-4 px-4">
                                    <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-lg font-bold text-sm">
                                        {{ $asset->stock }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-block px-3 py-1 rounded-lg text-sm font-semibold
                                        @if($asset->condition == 'baik') bg-green-100 text-green-700
                                        @elseif($asset->condition == 'cukup') bg-yellow-100 text-yellow-700
                                        @else bg-red-100 text-red-700 @endif">
                                        {{ ucfirst($asset->condition) }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-block px-3 py-1 rounded-lg text-sm font-semibold
                                        @if($asset->status == 'tersedia') bg-green-100 text-green-700
                                        @elseif($asset->status == 'dipinjam') bg-blue-100 text-blue-700
                                        @else bg-orange-100 text-orange-700 @endif">
                                        {{ ucfirst($asset->status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex justify-center gap-2">
                                        <button onclick="editAsset({{ $asset->id }})" 
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm font-bold transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteAsset({{ $asset->id }})" 
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm font-bold transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-12">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-inbox text-4xl text-slate-300 mb-4"></i>
                                        <h3 class="text-lg font-bold text-slate-900 mb-2">Belum Ada Data Aset</h3>
                                        <p class="text-sm text-slate-500">Mulai dengan menambah aset pertama Anda</p>
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
                        Menampilkan {{ $assets->firstItem() }}-{{ $assets->lastItem() }} dari {{ $assets->total() }} aset
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
                        @if($assets->onLastPage())
                            <button class="px-4 py-2 bg-slate-100 text-slate-400 rounded-lg font-semibold cursor-not-allowed" disabled>
                                Selanjutnya<i class="fas fa-chevron-right ml-2"></i>
                            </button>
                        @else
                            <a href="{{ $assets->nextPageUrl() }}" 
                               class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg font-semibold hover:bg-slate-50 transition-colors">
                                Selanjutnya<i class="fas fa-chevron-right ml-2"></i>
                            </a>
                        @endif
                    </div>
                    
                                    </div>
            @endif
        </div>
    </div>
</div>

{{-- INCLUDE MODALS --}}
@include('assets.tambah-asset')
@include('assets.tambah-kategori')
@include('assets.category-list')
@include('assets.edit-kategori')
@include('assets.edit-asset')
@include('assets.delete-asset')

{{-- BULK DELETE MODAL --}}
<div id="bulkDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-md w-full mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-900">Hapus Aset Terpilih</h3>
                <p class="text-sm text-slate-600">Aksi ini tidak dapat dibatalkan</p>
            </div>
        </div>
        
        <div class="mb-6">
            <p class="text-slate-700 mb-2">
                Apakah Anda yakin ingin menghapus <span id="selectedCount" class="font-bold text-red-600">0</span> aset terpilih?
            </p>
            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                <p class="text-sm text-red-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Semua aset yang dipilih akan dihapus secara permanen dari sistem.
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
                <i class="fas fa-trash mr-2"></i> Hapus Aset
            </button>
        </div>
    </div>
</div>

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
// Sorting functionality
function applySorting() {
    const sortValue = document.getElementById('sortSelect').value;
    const url = new URL(window.location);
    
    // Parse sort value
    let sortBy, order, categoryId;
    switch(sortValue) {
        case 'latest':
            sortBy = 'latest';
            order = 'desc';
            break;
        case 'oldest':
            sortBy = 'created_at';
            order = 'asc';
            break;
        default:
            // Handle dynamic category sorting (category_1, category_2, etc.)
            if (sortValue.startsWith('category_')) {
                sortBy = sortValue; // Keep the full value for backend parsing
                categoryId = sortValue.replace('category_', '');
                order = 'desc';
            } else {
                sortBy = 'latest';
                order = 'desc';
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
    
    // Update button state
    bulkDeleteBtn.disabled = checkboxes.length === 0;
    
    // Update select all checkbox state
    if (checkboxes.length === 0) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
    } else if (checkboxes.length === totalCheckboxes.length) {
        selectAllCheckbox.checked = true;
        selectAllCheckbox.indeterminate = false;
    } else {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = true;
    }
}

function showBulkDeleteModal() {
    const checkboxes = document.querySelectorAll('.asset-checkbox:checked');
    const selectedCount = document.getElementById('selectedCount');
    
    selectedCount.textContent = checkboxes.length;
    document.getElementById('bulkDeleteModal').style.display = 'flex';
}

function closeBulkDeleteModal() {
    document.getElementById('bulkDeleteModal').style.display = 'none';
}

function confirmBulkDelete() {
    const checkboxes = document.querySelectorAll('.asset-checkbox:checked');
    const assetIds = Array.from(checkboxes).map(cb => cb.value);
    
    if (assetIds.length === 0) {
        alert('Pilih minimal satu aset untuk dihapus');
        return;
    }
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        alert('CSRF token tidak ditemukan');
        return;
    }
    
    // Store current sorting parameters before deletion
    const currentUrl = new URL(window.location);
    sessionStorage.setItem('currentSort', currentUrl.searchParams.toString());
    
    // Create form data for bulk delete
    const formData = new FormData();
    formData.append('_token', csrfToken);
    formData.append('asset_ids', JSON.stringify(assetIds));
    
    // Show loading
    const deleteBtn = event.target;
    const originalText = deleteBtn.innerHTML;
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menghapus...';
    deleteBtn.disabled = true;
    
    // Send bulk delete request
    fetch('/assets/bulk-delete', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Store success message for post-refresh notification
            sessionStorage.setItem('bulkDeleteSuccess', 'true');
            sessionStorage.setItem('bulkDeleteMessage', data.message);
            
            // Close modal and hard refresh
            closeBulkDeleteModal();
            
            setTimeout(() => {
                location.reload(true);
            }, 500);
        } else {
            // Restore button
            deleteBtn.innerHTML = originalText;
            deleteBtn.disabled = false;
            
            alert('Gagal menghapus aset: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error deleting assets:', error);
        
        // Restore button
        deleteBtn.innerHTML = originalText;
        deleteBtn.disabled = false;
        
        alert('Terjadi kesalahan saat menghapus aset: ' + error.message);
    });
}

// Check for bulk delete success on page load
document.addEventListener('DOMContentLoaded', function() {
    const bulkDeleteSuccess = sessionStorage.getItem('bulkDeleteSuccess');
    const bulkDeleteMessage = sessionStorage.getItem('bulkDeleteMessage');
    
    if (bulkDeleteSuccess === 'true' && bulkDeleteMessage) {
        // Create and show notification
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-emerald-500 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-2 z-50';
        notification.style.cssText = `
            position: fixed;
            top: 1rem;
            right: 1rem;
            background-color: #10b981;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            z-index: 9999;
            transform: translateX(100%);
            transition: transform 0.3s ease-out;
        `;
        
        notification.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <path d="m22 4-10 10.01L7 9.01"/>
            </svg>
            <span class="font-medium">${bulkDeleteMessage}</span>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
        
        // Clear sessionStorage
        sessionStorage.removeItem('bulkDeleteSuccess');
        sessionStorage.removeItem('bulkDeleteMessage');
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
    const form = document.getElementById('form-tambah-asset');
    if (form) form.reset();
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
                document.getElementById('editAssetId').value = asset.id;
                document.getElementById('editCategory').value = asset.category_id;
                document.getElementById('editName').value = asset.name;
                document.getElementById('editCode').value = asset.code;
                document.getElementById('editDescription').value = asset.description || '';
                document.getElementById('editStock').value = asset.stock;
                document.getElementById('editCondition').value = asset.condition;
                document.getElementById('editStatus').value = asset.status;
                document.getElementById('editAssetModal').style.display = 'flex';
            }
        });
}

function closeEditAssetModal() {
    document.getElementById('editAssetModal').style.display = 'none';
}

function deleteAsset(id) {
    document.getElementById('deleteAsset').style.display = 'flex';
    
    // Preserve current sorting parameters in the form action
    const currentUrl = new URL(window.location);
    const sortParams = currentUrl.searchParams.toString();
    
    // Set form action with preserved parameters
    const baseUrl = '/assets/' + id;
    const fullUrl = sortParams ? baseUrl + '?' + sortParams : baseUrl;
    document.getElementById('deleteForm').action = fullUrl;
}

function closeDeleteModal() {
    document.getElementById('deleteAsset').style.display = 'none';
}

// Instant Preview Function
function updateCodePreview() {
    const categorySelect = document.getElementById('addCategory');
    const stockInput = document.getElementById('addStock');
    const codeInput = document.getElementById('assetCodePreview');
    
    if (!categorySelect || !stockInput || !codeInput) return;
    
    const categoryId = parseInt(categorySelect.value);
    const stock = parseInt(stockInput.value) || 1;
    
    if (!categoryId || stock < 1) {
        codeInput.value = '';
        return;
    }
    
    // Find category name
    const category = categories.find(cat => cat.id === categoryId);
    if (!category) return;
    
    // Generate category prefix (first 4 letters, uppercase)
    const categoryPrefix = category.name.toUpperCase().substring(0, 4);
    
    // Start Number = categoryHighestCodes[selected_id] + 1
    const startNumber = (categoryHighestCodes[categoryId] || 0) + 1;
    
    // Instant Preview Formula
    if (stock === 1) {
        codeInput.value = categoryPrefix + startNumber;
    } else {
        codeInput.value = categoryPrefix + startNumber + '-' + (startNumber + stock - 1);
    }
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

// AJAX Submission for #form-tambah-asset
function handleAssetFormSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    
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
            showSuccessNotification('Berhasil');
            
            // Close modal
            closeAddAssetModal();
            
            // Instant refresh
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
        // Listen for input changes on #addCategory and #addStock
        if (e.target.id === 'addCategory' || e.target.id === 'addStock') {
            updateCodePreview();
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
            handleAssetFormSubmit(e);
        }
    });
    
    // Handle category form submission
    document.addEventListener('submit', function(e) {
        if (e.target.id === 'addCategoryForm') {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const categoryInput = document.getElementById('categoryName');
            const categoryError = document.getElementById('categoryError');
            
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
                    showSuccessNotification('Berhasil');
                    closeAddCategoryModal();
                    location.reload();
                } else {
                    // Show error message with SIMAS theme
                    if (data.message === 'Kategori sudah ada!') {
                        categoryInput.classList.remove('border-slate-200');
                        categoryInput.classList.add('border-red-600');
                        categoryError.textContent = 'Kategori ini sudah terdaftar!';
                        categoryError.classList.remove('hidden');
                        categoryInput.focus();
                    } else {
                        alert('Gagal: ' + (data.message || 'Unknown error'));
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
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
</script>
@endsection
