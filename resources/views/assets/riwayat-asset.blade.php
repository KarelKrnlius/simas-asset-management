{{-- ============================================================
     MODAL RIWAYAT ASSET
     Di-include dari master-asset.blade.php
     Dipanggil via JS: openAssetHistory(assetId)
     ============================================================ --}}

<div id="assetHistoryModal"
     class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden"
     onclick="closeHistoryOnBackdrop(event)">

    <div class="bg-white w-full max-w-5xl rounded-[2rem] shadow-2xl mx-4 max-h-[90vh] overflow-y-auto">

        {{-- ===== HEADER ===== --}}
        <div class="sticky top-0 bg-white rounded-t-[2rem] px-8 pt-7 pb-5 border-b border-slate-100 z-10">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tighter">Riwayat Asset</h2>
                    <p class="text-xs font-semibold text-slate-400 mt-0.5 uppercase tracking-wider">Riwayat peminjaman asset</p>
                </div>
                <button onclick="closeAssetHistory()"
                    class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-200 hover:bg-slate-300 text-slate-500 transition-colors">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            {{-- INFO ASSET — detail kiri, stats kanan --}}
            <div id="historyAssetInfo" class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
                {{-- Skeleton loading --}}
                <div class="sm:col-span-2 bg-slate-50 rounded-2xl p-4 animate-pulse">
                    <div class="h-4 bg-slate-200 rounded w-3/4 mb-3"></div>
                    <div class="h-4 bg-slate-200 rounded w-1/2"></div>
                </div>
                <div class="grid grid-cols-3 sm:grid-cols-1 gap-2">
                    <div class="bg-slate-50 rounded-xl p-3 animate-pulse h-16"></div>
                    <div class="bg-slate-50 rounded-xl p-3 animate-pulse h-16"></div>
                    <div class="bg-slate-50 rounded-xl p-3 animate-pulse h-16"></div>
                </div>
            </div>
        </div>

        {{-- ===== BODY ===== --}}
        <div id="historyBody" class="px-8 py-6 space-y-8">
            {{-- Konten diisi via JS --}}
            <div class="flex flex-col items-center py-12 text-slate-400">
                <i class="fas fa-spinner fa-spin text-3xl mb-3"></i>
                <p class="text-sm font-semibold">Memuat data...</p>
            </div>
        </div>

        {{-- ===== FOOTER ===== --}}
        <div class="px-8 pb-8">
            <button onclick="closeAssetHistory()"
                class="w-full bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 rounded-2xl transition-colors">
                Tutup
            </button>
        </div>

    </div>
</div>

{{-- ===== JAVASCRIPT ===== --}}
<script>
// ---------------------------------------------------------------
// Buka modal riwayat asset
// ---------------------------------------------------------------
function openAssetHistory(assetId) {
    const modal = document.getElementById('assetHistoryModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Reset ke loading state
    resetHistoryModal();

    // Fetch data dari server
    fetch(`/assets/${assetId}/history`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            renderHistoryModal(data);
        } else {
            showHistoryError('Gagal memuat data riwayat.');
        }
    })
    .catch(() => showHistoryError('Terjadi kesalahan jaringan.'));
}

