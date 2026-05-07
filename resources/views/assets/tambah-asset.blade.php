{{-- TOMBOL TAMBAH ASSET --}}
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
            <h3 class="text-xl font-black text-slate-900">Tambah Asset Baru</h3>
            <p class="text-slate-600 text-sm mt-2">Isi detail Asset di bawah ini</p>
        </div>
        
        <form id="addAssetForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="condition" value="baik">
            <input type="hidden" name="status" value="tersedia">
            
            {{-- CATEGORY --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Kategori
                </label>
                <select name="category_id" id="addCategory" required
                    class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-bold text-slate-700 focus:border-red-primary focus:outline-none transition-colors">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" data-category-name="{{ $category->name }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- NAME --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Nama Asset
                </label>
                <input type="text" name="name" required
                    class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-bold text-slate-700 focus:border-red-primary focus:outline-none transition-colors"
                    placeholder="Masukkan nama Asset">
            </div>
            
            {{-- CODE --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Kode Asset
                </label>
                <div class="flex items-center gap-3">
                    <div class="flex-1 px-4 py-3 bg-slate-100 border-2 border-slate-200 rounded-xl font-black text-slate-700">
                        <span>Auto-generate</span>
                    </div>
                    <div class="text-sm text-slate-500">
                        <i class="fas fa-info-circle"></i>
                        Otomatis
                    </div>
                </div>
            </div>
                        
            {{-- DESCRIPTION --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Deskripsi
                </label>
                <textarea name="description" rows="3" maxlength="500"
                    class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-bold text-slate-700 focus:border-red-primary focus:outline-none transition-colors"
                    placeholder="Masukkan deskripsi Asset (opsional)"></textarea>
                <p class="text-xs text-slate-500 mt-1">Maksimal 500 karakter</p>
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
                    • Stok akan otomatis diatur menjadi <span class="font-bold">"1"</span>
                </p>
            </div>
            
            {{-- PHOTO UPLOAD --}}
            <div class="mb-6">
                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                    Foto Asset <span class="text-red-500">*</span>
                </label>
                
                {{-- PHOTO BUTTON --}}
                <button type="button" onclick="document.getElementById('addAssetPhoto').click()" 
                    class="w-full bg-red-primary hover:bg-red-700 text-white font-bold py-3 px-4 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-folder-open"></i>
                    <span>Pilih File</span>
                </button>
                
                {{-- HIDDEN FILE INPUT --}}
                <input type="file" name="photo" id="addAssetPhoto" accept="image/*" class="hidden" onchange="previewAddAssetPhoto(event)">
                
                <p class="text-xs text-slate-500 mt-2">
                    <i class="fas fa-exclamation-circle text-red-500"></i> 
                    Format: JPG, PNG, JPEG (Max: 2MB) - <span class="font-bold text-red-600">Wajib diisi</span>
                </p>
                
                {{-- PHOTO PREVIEW --}}
                <div id="addPhotoPreviewContainer" class="mt-4 hidden">
                    <div class="relative inline-block">
                        <img id="addPhotoPreview" src="" alt="Preview" class="w-full max-w-xs h-48 object-cover rounded-xl border-2 border-slate-200 shadow-md">
                        <button type="button" onclick="removeAddAssetPhoto()" 
                            class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <p class="text-xs text-green-600 mt-2 font-bold">
                        <i class="fas fa-check-circle"></i> Foto berhasil dipilih
                    </p>
                </div>
            </div>
            
                        
            {{-- BUTTONS --}}
            <div class="flex gap-4">
                <button type="button" onclick="closeAddAssetModal()" 
                    class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit" 
                    class="flex-1 bg-red-primary hover:bg-red-700 text-white font-black py-3 rounded-xl transition-all duration-300 hover:shadow-xl">
                    <i class="fas fa-save mr-2"></i> Simpan Asset
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Simpan file dan preview state
let currentAddFile = null;
let hasValidPhoto = false;

// Preview foto saat upload
function previewAddAssetPhoto(event) {
    const file = event.target.files[0];
    const fileInput = document.getElementById('addAssetPhoto');
    
    // Jika user cancel, cek apakah sudah ada foto sebelumnya
    if (!file) {
        // Jika sudah ada foto valid sebelumnya, pertahankan state
        if (hasValidPhoto && currentAddFile) {
            // Coba restore file
            try {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(currentAddFile);
                fileInput.files = dataTransfer.files;
            } catch (e) {
                // Jika gagal, tetap tandai sebagai valid
                console.log('DataTransfer not supported, but photo is still valid');
            }
        }
        return;
    }
    
    const previewContainer = document.getElementById('addPhotoPreviewContainer');
    const previewImage = document.getElementById('addPhotoPreview');
    
    // Validasi ukuran file (max 2MB)
    if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran file terlalu besar! Maksimal 2MB');
        event.target.value = '';
        currentAddFile = null;
        hasValidPhoto = false;
        return;
    }
    
    // Validasi tipe file
    if (!file.type.match('image.*')) {
        alert('File harus berupa gambar!');
        event.target.value = '';
        currentAddFile = null;
        hasValidPhoto = false;
        return;
    }
    
    // Simpan file yang valid
    currentAddFile = file;
    hasValidPhoto = true;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        previewImage.src = e.target.result;
        previewContainer.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}

// Hapus foto yang dipilih
function removeAddAssetPhoto() {
    const fileInput = document.getElementById('addAssetPhoto');
    const previewContainer = document.getElementById('addPhotoPreviewContainer');
    const previewImage = document.getElementById('addPhotoPreview');
    
    fileInput.value = '';
    previewImage.src = '';
    previewContainer.classList.add('hidden');
    currentAddFile = null;
    hasValidPhoto = false;
}

// Reset form saat modal ditutup
function closeAddAssetModal() {
    document.getElementById('addAssetModal').classList.add('hidden');
    document.getElementById('addAssetForm').reset();
    removeAddAssetPhoto();
}

// Validasi form sebelum submit
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addAssetForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const fileInput = document.getElementById('addAssetPhoto');
            
            // Cek apakah ada file atau ada foto valid yang sudah dipilih
            if (!fileInput.files.length && !hasValidPhoto) {
                e.preventDefault();
                alert('Foto wajib diisi!');
                return false;
            }
            
            // Jika ada foto valid tapi input kosong, coba restore
            if (!fileInput.files.length && hasValidPhoto && currentAddFile) {
                try {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(currentAddFile);
                    fileInput.files = dataTransfer.files;
                } catch (err) {
                    e.preventDefault();
                    alert('Silakan pilih foto lagi');
                    return false;
                }
            }
        });
    }
});
</script>
