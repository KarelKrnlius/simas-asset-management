@extends('layouts.app')

@section('content')
    <!-- CARD -->
    <div class="bg-red-700 p-8 rounded-3xl shadow-2xl text-center w-[420px] relative">

        <h2 class="text-white text-xl font-semibold mb-6">
            Scan QR Asset
        </h2>

        <!-- SCANNER -->
        <div class="relative w-[340px] h-[340px] mx-auto mb-6 rounded-2xl overflow-hidden">

            <div id="reader" class="w-full h-full"></div>

            <!-- FRAME -->
            <div class="absolute inset-0 border-2 border-white/40 rounded-2xl pointer-events-none"></div>

            <!-- FLASH -->
            <button onclick="toggleFlash()"
                class="absolute top-3 right-3 text-white text-xl opacity-70 hover:opacity-100 transition">
                ⚡
            </button>

        </div>

        <!-- UPLOAD -->
        <label class="bg-white text-red-600 px-6 py-2 rounded-xl text-sm font-semibold cursor-pointer hover:opacity-90">
            Upload QR
            <input type="file" accept="image/*" class="hidden" onchange="scanFile(event)">
        </label>

        <p class="text-white/80 text-xs mt-3">
            Scan atau upload QR
        </p>

    </div>

</div>

<style>
#reader video {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    transform: scaleX(-1) !important;
}
</style>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
let html5QrCode;
let isFlashOn = false;
let isScanning = false;

function onScanSuccess(decodedText) {
    if (isScanning) return;
    isScanning = true;

    console.log("QR decoded:", decodedText);

    // Extract code from URL or use directly
    let code = decodedText;

    // If QR contains full URL, extract code
    if (decodedText.includes('/asset-library/')) {
        const parts = decodedText.split('/asset-library/');
        code = parts[parts.length - 1];
    }
    // If QR contains just the code directly
    else if (decodedText.startsWith('QR-')) {
        code = decodedText.replace('QR-', '');
    }
    // If QR is just the asset code
    else {
        code = decodedText;
    }

    console.log("Final code extracted:", code);
    console.log("Redirecting to asset detail:", `/asset-library/search?qr_code=${code}`);

    // Redirect to search page which will find and show the asset
    window.location.href = `/asset-library/search?qr_code=${encodeURIComponent(code)}`;
}

// START CAMERA
function startScanner() {
    html5QrCode = new Html5Qrcode("reader");

    html5QrCode.start(
        { facingMode: "environment" },
        {
            fps: 10,
            qrbox: 300 // lebih gede
        },
        onScanSuccess
    ).catch(err => {
        console.error(err);
        alert("Kamera tidak bisa diakses");
    });
}

// FLASH
function toggleFlash() {
    if (!html5QrCode) return;

    try {
        html5QrCode.applyVideoConstraints({
            advanced: [{ torch: !isFlashOn }]
        });
        isFlashOn = !isFlashOn;
    } catch {
        alert("Flash tidak support di device ini");
    }
}

// UPLOAD
function scanFile(event) {
    const file = event.target.files[0];
    if (!file) return;

    console.log("Upload file selected:", file.name);

    // Stop camera scanning first
    if (html5QrCode && html5QrCode.isScanning) {
        html5QrCode.stop().then(() => {
            scanFileInternal(file);
        }).catch(err => {
            console.error("Error stopping camera:", err);
            scanFileInternal(file);
        });
    } else {
        scanFileInternal(file);
    }
}

function scanFileInternal(file) {
    console.log("Starting file scan...");
    const scanner = new Html5Qrcode("reader");

    scanner.scanFile(file, true)
    .then(decodedText => {
        console.log("QR decoded from file:", decodedText);

        // Extract code from URL or use directly
        let code = decodedText;

        // If QR contains full URL, extract code
        if (decodedText.includes('/asset-library/')) {
            const parts = decodedText.split('/asset-library/');
            code = parts[parts.length - 1];
        }
        // If QR contains just the code directly
        else if (decodedText.startsWith('QR-')) {
            code = decodedText.replace('QR-', '');
        }
        // If QR is just an asset code
        else {
            code = decodedText;
        }

        console.log("Final code extracted:", code);
        console.log("Redirecting to:", `/asset-library/search?qr_code=${encodeURIComponent(code)}`);

        // Redirect to search page which will find and show the asset
        window.location.href = `/asset-library/search?qr_code=${encodeURIComponent(code)}`;
    })
    .catch(err => {
        console.error("QR scan error:", err);
        alert("QR tidak terbaca. Pastikan QR code jelas dan tidak blur.");
        // Restart camera
        startScanner();
    });
}

// INIT
startScanner();
</script>
@endsection