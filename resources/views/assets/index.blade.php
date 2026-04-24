@extends('layouts.app')

@section('title', 'Data Asset')

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
        <h1 class="text-2xl font-black">Data Asset</h1>

        <a href="{{ route('assets.create') }}"
           class="bg-red-600 text-white px-5 py-2 rounded-xl text-xs font-bold hover:bg-red-700 transition">
            + Tambah Asset
        </a>
    </div>

    {{-- TABLE --}}
    <div class="bg-white p-6 rounded-2xl shadow">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="p-3">No</th>
                    <th class="p-3">Nama Barang</th>
                    <th class="p-3">Kategori</th>
                    <th class="p-3">Kondisi</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($assets as $i => $asset)
                <tr class="border-b hover:bg-gray-50">

                    {{-- FIX UTAMA: HAPUS currentPage() --}}
                    <td class="p-3">
                        {{ $assets->firstItem() + $i }}
                    </td>

                    <td class="p-3">{{ $asset->name }}</td>
                    <td class="p-3">{{ $asset->category->name ?? '-' }}</td>
                    <td class="p-3">{{ $asset->condition }}</td>

                    <td class="p-3">
                        <span class="px-2 py-1 rounded text-xs font-bold
                            {{ $asset->status == 'tersedia' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                            {{ $asset->status }}
                        </span>
                    </td>

                    {{-- AKSI --}}
                    <td class="p-3 flex gap-2 justify-center">

                        <a href="{{ route('assets.edit', $asset->id) }}"
                           class="bg-yellow-400 px-3 py-1 rounded text-xs font-bold hover:bg-yellow-500">
                            Edit
                        </a>

                        <form action="{{ route('assets.destroy', $asset->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Yakin hapus data?')"
                                class="bg-red-600 text-white px-3 py-1 rounded text-xs font-bold hover:bg-red-700">
                                Hapus
                            </button>
                        </form>

                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="6" class="text-center p-4 text-gray-400">
                        Data asset kosong
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>

        {{-- PAGINATION --}}
        <div class="mt-4">
            {{ $assets->links() }}
        </div>

    </div>

</div>

@endsection