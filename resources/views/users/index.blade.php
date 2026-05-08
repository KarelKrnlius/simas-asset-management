@extends('layouts.app')

@section('title', 'Master User SIMAS')

@section('content')
<div class="min-h-screen pt-1 items-start">
    <div class="container mx-auto px-4 py-8">
        {{-- HEADER, BUTTONS, AND CATATAN CONTAINER --}}
        <div class="bg-white rounded-[2rem] shadow-xl p-8 mb-8">
            {{-- HEADER --}}
            <div class="mb-6">
                <h2 class="text-3xl font-black text-red-600 uppercase tracking-tighter mb-2">
                    Master User 
                </h2>
                <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">
                    Kelola pengguna sistem SIMAS
                </p>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="flex flex-col sm:flex-row gap-4 mb-6">
                <a href="{{ route('users.create') }}" 
                    class="bg-red-600 hover:bg-red-700 text-white font-black text-sm uppercase tracking-wider px-6 py-3 rounded-xl transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-plus mr-2"></i> Tambah User
                </a>
            </div>
        </div>

    {{-- MAIN CONTAINER --}}
    <div class="container mx-auto px-4">
        
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
            
            <!-- Controls Bar -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <!-- Total User Count -->
                <div class="flex items-center gap-2">
                    <i class="fas fa-users text-red-primary"></i>
                    <span class="font-black text-slate-900">
                        Total User: <span class="text-red-primary">{{ $users->total() }}</span> user
                    </span>
                    <span class="text-sm text-slate-500">
                        (Menampilkan {{ $users->firstItem() }}-{{ $users->lastItem() }})
                    </span>
                </div>
                
                <!-- Sort and Bulk Actions -->
                <div class="flex flex-col lg:flex-row gap-3">
                    <!-- Search Input -->
                    <div class="flex-1">
                        <div class="relative flex-1">
                            <input type="text"
                                   id="searchInput"
                                   placeholder="Cari user berdasarkan nama, email, role, atau status..."
                                   value="{{ request('search') }}"
                                   class="w-full px-4 py-2 pr-10 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-red-primary focus:border-transparent"
                                   onkeypress="handleKeyPress(event)">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" onclick="performSearchFromInput()">
                                <i class="fas fa-search text-slate-400 hover:text-red-600"></i>
                            </div>
                        </div>
                    </div>
                    <button onclick="performRefresh()"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-semibold transition-colors flex items-center gap-2">
                        <i class="fas fa-sync-alt"></i>
                        Clear
                    </button>
                    
                    <!-- Sort Dropdown -->
                    <div class="relative">
                        <select id="sortSelect" onchange="applySorting()" 
                            class="appearance-none bg-white border border-slate-200 rounded-lg px-4 py-2 pr-8 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-red-primary focus:border-transparent">
                            <option value="latest" {{ request('sort_by') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="oldest" {{ request('sort_by') == 'created_at' && request('order') == 'asc' ? 'selected' : '' }}>Terlama</option>
                            <option value="name_asc" {{ request('sort_by') == 'name' && request('order') == 'asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                            <option value="name_desc" {{ request('sort_by') == 'name' && request('order') == 'desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                            @if($roles->count() > 0)
                                <optgroup label="role">
                                    @foreach($roles as $role)
                                        <option value="role_{{ $role->id }}" 
                                            {{ request('sort_by') == 'role_' . $role->id ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-700">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                    
                    <!-- Bulk Actions -->
                    <div class="flex gap-2">
                        <button onclick="showBulkDeleteModal()" id="bulkDeleteBtn" disabled
                            class="px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-trash mr-1"></i> Hapus Terpilih
                        </button>
                    </div>
                </div>
            </div>

            {{-- USERS TABLE --}}
            <div class="overflow-x-auto">
                <table class="w-full table-auto min-w-[800px]" id="usersTable">
                    <thead>
                        <tr class="border-b-2 border-slate-200">
                            <th class="text-center py-3 px-2 font-black text-slate-900 uppercase tracking-wider text-xs" style="min-width: 50px;">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()" 
                                    class="w-4 h-4 text-red-primary border-slate-300 rounded focus:ring-red-primary focus:ring-2">
                            </th>
                            <th class="text-center py-3 px-2 font-black text-slate-900 uppercase tracking-wider text-xs" style="min-width: 60px;">NO</th>
                            <th class="text-left py-3 px-2 font-black text-slate-900 uppercase tracking-wider text-xs" style="min-width: 180px;">Nama</th>
                            <th class="text-left py-3 px-2 font-black text-slate-900 uppercase tracking-wider text-xs" style="min-width: 200px;">Email</th>
                            <th class="text-center py-3 px-2 font-black text-slate-900 uppercase tracking-wider text-xs" style="min-width: 100px;">Role</th>
                            <th class="text-center py-3 px-2 font-black text-slate-900 uppercase tracking-wider text-xs" style="min-width: 100px;">Status</th>
                            <th class="text-center py-3 px-2 font-black text-slate-900 uppercase tracking-wider text-xs" style="min-width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        @forelse($users as $user)
                            @php
                                $hasLoans = \App\Models\Loan::where('user_id', $user->id)->exists();
                                $isSelf = $user->id === auth()->id();
                            @endphp
                            <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors user-row">
                                <td class="py-3 px-2 text-center">
                                    <input type="checkbox" class="user-checkbox" value="{{ $user->id }}" onchange="updateBulkDeleteButton()"
                                        class="w-4 h-4 text-red-primary border-slate-300 rounded focus:ring-red-primary focus:ring-2">
                                </td>
                                <td class="py-3 px-2 text-center">
                                    <span class="inline-block bg-slate-100 text-slate-700 px-2 py-1 rounded-lg font-bold text-xs">
                                        {{ ($users->currentPage() - 1) * $users->perPage() + $loop->index + 1 }}
                                    </span>
                                </td>
                                <td class="py-3 px-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <span class="text-red-600 font-black text-xs">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-black text-slate-900 text-sm truncate">{{ $user->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-2">
                                    <p class="font-semibold text-slate-700 text-sm truncate">{{ $user->email }}</p>
                                </td>
                                <td class="py-3 px-2 text-center">
                                    @php
                                        $roleColor = match(strtolower($user->role->name ?? '')) {
                                            'admin' => 'bg-purple-100 text-purple-700',
                                            default => 'bg-blue-100 text-blue-700',
                                        };
                                    @endphp
                                    <div class="flex justify-center">
                                        <span class="px-2 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $roleColor }}">
                                            {{ $user->role ? ucfirst($user->role->name) : 'Unknown' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3 px-2 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                            {{ $user->is_active ? 'checked' : '' }}
                                            {{ $isSelf ? 'disabled' : '' }}
                                            onchange="toggleUserStatus({{ $user->id }}, this.checked)"
                                            class="sr-only peer">
                                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500 {{ $isSelf ? 'opacity-50 cursor-not-allowed' : '' }}"></div>
                                        <span id="status-text-{{ $user->id }}" class="ml-3 text-xs font-semibold {{ $user->is_active ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </label>
                                </td>
                                <td class="py-3 px-2">
                                    <div class="flex justify-center gap-1 flex-wrap max-w-[120px]">
                                        {{-- Edit --}}
                                        <button onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', {{ $user->role_id }})" 
                                            class="bg-blue-500 hover:bg-blue-600 text-white p-1.5 rounded-lg transition-colors"
                                            title="Edit User">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        
                                        {{-- History --}}
                                        <button onclick="openHistoryModal({{ $user->id }})" 
                                            class="bg-green-500 hover:bg-green-600 text-white p-1.5 rounded-lg transition-colors"
                                            title="Riwayat Peminjaman">
                                            <i class="fas fa-history text-xs"></i>
                                        </button>
                                        
                                        {{-- Reset Password --}}
                                        <button onclick="resetPassword({{ $user->id }})" 
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white p-1.5 rounded-lg transition-colors"
                                            title="Reset Password">
                                            <i class="fas fa-key text-xs"></i>
                                        </button>
                                        
                                        {{-- Delete --}}
                                        <button onclick="deleteUser({{ $user->id }}, '{{ $user->name }}', {{ $hasLoans ? 'true' : 'false' }}, {{ $isSelf ? 'true' : 'false' }})" 
                                            class="bg-red-500 hover:bg-red-600 text-white p-1.5 rounded-lg transition-colors {{ $hasLoans || $isSelf ? 'opacity-50 cursor-not-allowed' : '' }}"
                                            title="{{ $hasLoans ? 'User memiliki riwayat peminjaman' : ($isSelf ? 'Tidak bisa hapus diri sendiri' : 'Hapus User') }}"
                                            {{ $hasLoans || $isSelf ? 'disabled' : '' }}>
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="h-20">
                                <td class="py-4 px-2 text-slate-400 text-center align-middle" colspan="7">
                                    <div class="text-slate-400">
                                        <i class="fas fa-users text-2xl mb-2"></i>
                                        <p class="font-bold uppercase tracking-wider">Belum ada data user</p>
                                    </div>
                                </td>
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

// Renumber rows based on visible rows
function renumberRows() {
    const rows = document.querySelectorAll('#usersTableBody .user-row');
    let visibleIndex = 1;
    
    rows.forEach(row => {
        const rowNumberCell = row.querySelector('.row-number');
        if (row.style.display !== 'none' && !row.style.display.includes('none')) {
            if (rowNumberCell) {
                rowNumberCell.textContent = visibleIndex;
            }
            visibleIndex++;
        }
    });
}

// Sort functionality
document.getElementById('sortSelect').addEventListener('change', function() {
    const sortValue = this.value;
    const rows = Array.from(document.querySelectorAll('#usersTableBody .user-row'));
    
    if (sortValue === '') {
        // Show all
        rows.forEach(row => row.style.display = '');
    } else {
        // Filter by role ID
        rows.forEach(row => {
            if (row.dataset.role === sortValue) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    // Renumber rows after filtering
    renumberRows();
});

// Call renumberRows on page load
document.addEventListener('DOMContentLoaded', function() {
    renumberRows();
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
            const allCheckboxes = document.querySelectorAll('input[type="checkbox"]');
            const checkbox = Array.from(allCheckboxes).find(cb => cb.getAttribute('onchange') && cb.getAttribute('onchange').includes(`toggleUserStatus(${userId}`));
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

// Master Asset JavaScript Functions - Adapted for Users
function performSearchFromInput() {
    const searchValue = document.getElementById('searchInput').value.trim();
    const currentUrl = new URL(window.location);
    
    // Remove page parameter to always start from page 1 when searching
    currentUrl.searchParams.delete('page');
    
    if (searchValue) {
        currentUrl.searchParams.set('search', searchValue);
    } else {
        currentUrl.searchParams.delete('search');
    }
    
    // Preserve other parameters
    const sortBy = currentUrl.searchParams.get('sort_by');
    const order = currentUrl.searchParams.get('order');
    
    window.location.href = currentUrl.toString();
}

function performRefresh() {
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.delete('page');
    currentUrl.searchParams.delete('search');
    currentUrl.searchParams.delete('sort_by');
    currentUrl.searchParams.delete('order');
    window.location.href = currentUrl.toString();
}

function handleKeyPress(event) {
    if (event.key === 'Enter') {
        performSearchFromInput();
    }
}

function applySorting() {
    const sortSelect = document.getElementById('sortSelect');
    const selectedValue = sortSelect.value;
    const currentUrl = new URL(window.location);
    
    // Clear existing sort parameters
    currentUrl.searchParams.delete('sort_by');
    currentUrl.searchParams.delete('order');
    
    if (selectedValue) {
        if (selectedValue === 'latest') {
            // Default - no parameters needed
        } else if (selectedValue === 'oldest') {
            currentUrl.searchParams.set('sort_by', 'created_at');
            currentUrl.searchParams.set('order', 'asc');
        } else if (selectedValue.startsWith('name_')) {
            currentUrl.searchParams.set('sort_by', 'name');
            currentUrl.searchParams.set('order', selectedValue === 'name_asc' ? 'asc' : 'desc');
        } else if (selectedValue.startsWith('role_')) {
            currentUrl.searchParams.set('sort_by', selectedValue);
        }
    }
    
    window.location.href = currentUrl.toString();
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    
    userCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateBulkDeleteButton();
}

function updateBulkDeleteButton() {
    const selectedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    
    if (selectedCheckboxes.length > 0) {
        bulkDeleteBtn.disabled = false;
        bulkDeleteBtn.innerHTML = `<i class="fas fa-trash mr-1"></i> Hapus Terpilih (${selectedCheckboxes.length})`;
    } else {
        bulkDeleteBtn.disabled = true;
        bulkDeleteBtn.innerHTML = '<i class="fas fa-trash mr-1"></i> Hapus Terpilih';
    }
}

function showBulkDeleteModal() {
    const selectedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
    
    if (selectedCheckboxes.length === 0) {
        alert('Pilih minimal satu user untuk dihapus');
        return;
    }
    
    const userIds = Array.from(selectedCheckboxes).map(cb => cb.value);
    document.getElementById('bulkDeleteCount').textContent = userIds.length;
    document.getElementById('bulkDeleteUserIds').value = JSON.stringify(userIds);
    
    document.getElementById('bulkDeleteModal').classList.remove('hidden');
    document.getElementById('bulkDeleteModal').classList.add('flex');
}

function closeBulkDeleteModal() {
    document.getElementById('bulkDeleteModal').classList.add('hidden');
    document.getElementById('bulkDeleteModal').classList.remove('flex');
}

function confirmBulkDelete() {
    const userIds = JSON.parse(document.getElementById('bulkDeleteUserIds').value);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!csrfToken) {
        alert('CSRF token tidak ditemukan');
        return;
    }
    
    // Store current sorting parameters before deletion
    const currentUrl = new URL(window.location);
    sessionStorage.setItem('currentSort', currentUrl.searchParams.toString());
    
    // Create form data for bulk delete
    const formData = new FormData();
    formData.append('_token', csrfToken);
    formData.append('user_ids', JSON.stringify(userIds));
    
    // Show loading
    const deleteBtn = event.target;
    const originalText = deleteBtn.innerHTML;
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menghapus...';
    deleteBtn.disabled = true;
    
    // Send bulk delete request
    fetch('/users/bulk-delete', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Store success message for post-refresh notification
            sessionStorage.setItem('bulkDeleteSuccess', 'true');
            sessionStorage.setItem('bulkDeleteMessage', data.message);
            
            // Close modal and hard refresh
            closeBulkDeleteModal();
            
            setTimeout(() => {
                location.reload(true);
            }, 500);
        } else {
            // Restore button
            deleteBtn.innerHTML = originalText;
            deleteBtn.disabled = false;
            
            alert('Gagal menghapus user: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error deleting users:', error);
        
        // Restore button
        deleteBtn.innerHTML = originalText;
        deleteBtn.disabled = false;
        
        alert('Terjadi kesalahan saat menghapus user: ' + error.message);
    });
}

// Check for bulk delete success on page load
document.addEventListener('DOMContentLoaded', function() {
    const bulkDeleteSuccess = sessionStorage.getItem('bulkDeleteSuccess');
    const bulkDeleteMessage = sessionStorage.getItem('bulkDeleteMessage');
    
    if (bulkDeleteSuccess === 'true' && bulkDeleteMessage) {
        // Create and show notification
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 1rem;
            right: 1rem;
            background-color: #10b981;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            z-index: 9999;
            transform: translateX(100%);
            transition: transform 0.3s ease-out;
        `;
        
        notification.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <path d="m22 4-10 10.01L7 9.01"/>
            </svg>
            <span class="font-medium">${bulkDeleteMessage}</span>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
        
        // Clear sessionStorage
        sessionStorage.removeItem('bulkDeleteSuccess');
        sessionStorage.removeItem('bulkDeleteMessage');
    }
    
    // Initialize bulk delete button state
    updateBulkDeleteButton();
});

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