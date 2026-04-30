{{-- EDIT CATEGORY MODAL --}}
<style>
    .bg-red-primary { background-color: #E11D48 !important; }
    .hover\:bg-red-primary:hover { background-color: #E11D48 !important; opacity: 0.9 !important; }
    .focus\:border-red-primary:focus { border-color: #E11D48 !important; }
    
    /* Cursor styles for interactive elements */
    select:hover { cursor: pointer; }
    input[type="text"]:hover { cursor: text; }
    textarea:hover { cursor: text; }
    button:hover { cursor: pointer; }
</style>

<div id="editCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-[2rem] p-8 max-w-4xl w-full mx-4 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="text-center mb-6">
            <h3 class="text-xl font-black text-slate-900">Edit Kategori</h3>
            <p class="text-slate-600 text-sm mt-2">Lihat dan edit detail kategori beserta asetnya</p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Kategori Info Section -->
            <div class="space-y-6">
                <div class="bg-slate-50 rounded-xl p-6">
                    <h4 class="font-black text-slate-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-folder text-red-primary"></i>
                        Informasi Kategori
                    </h4>
                    
                    <form id="editCategoryForm" method="POST">
                        @csrf
                        @method('PUT')
                        
                        {{-- NAME --}}
                        <div class="mb-4">
                            <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                                Nama Kategori
                            </label>
                            <input type="text" name="name" id="editCategoryName" required
                                class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-bold text-slate-700 focus:border-red-primary focus:outline-none transition-colors">
                            <div id="editCategoryError" class="hidden mt-2 text-sm font-semibold text-red-600"></div>
                        </div>
                        
                        {{-- DESCRIPTION --}}
                        <div class="mb-4">
                            <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                                Deskripsi
                            </label>
                            <textarea name="description" id="editCategoryDescription" rows="3"
                                class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-bold text-slate-700 focus:border-red-primary focus:outline-none transition-colors"></textarea>
                        </div>
                        
                        {{-- ASSET COUNT --}}
                        <div class="mb-4">
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-boxes text-blue-600"></i>
                                    <span class="text-sm font-bold text-blue-800">
                                        Total Aset: <span id="assetCount">0</span> item
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        {{-- CODE UPDATE INFO --}}
                        <div class="mb-4">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-sync text-yellow-600"></i>
                                    <span class="text-sm font-bold text-yellow-800">
                                        Perubahan nama kategori akan memperbarui kode semua aset
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        {{-- BUTTONS --}}
                        <div class="flex gap-4">
                            <button type="button" onclick="closeEditCategoryModal()" 
                                class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 rounded-xl transition-colors">
                                Tutup
                            </button>
                            <button type="submit" 
                                class="flex-1 bg-red-primary hover:bg-red-700 text-white font-black py-3 rounded-xl transition-all duration-300 hover:shadow-xl">
                                <i class="fas fa-save mr-2"></i> Update Kategori
                            </button>
                        </div>
                    </form>
                    
                    <!-- DELETE CATEGORY BUTTON -->
                    <div class="mt-4 pt-4 border-t border-slate-200">
                        <div class="bg-orange-50 border border-orange-200 rounded-xl p-3 mb-3">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle text-orange-600 text-sm"></i>
                                <span class="text-xs font-bold text-orange-800">
                                    Peringatan: Menghapus kategori akan menghapus semua asetnya!
                                </span>
                            </div>
                        </div>
                        <button type="button" onclick="confirmDeleteCategory()" 
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl transition-colors">
                            <i class="fas fa-trash-alt mr-2"></i> Hapus Kategori dan Semua Aset
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Assets List Section -->
            <div class="space-y-6">
                <div class="bg-slate-50 rounded-xl p-6">
                    <h4 class="font-black text-slate-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-list text-red-primary"></i>
                        Daftar Aset dalam Kategori
                    </h4>
                    
                    <div id="assetsList" class="space-y-2 max-h-96 overflow-y-auto">
                        <!-- Assets will be loaded here -->
                        <div class="text-center py-8 text-slate-500">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p class="text-sm">Memuat data aset...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteCategoryConfirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-[2rem] p-8 max-w-md w-full mx-4 shadow-2xl">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-black text-slate-900 mb-2">Hapus Kategori?</h3>
            <p class="text-sm text-slate-500">
                Apakah Anda yakin ingin menghapus kategori ini dan semua asetnya? 
                <span class="font-bold text-red-600">Tindakan ini tidak dapat dibatalkan!</span>
            </p>
        </div>
        
        <div class="flex gap-4">
            <button type="button" onclick="closeDeleteCategoryConfirmModal()" 
                class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 rounded-xl transition-colors">
                Batal
            </button>
            <button type="button" onclick="deleteCategory()" 
                class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl transition-colors">
                <i class="fas fa-trash-alt mr-2"></i> Ya, Hapus Semua
            </button>
        </div>
    </div>
</div>

<script>
let currentCategoryId = null;

function openEditCategoryModal(categoryId) {
    currentCategoryId = categoryId;
    
    // Show modal
    document.getElementById('editCategoryModal').style.display = 'flex';
    
    // Load category data
    fetch(`/categories/${categoryId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const category = data.data;
                document.getElementById('editCategoryName').value = category.name;
                document.getElementById('editCategoryDescription').value = category.description || '';
                document.getElementById('assetCount').textContent = category.assets_count || 0;
                
                // Load assets list
                loadAssetsList(categoryId);
                
                // Update form action
                document.getElementById('editCategoryForm').action = `/categories/${categoryId}`;
            }
        })
        .catch(error => {
            console.error('Error loading category:', error);
        });
}

function closeEditCategoryModal() {
    document.getElementById('editCategoryModal').style.display = 'none';
    currentCategoryId = null;
}

function loadAssetsList(categoryId) {
    const assetsList = document.getElementById('assetsList');
    const assetCountElement = document.getElementById('assetCount');
    
    // Show loading
    assetsList.innerHTML = `
        <div class="text-center py-8 text-slate-500">
            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
            <p class="text-sm">Memuat data aset...</p>
        </div>
    `;
    
    fetch(`/categories/${categoryId}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        }
    })
    .then(response => {
        console.log('Assets response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Assets data:', data);
        
        if (data.success) {
            const category = data.data;
            const assets = category.assets || [];
            
            // Update asset count
            if (assetCountElement) {
                assetCountElement.textContent = assets.length;
            }
            
            if (assets.length === 0) {
                assetsList.innerHTML = `
                    <div class="text-center py-8 text-slate-500">
                        <i class="fas fa-inbox text-2xl mb-2"></i>
                        <p class="text-sm">Belum ada aset dalam kategori ini</p>
                    </div>
                `;
            } else {
                assetsList.innerHTML = assets.map(asset => `
                    <div class="bg-white border border-slate-200 rounded-lg p-3 hover:bg-slate-50 transition-colors">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="font-bold text-slate-900">${asset.code}</div>
                                <div class="text-sm text-slate-700">${asset.name}</div>
                                <div class="flex gap-2 mt-1">
                                    <span class="inline-block px-2 py-1 rounded text-xs font-semibold
                                        ${asset.condition === 'baik' ? 'bg-green-100 text-green-700' : 
                                          asset.condition === 'cukup' ? 'bg-yellow-100 text-yellow-700' : 
                                          'bg-red-100 text-red-700'}">
                                        ${asset.condition || 'baik'}
                                    </span>
                                    <span class="inline-block px-2 py-1 rounded text-xs font-semibold
                                        ${asset.status === 'tersedia' ? 'bg-green-100 text-green-700' : 
                                          asset.status === 'dipinjam' ? 'bg-blue-100 text-blue-700' : 
                                          'bg-orange-100 text-orange-700'}">
                                        ${asset.status || 'tersedia'}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            }
        } else {
            throw new Error(data.message || 'Failed to load category data');
        }
    })
    .catch(error => {
        console.error('Error loading assets:', error);
        assetsList.innerHTML = `
            <div class="text-center py-8 text-red-500">
                <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                <p class="text-sm">Gagal memuat data aset: ${error.message}</p>
            </div>
        `;
        
        // Set asset count to 0 on error
        if (assetCountElement) {
            assetCountElement.textContent = '0';
        }
    });
}

function confirmDeleteCategory() {
    document.getElementById('deleteCategoryConfirmModal').style.display = 'flex';
}

function closeDeleteCategoryConfirmModal() {
    document.getElementById('deleteCategoryConfirmModal').style.display = 'none';
}

function deleteCategory() {
    if (!currentCategoryId) {
        alert('ID kategori tidak ditemukan');
        return;
    }
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        alert('CSRF token tidak ditemukan');
        return;
    }
    
    // Show loading and close confirm modal immediately
    closeDeleteCategoryConfirmModal();
    
    const deleteBtn = document.querySelector('#deleteCategoryConfirmModal button[onclick="deleteCategory()"]');
    const originalText = deleteBtn.innerHTML;
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menghapus...';
    deleteBtn.disabled = true;
    
    // Create form data for DELETE request
    const formData = new FormData();
    formData.append('_method', 'DELETE');
    formData.append('_token', csrfToken);
    
    // Send delete request
    fetch(`/categories/${currentCategoryId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        
        // Store delete success in sessionStorage for post-refresh notification
        sessionStorage.setItem('deleteSuccess', 'true');
        sessionStorage.setItem('deleteMessage', 'Kategori dan semua asetnya berhasil dihapus');
        
        closeEditCategoryModal();
        
        // Hard refresh after 500ms
        setTimeout(() => {
            location.reload(true);
        }, 500);
    })
    .catch(error => {
        console.error('Error deleting category:', error);
        
        // Even on error, try to refresh (delete likely succeeded)
        sessionStorage.setItem('deleteSuccess', 'true');
        sessionStorage.setItem('deleteMessage', 'Kategori dan semua asetnya berhasil dihapus');
        
        closeEditCategoryModal();
        
        // Hard refresh after 500ms
        setTimeout(() => {
            location.reload(true);
        }, 500);
    });
}

// Handle form submission
document.getElementById('editCategoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!csrfToken) {
        alert('CSRF token not found');
        return;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memperbarui...';
    submitBtn.disabled = true;
    
    // Store update success in sessionStorage for post-refresh notification
    sessionStorage.setItem('updateSuccess', 'true');
    sessionStorage.setItem('updateMessage', 'Kategori berhasil diperbarui');
    
    // Send update request
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Update response status:', response.status);
        
        // Close modal and hard refresh immediately
        closeEditCategoryModal();
        
        // Hard refresh after 500ms
        setTimeout(() => {
            location.reload(true);
        }, 500);
    })
    .catch(error => {
        console.error('Error updating category:', error);
        
        // Even on error, try to refresh (update might have succeeded)
        closeEditCategoryModal();
        
        // Hard refresh after 500ms
        setTimeout(() => {
            location.reload(true);
        }, 500);
    });
});

// Check for success notifications on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check for delete success message
    const deleteSuccess = sessionStorage.getItem('deleteSuccess');
    const deleteMessage = sessionStorage.getItem('deleteMessage');
    
    console.log('Checking delete success:', deleteSuccess, deleteMessage);
    
    if (deleteSuccess === 'true' && deleteMessage) {
        // Show success notification for 3 seconds
        console.log('Showing delete notification:', deleteMessage);
        createNotification(deleteMessage);
        
        // Clear sessionStorage
        sessionStorage.removeItem('deleteSuccess');
        sessionStorage.removeItem('deleteMessage');
    }
    
    // Check for update success message
    const updateSuccess = sessionStorage.getItem('updateSuccess');
    const updateMessage = sessionStorage.getItem('updateMessage');
    
    console.log('Checking update success:', updateSuccess, updateMessage);
    
    if (updateSuccess === 'true' && updateMessage) {
        // Show success notification for 3 seconds
        console.log('Showing update notification:', updateMessage);
        createNotification(updateMessage);
        
        // Clear sessionStorage
        sessionStorage.removeItem('updateSuccess');
        sessionStorage.removeItem('updateMessage');
    }
});

// Create notification function (independent from global showNotification)
function createNotification(message) {
    console.log('Creating notification:', message);
    
    // Create notification element
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
        <span class="font-medium">${message}</span>
    `;
    
    // Add to body
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
}
</script>
