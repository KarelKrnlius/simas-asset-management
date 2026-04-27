@extends('layouts.app')

@section('title', 'Master User')

@section('content')
<div class="w-full px-8 py-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-black">Master User</h1>

        <button onclick="openModal()" 
            class="bg-red-600 text-white px-5 py-2 rounded-xl text-sm font-bold">
            + Tambah User
        </button>
    </div>

    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="mb-4 bg-green-100 text-green-700 p-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- ERROR --}}
    @if ($errors->any())
        <div class="mb-4 bg-red-100 text-red-700 p-3 rounded">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- TABLE --}}
    <div class="bg-white p-6 rounded-xl shadow">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="p-3">No</th>
                    <th class="p-3">Nama</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Role</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $i => $user)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ $i + 1 }}</td>
                    <td class="p-3">{{ $user->name }}</td>
                    <td class="p-3">{{ $user->email }}</td>
                    <td class="p-3">
                        {{ $user->role_id == 1 ? 'Admin' : 'Staff' }}
                    </td>
                    <td class="p-3">
                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </td>
                    <td class="p-3 flex gap-2">

                        {{-- RESET PASSWORD --}}
                        <form action="/users/{{ $user->id }}/reset-password" method="POST">
                            @csrf
                            <button class="bg-yellow-500 text-white px-2 py-1 rounded text-xs">
                                🔑
                            </button>
                        </form>

                        {{-- DELETE --}}
                        <form action="/users/{{ $user->id }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-500 text-white px-2 py-1 rounded text-xs">
                                🗑️
                            </button>
                        </form>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center p-4 text-gray-400">
                        Data user kosong
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- PAGINATION --}}
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>

</div>

{{-- ================= MODAL ================= --}}
<div id="userModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="bg-white w-full max-w-lg rounded-2xl p-6 shadow-xl">

        <h2 class="text-xl font-black mb-4">Tambah User</h2>

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <input name="name" placeholder="Nama"
                class="border p-3 w-full mb-3 rounded">

            <input name="email" placeholder="Email"
                class="border p-3 w-full mb-1 rounded">

            @error('email')
                <div class="text-red-500 text-sm mb-2">
                    {{ $message }}
                </div>
            @enderror

            {{-- 🔥 PASSWORD UPDATED --}}
            <div class="relative mb-3">
                <input id="password" name="password" type="password"
                    placeholder="Password (opsional)"
                    class="border p-3 w-full rounded pr-16">

                <button type="button" onclick="togglePassword()" id="toggleText"
                    class="absolute right-3 top-3 text-sm text-blue-600 font-semibold">
                    Show
                </button>
            </div>

            <select name="role_id" class="border p-3 w-full mb-4 rounded">
                <option value="">-- Pilih Role --</option>
                <option value="1">Admin</option>
                <option value="2">Staff</option>
            </select>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal()"
                    class="bg-gray-300 px-4 py-2 rounded">
                    Batal
                </button>

                <button class="bg-red-600 text-white px-4 py-2 rounded">
                    Simpan
                </button>
            </div>

        </form>
    </div>
</div>

{{-- SCRIPT --}}
<script>
function openModal() {
    document.getElementById('userModal').classList.remove('hidden');
    document.getElementById('userModal').classList.add('flex');
}

function closeModal() {
    document.getElementById('userModal').classList.add('hidden');
}

function togglePassword() {
    const input = document.getElementById('password');
    const text = document.getElementById('toggleText');

    if (input.type === "password") {
        input.type = "text";
        text.innerText = "Hide";
    } else {
        input.type = "password";
        text.innerText = "Show";
    }
}
</script>

{{-- AUTO OPEN MODAL JIKA ERROR --}}
@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        openModal();
    });
</script>
@endif

@endsection