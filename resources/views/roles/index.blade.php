@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col items-start pt-4 px-6"
     x-data="{ 
        selected: [], 
        openModal: false, 
        editModal: false, 
        editData: {} 
     }">

<!-- HEADER -->
<div class="w-full max-w-7xl mx-auto mb-6">
    <div class="flex justify-between items-center">
        <div> 
            <h1 class="text-3xl font-black text-red-600 uppercase tracking-tighter">
                Master Role
            </h1>
            <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider mt-1">
                Manajemen role sistem
            </p>
        </div>
        
        <button @click="openModal = true"
            class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-xl font-bold text-sm transition">
            <i class="fas fa-plus mr-2"></i> Tambah Role
        </button>
    </div>
</div>

<!-- MAIN -->
<div class="w-full max-w-7xl mx-auto">
<div class="bg-white rounded-[2rem] shadow-sm p-6">

<!-- CONTROL BAR -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-5">

    <!-- TOTAL -->
    <div class="flex items-center gap-2">
        <i class="fas fa-user-shield text-black"></i>
        <span class="font-bold text-slate-900 text-sm">
            Total Role: <span class="text-black">{{ $roles->total() }}</span>
        </span>
        <span class="text-xs text-slate-500">
            ({{ $roles->firstItem() }}-{{ $roles->lastItem() }})
        </span>
    </div>

    <!-- RIGHT -->
    <form method="GET" action="{{ route('roles.index') }}" class="flex flex-wrap items-center gap-2">

        <!-- SEARCH -->
        <div class="relative">
            <input type="text" name="search"
                value="{{ request('search') }}"
                placeholder="Cari role..."
                class="w-52 px-3 py-2 pr-8 border border-slate-200 rounded-lg text-xs">
            <i class="fas fa-search absolute right-2 top-2.5 text-slate-400 text-xs"></i>
        </div>

        <!-- CLEAR -->
        <a href="{{ route('roles.index') }}"
            class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-xs rounded-lg flex items-center gap-1">
            <i class="fas fa-sync-alt text-xs"></i>
            Clear
        </a>

        <!-- SORT -->
        <select name="sort" onchange="this.form.submit()"
            class="border border-slate-200 rounded-lg px-3 py-2 text-xs">
            <option value="">Terbaru</option>
            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>
                Terlama
            </option>
        </select>

        <!-- HAPUS TERPILIH -->
        <button type="button"
            @click="bulkDelete()"
            :class="selected.length > 0 
                ? 'bg-red-100 text-red-600 opacity-100' 
                : 'bg-red-100 text-red-400 opacity-50 cursor-not-allowed'"
            class="px-3 py-2 rounded-lg text-xs flex items-center gap-1"
            :disabled="selected.length === 0">

            <i class="fas fa-trash text-xs"></i>
            Hapus Terpilih
        </button>

    </form>
</div>

<!-- TABLE -->
<div class="overflow-x-auto">
<table class="w-full text-sm">

<thead>
<tr class="border-b border-slate-200">
    <th class="py-3 px-2 text-center w-[40px]">
        <input type="checkbox"
            @click="selected = selected.length === {{ $roles->count() }} ? [] : {{ $roles->pluck('id') }}"
            class="w-4 h-4">
    </th>

    <th class="py-3 px-2 text-center text-xs font-bold w-[50px]">NO</th>
    <th class="py-3 px-2 text-left text-xs font-bold">ROLE</th>
    <th class="py-3 px-2 text-center text-xs font-bold">USER</th>
    <th class="py-3 px-2 text-center text-xs font-bold">AKSI</th>
</tr>
</thead>

<tbody>
@foreach($roles as $index => $role)
<tr class="border-b hover:bg-slate-50">

    <!-- CHECK -->
    <td class="py-3 px-2 text-center">
        @if($role->name !== 'Admin')
            <input type="checkbox"
                :value="{{ $role->id }}"
                x-model="selected"
                class="w-4 h-4">
        @endif
    </td>

    <!-- NO -->
    <td class="py-3 px-2 text-center text-slate-600">
        {{ $roles->firstItem() + $index }}
    </td>

    <!-- ROLE -->
    <td class="py-3 px-2 font-semibold text-slate-700">
        {{ $role->name }}
    </td>

    <!-- USER -->
    <td class="py-3 px-2 text-center">
        <span class="bg-blue-100 text-blue-600 px-2 py-1 rounded-full text-xs font-bold">
            {{ $role->users_count }}
        </span>
    </td>

    <!-- AKSI -->
    <td class="py-3 px-2 text-center">
        <div class="flex justify-center gap-2">

            <!-- EDIT -->
            <button 
                @click='editModal = true; editData = @json($role)'
                class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-lg">
                <i class="fas fa-edit text-xs"></i>
            </button>

            <!-- DELETE -->
            @if($role->name == 'Admin')
                <button class="bg-gray-300 text-white p-2 rounded-lg cursor-not-allowed">
                    <i class="fas fa-trash text-xs"></i>
                </button>
            @else
                <form action="{{ route('roles.destroy', $role->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </form>
            @endif

        </div>
    </td>

</tr>
@endforeach
</tbody>

</table>
</div>

</div>
</div>

<!-- SCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function bulkDelete() {
    let selected = document.querySelectorAll('input[type=checkbox]:checked');

    if (selected.length === 0) {
        Swal.fire('Pilih dulu!');
        return;
    }

    Swal.fire({
        title: 'Hapus data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya'
    }).then((result) => {
        if (result.isConfirmed) {

            let ids = [];
            selected.forEach(el => {
                if(el.value) ids.push(el.value);
            });

            fetch('/roles/bulk-delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ids: ids })
            }).then(() => location.reload());
        }
    });
}
</script>

<!-- MODAL TAMBAH ROLE -->
<div x-show="openModal" 
     x-transition
     class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

    <div class="bg-white p-6 rounded-xl w-96 shadow-lg">
        <h2 class="font-bold mb-4 text-lg">Tambah Role</h2>

        <form method="POST" action="{{ route('roles.store') }}">
            @csrf

            <input type="text" name="name" placeholder="Nama Role"
                class="w-full border p-2 mb-3 rounded-lg text-sm">

            <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm w-full">
                Simpan
            </button>
        </form>

        <button @click="openModal = false"
            class="mt-3 text-xs text-slate-500 w-full">
            Tutup
        </button>
    </div>
</div>

<!-- MODAL EDIT ROLE -->
<div x-show="editModal" x-cloak
     class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

    <div class="bg-white p-6 rounded-xl w-96">
        <h2 class="font-bold mb-4">Edit Role</h2>

        <form method="POST" :action="`{{ url('roles') }}/${editData.id}`">
            @csrf
            @method('PUT')

            <input type="text" name="name" x-model="editData.name"
                class="w-full border p-2 mb-3 rounded">

            <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">
                Update
            </button>
        </form>

        <button @click="editModal = false"
            class="mt-2 text-sm text-gray-500 w-full">
            Tutup
        </button>
    </div>
</div>

@endsection