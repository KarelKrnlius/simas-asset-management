@extends('layouts.app')

@section('title', 'Master User SIMAS')

@section('content')
<div class="min-h-screenflex flex-col items-start pt-4 px-6">
    
    {{-- HEADER --}}
    <div class="w-full max-w-7xl mx-auto mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tighter">
                    Master User
                </h1>
                <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider mt-1">
                    Kelola pengguna sistem SIMAS
                </p>
            </div>
            
            <a href="{{ route('users.create') }}" 
                class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-bold text-sm transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1 inline-block">
                <i class="fas fa-plus mr-2"></i> Tambah User
            </a>
        </div>
    </div>

    {{-- MAIN CONTAINER --}}
    <div class="w-full max-w-7xl mx-auto">
        
        {{-- ERROR MESSAGE --}}
        @if(session('error'))
            <div class="mb-6 bg-red-50 border-2 border-red-200 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                    <div>
                        <h3 class="font-black text-red-800">Error!</h3>
                        <p class="text-red-700 text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-[2.5rem] shadow-sm p-8">
            
            {{-- SEARCH & FILTER BAR --}}
            <div class="flex flex-col md:flex-row gap-4 mb-6">
                {{-- Search --}}
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" 
                            id="searchInput" 
                            placeholder="Cari user berdasarkan nama atau email..."
                            class="w-full pl-12 pr-4 py-3 border-2 border-slate-200 rounded-xl font-semibold text-slate-700 focus:border-red-500 focus:outline-none transition-colors">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>
                
                {{-- Sort By --}}
                <div class="w-full md:w-64">
                    <select id="sortSelect" 
                        class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl font-semibold text-slate-700 focus:border-red-500 focus:outline-none transition-colors">
                        <option value="">Semua Role</option>
                        <option value="admin">Admin</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
            </div>

            {{-- USERS TABLE --}}
            <div class="overflow-x-auto">
                <table class="w-full table-fixed" id="usersTable">
                    <thead>
                        <tr class="border-b-2 border-slate-200">
                            <th class="text-left py-4 px-4 font-black text-slate-700 uppercase tracking-wider text-xs" style="width: 60px">No</th>
                            <th class="text-left py-4 px-4 font-black text-slate-700 uppercase tracking-wider text-xs" style="width: 250px">Nama</th>
                            <th class="text-left py-4 px-4 font-black text-slate-700 uppercase tracking-wider text-xs" style="width: 280px">Email</th>
                            <th class="text-left py-4 px-4 font-black text-slate-700 uppercase tracking-wider text-xs" style="width: 100px">Role</th>
                            <th class="text-left py-4 px-4 font-black text-slate-700 uppercase tracking-wider text-xs" style="width: 120px">Status</th>
                            <th class="text-center py-4 px-4 font-black text-slate-700 uppercase tracking-wider text-xs" style="width: 200px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        @forelse($users as $user)
                            @php
                                $hasLoans = \App\Models\Loan::where('user_id', $user->id)->exists();
                                $isSelf = $user->id === auth()->id();
                            @endphp
                            <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors user-row" 
                                data-name="{{ strtolower($user->name) }}" 
                                data-email="{{ strtolower($user->email) }}" 
                                data-role="{{ $user->role }}">
                                <td class="py-4 px-4 font-semibold text-slate-700">
                                    {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                            <span class="text-red-600 font-black text-sm">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-900">{{ $user->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <p class="font-semibold text-slate-700">{{ $user->email }}</p>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider
                                        {{ $user->role_id === 1 ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $user->role_id === 1 ? 'Admin' : 'Staff' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                            {{ $user->is_active ? 'checked' : '' }}
                                            {{ $isSelf ? 'disabled' : '' }}
                                            onchange="toggleUserStatus({{ $user->id }}, this.checked)"
                                            class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500 {{ $isSelf ? 'opacity-50 cursor-not-allowed' : '' }}"></div>
                                        <span id="status-text-{{ $user->id }}" class="ml-3 text-xs font-semibold {{ $user->is_active ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </label>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex justify-center gap-2">
                                        {{-- Edit --}}
                                        <button onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', {{ $user->role_id }})" 
                                            class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-lg transition-colors"
                                            title="Edit User">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        
                                        {{-- History --}}
                                        <button onclick="openHistoryModal({{ $user->id }})" 
                                            class="bg-green-500 hover:bg-green-600 text-white p-2 rounded-lg transition-colors"
                                            title="Riwayat Peminjaman">
                                            <i class="fas fa-history text-xs"></i>
                                        </button>
                                        
                                        {{-- Reset Password --}}
                                        <button onclick="resetPassword({{ $user->id }})" 
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded-lg transition-colors"
                                            title="Reset Password">
                                            <i class="fas fa-key text-xs"></i>
                                        </button>
                                        
                                        {{-- Delete --}}
                                        <button onclick="deleteUser({{ $user->id }}, '{{ $user->name }}', {{ $hasLoans ? 'true' : 'false' }}, {{ $isSelf ? 'true' : 'false' }})" 
                                            class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg transition-colors {{ $hasLoans || $isSelf ? 'opacity-50 cursor-not-allowed' : '' }}"
                                            title="{{ $hasLoans ? 'User memiliki riwayat peminjaman' : ($isSelf ? 'Tidak bisa hapus diri sendiri' : 'Hapus User') }}"
                                            {{ $hasLoans || $isSelf ? 'disabled' : '' }}>
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="h-20">
                                <td class="py-4 px-4 text-slate-400 text-center align-middle">-</td>
                                <td class="py-4 px-4 text-slate-400 text-center align-middle">
                                    <div class="text-slate-400">
                                        <i class="fas fa-users text-2xl mb-2"></i>
                                        <p class="font-bold uppercase tracking-wider">Belum ada data user</p>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-slate-400 text-center align-middle">-</td>
                                <td class="py-4 px-4 text-slate-400 text-center align-middle">-</td>
                                <td class="py-4 px-4 text-slate-400 text-center align-middle">-</td>
                                <td class="py-4 px-4 text-slate-400 text-center align-middle">-</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if($users->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $users->links('pagination::bootstrap-4') }}
                </div>
            @endif

            {{-- TOTAL INFO --}}
            <div class="mt-6 text-center text-sm text-slate-500">
                Menampilkan {{ $users->firstItem() }} - {{ $users->lastItem() }} dari {{ $users->total() }} user
            </div>
        </div>
    </div>
</div>

{{-- ================= MODALS ================= --}}
@include('users.edit')

{{-- USER HISTORY MODAL --}}
<div id="historyModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl p-8 m-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tighter">
                    Riwayat Peminjaman
                </h2>
                <p id="historyUserName" class="text-sm font-semibold text-slate-500 mt-1"></p>
            </div>
            <button onclick="closeHistoryModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="space-y-8">
            {{-- Active Loans --}}
            <div>
                <h3 class="text-lg font-black text-red-600 uppercase tracking-wider mb-4">
                    <i class="fas fa-clock mr-2"></i>Peminjaman Aktif
                </h3>
                <div id="activeLoans" class="space-y-3">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>

            {{-- Past History --}}
            <div>
                <h3 class="text-lg font-black text-slate-600 uppercase tracking-wider mb-4">
                    <i class="fas fa-history mr-2"></i>Riwayat Selesai
                </h3>
                <div id="pastHistory" class="space-y-3">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- JavaScript --}}
