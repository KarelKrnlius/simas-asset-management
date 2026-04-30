@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto"
     x-data="{
        openModal: false,
        editModal: false,
        editData: {},
        search: ''
     }">

    <!-- HEADER -->
   <!-- HEADER -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-extrabold text-red-600">Master Role</h1>
        <p class="text-sm text-slate-400">Manajemen role sistem</p>
    </div>

    <button @click="openModal = true"
        class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-xl text-sm font-bold shadow transition hover:scale-105">
        + Tambah Role
    </button>
</div>
    <!-- STATS -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl shadow">
            <p class="text-xs text-slate-400">Total Role</p>
            <h2 class="text-xl font-bold">{{ $roles->count() }}</h2>
        </div>
        <div class="bg-white p-4 rounded-xl shadow">
            <p class="text-xs text-slate-400">Total User</p>
            <h2 class="text-xl font-bold">{{ $roles->sum('users_count') }}</h2>
        </div>
    </div>

    <!-- SEARCH -->
    <input type="text"
        x-model="search"
        placeholder="Cari role..."
        class="w-full md:w-1/3 border border-slate-200 rounded-xl px-4 py-2 text-sm mb-4 focus:ring-2 focus:ring-red-500 outline-none">

    <!-- TABLE -->
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="w-full text-sm">
           <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
    <tr>
        <th class="p-4 text-left">No</th>
        <th class="p-4 text-left">Role</th>
        <th class="text-center">Total User</th>
        <th class="text-center">Aksi</th>
    </tr>
</thead>

<tbody>
@forelse($roles as $index => $role)
<tr class="border-t hover:bg-slate-50 transition">

    <!-- NO (AUTO SESUAI PAGINATION) -->
    <td class="p-4 text-slate-500 font-semibold">
        {{ $roles->firstItem() + $index }}
    </td>

    <!-- ROLE -->
    <td class="p-4 font-semibold text-slate-700">
        {{ $role->name }}
    </td>

    <!-- TOTAL USER -->
    <td class="p-4 text-center align-middle">
        <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-xs font-bold inline-flex items-center justify-center">
            {{ $role->users_count }} User
        </span>
    </td>

    <!-- ACTION -->
    <td class="p-4 text-center">
        <div class="flex justify-center items-center gap-3">

            <!-- EDIT -->
            <button 
                @click="
                    editModal = true;
                    editData = {
                        id: {{ $role->id }},
                        name: '{{ $role->name }}'
                    }
                "
                class="w-10 h-10 flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-md transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1">
                <i class="fas fa-edit"></i>
            </button>

            <!-- DELETE -->
            @if($role->name == 'Admin')
                <div class="w-10 h-10 flex items-center justify-center bg-gray-300 text-white rounded-xl cursor-not-allowed">
                    <i class="fas fa-trash"></i>
                </div>
            @else
                <button onclick="confirmDelete({{ $role->id }})"
                    class="w-10 h-10 flex items-center justify-center bg-red-500 hover:bg-red-600 text-white rounded-xl shadow-md transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-trash"></i>
                </button>
            @endif

        </div>
    </td>

</tr>
@empty
<tr>
    <td colspan="4" class="text-center py-10 text-slate-400">
        Belum ada role
    </td>
</tr>
@endforelse
</tbody>
        </table>
    </div>

    <!-- MODAL TAMBAH -->
    <div x-show="openModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
            <h2 class="text-lg font-bold mb-4">Tambah Role</h2>

            <form action="{{ route('roles.store') }}" method="POST">
                @csrf

                <input name="name" placeholder="Nama Role"
                    class="border p-2 w-full mb-3 rounded">

                <div class="flex justify-end gap-2">
                    <button type="button" @click="openModal=false"
                        class="px-4 py-2 bg-gray-200 rounded">Batal</button>

                    <button class="px-4 py-2 bg-red-600 text-white rounded">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div x-show="editModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
            <h2 class="text-lg font-bold mb-4">Edit Role</h2>

            <form :action="'/roles/' + editData.id" method="POST">
                @csrf
                @method('PUT')

                <input name="name" x-model="editData.name"
                    class="border p-2 w-full mb-3 rounded">

                <div class="flex justify-end gap-2">
                    <button type="button" @click="editModal=false"
                        class="px-4 py-2 bg-gray-200 rounded">Batal</button>

                    <button class="px-4 py-2 bg-blue-600 text-white rounded">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<!-- SWEET ALERT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Hapus role?',
        text: "Data tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Ya hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-' + id).submit();
        }
    });
}
</script>
@endsection