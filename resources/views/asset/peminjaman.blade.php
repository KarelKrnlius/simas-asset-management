@extends('layouts.app')

@section('title', 'Form Peminjaman Asset')

@section('content')

<div style="min-height:100vh; display:flex; justify-content:center; align-items:center; background:#f4f6f9;">

<div style="
width:520px;
background:white;
padding:35px;
border-radius:20px;
box-shadow:0 15px 40px rgba(0,0,0,0.08);
border:1px solid #eee;
animation:fadeIn 0.4s ease;
">

    {{-- JUDUL --}}
    <h2 style="text-align:center; font-weight:800; color:#c1121f;">
        PEMINJAMAN ASSET
    </h2>

    <p style="text-align:center; color:#777; margin-bottom:25px;">
        Isi data dengan teliti dan benar
    </p>

    <form action="{{ route('peminjaman') }}" method="POST">
        @csrf

        {{-- NAMA --}}
        <div style="margin-bottom:15px;">
            <label>Nama Peminjam</label>
            <input type="text"
                value="{{ auth()->user()->name ?? '' }}"
                readonly
                style="width:100%; padding:10px; border-radius:10px; border:1px solid #ddd; background:#f9f9f9;">
        </div>

        {{-- ASSET --}}
        <label>Asset (max 5)</label>

        <div id="asset-wrapper"></div>

        <button type="button" onclick="addAsset()"
            style="
            margin-top:10px;
            background:#c1121f;
            color:white;
            padding:8px 14px;
            border:none;
            border-radius:8px;
            cursor:pointer;
            transition:0.3s;
            ">
            + Tambah Asset
        </button>

        <small style="color:#888; display:block; margin-top:5px;">
            🟢 Tersedia | 🔴 Sedang dipinjam (tidak bisa dipilih)
        </small>

        {{-- DATE --}}
        <div style="display:flex; gap:10px; margin-top:15px;">
            <input type="date" name="borrow_date" required class="input">
            <input type="date" name="return_date" required class="input">
        </div>

        {{-- BUTTON --}}
        <button type="submit"
            style="
            width:100%;
            margin-top:25px;
            background:#c1121f;
            color:white;
            padding:12px;
            border:none;
            border-radius:12px;
            font-weight:bold;
            cursor:pointer;
            transition:0.3s;
            ">
            Kirim
        </button>

    </form>

</div>
</div>

{{-- STYLE --}}
<style>
.input {
    flex:1;
    padding:10px;
    border-radius:10px;
    border:1px solid #ddd;
    transition:0.2s;
}

.input:focus {
    border-color:#c1121f;
    box-shadow:0 0 0 2px rgba(193,18,31,0.1);
    outline:none;
}

button:hover {
    transform:translateY(-2px);
    opacity:0.9;
}

@keyframes fadeIn {
    from { opacity:0; transform:translateY(15px);}
    to { opacity:1; transform:translateY(0);}
}
</style>

{{-- JS --}}
<script>
let maxAsset = 5;
let count = 0;

const assets = @json($assets);

function addAsset() {
    if (count >= maxAsset) {
        alert("Maksimal 5 asset!");
        return;
    }

    let wrapper = document.getElementById('asset-wrapper');

    let div = document.createElement('div');
    div.style.marginTop = "10px";
    div.style.display = "flex";
    div.style.gap = "10px";

    let select = `<select name="asset_id[]"
        style="flex:1; padding:10px; border-radius:10px; border:1px solid #ddd;" required>
        <option value="">-- pilih asset --</option>`;

    assets.forEach(a => {
        let isAvailable = a.status === 'tersedia';

        let label = isAvailable 
            ? `🟢 ${a.name} (Tersedia)` 
            : `🔴 ${a.name} (Dipinjam)`;

        let disabled = isAvailable ? '' : 'disabled';

        select += `<option value="${a.id}" ${disabled}>
            ${label}
        </option>`;
    });

    select += `</select>`;

    div.innerHTML = select + `
        <button type="button" onclick="this.parentElement.remove(); count--"
            style="background:#e63946; color:white; border:none; padding:8px; border-radius:8px;">
            ✕
        </button>
    `;

    wrapper.appendChild(div);
    count++;
}
</script>

@endsection