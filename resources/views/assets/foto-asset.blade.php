{{-- PHOTO MODAL --}}
<div id="photoModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50" style="display: none;">
    <div class="relative max-w-4xl max-h-[90vh] p-4" onclick="event.stopPropagation()">
        {{-- CLOSE BUTTON --}}
        <button onclick="closePhotoModal()" 
            class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-10 h-10 flex items-center justify-center shadow-lg transition-colors z-10">
            <i class="fas fa-times text-lg"></i>
        </button>
        
        {{-- PHOTO IMAGE --}}
        <img id="photoModalImage" src="" alt="" class="max-w-full max-h-[85vh] rounded-xl shadow-2xl">
        
        {{-- ASSET NAME --}}
        <p id="photoModalTitle" class="text-white text-center mt-4 font-bold text-lg"></p>
    </div>
</div>

<script>
// Photo modal functions
function showPhotoModal(photoUrl, assetName) {
    const modal = document.getElementById('photoModal');
    const image = document.getElementById('photoModalImage');
    const title = document.getElementById('photoModalTitle');
    
    if (!modal || !image || !title) {
        console.error('Photo modal elements not found');
        return;
    }
    
    // Reset error handler setiap kali buka modal
    image.onerror = null;
    
    image.src = photoUrl;
    image.onerror = function() {
        console.error('Failed to load image:', photoUrl);
        title.textContent = 'Gagal memuat foto: ' + assetName;
    };
    title.textContent = assetName;
    modal.style.display = 'flex';
}

function closePhotoModal() {
    const modal = document.getElementById('photoModal');
    if (modal) {
        modal.style.display = 'none';
        // Reset image dan error handler
        const image = document.getElementById('photoModalImage');
        if (image) {
            image.onerror = null;
            image.src = '';
        }
    }
}

// Close modal with ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closePhotoModal();
    }
});

// Ensure modal is closed on page load
document.addEventListener('DOMContentLoaded', function() {
    closePhotoModal();
});
</script>
