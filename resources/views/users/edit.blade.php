{{-- EDIT USER MODAL --}}
<div id="userModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-2xl rounded-[2.5rem] shadow-2xl p-8 m-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tighter">
                Edit User
            </h2>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="userForm" action="" method="POST">
            @csrf
            <input type="hidden" id="formMethod" name="_method" value="PUT">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nama --}}
                <div>
                    <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                        Nama Lengkap
                    </label>
                    <input type="text" 
                        id="userName" 
                        name="name" 
                        required
                        class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-semibold text-slate-700 focus:border-red-500 focus:outline-none transition-colors">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                        Email Address
                    </label>
                    <input type="email" 
                        id="userEmail" 
                        name="email" 
                        required
                        class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-semibold text-slate-700 focus:border-red-500 focus:outline-none transition-colors">
                </div>
                
                {{-- Role --}}
                <div>
                    <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">
                        Role User
                    </label>
                    <select id="userRole" 
                        name="role_id" 
                        required
                        class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl font-semibold text-slate-700 focus:border-red-500 focus:outline-none transition-colors">
                        <option value="">-- Pilih Role --</option>
                        <option value="1">Admin</option>
                        <option value="2">Staff</option>
                    </select>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex justify-end gap-4 mt-8">
                <button type="button" 
                    onclick="closeModal()" 
                    class="px-6 py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl font-bold transition-colors">
                    Batal
                </button>
                <button type="submit" 
                    class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-colors">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- JavaScript for Edit Modal --}}
<script>
// Edit modal functions
function openEditModal(userId, userName, userEmail, userRole) {
    currentEditingUserId = userId;
    
    console.log('Opening edit modal with data:', {userId, userName, userEmail, userRole}); // Debug
    
    // Store original data for comparison
    window.originalUserData = {
        name: userName,
        email: userEmail,
        role_id: userRole
    };
    
    // Set form fields directly with passed data
    document.getElementById('userName').value = userName || '';
    document.getElementById('userEmail').value = userEmail || '';
    document.getElementById('userRole').value = userRole || '';
    
    // Set form action
    document.getElementById('userForm').action = `/users/${userId}`;
    
    // Add change listeners to monitor form changes
    setupChangeListeners();
    
    // Show modal
    document.getElementById('userModal').classList.remove('hidden');
    document.getElementById('userModal').classList.add('flex');
}

function setupChangeListeners() {
    const nameInput = document.getElementById('userName');
    const emailInput = document.getElementById('userEmail');
    const roleSelect = document.getElementById('userRole');
    const updateButton = document.querySelector('button[type="submit"]');
    
    function checkChanges() {
        const currentData = {
            name: nameInput.value.trim(),
            email: emailInput.value.trim(),
            role_id: roleSelect.value
        };
        
        const originalData = window.originalUserData || {};
        
        const hasChanges = (
            currentData.name !== originalData.name ||
            currentData.email !== originalData.email ||
            currentData.role_id !== originalData.role_id
        );
        
        // Enable/disable update button based on changes
        if (hasChanges) {
            updateButton.disabled = false;
            updateButton.classList.remove('opacity-50', 'cursor-not-allowed');
            updateButton.classList.add('hover:bg-red-700');
        } else {
            updateButton.disabled = true;
            updateButton.classList.add('opacity-50', 'cursor-not-allowed');
            updateButton.classList.remove('hover:bg-red-700');
        }
        
        console.log('Changes detected:', hasChanges); // Debug
    }
    
    // Add event listeners
    nameInput.addEventListener('input', checkChanges);
    emailInput.addEventListener('input', checkChanges);
    roleSelect.addEventListener('change', checkChanges);
    
    // Initial check
    checkChanges();
}

function closeModal() {
    document.getElementById('userModal').classList.add('hidden');
    document.getElementById('userModal').classList.remove('flex');
    document.getElementById('userForm').reset();
}

// Form submission
document.addEventListener('DOMContentLoaded', function() {
    const userForm = document.getElementById('userForm');
    if (userForm) {
        userForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(userForm);
            const formAction = userForm.action;
            
            console.log('Submitting form to:', formAction); // Debug
            console.log('Form data:', Object.fromEntries(formData)); // Debug
            
            fetch(formAction, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                console.log('Response status:', response.status); // Debug
                console.log('Response headers:', response.headers.get('content-type')); // Debug
                
                if (!response.ok) {
                    // Jika response tidak OK, coba dapatkan text untuk melihat error
                    return response.text().then(text => {
                        console.log('Error response text:', text); // Debug
                        throw new Error(`HTTP error! status: ${response.status} - ${text.substring(0, 100)}`);
                    });
                }
                
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        console.log('Non-JSON response:', text); // Debug
                        throw new Error('Server mengembalikan HTML bukan JSON. Cek console untuk detail.');
                    });
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data); // Debug
                if (data.success) {
                    closeModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Terjadi kesalahan'
                    });
                }
            })
            .catch(error => {
                console.error('Error updating user:', error); // Debug
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan server'
                });
            });
        });
    }
});
</script>
