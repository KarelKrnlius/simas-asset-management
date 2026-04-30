{{-- CATEGORY LIST MODAL --}}
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

<div id="categoryListModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-[2rem] p-8 max-w-6xl w-full mx-4 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="text-center mb-6">
            <h3 class="text-xl font-black text-slate-900">Manajemen Kategori</h3>
            <p class="text-slate-600 text-sm mt-2">Lihat, edit, atau hapus kategori beserta asetnya</p>
        </div>
        
        <!-- Add New Category Button -->
        <div class="flex justify-end mb-6">
            <button onclick="openAddCategoryModal(); closeCategoryListModal();" 
                class="bg-red-primary hover:bg-red-700 text-white font-black text-sm uppercase tracking-wider px-6 py-3 rounded-xl transition-all duration-300 hover:shadow-xl">
                <i class="fas fa-plus mr-2"></i> Tambah Kategori Baru
            </button>
        </div>
        
        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($categories as $category)
                <div class="bg-slate-50 rounded-xl p-6 hover:shadow-lg transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h4 class="font-bold text-slate-900 text-lg mb-2">{{ $category->name }}</h4>
                            @if($category->description)
                                <p class="text-sm text-slate-600 mb-3">{{ $category->description }}</p>
                            @endif
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <i class="fas fa-boxes"></i>
                                <span>{{ $category->assets_count ?? 0 }} aset</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 ml-4">
                            <button onclick="openEditCategoryModal({{ $category->id }}); closeCategoryListModal();" 
                                class="w-10 h-10 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-lg flex items-center justify-center transition-colors"
                                title="Edit Kategori">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="openEditCategoryModal({{ $category->id }}); closeCategoryListModal();" 
                                class="w-10 h-10 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg flex items-center justify-center transition-colors"
                                title="Hapus Kategori">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Preview Assets (max 3 items) -->
                    @if($category->assets_count > 0)
                        <div class="border-t border-slate-200 pt-3">
                            <p class="text-xs font-semibold text-slate-700 mb-2">Preview Aset:</p>
                            <div class="space-y-1">
                                {{-- This would need to be loaded via AJAX for real preview --}}
                                <div class="text-xs text-slate-500">
                                    <i class="fas fa-info-circle"></i>
                                    Klik edit untuk melihat detail semua aset
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        
        @if($categories->count() === 0)
            <div class="text-center py-12 text-slate-500">
                <i class="fas fa-folder-open text-5xl mb-4"></i>
                <h4 class="font-bold text-slate-900 mb-2">Belum Ada Kategori</h4>
                <p class="text-sm mb-6">Tambah kategori pertama untuk memulai mengelola aset</p>
                <button onclick="openAddCategoryModal(); closeCategoryListModal();" 
                    class="bg-red-primary hover:bg-red-700 text-white font-black text-sm uppercase tracking-wider px-6 py-3 rounded-xl transition-all duration-300 hover:shadow-xl">
                    <i class="fas fa-plus mr-2"></i> Tambah Kategori Pertama
                </button>
            </div>
        @endif
        
        <!-- Close Button -->
        <div class="flex justify-end mt-8 pt-6 border-t border-slate-200">
            <button onclick="closeCategoryListModal()" 
                class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 px-8 rounded-xl transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
function openCategoryListModal() {
    document.getElementById('categoryListModal').style.display = 'flex';
}

function closeCategoryListModal() {
    document.getElementById('categoryListModal').style.display = 'none';
}
</script>
