@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto"
     x-data="{
        openModal: false,
        editModal: false,
        editData: {},
        search: '',
        selected: []
     }">

<!-- HEADER -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-extrabold text-red-600">Master Role</h1>
        <p class="text-sm text-slate-400">Manajemen role sistem</p>
    </div>

    <button @click="openModal = true"
        class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-xl text-sm font-bold shadow">
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

<!-- BUTTON HAPUS TERPILIH -->
<div class="flex justify-end mb-3">
    <button 
        @click="bulkDelete()"
        :class="selected.length > 0 
            ? 'bg-red-600 hover:bg-red-700 text-white opacity-100' 
            : 'bg-red-600 text-white opacity-40 cursor-not-allowed'"
        class="px-4 py-2 rounded-xl text-sm font-bold shadow transition-all duration-300"
        :disabled="selected.length === 0">
        
        <i class="fas fa-trash mr-2"></i> Hapus Terpilih
    </button>
</div>

<!-- TABLE -->
<div class="bg-white rounded-2xl shadow overflow-hidden">
<table class="w-full text-sm">

<thead class="bg-slate-50 text-slate-500 uppercase text-xs">
<tr>
    <th class="p-4">
        <input type="checkbox"
            @click="selected = selected.length === {{ $roles->count() }} ? [] : {{ $roles->pluck('id') }}">
    </th>
    <th class="p-4 text-left">No</th>
    <th class="p-4 text-left">Role</th>
    <th class="text-center">Total User</th>
    <th class="text-center">Aksi</th>
</tr>
</thead>

<tbody>
@foreach($roles as $index => $role)
<tr class="border-t hover:bg-slate-50">

    <!-- CHECKBOX -->
    <td class="p-4">
        @if($role->name !== 'Admin')
            <input type="checkbox" :value="{{ $role->id }}" x-model="selected">
        @endif
    </td>

    <!-- NO -->
    <td class="p-4 text-slate-500 font-semibold">
        {{ $roles->firstItem() + $index }}
    </td>

    <!-- ROLE -->
    <td class="p-4 font-semibold text-slate-700">
        {{ $role->name }}
    </td>

    <!-- TOTAL USER -->
    <td class="p-4 text-center">
        <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-xs font-bold">
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
                class="w-10 h-10 flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-md">
                <i class="fas fa-edit"></i>
            </button>

            <!-- DELETE -->
            @if($role->name == 'Admin')
                <div class="w-10 h-10 flex items-center justify-center bg-gray-300 text-white rounded-xl cursor-not-allowed">
                    <i class="fas fa-trash"></i>
                </div>
            @else
                <form action="{{ route('roles.destroy', $role->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="w-10 h-10 flex items-center justify-center bg-red-500 hover:bg-red-600 text-white rounded-xl">
                        <i class="fas fa-trash"></i>
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

@endsection

