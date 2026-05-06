@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Asset List</h1>

    <div class="mb-4 flex gap-3">
        <a href="{{ url('/asset-library/qr') }}" class="bg-blue-500 text-white px-4 py-2 rounded">QR Generator</a>
        <a href="{{ url('/asset-library/scan') }}" class="bg-green-500 text-white px-4 py-2 rounded">Scan QR</a>
    </div>

    <div class="bg-white rounded-xl shadow p-4">
        <table class="w-full">
            <thead>
                <tr class="text-left border-b">
                    <th class="p-2">Nama</th>
                    <th class="p-2">Kode</th>
                    <th class="p-2">Lokasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assets as $asset)
                <tr class="border-b">
                    <td class="p-2">{{ $asset->name }}</td>
                    <td class="p-2">{{ $asset->code }}</td>
                    <td class="p-2">{{ $asset->location }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection