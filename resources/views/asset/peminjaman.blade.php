@extends('layouts.app')

@section('title', 'Form Peminjaman Asset')

@section('content')

<div style="
    min-height:80vh;
    display:flex;
    justify-content:center;
    align-items:center;
">

<div style="
    width:500px;
    background:white;
    padding:30px;
    border-radius:15px;
    box-shadow:0 10px 40px rgba(0,0,0,0.1);
">

    <h2 style="color:#c1121f; margin-bottom:5px;">
        Form Peminjaman Asset
    </h2>

    <p style="color:#777; margin-bottom:20px;">
        Isi data dengan benar sebelum mengajukan peminjaman
    </p>

    {{-- ALERT --}}
    @if(session('success'))
        <div style="background:#d4edda; padding:10px; border-radius:8px; margin-bottom:10px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background:#f8d7da; padding:10px; border-radius:8px; margin-bottom:10px;">
            {{ session('error') }}
        </div>
    @endif

    {{-- FORM --}}
    <form action="{{ route('peminjaman.store') }}" method="POST">
        @csrf

        {{-- NAMA USER --}}
        <div style="margin-bottom:12px;">
            <label>Nama Peminjam</label>
            <input type="text" value="{{ auth()->user()->name ?? 'Guest' }}" readonly
                style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
        </div>

        {{-- PILIH ASSET --}}
        <div style="margin-bottom:12px;">
            <label>Pilih Asset (max 5)</label>

            <select name="asset_id[]" multiple
                style="width:100%; height:120px; padding:8px; border-radius:8px;">

                @foreach($assets as $asset)
                    <option value="{{ $asset->id }}">
                        {{ $asset->name }} ({{ $asset->status }})
                    </option>
                @endforeach

            </select>

            <small style="color:gray;">* Maksimal 5 asset</small>
        </div>

        {{-- TANGGAL --}}
        <div style="display:flex; gap:10px; margin-bottom:12px;">
            <input type="date" name="borrow_date"
                style="flex:1; padding:10px; border-radius:8px; border:1px solid #ccc;">

            <input type="date" name="return_date"
                style="flex:1; padding:10px; border-radius:8px; border:1px solid #ccc;">
        </div>

        {{-- BUTTON --}}
        <button type="submit"
            style="
                width:100%;
                background:#c1121f;
                color:white;
                padding:12px;
                border:none;
                border-radius:10px;
                font-weight:bold;
                cursor:pointer;
                transition:0.3s;
            "
            onmouseover="this.style.opacity='0.8'"
            onmouseout="this.style.opacity='1'"
        >
            Ajukan Peminjaman
        </button>

    </form>

</div>
</div>

@endsection