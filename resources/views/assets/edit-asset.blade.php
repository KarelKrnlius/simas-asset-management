<div id="editAssetModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-[2rem] p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="text-center mb-6">
            <h3 class="text-xl font-black text-slate-900">Edit Asset</h3>
            <p class="text-slate-600 text-sm mt-2">Ubah detail Asset di bawah ini</p>
        </div>
        
        <form id="editAssetForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="asset_id" id="editAssetId">
            <input type="hidden" name="current_photo" id="editCurrentPhoto">
            
            {{-- CATEGORY --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Kategori
                </label>
                <select name="category_id" id="editCategory" required disabled
                    class="w-full px-4 py-3 bg-slate-100 border-2 border-slate-200 rounded-xl font-bold text-slate-500 cursor-not-allowed">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="category_id" id="editCategoryHidden">
            </div>
            
            {{-- NAME --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Nama Asset
                </label>
                <input type="text" name="name" id="editName" required
                    class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-bold text-slate-700 focus:border-red-primary focus:outline-none transition-colors"
                    placeholder="Masukkan nama Asset">
            </div>
            
            {{-- CODE --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Kode Asset 
                </label>
                <input type="text" name="code" id="editCode" readonly
                    class="w-full px-4 py-3 bg-slate-100 border-2 border-slate-200 rounded-xl font-bold text-slate-500"
                    placeholder="Kode akan otomatis di-generate">
                <p class="text-xs text-slate-500 mt-2">Kode asset digenerate otomatis</p>
            </div>
            
            {{-- DESCRIPTION --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Deskripsi
                </label>
                <textarea name="description" id="editDescription" rows="3" maxlength="500"
                    class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-bold text-slate-700 focus:border-red-primary focus:outline-none transition-colors"
                    placeholder="Masukkan deskripsi Asset (opsional)"></textarea>
                <p class="text-xs text-slate-500 mt-1">Maksimal 500 karakter</p>
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
            
            {{-- PHOTO UPLOAD --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Foto Asset
                </label>
                
                {{-- CURRENT PHOTO PREVIEW --}}
                <div id="editCurrentPhotoContainer" class="mb-4 hidden">
                    <p class="text-xs text-slate-600 mb-2 font-bold">Foto Saat Ini:</p>
                    <div class="relative inline-block">
                        <img id="editCurrentPhotoPreview" src="" alt="Current Photo" class="w-full max-w-xs h-48 object-cover rounded-xl border-2 border-slate-200 shadow-md">
                    </div>
                </div>
                
                {{-- PHOTO BUTTON --}}
                <button type="button" onclick="document.getElementById('editAssetPhoto').click()" 
                    class="w-full bg-red-primary hover:bg-red-700 text-white font-bold py-3 px-4 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-folder-open"></i>
                    <span id="editPhotoButtonText">Pilih Foto Baru</span>
                </button>
                
                {{-- HIDDEN FILE INPUT --}}
                <input type="file" name="photo" id="editAssetPhoto" accept="image/*" class="hidden" onchange="previewEditAssetPhoto(event)">
                
                <p class="text-xs text-slate-500 mt-2">
                    <i class="fas fa-info-circle text-blue-500"></i> 
                    Format: JPG, PNG, JPEG (Max: 2MB) - Kosongkan jika tidak ingin mengubah foto
                </p>
                
                {{-- NEW PHOTO PREVIEW --}}
                <div id="editPhotoPreviewContainer" class="mt-4 hidden">
                    <p class="text-xs text-green-600 mb-2 font-bold">Foto Baru:</p>
                    <div class="relative inline-block">
                        <img id="editPhotoPreview" src="" alt="Preview" class="w-full max-w-xs h-48 object-cover rounded-xl border-2 border-green-200 shadow-md">
                        <button type="button" onclick="removeEditAssetPhoto()" 
                            class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <p class="text-xs text-green-600 mt-2 font-bold">
                        <i class="fas fa-check-circle"></i> Foto baru berhasil dipilih
                    </p>
                </div>
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
                    <option value="perlu_perbaikan">Perlu Perbaikan</option>
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
                    <i class="fas fa-save mr-2"></i> Update Asset
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Simpan file yang sudah dipilih
let currentEditFile = null;

// Preview foto baru saat upload
function previewEditAssetPhoto(event) {
    const file = event.target.files[0];
    
    // Jika user cancel (tidak pilih file), restore file sebelumnya
    if (!file) {
        if (currentEditFile) {
            // Restore file sebelumnya
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(currentEditFile);
            event.target.files = dataTransfer.files;
        }
        return;
    }
    
    const previewContainer = document.getElementById('editPhotoPreviewContainer');
    const previewImage = document.getElementById('editPhotoPreview');
    const buttonText = document.getElementById('editPhotoButtonText');
    
    // Validasi ukuran file (max 2MB)
    if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran file terlalu besar! Maksimal 2MB');
        event.target.value = '';
        currentEditFile = null;
        return;
    }
    
    // Validasi tipe file
    if (!file.type.match('image.*')) {
        alert('File harus berupa gambar!');
        event.target.value = '';
        currentEditFile = null;
        return;
    }
    
    // Simpan file yang valid
    currentEditFile = file;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        previewImage.src = e.target.result;
        previewContainer.classList.remove('hidden');
        buttonText.textContent = 'Ganti Foto Lain';
    };
    reader.readAsDataURL(file);
}

// Hapus foto baru yang dipilih
function removeEditAssetPhoto() {
    const fileInput = document.getElementById('editAssetPhoto');
    const previewContainer = document.getElementById('editPhotoPreviewContainer');
    const previewImage = document.getElementById('editPhotoPreview');
    const buttonText = document.getElementById('editPhotoButtonText');
    const currentPhotoContainer = document.getElementById('editCurrentPhotoContainer');
    
    fileInput.value = '';
    previewImage.src = '';
    previewContainer.classList.add('hidden');
    currentEditFile = null;
    
    // Update button text based on current photo
    if (!currentPhotoContainer.classList.contains('hidden')) {
        buttonText.textContent = 'Pilih Foto Baru';
    } else {
        buttonText.textContent = 'Pilih File';
    }
}

// Reset form saat modal ditutup
function closeEditAssetModal() {
    document.getElementById('editAssetModal').classList.add('hidden');
    document.getElementById('editAssetForm').reset();
    
    // Reset photo previews
    document.getElementById('editCurrentPhotoContainer').classList.add('hidden');
    document.getElementById('editPhotoPreviewContainer').classList.add('hidden');
    document.getElementById('editCurrentPhotoPreview').src = '';
    document.getElementById('editPhotoPreview').src = '';
    document.getElementById('editPhotoButtonText').textContent = 'Pilih File';
    currentEditFile = null;
}
</script>