<script>
// Global variables
let currentEditingUserId = null;

// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#usersTableBody .user-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const name = row.dataset.name;
        const email = row.dataset.email;
        
        if (name.includes(searchTerm) || email.includes(searchTerm)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Handle no results message
    let noResultsRow = document.getElementById('noResultsRow');
    
    if (visibleCount === 0 && searchTerm !== '') {
        if (!noResultsRow) {
            noResultsRow = document.createElement('tr');
            noResultsRow.id = 'noResultsRow';
            noResultsRow.className = 'h-20';
            noResultsRow.innerHTML = `
                <td class="py-4 px-4 text-slate-400 text-center align-middle">-</td>
                <td class="py-4 px-4 text-slate-400 text-center align-middle">
                    <div class="text-slate-400">
                        <i class="fas fa-search text-2xl mb-2"></i>
                        <p class="font-bold uppercase tracking-wider">Tidak ada hasil pencarian</p>
                        <p class="text-sm">Untuk "${searchTerm}"</p>
                    </div>
                </td>
                <td class="py-4 px-4 text-slate-400 text-center align-middle">-</td>
                <td class="py-4 px-4 text-slate-400 text-center align-middle">-</td>
                <td class="py-4 px-4 text-slate-400 text-center align-middle">-</td>
                <td class="py-4 px-4 text-slate-400 text-center align-middle">-</td>
            `;
            document.getElementById('usersTableBody').appendChild(noResultsRow);
        }
    } else {
        if (noResultsRow) {
            noResultsRow.remove();
        }
    }
});

