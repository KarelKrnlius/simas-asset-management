@extends('layouts.app')

@section('title', 'QR Code Scanner - Asset Library')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center p-4">
    
    <div class="w-full max-w-7xl">
        
        {{-- Clean Header --}}
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center text-white">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4 4H6V6H4V4M8 4H10V6H8V4M12 4H14V6H12V4M16 4H18V6H16V4M20 4H22V6H20V4M4 8H6V10H4V8M20 8H22V10H20V8M4 12H6V14H4V12M20 12H22V14H20V12M4 16H6V18H4V16M20 16H22V18H20V16M4 20H6V22H4V20M8 20H10V22H8V20M12 20H14V22H12V20M16 20H18V22H16V20M20 20H22V22H20V20Z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-black text-slate-900 uppercase italic">QR Scanner</h1>
                <p class="text-[9px] font-bold text-red-600 uppercase tracking-widest">Asset Identification</p>
            </div>
        </div>

        {{-- Main Scanner Card --}}
        <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-200 overflow-hidden">
            
            {{-- Camera View --}}
            <div class="relative bg-slate-900 aspect-square">
                <video id="camera-video" class="w-full h-full object-cover" autoplay playsinline></video>
                
                {{-- Clean Red Scanner Frame --}}
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="relative">
                        <!-- Main scanner frame -->
                        <div class="relative w-[28rem] h-[28rem] border-3 border-red-600 rounded-3xl">
                            <!-- Corner decorations -->
                            <div class="absolute top-0 left-0 w-8 h-8 border-t-3 border-l-3 border-white rounded-tl-lg"></div>
                            <div class="absolute top-0 right-0 w-8 h-8 border-t-3 border-r-3 border-white rounded-tr-lg"></div>
                            <div class="absolute bottom-0 left-0 w-8 h-8 border-b-3 border-l-3 border-white rounded-bl-lg"></div>
                            <div class="absolute bottom-0 right-0 w-8 h-8 border-b-3 border-r-3 border-white rounded-br-lg"></div>
                            
                            <!-- Single animated scanning line -->
                            <div class="absolute top-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-red-600 to-transparent animate-pulse"></div>
                        </div>
                        
                        <!-- Minimal center crosshair -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-16 h-16 border border-red-300/30 rounded-full flex items-center justify-center">
                                <div class="w-2 h-2 bg-red-600 rounded-full animate-pulse"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Flash Button in Scanner Box --}}
                <button onclick="toggleFlash()" id="flash-btn" class="absolute top-6 right-6 z-10">
                    <div class="w-10 h-10 bg-black/50 backdrop-blur-md rounded-xl flex items-center justify-center border-2 border-white/30 transition-all duration-300 hover:bg-red-600 hover:border-red-600">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                        </svg>
                    </div>
                    <!-- Flash Status Dot -->
                    <div id="flash-status-dot" class="absolute -top-1 -right-1 w-3 h-3 bg-red-600 rounded-full border-2 border-white hidden"></div>
                </button>

                <!-- Clean Scanning Status -->
                <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 bg-black/60 backdrop-blur-md px-5 py-2 rounded-full">
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-[10px] font-bold text-white uppercase tracking-widest">SCANNING</span>
                    </div>
                </div>
            </div>

            {{-- Clean Controls Section --}}
            <div class="p-6 bg-white">
                
                {{-- Instructions --}}
                <div class="text-center mb-6">
                    <p class="text-sm font-black text-slate-900 uppercase mb-1">Center the QR code within the frame</p>
                    <p class="text-xs text-slate-500 font-medium">Hold steady for automatic identification</p>
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-3">
                    <button onclick="uploadFromGallery()" class="flex-1 bg-red-600 text-white py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-red-700 transition-all duration-300 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Upload
                    </button>
                </div>
            </div>
        </div>

        {{-- Clean Recent Scans --}}
        <div class="mt-6 bg-white rounded-[2rem] border border-slate-200 p-6">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center text-white">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-black text-slate-900 uppercase">Recent Scans</h3>
            </div>
            
            <div class="space-y-2">
                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100 hover:bg-slate-100 transition-all duration-300 cursor-pointer" onclick="viewRecentScan('A-4928')">
                    <div class="w-10 h-10 bg-red-600 text-white rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4 4H6V6H4V4M8 4H10V6H8V4M12 4H14V6H12V4M16 4H18V6H16V4M20 4H22V6H20V4M4 8H6V10H4V8M20 8H22V10H20V8M4 12H6V14H4V12M20 12H22V14H20V12M4 16H6V18H4V16M20 16H22V18H20V16M4 20H6V22H4V20M8 20H10V22H8V20M12 20H14V22H12V20M16 20H18V22H16V20M20 20H22V22H20V20Z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-black text-slate-900 uppercase">A-4928 Workstation Server</p>
                        <p class="text-[9px] text-slate-400 font-bold uppercase mt-0.5">2 minutes ago</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden file input for gallery upload -->
