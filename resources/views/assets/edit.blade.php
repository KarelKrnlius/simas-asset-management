@extends('layouts.app')

@section('title', 'Edit Asset')

@section('content')

<div class="p-6 flex justify-center">

    <div class="w-full max-w-lg bg-white p-6 rounded-2xl shadow">

        <h2 class="text-xl font-black mb-4">Edit Asset</h2>

        <form action="{{ route('assets.update', $asset->id) }}" method="POST">
            @csrf
            @method('PUT')

            <input type="text" name="name" value="{{ $asset->name }}"
                   class="w-full mb-3 p-2 border rounded" placeholder="Nama">

            <input type="text" name="code" value="{{ $asset->code }}"
                   class="w-full mb-3 p-2 border rounded" placeholder="Kode">

            <input type="number" name="stock" value="{{ $asset->stock }}"
                   class="w-full mb-3 p-2 border rounded" placeholder="Stock">

            <input type="text" name="condition" value="{{ $asset->condition }}"
                   class="w-full mb-3 p-2 border rounded" placeholder="Kondisi">

            <input type="text" name="status" value="{{ $asset->status }}"
                   class="w-full mb-3 p-2 border rounded" placeholder="Status">

            <div class="flex justify-between">
                <a href="{{ route('assets') }}"
                   class="bg-gray-400 text-white px-4 py-2 rounded">
                    Kembali
                </a>

                <button class="bg-blue-600 text-white px-4 py-2 rounded">
                    Update
                </button>
            </div>

        </form>

    </div>

</div>

@endsection