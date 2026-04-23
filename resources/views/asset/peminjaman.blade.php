@extends('layouts.app')

@section('title', 'Form Peminjaman Asset')

@section('content')

<div style="min-height:100vh; display:flex; justify-content:center; align-items:center;">

<div style="width:500px; background:white; padding:30px; border-radius:15px; box-shadow:0 10px 40px rgba(0,0,0,0.1);">

    {{-- JUDUL --}}
    <h2 style="text-align:center; font-weight:bold; color:#c1121f;">
        PEMINJAMAN ASSET
    </h2>

    {{-- SUBJUDUL --}}
    <p style="text-align:center; color:#777; margin-bottom:25px;">
        Isi data dengan teliti dan benar
    </p>

    <form action="{{ route('peminjaman') }}" method="POST">
        @csrf

        {{-- NAMA PEMINJAM --}}
        <div style="margin-bottom:12px;">
            <label>Nama Peminjam</label>
            <input type="text"
                   value="{{ auth()->user()->name ?? '' }}"
                   readonly
                   style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
        </div>

        {{-- ASSET LIST --}}
        <label>Asset (max 5)</label>

        <div id="asset-wrapper"></div>

        <button type="button" onclick="addAsset()"
            style="margin-top:10px; background:#444; color:white; padding:8px 12px; border:none; border-radius:8px;">
            + Tambah Asset
        </button>

        <small style="color:gray; display:block; margin-top:5px;">Maksimal 5 asset</small>

        {{-- TANGGAL --}}
        <div style="display:flex; gap:10px; margin-top:15px;">
            <input type="date" name="borrow_date" required
                style="flex:1; padding:10px; border-radius:8px; border:1px solid #ccc;">

            <input type="date" name="return_date" required
                style="flex:1; padding:10px; border-radius:8px; border:1px solid #ccc;">
        </div>

        {{-- SUBMIT --}}
        <button type="submit"
            style="width:100%; margin-top:20px; background:#c1121f; color:white; padding:12px; border:none; border-radius:10px;">
            Kirim
        </button>

    </form>

</div>
</div>

{{-- JS DINAMIS --}}
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
                    style="flex:1; padding:10px; border-radius:8px; border:1px solid #ccc;" required>
                    <option value="">-- pilih asset --</option>
                `;

    assets.forEach(a => {
        select += `<option value="${a.id}">
                    ${a.name} (${a.status})
                   </option>`;
    });

    select += `</select>`;

    div.innerHTML = select + `
        <button type="button" onclick="this.parentElement.remove(); count--"
            style="background:red; color:white; border:none; padding:8px; border-radius:6px;">
            X
        </button>
    `;

    wrapper.appendChild(div);
    count++;
}
</script>

@endsection