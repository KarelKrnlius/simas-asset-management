<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Asset - {{ $asset->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">

    <!-- CONTENT -->
    <div class="max-w-5xl mx-auto px-6 py-8 space-y-8">

        <!-- BREADCRUMB -->
        <div class="flex items-center gap-2 text-sm text-slate-400">
            <i class="fas fa-home"></i>
            <span>Asset Library</span>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-slate-600 font-medium">{{ $asset->name }}</span>
        </div>

        <!-- MAIN GRID -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <!-- LEFT: SPECIFICATION -->
            <div class="bg-white p-8 rounded-3xl shadow-sm">
                <h2 class="text-lg font-bold text-slate-700 mb-6 flex items-center gap-2">
                    <i class="fas fa-info-circle text-red-600"></i>
                    Spesifikasi Aset
                </h2>

                <div class="space-y-5">
                    <div>
                        <p class="text-gray-400 text-xs uppercase tracking-wider font-medium">Kode Asset</p>
                        <p class="text-xl font-bold text-slate-800 mt-1">{{ $asset->code }}</p>
                    </div>

                    <div>
                        <p class="text-gray-400 text-xs uppercase tracking-wider font-medium">Nama Asset</p>
                        <p class="text-lg font-semibold text-slate-800 mt-1">{{ $asset->name }}</p>
                    </div>

                    <div>
                        <p class="text-gray-400 text-xs uppercase tracking-wider font-medium mb-2">Status</p>
                        <span class="px-4 py-2 text-sm rounded-full font-bold uppercase tracking-wide
                            @if(strtolower($asset->status) == 'tersedia')
                                bg-green-100 text-green-600
                            @elseif(strtolower($asset->status) == 'dipinjam')
                                bg-blue-100 text-blue-600
                            @elseif(strtolower($asset->status) == 'maintenance')
                                bg-yellow-100 text-yellow-600
                            @else
                                bg-red-100 text-red-600
                            @endif">
                            <i class="fas fa-circle text-xs mr-2"></i>{{ $asset->status }}
                        </span>
                    </div>

                    <div>
                        <p class="text-gray-400 text-xs uppercase tracking-wider font-medium mb-2">Kondisi</p>
                        <span class="px-4 py-2 text-sm rounded-full font-bold uppercase tracking-wide
                            @if(strtolower($asset->condition) == 'baik')
                                bg-green-100 text-green-600
                            @elseif(strtolower($asset->condition) == 'rusak')
                                bg-yellow-100 text-yellow-600
                            @elseif(strtolower($asset->condition) == 'hilang')
                                bg-red-100 text-red-600
                            @else
                                bg-gray-100 text-gray-600
                            @endif">
                            {{ $asset->condition }}
                        </span>
                    </div>

                    @if($asset->description)
                    <div>
                        <p class="text-gray-400 text-xs uppercase tracking-wider font-medium">Deskripsi</p>
                        <p class="text-sm text-slate-600 mt-1 leading-relaxed">{{ $asset->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- RIGHT: IMAGE -->
            <div class="md:col-span-2 bg-white rounded-3xl p-8 flex items-center justify-center shadow-sm min-h-[400px]">
                @php
                    $photoUrl = $asset->photo ? route('asset.photo', $asset->photo) : asset('images/no-image.png');
                @endphp
                <img src="{{ $photoUrl }}" 
                     alt="{{ $asset->name }}"
                     class="max-h-[380px] object-contain rounded-2xl transition duration-300 hover:scale-105"
                     onerror="this.src='{{ asset('images/no-image.png') }}'">
            </div>

        </div>

        <!-- HISTORY -->
        <div class="bg-white rounded-3xl p-8 shadow-sm">
            <h2 class="text-lg font-bold text-slate-700 mb-8 flex items-center gap-2">
                <i class="fas fa-history text-red-600"></i>
                Riwayat Penggunaan
            </h2>

            <div class="space-y-6">
                @forelse($asset->loans as $loan)
                    <div class="flex gap-5 items-start p-4 rounded-2xl bg-slate-50 hover:bg-slate-100 transition">
                        
                        <!-- DOT & LINE -->
                        <div class="flex flex-col items-center pt-2">
                            <div class="w-4 h-4 rounded-full border-4 
                                {{ $loan->status == 'returned' ? 'bg-green-500 border-green-200' : 'bg-blue-500 border-blue-200' }}">
                            </div>
                            @if(!$loop->last)
                            <div class="w-0.5 h-full bg-slate-200 mt-2"></div>
                            @endif
                        </div>

                        <!-- CONTENT -->
                        <div class="flex-1 pb-4">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2">
                                <div>
                                    <p class="font-bold text-slate-800 text-base">
                                        <i class="fas fa-user-circle text-slate-400 mr-2"></i>{{ $loan->user->name }}
                                    </p>
                                    <p class="text-sm text-slate-500 mt-1">
                                        <i class="far fa-calendar-alt mr-1"></i>
                                        {{ \Carbon\Carbon::parse($loan->borrow_date)->format('d M Y') }} 
                                        <span class="mx-2 text-slate-300">→</span>
                                        {{ $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->format('d M Y') : 'Sekarang' }}
                                    </p>
                                    @if($loan->borrow_date && $loan->return_date)
                                    <p class="text-xs text-slate-400 mt-1">
                                        <i class="far fa-clock mr-1"></i>
                                        Durasi {{ \Carbon\Carbon::parse($loan->borrow_date)->diffInDays(\Carbon\Carbon::parse($loan->return_date)) }} hari
                                    </p>
                                    @endif
                                </div>
                                <span class="px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide
                                    {{ $loan->status == 'returned' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600' }}">
                                    {{ $loan->status == 'returned' ? 'Dikembalikan' : 'Dipinjam' }}
                                </span>
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-inbox text-slate-300 text-2xl"></i>
                        </div>
                        <p class="text-gray-400 text-sm">Belum ada riwayat penggunaan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- FOOTER -->
        <div class="text-center py-8">
            <p class="text-xs text-slate-400">
                <i class="fas fa-shield-alt mr-1"></i>
                SIMAS Asset Management &copy; {{ date('Y') }}
            </p>
        </div>

    </div>

</body>
</html>
