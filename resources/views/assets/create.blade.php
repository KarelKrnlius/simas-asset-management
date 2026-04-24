@extends('layouts.app')

@section('title', 'Tambah Asset')

@section('content')

<div class="p-6 flex justify-center">

<div class="w-full max-w-lg bg-white p-6 rounded-2xl shadow">

    <h2 class="text-xl font-black mb-4">Tambah Asset</h2>

    <form method="POST" action="{{ route('assets.store') }}">
        @csrf

        {{-- NAMA --}}
        <div class="mb-3">
            <label>Nama Barang</label>
            <input type="text" name="name" class="w-full p-2 border rounded">
        </div>

        {{-- KODE --}}
        <div class="mb-3">
            <label>Kode Asset</label>
            <input type="text" name="code" class="w-full p-2 border rounded">
        </div>

        {{-- CATEGORY --}}
        <div class="mb-3">
            <label>Kategori (ID)</label>
            <input type="number" name="category_id" class="w-full p-2 border rounded">
        </div>

        {{-- STOCK --}}
        <div class="mb-3">
            <label>Stock</label>
            <input type="number" name="stock" class="w-full p-2 border rounded">
        </div>

        {{-- KONDISI --}}
        <div class="mb-3">
            <label>Kondisi</label>
            <input type="text" name="condition" class="w-full p-2 border rounded">
        </div>

        {{-- STATUS --}}
        <div class="mb-3">
            <label>Status</label>
            <input type="text" name="status" class="w-full p-2 border rounded">
        </div>

        {{-- DESKRIPSI --}}
        <div class="mb-3">
            <label>Deskripsi</label>
            <textarea name="description" class="w-full p-2 border rounded"></textarea>
        </div>

        {{-- BUTTON --}}
        <button class="w-full bg-red-600 text-white py-2 rounded-xl mt-3">
            Simpan
        </button>

    </form>

</div>

</div>

@endsection