// ---------------------------------------------------------------
// Tutup modal
// ---------------------------------------------------------------
function closeAssetHistory() {
    document.getElementById('assetHistoryModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function closeHistoryOnBackdrop(e) {
    if (e.target === document.getElementById('assetHistoryModal')) {
        closeAssetHistory();
    }
}

// ---------------------------------------------------------------
// Reset ke skeleton loading
// ---------------------------------------------------------------
function resetHistoryModal() {
    document.getElementById('historyAssetInfo').innerHTML = `
        <div class="sm:col-span-2 bg-slate-50 rounded-2xl p-4 animate-pulse">
            <div class="h-4 bg-slate-200 rounded w-3/4 mb-3"></div>
            <div class="h-4 bg-slate-200 rounded w-1/2"></div>
        </div>
        <div class="grid grid-cols-3 sm:grid-cols-1 gap-2">
            <div class="bg-slate-50 rounded-xl p-3 animate-pulse h-16"></div>
            <div class="bg-slate-50 rounded-xl p-3 animate-pulse h-16"></div>
            <div class="bg-slate-50 rounded-xl p-3 animate-pulse h-16"></div>
        </div>`;

    document.getElementById('historyBody').innerHTML = `
        <div class="flex flex-col items-center py-12 text-slate-400">
            <i class="fas fa-spinner fa-spin text-3xl mb-3"></i>
            <p class="text-sm font-semibold">Memuat data...</p>
        </div>`;
}

// ---------------------------------------------------------------
// Tampilkan error
// ---------------------------------------------------------------
function showHistoryError(msg) {
    document.getElementById('historyBody').innerHTML = `
        <div class="flex flex-col items-center py-12 text-red-400">
            <i class="fas fa-exclamation-circle text-3xl mb-3"></i>
            <p class="text-sm font-semibold">${msg}</p>
        </div>`;
}

// ---------------------------------------------------------------
// Helper format tanggal — data sudah diformat WIB dari server
// Jika string sudah dalam format "dd M yyyy" atau "dd M yyyy, HH:mm"
// langsung kembalikan, tidak perlu parse ulang
// ---------------------------------------------------------------
function fmtDate(dateStr) {
    if (!dateStr) return '-';
    return dateStr; // sudah diformat PHP: "19 Mei 2026"
}

function fmtDateTime(dateStr) {
    if (!dateStr) return '-';
    return dateStr; // sudah diformat PHP: "19 Mei 2026, 14:30"
}

// ---------------------------------------------------------------
// Badge kondisi
// ---------------------------------------------------------------
function conditionBadge(cond) {
    if (!cond) return '<span class="text-slate-400 text-xs">-</span>';
    const map = {
        baik:   'bg-green-100 text-green-700',
        rusak:  'bg-yellow-100 text-yellow-700',
        hilang: 'bg-red-100 text-red-700',
    };
    const cls = map[cond.toLowerCase()] || 'bg-slate-100 text-slate-600';
    return `<span class="inline-block ${cls} px-2 py-0.5 rounded-lg text-[10px] font-black">${cond.charAt(0).toUpperCase() + cond.slice(1)}</span>`;
}

// ---------------------------------------------------------------
// Render seluruh isi modal
// ---------------------------------------------------------------
function renderHistoryModal(data) {
    const asset       = data.asset;
    const stats       = data.stats;
    const activeLoans = data.active_loans   || [];
    const doneLoans   = data.completed_loans || [];
    const canDelete   = data.can_delete;
    const hasHistory  = data.has_loan_history;

    // ---- Kondisi badge warna ----
    const condMap = {
        baik:   'bg-green-100 text-green-700',
        rusak:  'bg-yellow-100 text-yellow-700',
        hilang: 'bg-red-100 text-red-700',
    };
    const condCls = condMap[(asset.condition || '').toLowerCase()] || 'bg-slate-100 text-slate-600';

    const statusMap = {
        tersedia:        'bg-green-100 text-green-700',
        dipinjam:        'bg-blue-100 text-blue-700',
        perlu_perbaikan: 'bg-yellow-100 text-yellow-700',
        tidak_tersedia:  'bg-red-100 text-red-700',
    };
    const statusCls = statusMap[(asset.status || '').toLowerCase()] || 'bg-slate-100 text-slate-600';
    const statusLabel = (asset.status || '-').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());

    // ---- Keterangan hapus ----
    let deleteInfoHtml = '';
    if (!hasHistory) {
        deleteInfoHtml = `
            <div class="mt-3 flex items-center gap-2 bg-green-50 border border-green-200 rounded-xl px-4 py-2.5">
                <i class="fas fa-check-circle text-green-500 text-sm flex-shrink-0"></i>
                <p class="text-xs font-semibold text-green-700">Asset ini belum pernah dipinjam dan <strong>dapat dihapus</strong>.</p>
            </div>`;
    } else if (canDelete) {
        deleteInfoHtml = `
            <div class="mt-3 flex items-center gap-2 bg-orange-50 border border-orange-200 rounded-xl px-4 py-2.5">
                <i class="fas fa-exclamation-triangle text-orange-500 text-sm flex-shrink-0"></i>
                <p class="text-xs font-semibold text-orange-700">Asset ini berstatus <strong>Hilang</strong> dan memiliki riwayat peminjaman. Asset <strong>dapat dihapus</strong> karena kondisinya hilang.</p>
            </div>`;
    } else {
        deleteInfoHtml = `
            <div class="mt-3 flex items-center gap-2 bg-red-50 border border-red-200 rounded-xl px-4 py-2.5">
                <i class="fas fa-ban text-red-500 text-sm flex-shrink-0"></i>
                <p class="text-xs font-semibold text-red-700">Asset ini pernah dipinjam sehingga <strong>tidak dapat dihapus</strong>. Hanya asset dengan kondisi "Hilang" yang boleh dihapus.</p>
            </div>`;
    }

    // ---- Header info asset ----
    document.getElementById('historyAssetInfo').innerHTML = `
        <div class="sm:col-span-2 bg-slate-50 rounded-2xl p-4">
            <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kode</p>
                    <p class="font-black text-slate-900 text-sm font-mono mt-0.5">${asset.code}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama</p>
                    <p class="font-black text-slate-900 text-sm mt-0.5">${asset.name}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kategori</p>
                    <span class="inline-block bg-blue-100 text-blue-700 px-2 py-0.5 rounded-lg text-xs font-bold mt-0.5">${asset.category?.name || '-'}</span>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kondisi Saat Ini</p>
                    <span class="inline-block ${condCls} px-2 py-0.5 rounded-lg text-xs font-bold mt-0.5">${(asset.condition || '-').charAt(0).toUpperCase() + (asset.condition || '-').slice(1)}</span>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</p>
                    <span class="inline-block ${statusCls} px-2 py-0.5 rounded-lg text-xs font-bold mt-0.5">${statusLabel}</span>
                </div>
            </div>
            ${deleteInfoHtml}
        </div>

        <div class="grid grid-cols-3 sm:grid-cols-1 gap-2">
            <div class="bg-blue-50 rounded-xl p-3 flex sm:flex-row items-center gap-3">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-boxes text-blue-500 text-sm"></i>
                </div>
                <div>
                    <p class="text-xl font-black text-blue-600 leading-none">${stats.total}</p>
                    <p class="text-[10px] font-bold text-blue-400 uppercase">Total Pinjam</p>
                </div>
            </div>
            <div class="bg-green-50 rounded-xl p-3 flex sm:flex-row items-center gap-3">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check-circle text-green-500 text-sm"></i>
                </div>
                <div>
                    <p class="text-xl font-black text-green-600 leading-none">${stats.returned}</p>
                    <p class="text-[10px] font-bold text-green-400 uppercase">Sudah Kembali</p>
                </div>
            </div>
            <div class="bg-red-50 rounded-xl p-3 flex sm:flex-row items-center gap-3">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-clock text-red-500 text-sm"></i>
                </div>
                <div>
                    <p class="text-xl font-black text-red-600 leading-none">${stats.active}</p>
                    <p class="text-[10px] font-bold text-red-400 uppercase">Dipinjam</p>
                </div>
            </div>
        </div>`;

    // ---- Body ----
    let bodyHtml = '';

    // ---- Sedang Dipinjam ----
    if (activeLoans.length > 0) {
        let cardsHtml = activeLoans.map(loan => `
            <div class="border-2 border-red-200 bg-red-50 rounded-2xl p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="font-mono font-black text-slate-700 text-sm bg-white border border-slate-200 px-2 py-0.5 rounded-lg">${loan.loan_code || 'PIN-' + String(loan.loan_id).padStart(6,'0')}</span>
                    <span class="bg-red-500 text-white text-[10px] font-black uppercase px-2 py-0.5 rounded-full">Aktif</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-white rounded-xl p-2.5">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Peminjam</p>
                        <p class="font-bold text-slate-900 text-xs mt-0.5">${loan.borrower_name || '-'}</p>
                        <p class="text-[10px] text-slate-400">${loan.borrower_email || ''}</p>
                    </div>
                    <div class="bg-white rounded-xl p-2.5">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Tgl Pinjam</p>
                        <p class="font-bold text-slate-900 text-xs mt-0.5">${fmtDate(loan.borrow_date)}</p>
                    </div>
                    <div class="bg-white rounded-xl p-2.5">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Rencana Kembali</p>
                        <p class="font-bold text-slate-900 text-xs mt-0.5">${fmtDate(loan.return_date)}</p>
                    </div>
                    <div class="bg-white rounded-xl p-2.5">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Qty</p>
                        <p class="font-bold text-slate-900 text-xs mt-0.5">${loan.quantity || 1} Unit</p>
                    </div>
                    <div class="bg-white rounded-xl p-2.5 col-span-2">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Diproses Oleh</p>
                        <p class="font-bold text-slate-900 text-xs mt-0.5">${loan.processed_by_name || '-'}</p>
                        <p class="text-[10px] text-slate-400">${loan.processed_by_email || ''}</p>
                    </div>
                </div>
            </div>`).join('');

        bodyHtml += `
            <div>
                <h3 class="text-xs font-black text-red-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <i class="fas fa-clock"></i> Sedang Dipinjam
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">${cardsHtml}</div>
            </div>`;
    }

    // ---- Riwayat Selesai ----
    if (doneLoans.length > 0) {
        let rowsHtml = doneLoans.map(loan => `
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="py-3 px-4">
                    <span class="font-mono font-bold text-slate-700 text-xs bg-slate-100 px-2 py-0.5 rounded-lg">${loan.loan_code || 'PIN-' + String(loan.loan_id).padStart(6,'0')}</span>
                </td>
                <td class="py-3 px-4">
                    <p class="font-bold text-slate-900 text-xs">${loan.borrower_name || '-'}</p>
                    <p class="text-[10px] text-slate-400">${loan.borrower_email || ''}</p>
                </td>
                <td class="py-3 px-4 text-xs text-slate-700 font-semibold whitespace-nowrap">${fmtDate(loan.borrow_date)}</td>
                <td class="py-3 px-4 text-xs text-slate-700 font-semibold whitespace-nowrap">${fmtDate(loan.return_date)}</td>
                <td class="py-3 px-4 text-center text-xs font-bold text-slate-700">${loan.quantity || 1}</td>
                <td class="py-3 px-4 text-center">${conditionBadge(loan.return_condition)}</td>
                <td class="py-3 px-4">
                    <p class="text-xs font-bold text-slate-700">${loan.processed_by_name || '-'}</p>
                    <p class="text-[10px] text-slate-400">${fmtDateTime(loan.returned_at)}</p>
                </td>
            </tr>`).join('');

        bodyHtml += `
            <div>
                <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <i class="fas fa-history text-slate-400"></i> Riwayat Selesai
                </h3>
                <div class="border border-slate-200 rounded-2xl overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="text-left py-3 px-4 text-[10px] font-black text-slate-500 uppercase tracking-wider">Kode PIN</th>
                                <th class="text-left py-3 px-4 text-[10px] font-black text-slate-500 uppercase tracking-wider">Peminjam</th>
                                <th class="text-left py-3 px-4 text-[10px] font-black text-slate-500 uppercase tracking-wider">Tgl Pinjam</th>
                                <th class="text-left py-3 px-4 text-[10px] font-black text-slate-500 uppercase tracking-wider">Tgl Kembali</th>
                                <th class="text-center py-3 px-4 text-[10px] font-black text-slate-500 uppercase tracking-wider">Qty</th>
                                <th class="text-center py-3 px-4 text-[10px] font-black text-slate-500 uppercase tracking-wider">Kondisi</th>
                                <th class="text-left py-3 px-4 text-[10px] font-black text-slate-500 uppercase tracking-wider">Diproses Oleh</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">${rowsHtml}</tbody>
                    </table>
                </div>
            </div>`;
    }

    // ---- Belum ada riwayat sama sekali ----
    if (activeLoans.length === 0 && doneLoans.length === 0) {
        bodyHtml = `
            <div class="flex flex-col items-center py-12 text-slate-400">
                <i class="fas fa-inbox text-4xl mb-3"></i>
                <p class="text-sm font-semibold">Belum ada riwayat peminjaman untuk asset ini.</p>
            </div>`;
    }

    document.getElementById('historyBody').innerHTML = bodyHtml;
}
</script>
