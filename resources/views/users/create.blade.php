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

    {{-- ERROR --}}
    @if ($errors->any())
        <div class="mb-4 bg-red-100 text-red-700 p-3 rounded">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

</div>

{{-- ================= MODAL ================= --}}
<div id="userModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="bg-white w-full max-w-lg rounded-2xl p-6 shadow-xl">

        <h2 class="text-xl font-black mb-4">Tambah User</h2>

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            {{-- NAMA --}}
            <input name="name" placeholder="Nama"
                class="border p-3 w-full mb-3 rounded">

            {{-- EMAIL --}}
            <input name="email" placeholder="Email"
                class="border p-3 w-full mb-3 rounded">

            {{-- PASSWORD --}}
            <div class="relative mb-3">
                <input id="password" name="password" type="password"
                    placeholder="Password (opsional)"
                    class="border p-3 w-full rounded pr-10">

                <button type="button" onclick="togglePassword()"
                    class="absolute right-3 top-3 text-gray-500">
                    👁️
                </button>
            </div>

            {{-- ROLE --}}
            <select name="role_id" class="border p-3 w-full mb-4 rounded">
                <option value="">-- Pilih Role --</option>
                <option value="1">Admin</option>
                <option value="2">Staff</option>
            </select>

            {{-- BUTTON --}}
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

{{-- ================= SCRIPT ================= --}}
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

    if (input.type === "password") {
        input.type = "text";
    } else {
        input.type = "password";
    }
}
</script>

@endsection