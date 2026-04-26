{{-- DELETE ASSET --}}
<style>
    .bg-red-primary { background-color: #E11D48 !important; }
    .hover\:bg-red-primary:hover { background-color: #E11D48 !important; opacity: 0.9 !important; }
    .focus\:border-red-primary:focus { border-color: #E11D48 !important; }
    
    /* Cursor styles for interactive elements */
    button:hover { cursor: pointer; }
</style>
<div id="deleteAsset" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-[2rem] p-8 max-w-md w-full mx-4 shadow-2xl">
        <div class="text-center mb-6">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-black text-slate-900 mb-2">Hapus Aset</h3>
            <p class="text-slate-600 text-sm">Apakah Anda yakin ingin menghapus aset ini?</p>
            <p class="text-red-600 text-xs mt-2 font-semibold">Tindakan ini tidak dapat dibatalkan!</p>
        </div>
        
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            
            <div class="flex gap-4">
                <button type="button" onclick="closeDeleteModal()" 
                    class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit" 
                    class="flex-1 bg-red-primary hover:bg-red-primary text-white font-black py-3 rounded-xl transition-all duration-300 hover:shadow-xl">
                    <i class="fas fa-trash mr-2"></i> Hapus
                </button>
            </div>
        </form>
    </div>
</div>
