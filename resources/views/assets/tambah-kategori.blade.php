{{-- ADD CATEGORY MODAL --}}
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
<div id="addCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-[2rem] p-8 max-w-md w-full mx-4 shadow-2xl">
        <div class="text-center mb-6">
            <h3 class="text-xl font-black text-slate-900">Tambah Kategori Baru</h3>
            <p class="text-slate-600 text-sm mt-2">Buat kategori baru untuk aset</p>
        </div>
        
        <form id="addCategoryForm" method="POST">
            @csrf
            
            {{-- NAME --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Nama Kategori
                </label>
                <input type="text" name="name" id="categoryName" required
                    class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-bold text-slate-700 focus:border-red-primary focus:outline-none transition-colors"
                    placeholder="Masukkan nama kategori">
                <div id="categoryError" class="hidden mt-2 text-sm font-semibold text-red-600"></div>
            </div>
            
            {{-- DESCRIPTION --}}
            <div class="mb-8">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Deskripsi
                </label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-bold text-slate-700 focus:border-red-primary focus:outline-none transition-colors"
                    placeholder="Masukkan deskripsi kategori (opsional)"></textarea>
            </div>
            
            {{-- BUTTONS --}}
            <div class="flex gap-4">
                <button type="button" onclick="closeAddCategoryModal()" 
                    class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit" 
                    class="flex-1 bg-red-primary hover:bg-red-700 text-white font-black py-3 rounded-xl transition-all duration-300 hover:shadow-xl">
                    <i class="fas fa-save mr-2"></i> Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>
