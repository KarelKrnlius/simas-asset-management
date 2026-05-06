@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6">QR Generator</h1>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @foreach($assets as $asset)
            <div class="bg-white p-4 rounded-xl shadow text-center">
                <h2 class="font-semibold mb-2">{{ $asset->name }}</h2>
                <div id="qrcode-{{ $asset->id }}"></div>
                <p class="text-sm mt-2 text-gray-500">{{ $asset->code }}</p>
            </div>
        @endforeach
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<script>
const baseUrl = "http://192.168.1.5:8000/asset-library";
const assets = @json($assets);

assets.forEach(asset => {
    const qrText = `${baseUrl}/${asset.code}`;

    new QRCode(document.getElementById(`qrcode-${asset.id}`), {
        text: qrText,
        width: 150,
        height: 150,
    });
});
</script>
@endsection