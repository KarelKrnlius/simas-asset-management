@extends('layouts.app')

@section('title', 'Master Asset')

@section('content')
<div class="min-h-screen bg-slate-50 pt-8 items-start">
    <div class="container mx-auto px-4 py-8">
        {{-- HEADER, BUTTONS, AND CATATAN CONTAINER --}}
        <div class="bg-white rounded-[2rem] shadow-xl p-8 mb-8">
            {{-- HEADER --}}
            <div class="mb-6">
                <h2 class="text-3xl font-black text-slate-900 uppercase italic tracking-tighter mb-2">
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
                <button onclick="openAddCategoryModal()" 
                    class="bg-slate-900 hover:bg-slate-800 text-white font-black text-sm uppercase tracking-wider px-6 py-3 rounded-xl transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-folder-plus mr-2"></i> Tambah Kategori
                </button>
            </div>

            {{-- INSTRUCTIONAL NOTE --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Catatan:</strong> Jika kategori yang Anda butuhkan tidak tersedia, silakan tambahkan terlebih dahulu menggunakan tombol "Tambah Kategori".
                </p>
            </div>
        </div>

        {{-- ASSETS TABLE --}}
        <div class="bg-white rounded-[2rem] shadow-xl p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-slate-200">
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
                                    <span class="inline-block bg-slate-100 text-slate-700 px-3 py-1 rounded-lg font-bold text-sm">
                                        {{ $index + 1 }}
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
                                <td colspan="8" class="py-12 text-center">
                                    <div class="text-slate-400">
                                        <i class="fas fa-box-open text-4xl mb-4"></i>
                                        <p class="text-lg font-semibold">Belum ada data aset</p>
                                        <p class="text-sm mt-2">Tambahkan aset pertama menggunakan tombol di atas</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- INCLUDE MODALS --}}
@include('assets.tambah-asset')
@include('assets.tambah-kategori')
@include('assets.edit-asset')
@include('assets.delete-asset')

{{-- JAVASCRIPT DATA --}}
<script>
    // Pass existing categories to JavaScript
    window.existingCategories = @json($categories->pluck('name')->toArray());

    // Pass category highest codes for instant code generation
    window.categoryHighestCodes = @json($categoryHighestCodes);
    
    // Pass flash message if exists
    window.flashMessage = @json(session('success'));
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
    document.getElementById('deleteForm').action = '/assets/' + id;
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
    const modals = ['addAssetModal', 'addCategoryModal', 'editAssetModal', 'deleteAsset'];
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
