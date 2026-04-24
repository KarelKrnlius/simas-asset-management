@extends('layouts.app')

@section('title', 'Edit User')

@section('content')

<div class="p-6 flex justify-center">

    <div class="w-full max-w-lg bg-white p-6 rounded-2xl shadow">

        <h2 class="text-xl font-black mb-4">Edit User</h2>

        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- NAMA --}}
            <div class="mb-4">
                <label class="text-sm font-bold">Nama</label>
                <input type="text" name="name" value="{{ $user->name }}"
                       class="w-full p-2 border rounded">
            </div>

            {{-- EMAIL --}}
            <div class="mb-4">
                <label class="text-sm font-bold">Email</label>
                <input type="email" name="email" value="{{ $user->email }}"
                       class="w-full p-2 border rounded">
            </div>

            {{-- ROLE --}}
            <div class="mb-4">
                <label class="text-sm font-bold">Role</label>
                <select name="role_id" class="w-full p-2 border rounded">
                    <option value="1" {{ $user->role_id == 1 ? 'selected' : '' }}>Admin</option>
                    <option value="2" {{ $user->role_id == 2 ? 'selected' : '' }}>Staff</option>
                </select>
            </div>

            {{-- BUTTON --}}
            <div class="flex justify-between">
                <a href="{{ route('users') }}"
                   class="bg-gray-400 text-white px-4 py-2 rounded">
                    Kembali
                </a>

                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded">
                    Update
                </button>
            </div>

        </form>

    </div>

</div>

@endsection