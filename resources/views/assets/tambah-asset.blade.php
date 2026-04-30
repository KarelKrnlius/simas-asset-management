{{-- ADD ASSET MODAL --}}
<style>
    .bg-red-primary { background-color: #E11D48 !important; }
    .hover\:bg-red-primary:hover { background-color: #E11D48 !important; opacity: 0.9 !important; }
    .focus\:border-red-primary:focus { border-color: #E11D48 !important; }
    
    /* Cursor styles for interactive elements */
    select#addCategory { cursor: pointer; }
    select#addCategory:hover { cursor: pointer; }
    input[type="text"]:hover { cursor: text; }
    input[type="number"]:hover { cursor: text; }
    textarea:hover { cursor: text; }
    button:hover { cursor: pointer; }
</style>
<div id="addAssetModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-[2rem] p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="text-center mb-6">
            <h3 class="text-xl font-black text-slate-900">Tambah Aset Baru</h3>
            <p class="text-slate-600 text-sm mt-2">Isi detail aset di bawah ini</p>
        </div>
        
        <form id="addAssetForm" method="POST">
            @csrf
            
            {{-- CATEGORY --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Kategori
                </label>
                <select name="category_id" id="addCategory" required
                    class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-bold text-slate-700 focus:border-red-primary focus:outline-none transition-colors"
                    onchange="updateCodePreview()">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" data-category-name="{{ $category->name }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- NAME --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Nama Aset
                </label>
                <input type="text" name="name" required
                    class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-bold text-slate-700 focus:border-red-primary focus:outline-none transition-colors"
                    placeholder="Masukkan nama aset">
            </div>
            
            {{-- CODE --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Kode Aset (Auto-Generated)
                </label>
                <input type="text" name="code" id="assetCodePreview" readonly
                    class="w-full px-4 py-3 bg-slate-100 border-2 border-slate-200 rounded-xl font-bold text-slate-500"
                    placeholder="Pilih kategori untuk melihat kode">
                <p class="text-xs text-slate-500 mt-2">Kode akan otomatis di-generate berdasarkan kategori dan stok</p>
            </div>
                        
            {{-- DESCRIPTION --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Deskripsi
                </label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-bold text-slate-700 focus:border-red-primary focus:outline-none transition-colors"
                    placeholder="Masukkan deskripsi aset (opsional)"></textarea>
            </div>
            
            {{-- STOCK --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Stok
                </label>
                <input type="number" name="stock" id="addStock" required min="0" value="1"
                    class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-bold text-slate-700 focus:border-red-primary focus:outline-none transition-colors"
                    placeholder="Masukkan jumlah stok"
                    oninput="updateCodePreview()">
            </div>
            
            {{-- INFO AUTO-SET --}}
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-info-circle text-green-600"></i>
                    <span class="text-sm font-bold text-green-800">Informasi Otomatis</span>
                </div>
                <p class="text-xs text-green-700">
                    • Status akan otomatis diatur menjadi <span class="font-bold">"Tersedia"</span><br>
                    • Kondisi akan otomatis diatur menjadi <span class="font-bold">"Baik"</span><br>
                </p>
            </div>
            
                        
            {{-- BUTTONS --}}
            <div class="flex gap-4">
                <button type="button" onclick="closeAddAssetModal()" 
                    class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit" 
                    class="flex-1 bg-red-primary hover:bg-red-700 text-white font-black py-3 rounded-xl transition-all duration-300 hover:shadow-xl">
                    <i class="fas fa-save mr-2"></i> Simpan Aset
                </button>
            </div>
        </form>
    </div>
</div>
