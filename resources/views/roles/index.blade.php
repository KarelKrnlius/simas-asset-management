@extends('layouts.app')
@section('title', 'Master Role')

@section('content')
<div class="w-full"
    x-data="{
        selected: [],
        openModal: {{ $errors->any() ? 'true' : 'false' }},
        editModal: false,
        editData: {}
    }">

    {{-- HEADER --}}
    <div class="bg-white rounded-2xl shadow-xl p-6 lg:p-8 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-0">
            <div>
                <h1 class="text-2xl lg:text-3xl font-black text-red-600 uppercase tracking-tighter">Master Role</h1>
                <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider mt-1">Manajemen role sistem</p>
            </div>
            <button @click="openModal = true"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-3 rounded-xl font-bold text-sm transition flex items-center gap-2 self-start sm:self-auto">
                <i class="fas fa-plus"></i> Tambah Role
            </button>
        </div>
    </div>

    {{-- MAIN TABLE --}}
    <div class="bg-white rounded-2xl shadow-xl p-4 lg:p-8">

        {{-- CONTROL BAR --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-5">
            <div class="flex items-center gap-2 flex-wrap">
                <i class="fas fa-user-shield text-slate-700"></i>
                <span class="font-bold text-slate-900 text-sm">
                    Total Role: <span class="text-red-600">{{ $roles->total() }}</span>
                </span>
                <span class="text-xs text-slate-500">({{ $roles->firstItem() }}-{{ $roles->lastItem() }})</span>
            </div>

            <form method="GET" action="{{ route('roles.index') }}" class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <input type="text" name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari role..."
                        class="w-40 sm:w-52 px-3 py-2 pr-8 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-red-500">
                    <i class="fas fa-search absolute right-2 top-2.5 text-slate-400 text-xs"></i>
                </div>

                <a href="{{ route('roles.index') }}"
                    class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-xs rounded-lg flex items-center gap-1 whitespace-nowrap">
                    <i class="fas fa-sync-alt text-xs"></i>
                    Clear
                </a>

                <select name="sort" onchange="this.form.submit()"
                    class="border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none">
                    <option value="az"     {{ request('sort', 'az') == 'az'     ? 'selected' : '' }}>A - Z</option>
                    <option value="za"     {{ request('sort') == 'za'           ? 'selected' : '' }}>Z - A</option>
                    <option value="oldest" {{ request('sort') == 'oldest'       ? 'selected' : '' }}>Terlama</option>
                    <option value="newest" {{ request('sort') == 'newest'       ? 'selected' : '' }}>Terbaru</option>
                </select>

                <button type="button"
                    @click="bulkDelete()"
                    :disabled="selected.length === 0"
                    :class="selected.length > 0 ? 'opacity-100' : 'opacity-50 cursor-not-allowed'"
                    class="px-3 py-2 bg-red-100 text-red-600 rounded-lg text-xs flex items-center gap-1 whitespace-nowrap">
                    <i class="fas fa-trash text-xs"></i>
                    <span class="hidden sm:inline">Hapus Terpilih</span>
                </button>
            </form>
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="w-full min-w-[400px]">
                <thead>
                    <tr class="border-b-2 border-slate-200">
                        <th class="py-3 px-3 text-center w-10">
                            <input type="checkbox"
                                @click="selected = selected.length === {{ $roles->count() }} ? [] : @json($roles->pluck('id'))"
                                class="w-4 h-4 rounded border-slate-300">
                        </th>
                        <th class="text-center py-3 px-3 font-black text-xs uppercase w-14">No</th>
                        <th class="text-left py-3 px-3 font-black text-xs uppercase">Role</th>
                        <th class="text-center py-3 px-3 font-black text-xs uppercase w-20">User</th>
                        <th class="text-center py-3 px-3 font-black text-xs uppercase w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $index => $role)
                    <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                        <td class="py-3 px-3 text-center">
                            @if($role->name !== 'Admin')
                                <input type="checkbox"
                                    :value="{{ $role->id }}"
                                    x-model="selected"
                                    class="w-4 h-4 rounded border-slate-300">
                            @endif
                        </td>
                        <td class="py-3 px-3 text-center">
                            <span class="inline-block bg-slate-100 text-slate-700 px-2 py-1 rounded-lg font-bold text-xs">
                                {{ $roles->firstItem() + $index }}
                            </span>
                        </td>
                        <td class="py-3 px-3">
                            <span class="font-bold text-slate-900 text-sm">{{ $role->name }}</span>
                        </td>
                        <td class="py-3 px-3 text-center">
                            <span class="bg-blue-100 text-blue-600 px-2.5 py-1 rounded-full text-xs font-bold">
                                {{ $role->users_count }}
                            </span>
                        </td>
                        <td class="py-3 px-3 text-center">
                            <div class="flex justify-center gap-2">
                                <button
                                    @click='editModal = true; editData = @json($role)'
                                    class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-lg transition-colors"
                                    title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>

                                @if($role->name == 'Admin')
                                    <button class="bg-slate-200 text-slate-400 p-2 rounded-lg cursor-not-allowed" title="Tidak bisa dihapus" disabled>
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                @else
                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Hapus role ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg transition-colors" title="Hapus">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-12 text-slate-400">
                            <i class="fas fa-user-shield text-3xl mb-3"></i>
                            <p class="font-bold uppercase tracking-wider text-sm">Belum ada data role</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($roles->hasPages())
        <div class="flex flex-wrap justify-between items-center mt-6 pt-6 border-t border-slate-200 gap-3">
            <div class="text-sm text-slate-600">
                Menampilkan {{ $roles->firstItem() }}-{{ $roles->lastItem() }} dari {{ $roles->total() }} role
            </div>
            <div class="flex flex-wrap gap-2">
                @if($roles->onFirstPage())
                    <button class="px-3 py-2 bg-slate-100 text-slate-400 rounded-lg text-sm font-semibold cursor-not-allowed" disabled>
                        <i class="fas fa-chevron-left mr-1"></i>Prev
                    </button>
                @else
                    <a href="{{ $roles->previousPageUrl() }}" class="px-3 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-50">
                        <i class="fas fa-chevron-left mr-1"></i>Prev
                    </a>
                @endif
                @if($roles->hasMorePages())
                    <a href="{{ $roles->nextPageUrl() }}" class="px-3 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-50">
                        Next<i class="fas fa-chevron-right ml-1"></i>
                    </a>
                @else
                    <button class="px-3 py-2 bg-slate-100 text-slate-400 rounded-lg text-sm font-semibold cursor-not-allowed" disabled>
                        Next<i class="fas fa-chevron-right ml-1"></i>
                    </button>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- MODAL TAMBAH ROLE --}}
    <div x-show="openModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white p-6 rounded-2xl w-full max-w-sm shadow-2xl">
            <h2 class="font-black text-lg text-slate-900 mb-4">Tambah Role</h2>
            <form method="POST" action="{{ route('roles.store') }}">
                @csrf
                <input type="text" name="name" placeholder="Nama Role"
                    value="{{ old('name') }}"
                    class="w-full border border-slate-200 px-4 py-3 mb-1 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                @error('name')
                    <p class="text-red-500 text-xs mb-2">{{ $message }}</p>
                @enderror
                <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-xl text-sm font-bold w-full mt-3 transition-colors">
                    Simpan
                </button>
            </form>
            <button @click="openModal = false" class="mt-3 text-xs text-slate-500 w-full hover:text-slate-700">
                Tutup
            </button>
        </div>
    </div>

    {{-- MODAL EDIT ROLE --}}
    <div x-show="editModal"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white p-6 rounded-2xl w-full max-w-sm shadow-2xl">
            <h2 class="font-black text-lg text-slate-900 mb-4">Edit Role</h2>
            <form method="POST" :action="`{{ url('roles') }}/${editData.id}`">
                @csrf
                @method('PUT')
                <input type="text" name="name" x-model="editData.name"
                    class="w-full border border-slate-200 px-4 py-3 mb-3 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-xl text-sm font-bold w-full transition-colors">
                    Update
                </button>
            </form>
            <button @click="editModal = false" class="mt-3 text-xs text-slate-500 w-full hover:text-slate-700">
                Tutup
            </button>
        </div>
    </div>

</div>

<script>
function bulkDelete() {
    const checkboxes = document.querySelectorAll('input[type=checkbox][x-model]:checked');
    if (checkboxes.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Pilih dulu!', text: 'Pilih minimal satu role untuk dihapus.', confirmButtonColor: '#E11D48' });
        return;
    }

    Swal.fire({
        title: 'Hapus data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#E11D48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const ids = Array.from(checkboxes).map(el => el.value).filter(Boolean);
            fetch('/roles/bulk-delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ids })
            }).then(() => location.reload());
        }
    });
}
</script>
@endsection