<input type="file" id="gallery-input" accept="image/*" style="display: none;" onchange="handleGalleryUpload(event)">

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let stream = null;
    let flashState = false;
    let flashTrack = null;

    const config = {
        fps: 10,
        qrbox: { width: 320, height: 320 },
        aspectRatio: 1.0
    };

    const html5QrCode = new Html5Qrcode("camera-video");

    // Start camera with flash support
    async function startCamera() {
        try {
            // Request camera stream with torch constraint for flash
            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: "environment",
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            });

            // Check if torch (flash) is supported
            const videoTracks = stream.getVideoTracks();
            if (videoTracks.length > 0) {
                const capabilities = videoTracks[0].getCapabilities();
                if (capabilities.torch) {
                    flashTrack = videoTracks[0];
                }
            }

            // Start QR scanning
            html5QrCode.start(
                { facingMode: "environment" },
                config,
                (decodedText, decodedResult) => {
                    handleScanSuccess(decodedText);
                },
                (errorMessage) => {
                    // Ignore error messages
                }
            );

        } catch (err) {
            console.error("Unable to start camera:", err);
            showCameraError();
        }
    }

    function showCameraError() {
        document.getElementById('camera-video').innerHTML = `
            <div class="flex items-center justify-center h-full bg-gradient-to-br from-gray-900 to-black">
                <div class="text-center text-gray-400">
                    <div class="w-20 h-20 bg-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <p class="text-lg font-medium mb-2">Camera not available</p>
                    <p class="text-sm">Use gallery upload instead</p>
                </div>
            </div>
        `;
    }

    function handleScanSuccess(decodedText) {
        // Stop scanning
        html5QrCode.stop().then(() => {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
            // Redirect to asset detail page
            window.location.href = `/asset-library/search?qr_code=${encodeURIComponent(decodedText)}`;
        }).catch((err) => {
            console.error("Unable to stop scanning:", err);
        });
    }

    // Functional flash toggle
    window.toggleFlash = async function() {
        if (!flashTrack) {
            alert('Flash tidak tersedia di perangkat ini');
            return;
        }

        try {
            flashState = !flashState;
            await flashTrack.applyConstraints({
                advanced: [{ torch: flashState }]
            });

            // Update UI for scanner box design
            const flashBtn = document.getElementById('flash-btn');
            const flashStatusDot = document.getElementById('flash-status-dot');
            const flashBox = flashBtn.querySelector('div');
            
            if (flashState) {
                flashBox.classList.add('bg-red-600', 'border-red-600');
                flashBox.classList.remove('bg-black/50', 'border-white/30');
                flashStatusDot.classList.remove('hidden');
            } else {
                flashBox.classList.remove('bg-red-600', 'border-red-600');
                flashBox.classList.add('bg-black/50', 'border-white/30');
                flashStatusDot.classList.add('hidden');
            }
        } catch (err) {
            console.error("Failed to toggle flash:", err);
            alert('Gagal mengaktifkan flash');
        }
    };

    // Handle gallery upload
    window.uploadFromGallery = function() {
        document.getElementById('gallery-input').click();
    };

    window.handleGalleryUpload = function(event) {
        const file = event.target.files[0];
        if (file) {
            const html5QrCode = new Html5Qrcode("camera-video");
            html5QrCode.scanFile(file, true)
                .then(decodedText => {
                    window.location.href = `/asset-library/search?qr_code=${encodeURIComponent(decodedText)}`;
                })
                .catch(err => {
                    console.error("Unable to scan file:", err);
                    alert('No QR code found in the image');
                });
        }
    };

    // Handle recent scan click
    window.viewRecentScan = function(assetCode) {
        window.location.href = `/asset-library/search?qr_code=${encodeURIComponent(assetCode)}`;
    };

    // Start camera on load
    startCamera();

    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    });
});
</script>
@endsection