// Sort functionality
document.getElementById('sortSelect').addEventListener('change', function() {
    const sortValue = this.value;
    const rows = Array.from(document.querySelectorAll('#usersTableBody .user-row'));
    
    if (sortValue === '') {
        // Show all
        rows.forEach(row => row.style.display = '');
    } else {
        // Filter by role
        rows.forEach(row => {
            if (row.dataset.role === sortValue) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
});

// Modal functions
function openEditModal(userId) {
    currentEditingUserId = userId;
    
    // Load user data
    fetch(`/users/${userId}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('userName').value = data.name;
            document.getElementById('userEmail').value = data.email;
            document.getElementById('userRole').value = data.role_id;
            document.getElementById('userStatus').value = data.is_active ? '1' : '0';
            document.getElementById('userPassword').value = '';
            document.getElementById('userPasswordConfirmation').value = '';
            
            // Set form action
            document.getElementById('userForm').action = `/users/${userId}`;
            
            // Show modal
            document.getElementById('userModal').classList.remove('hidden');
            document.getElementById('userModal').classList.add('flex');
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal memuat data user',
                confirmButtonColor: '#E11D48'
            });
        });
}

function closeModal() {
    document.getElementById('userModal').classList.add('hidden');
    document.getElementById('userModal').classList.remove('flex');
    document.getElementById('userForm').reset();
}

function togglePassword(inputId, toggleId) {
    const input = document.getElementById(inputId);
    const toggle = document.getElementById(toggleId);
    
    if (input.type === 'password') {
        input.type = 'text';
        toggle.classList.remove('fa-eye');
        toggle.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        toggle.classList.remove('fa-eye-slash');
        toggle.classList.add('fa-eye');
    }
}

// User actions
function toggleUserStatus(userId, isActive) {
    fetch(`/users/${userId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI to reflect new status
            const checkbox = document.querySelector(`input[onchange*="toggleUserStatus(${userId}"]`);
            if (checkbox) {
                checkbox.checked = data.is_active;
            }
            
            // Update status text display
            const statusText = document.querySelector(`#status-text-${userId}`);
            if (statusText) {
                if (data.is_active) {
                    statusText.textContent = 'Aktif';
                    statusText.className = 'ml-3 text-xs font-semibold text-green-600';
                } else {
                    statusText.textContent = 'Nonaktif';
                    statusText.className = 'ml-3 text-xs font-semibold text-red-600';
                }
            }
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan server'
        });
    });
}

function resetPassword(userId) {
    Swal.fire({
        title: 'Reset Password?',
        text: 'Password akan direset ke: password123',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#E11D48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Reset',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/users/${userId}/reset-password`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            });
        }
    });
}

