<div id="categoryListModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-[2rem] p-8 max-w-6xl w-full mx-4 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="text-center mb-6">
            <h3 class="text-xl font-black text-slate-900">Manajemen Kategori</h3>
            <p class="text-slate-600 text-sm mt-2">Lihat, edit, atau hapus kategori beserta Assetnya</p>
        </div>
        
        <!-- Add New Category Button -->
        <div class="flex justify-between items-center mb-6">
            <button onclick="closeCategoryListModal()" 
                class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 px-6 rounded-xl transition-colors">
                <i class="fas fa-times mr-2"></i> Batal
            </button>
            <button onclick="openAddCategoryModal(); closeCategoryListModal();" 
                class="bg-red-primary hover:bg-red-700 text-white font-black text-sm uppercase tracking-wider px-6 py-3 rounded-xl transition-all duration-300 hover:shadow-xl">
                <i class="fas fa-plus mr-2"></i> Tambah Kategori Baru
            </button>
        </div>
        
        <!-- Controls Bar -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <!-- Total Category Count -->
            <div class="flex items-center gap-2">
                <i class="fas fa-folder text-red-primary"></i>
                <span class="font-black text-slate-900">
                    Total Kategori: <span class="text-red-primary">{{ $categories->count() }}</span>
                </span>
            </div>
            
            <!-- Sort and Search -->
            <div class="flex flex-col lg:flex-row gap-3">
                <!-- Search Input -->
                <div class="flex-1">
                    <div class="relative flex-1">
                        <input type="text" id="categorySearchInput" 
                                   placeholder="Cari nama kategori..."
                                   class="w-full px-4 py-2 pr-10 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-red-primary focus:border-transparent"
                                   onkeypress="handleCategoryKeyPress(event)">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" onclick="performCategorySearch()">
                            <i class="fas fa-search text-slate-400 hover:text-red-600"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Clear Button -->
                <button onclick="clearCategorySearch()" 
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-semibold transition-colors flex items-center gap-2">
                    <i class="fas fa-sync-alt"></i>
                    Clear
                </button>
                
                <!-- Sort Dropdown -->
                <div class="relative">
                    <select id="categorySortSelect" onchange="performCategorySearch()"
                            class="appearance-none bg-white border border-slate-200 rounded-lg px-4 py-2 pr-8 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-red-primary focus:border-transparent">
                        <option value="code">Kode (Default)</option>
                        <option value="newest">Terbaru</option>
                        <option value="oldest">Terlama</option>
                        <option value="az">A - Z</option>
                        <option value="za">Z - A</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-700">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
                
                <!-- Hapus Terpilih Button -->
                <button onclick="openCategoryBulkDeleteModal()" 
                        id="categoryBulkDeleteBtn"
                        class="px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                    <i class="fas fa-trash mr-1"></i> Hapus Terpilih
                </button>
            </div>
        </div>
        
        <!-- Categories Table -->
        <div class="overflow-x-auto">
            <table class="w-full" id="categoriesTable">
                <thead>
                    <tr class="border-b-2 border-slate-200">
                        <th class="text-center py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">
                            <input type="checkbox" id="categorySelectAll" onclick="toggleCategorySelectAll()">
                        </th>
                        <th class="text-center py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">NO</th>
                        <th class="text-left py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">KODE</th>
                        <th class="text-left py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">NAMA</th>
                        <th class="text-left py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">DESKRIPSI</th>
                        <th class="text-center py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">ASSET</th>
                        <th class="text-center py-4 px-4 font-black text-slate-900 uppercase tracking-wider text-xs">AKSI</th>
                    </tr>
                </thead>
                <tbody id="categoriesTableBody">
                    @forelse($categories as $index => $category)
                        <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                            <td class="py-4 px-4 text-center">
                                <input type="checkbox" class="category-checkbox" value="{{ $category->id }}" onchange="updateCategoryBulkDeleteButton()"
                                    class="w-4 h-4 text-red-primary border-slate-300 rounded focus:ring-red-primary focus:ring-2">
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="inline-block bg-slate-100 text-slate-700 px-3 py-1 rounded-lg font-bold text-sm">
                                    {{ $loop->index + 1 }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-block bg-slate-100 text-slate-700 px-3 py-1 rounded-lg font-bold text-sm">
                                    {{ $category->category_code }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-slate-900">{{ $category->name }}</div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="text-sm text-slate-500">{{ $category->description ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-lg text-sm font-semibold">
                                    {{ $category->assets_count ?? 0 }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex justify-center gap-2">
                                    <button onclick="openEditCategoryModal({{ $category->id }}); closeCategoryListModal();" 
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm font-bold transition-colors"
                                        title="Edit Kategori">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteCategoryFromEdit({{ $category->id }})" 
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm font-bold transition-colors"
                                        title="Hapus Kategori">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-folder-open text-4xl text-slate-300 mb-4"></i>
                                    <h3 class="text-lg font-bold text-slate-900 mb-2">Belum Ada Data Kategori</h3>
                                    <p class="text-sm text-slate-500">Mulai dengan menambah kategori pertama Anda</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Close Button -->
        <div class="flex justify-end mt-8 pt-6 border-t border-slate-200">
            <button onclick="closeCategoryListModal()" 
                class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 px-8 rounded-xl transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Bulk Delete Modal -->
<div id="categoryBulkDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-md w-full mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-900">Hapus Kategori Terpilih</h3>
                <p class="text-sm text-slate-600">Aksi ini tidak dapat dibatalkan</p>
            </div>
        </div>
        
        <div class="mb-6">
            <p class="text-slate-700 mb-2">
                Apakah Anda yakin ingin menghapus <span id="categorySelectedCount" class="font-bold text-red-600">0</span> kategori terpilih?
            </p>
            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                <p class="text-sm text-red-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Semua kategori yang dipilih beserta Asset-nya akan dihapus secara permanen dari sistem.
                </p>
            </div>
        </div>
        
        <div class="flex gap-3">
            <button onclick="closeCategoryBulkDeleteModal()" 
                class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 rounded-xl transition-colors">
                Batal
            </button>
            <button onclick="confirmCategoryBulkDelete()" 
                class="flex-1 bg-red-600 hover:bg-red-700 text-white font-black py-3 rounded-xl transition-all duration-300 hover:shadow-xl">
                <i class="fas fa-trash mr-2"></i> Hapus Kategori
            </button>
        </div>
    </div>
</div>

<script>
// Notification function
function showNotification(message, type = 'info') {
    // Remove existing notification
    const existing = document.querySelector('.notification-toast');
    if (existing) {
        existing.remove();
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification-toast fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2 transform transition-all duration-300 translate-x-full`;
    
    // Set colors based on type
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        warning: 'bg-yellow-500 text-white',
        info: 'bg-blue-500 text-white'
    };
    
    notification.className += ' ' + colors[type];
    
    // Set icon based on type
    const icons = {
        success: '<i class="fas fa-check-circle"></i>',
        error: '<i class="fas fa-exclamation-circle"></i>',
        warning: '<i class="fas fa-exclamation-triangle"></i>',
        info: '<i class="fas fa-info-circle"></i>'
    };
    
    notification.innerHTML = `
        ${icons[type]}
        <span class="font-semibold">${message}</span>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

function openCategoryListModal() {
    document.getElementById('categoryListModal').style.display = 'flex';
}

function closeCategoryListModal() {
    document.getElementById('categoryListModal').style.display = 'none';
}

// Search functionality (client-side filtering)
function handleCategoryKeyPress(event) {
    if (event.key === 'Enter') {
        performCategorySearch();
    }
}

function performCategorySearch() {
    const searchValue = document.getElementById('categorySearchInput').value.toLowerCase();
    const sortValue = document.getElementById('categorySortSelect').value;
    const tbody = document.getElementById('categoriesTableBody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    // Filter rows
    const filteredRows = rows.filter(row => {
        const name = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
        const description = row.querySelector('td:nth-child(5)')?.textContent.toLowerCase() || '';
        const code = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
        
        return name.includes(searchValue) || description.includes(searchValue) || code.includes(searchValue);
    });
    
    // Sort filtered rows
    filteredRows.sort((a, b) => {
        const aCode = a.querySelector('td:nth-child(3)')?.textContent.trim() || '';
        const bCode = b.querySelector('td:nth-child(3)')?.textContent.trim() || '';
        const aName = a.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
        const bName = b.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
        
        switch(sortValue) {
            case 'code':
                return aCode.localeCompare(bCode);
            case 'newest':
                const aId = a.querySelector('input[type="checkbox"]')?.value || '0';
                const bId = b.querySelector('input[type="checkbox"]')?.value || '0';
                return parseInt(bId) - parseInt(aId);
            case 'oldest':
                const aId2 = a.querySelector('input[type="checkbox"]')?.value || '0';
                const bId2 = b.querySelector('input[type="checkbox"]')?.value || '0';
                return parseInt(aId2) - parseInt(bId2);
            case 'az':
                return aName.localeCompare(bName);
            case 'za':
                return bName.localeCompare(aName);
            default:
                return 0;
        }
    });
    
    // Hide all rows
    rows.forEach(row => row.style.display = 'none');
    
    // Show filtered and sorted rows
    filteredRows.forEach((row, index) => {
        tbody.appendChild(row);
        row.style.display = '';
        
        // Update row number
        const noCell = row.querySelector('td:nth-child(2) span');
        if (noCell) {
            noCell.textContent = index + 1;
        }
    });
}

function clearCategorySearch() {
    document.getElementById('categorySearchInput').value = '';
    document.getElementById('categorySortSelect').value = 'code';
    performCategorySearch();
}

// Bulk delete functionality
function toggleCategorySelectAll() {
    const selectAll = document.getElementById('categorySelectAll');
    const checkboxes = document.querySelectorAll('.category-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateCategoryBulkDeleteButton();
}

function updateCategoryBulkDeleteButton() {
    const checkboxes = document.querySelectorAll('.category-checkbox:checked');
    const deleteBtn = document.getElementById('categoryBulkDeleteBtn');
    const selectedCount = document.getElementById('categorySelectedCount');
    
    if (checkboxes.length > 0) {
        deleteBtn.disabled = false;
        selectedCount.textContent = checkboxes.length;
    } else {
        deleteBtn.disabled = true;
        selectedCount.textContent = '0';
    }
}

function openCategoryBulkDeleteModal() {
    const checkboxes = document.querySelectorAll('.category-checkbox:checked');
    if (checkboxes.length === 0) {
        showNotification('Pilih minimal satu kategori untuk dihapus', 'warning');
        return;
    }
    
    document.getElementById('categorySelectedCount').textContent = checkboxes.length;
    document.getElementById('categoryBulkDeleteModal').style.display = 'flex';
}

function closeCategoryBulkDeleteModal() {
    document.getElementById('categoryBulkDeleteModal').style.display = 'none';
}

function confirmCategoryBulkDelete() {
    const checkboxes = document.querySelectorAll('.category-checkbox:checked');
    const categoryIds = Array.from(checkboxes).map(cb => cb.value);
    
    if (categoryIds.length === 0) {
        showNotification('Pilih minimal satu kategori untuk dihapus', 'warning');
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        showNotification('CSRF token tidak ditemukan', 'error');
        return;
    }
    
    const formData = new FormData();
    categoryIds.forEach(id => formData.append('category_ids[]', id));
    
    // Show notification and close modal immediately
    showNotification('Menghapus kategori...', 'info');
    closeCategoryBulkDeleteModal();
    closeCategoryListModal();
    
    fetch('/categories/bulk-delete', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => {
                location.reload(true);
            }, 500);
        } else {
            showNotification('Gagal: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error deleting categories:', error);
        showNotification('Terjadi kesalahan saat menghapus kategori', 'error');
    });
}

// Delete category by opening edit modal and auto-triggering delete
function deleteCategoryFromEdit(categoryId) {
    closeCategoryListModal();
    openEditCategoryModal(categoryId);
    
    // Wait for modal to load then trigger delete
    setTimeout(() => {
        if (typeof confirmDeleteCategory === 'function') {
            confirmDeleteCategory();
        }
    }, 500);
}

</script>
