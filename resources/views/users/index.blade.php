@extends('layouts.app')

@section('title', 'Master User')

@section('content')

<div class="p-6">

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-black">Master User</h1>

        <a href="{{ route('users.create') }}"
           class="bg-red-600 text-white px-5 py-2 rounded-xl text-xs font-bold hover:bg-red-700 transition">
            + Tambah User
        </a>
    </div>

    {{-- TABLE --}}
    <div class="bg-white p-6 rounded-2xl shadow">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="p-3">No</th>
                    <th class="p-3">Nama</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Role</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($users as $i => $user)
                <tr class="border-b hover:bg-gray-50">

                    {{-- FIX AMAN: tidak pakai currentPage --}}
                    <td class="p-3">
                        {{ $i + 1 }}
                    </td>

                    <td class="p-3">{{ $user->name }}</td>
                    <td class="p-3">{{ $user->email }}</td>

                    <td class="p-3">
                        <span class="px-2 py-1 rounded text-xs font-bold
                            {{ $user->role_id == 1 ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600' }}">
                            {{ $user->role_id == 1 ? 'Admin' : 'Staff' }}
                        </span>
                    </td>

                    {{-- AKSI --}}
                    <td class="p-3 flex gap-2 justify-center">

                        <a href="{{ route('users.edit', $user->id) }}"
                           class="bg-yellow-400 px-3 py-1 rounded text-xs font-bold hover:bg-yellow-500">
                            Edit
                        </a>

                        <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Yakin hapus user?')"
                                class="bg-red-600 text-white px-3 py-1 rounded text-xs font-bold hover:bg-red-700">
                                Hapus
                            </button>
                        </form>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center p-4 text-gray-400">
                        Data user kosong
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- PAGINATION (AMAN kalau paginate dipakai) --}}
        <div class="mt-4">
            {{ $users->links() }}
        </div>

    </div>

</div>

@endsection