function deleteUser(userId, userName, hasLoans, isSelf) {
    if (isSelf) {
        Swal.fire({
            icon: 'error',
            title: 'Tidak Bisa Dihapus',
            text: 'Anda tidak bisa menghapus akun sendiri'
        });
        return;
    }
    
    if (hasLoans) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak Bisa Dihapus',
            text: 'User memiliki riwayat peminjaman. Disarankan untuk menonaktifkan akun saja.',
            confirmButtonColor: '#E11D48'
        });
        return;
    }
    
    Swal.fire({
        title: 'Hapus User?',
        html: `Apakah Anda yakin ingin menghapus user <strong>${userName}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#E11D48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
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
                        text: data.message
                    });
                }
            });
        }
    });
}

// Edit modal functions
function openEditModal(userId, userName, userEmail, userRole) {
    currentEditingUserId = userId;
    
    console.log('Opening edit modal with data:', {userId, userName, userEmail, userRole}); // Debug
    
    // Set form fields directly with passed data
    document.getElementById('userName').value = userName || '';
    document.getElementById('userEmail').value = userEmail || '';
    document.getElementById('userRole').value = userRole || '';
    
    // Set form action
    document.getElementById('userForm').action = `/users/${userId}`;
    
    // Show modal
    document.getElementById('userModal').classList.remove('hidden');
    document.getElementById('userModal').classList.add('flex');
}

function closeModal() {
    document.getElementById('userModal').classList.add('hidden');
    document.getElementById('userModal').classList.remove('flex');
    document.getElementById('userForm').reset();
}

// History modal
function openHistoryModal(userId) {
    fetch(`/users/${userId}/history`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('historyUserName').textContent = `User: ${data.user_name}`;
            
            // Active loans
            const activeLoansHtml = data.active_loans.length > 0 
                ? data.active_loans.map(loan => `
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-black text-slate-900">${loan.asset_name}</p>
                                <p class="text-sm text-slate-600">Kode: ${loan.asset_code}</p>
                                <p class="text-xs text-slate-500 mt-1">Pinjam: ${loan.borrow_date}</p>
                            </div>
                            <span class="px-3 py-1 bg-red-600 text-white rounded-full text-xs font-black">
                                Aktif
                            </span>
                        </div>
                    </div>
                `).join('')
                : '<p class="text-slate-400 text-center py-4">Tidak ada peminjaman aktif</p>';
            
            document.getElementById('activeLoans').innerHTML = activeLoansHtml;
            
            // Past history
            const pastHistoryHtml = data.past_history.length > 0
                ? data.past_history.map(loan => `
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-black text-slate-900">${loan.asset_name}</p>
                                <p class="text-sm text-slate-600">Kode: ${loan.asset_code}</p>
                                <div class="text-xs text-slate-500 mt-1">
                                    <p>Pinjam: ${loan.borrow_date}</p>
                                    <p>Kembali: ${loan.return_date}</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-green-600 text-white rounded-full text-xs font-black">
                                Selesai
                            </span>
                        </div>
                    </div>
                `).join('')
                : '<p class="text-slate-400 text-center py-4">Tidak ada riwayat peminjaman</p>';
            
            document.getElementById('pastHistory').innerHTML = pastHistoryHtml;
            
            // Show modal
            document.getElementById('historyModal').classList.remove('hidden');
            document.getElementById('historyModal').classList.add('flex');
        });
}

function closeHistoryModal() {
    document.getElementById('historyModal').classList.add('hidden');
    document.getElementById('historyModal').classList.remove('flex');
}

// Form validation
document.getElementById('userForm').addEventListener('submit', function(e) {
    const password = document.getElementById('userPassword').value;
    const passwordConfirmation = document.getElementById('userPasswordConfirmation').value;
    
    if (password && password !== passwordConfirmation) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Password Tidak Cocok',
            text: 'Password dan konfirmasi password harus sama'
        });
        return;
    }
    
    if (password && password.length < 8) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Password Terlalu Pendek',
            text: 'Password minimal 8 karakter'
        });
        return;
    }
});
</script>

@endsection
