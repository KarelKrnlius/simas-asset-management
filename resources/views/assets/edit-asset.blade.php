{{-- EDIT ASSET MODAL --}}
<style>
    .bg-red-primary { background-color: #E11D48 !important; }
    .hover\:bg-red-primary:hover { background-color: #E11D48 !important; opacity: 0.9 !important; }
    .focus\:border-red-primary:focus { border-color: #E11D48 !important; }
    
    /* Cursor styles for interactive elements */
    select:hover { cursor: pointer; }
    input[type="text"]:hover { cursor: text; }
    input[type="number"]:hover { cursor: text; }
    textarea:hover { cursor: text; }
    button:hover { cursor: pointer; }
</style>
<div id="editAssetModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-[2rem] p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="text-center mb-6">
            <h3 class="text-xl font-black text-slate-900">Edit Aset</h3>
            <p class="text-slate-600 text-sm mt-2">Ubah detail aset di bawah ini</p>
        </div>
        
        <form id="editAssetForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="asset_id" id="editAssetId">
            
            {{-- CATEGORY --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Kategori
                </label>
                <select name="category_id" id="editCategory" required
                    class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-bold text-slate-700 focus:border-red-primary focus:outline-none transition-colors">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- NAME --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Nama Aset
                </label>
                <input type="text" name="name" id="editName" required
                    class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-bold text-slate-700 focus:border-red-primary focus:outline-none transition-colors"
                    placeholder="Masukkan nama aset">
            </div>
            
            {{-- CODE --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Kode Aset (Auto-Generated)
                </label>
                <input type="text" name="code" id="editCode" readonly
                    class="w-full px-4 py-3 bg-slate-100 border-2 border-slate-200 rounded-xl font-bold text-slate-500"
                    placeholder="Kode akan otomatis di-generate">
                <p class="text-xs text-slate-500 mt-2">Kode asset di-generate otomatis berdasarkan kategori</p>
            </div>
            
            {{-- DESCRIPTION --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Deskripsi
                </label>
                <textarea name="description" id="editDescription" rows="3"
                    class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-bold text-slate-700 focus:border-red-primary focus:outline-none transition-colors"
                    placeholder="Masukkan deskripsi aset (opsional)"></textarea>
            </div>
            
            {{-- STOCK --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Stok
                </label>
                <input type="number" name="stock" id="editStock" required min="0"
                    class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-bold text-slate-700 focus:border-red-primary focus:outline-none transition-colors"
                    placeholder="Masukkan jumlah stok">
            </div>
            
            {{-- CONDITION --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Kondisi
                </label>
                <select name="condition" id="editCondition" required
                    class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-bold text-slate-700 focus:border-red-primary focus:outline-none transition-colors">
                    <option value="">-- Pilih Kondisi --</option>
                    <option value="baik">Baik</option>
                    <option value="rusak">Rusak</option>
                    <option value="hilang">Hilang</option>
                </select>
            </div>
            
            {{-- STATUS --}}
            <div class="mb-8">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Status
                </label>
                <select name="status" id="editStatus" required
                    class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-bold text-slate-700 focus:border-red-primary focus:outline-none transition-colors">
                    <option value="">-- Pilih Status --</option>
                    <option value="tersedia">Tersedia</option>
                    <option value="dipinjam">Dipinjam</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="tidak_tersedia">Tidak Tersedia</option>
                </select>
            </div>
            
            {{-- BUTTONS --}}
            <div class="flex gap-4">
                <button type="button" onclick="closeEditAssetModal()" 
                    class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit" 
                    class="flex-1 bg-red-primary hover:bg-red-primary text-white font-black py-3 rounded-xl transition-all duration-300 hover:shadow-xl">
                    <i class="fas fa-save mr-2"></i> Update Aset
                </button>
            </div>
        </form>
    </div>
</div>
