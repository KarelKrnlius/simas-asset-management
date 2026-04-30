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
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800">Role Master</h1>
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
                    <th class="p-4 text-left">Role</th>
                    <th>Total User</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($roles as $role)
                <tr x-show="search === '' || '{{ strtolower($role->name) }}'.includes(search.toLowerCase())"
                    class="border-t hover:bg-slate-50 transition">

                    <!-- ROLE -->
                    <td class="p-4 font-semibold text-slate-700 flex items-center gap-2">
                        {{ $role->name }}

                        @if($role->name == 'Admin')
                            <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded-full font-bold">
                                UTAMA
                            </span>
                        @endif
                    </td>

                    <!-- USER COUNT -->
                    <td class="p-4">
                        <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-xs font-bold">
                            {{ $role->users_count }} User
                        </span>
                    </td>

                    <!-- ACTION -->
                    <td class="p-4 text-center space-x-2">

                        <!-- EDIT -->
                        <button 
                            @click="
                                editModal = true;
                                editData = {
                                    id: {{ $role->id }},
                                    name: '{{ $role->name }}'
                                }
                            "
                            class="text-blue-500 hover:underline text-xs font-bold">
                            Edit
                        </button>

                        <!-- DELETE LOGIC -->
                        @if($role->name == 'Admin')
                            <button class="text-gray-400 text-xs cursor-not-allowed">
                                Tidak bisa dihapus
                            </button>
                        @else
                            <button onclick="confirmDelete({{ $role->id }})"
                                class="text-red-500 hover:underline text-xs font-bold">
                                Hapus
                            </button>
                        @endif

                        <form id="delete-{{ $role->id }}" action="{{ route('roles.destroy', $role->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                        </form>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center py-10 text-slate-400">
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