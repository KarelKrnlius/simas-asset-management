@extends('layouts.app')

@section('title', 'Pengembalian Aset | SIMAS')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="w-full max-w-3xl mx-auto bg-white rounded-[2rem] shadow-2xl p-8 space-y-8">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-red-600 uppercase tracking-wider">Pengembalian Aset</h1>
            <p class="text-slate-500 text-sm uppercase tracking-[0.2em] mt-2">Pilih peminjam & validasi kondisi barang</p>
        </div>

        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest ml-1">Cari Nama Peminjam</label>
            <div class="relative">
                <input id="user_search" type="text" placeholder="Ketik nama peminjam..." class="w-full h-14 px-4 rounded-2xl border border-slate-200 bg-white text-slate-800 focus:border-red-600 focus:outline-none" autocomplete="off">
                <div id="user_results" class="absolute z-10 w-full mt-1 max-h-64 overflow-y-auto rounded-2xl border border-slate-200 bg-white shadow-lg hidden"></div>
            </div>
            @if($users->isEmpty())
                <p class="text-xs text-red-500 mt-2">Tidak ada peminjam aktif saat ini.</p>
            @else
                <p id="user_search_helper" class="text-xs text-slate-400 mt-2">Ketik untuk mencari, lalu klik nama yang sesuai.</p>
            @endif
        </div>

        <div id="loan_list_container" class="hidden space-y-4">
            <div>
                <p class="text-[10px] font-black uppercase text-slate-500 tracking-widest ml-1">Nama Peminjam</p>
                <h2 id="selected_user_name" class="text-lg font-extrabold text-slate-800">-</h2>
            </div>
            <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest ml-1">Daftar Barang yang Masih Dipinjam</label>
            <div id="loan_items" class="grid grid-cols-1 gap-3"></div>
        </div>

        <div id="condition_form" class="hidden pt-6 border-t border-slate-100 space-y-6">
            <div class="p-4 bg-slate-50 rounded-xl border-l-4 border-red-600">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Barang yang dipilih:</p>
                <h3 id="selected_asset_name" class="text-lg font-extrabold text-slate-900"></h3>
                <p id="selected_asset_qty" class="text-xs font-bold text-rose-500"></p>
                <p id="selected_count" class="text-sm text-slate-600"></p>
                <button type="button" id="cancelSelectionBtn" class="mt-3 inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:border-red-600 hover:text-red-600 transition-all">
                    Batal Pilihan
                </button>
            </div>

            <div id="condition_options" class="space-y-4">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pilih Kondisi Barang</p>
                @foreach(['baik' => 'emerald', 'rusak' => 'amber', 'hilang' => 'rose'] as $status => $color)
                <label class="flex items-center gap-3 p-4 rounded-2xl border border-slate-200 hover:bg-slate-50 transition-all cursor-pointer">
                    <input type="radio" name="condition" value="{{ $status }}" class="h-4 w-4 text-{{ $color }}-600 border-slate-300 focus:ring-{{ $color }}-500">
                    <span class="text-[11px] font-bold uppercase text-{{ $color }}-600">{{ $status }}</span>
                </label>
                @endforeach
            </div>

            <button type="button" id="confirmBtn" class="w-full h-14 bg-red-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-red-700 transition-all">
                Konfirmasi Pengembalian
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@php
    $searchUsers = $users->map(function ($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'loans' => $user->loans,
        ];
    });
@endphp
<script>
    const users = @json($searchUsers);

    const selectedAssets = {};
    let currentLoanId = null;
    let currentAssetId = null;
    let currentAssetKey = null;
    let selectedCondition = null;

    const userSearch = document.getElementById('user_search');
    const selectedCountLabel = document.getElementById('selected_count');
    const cancelSelectionBtn = document.getElementById('cancelSelectionBtn');
    const userResults = document.getElementById('user_results');
    const loanListContainer = document.getElementById('loan_list_container');
    const loanItems = document.getElementById('loan_items');
    const selectedUserName = document.getElementById('selected_user_name');
    const conditionForm = document.getElementById('condition_form');
    const selectedAssetName = document.getElementById('selected_asset_name');
    const selectedAssetQty = document.getElementById('selected_asset_qty');

    function renderUserResults(matches) {
        if (!matches.length) {
            userResults.innerHTML = '<div class="px-4 py-3 text-sm text-slate-500">Nama tidak ditemukan.</div>';
            userResults.classList.remove('hidden');
            return;
        }

        userResults.innerHTML = matches.map(user => {
            return `
                <button type="button" data-user-id="${user.id}" class="w-full text-left px-4 py-3 hover:bg-slate-50 transition-all">
                    <span class="font-semibold text-slate-900">${user.name}</span>
                </button>
            `;
        }).join('');
        userResults.classList.remove('hidden');
    }

    function filterUsers(value) {
        const query = value.trim().toLowerCase();
        if (!query) {
            userResults.classList.add('hidden');
            return [];
        }

        const filtered = users.filter(user => user.name.toLowerCase().includes(query));
        renderUserResults(filtered);
        return filtered;
    }

    userSearch.addEventListener('input', function() {
        filterUsers(this.value);
    });

    userResults.addEventListener('click', function(event) {
        const button = event.target.closest('button[data-user-id]');
        if (!button) return;

        const userId = button.getAttribute('data-user-id');
        const selected = users.find(user => user.id.toString() === userId);
        if (!selected) return;

        userSearch.value = selected.name;
        userResults.classList.add('hidden');
        loadUserLoans(selected);
    });

    document.addEventListener('click', function(event) {
        if (!event.target.closest('#user_search') && !event.target.closest('#user_results')) {
            userResults.classList.add('hidden');
        }
    });

    function loadUserLoans(user) {
        loanItems.innerHTML = '';
        selectedUserName.innerText = user.name || '-';
        loanListContainer.classList.remove('hidden');
        conditionForm.classList.add('hidden');
        currentLoanId = null;
        currentAssetId = null;
        currentAssetKey = null;
        selectedCondition = null;
        Object.keys(selectedAssets).forEach(key => delete selectedAssets[key]);
        updateSelectedCount();

        user.loans.forEach(loan => {
            if (loan.status !== 'dikembalikan') {
                loan.assets.forEach(asset => {
                    const key = `${loan.id}-${asset.id}`;
                    const itemCard = `
                        <div id="asset_card_${key}" onclick="selectItem(${loan.id}, ${asset.id}, '${asset.name.replace(/'/g, "\\'")}', ${asset.pivot.quantity}, '${key}')" class="p-4 rounded-2xl border border-slate-200 flex justify-between items-center cursor-pointer hover:bg-slate-50 transition-all">
                            <div>
                                <p class="text-sm font-bold text-slate-900">${asset.name}</p>
                                <p class="text-[10px] text-slate-400">ID Pinjam: #${loan.id} | Qty: ${asset.pivot.quantity} Unit</p>
                            </div>
                            <span id="asset_label_${key}" class="text-[10px] font-black uppercase tracking-[0.2em] text-red-600">Pilih</span>
                        </div>
                    `;
                    loanItems.innerHTML += itemCard;
                });
            }
        });
    }

    function selectItem(loanId, assetId, name, qty, key) {
        if (currentAssetKey && currentAssetKey !== key) {
            const previousLabel = document.getElementById(`asset_label_${currentAssetKey}`);
            if (previousLabel) {
                previousLabel.textContent = selectedAssets[currentAssetKey]?.condition || 'Pilih';
            }
        }

        currentLoanId = loanId;
        currentAssetId = assetId;
        currentAssetKey = key;
        selectedCondition = selectedAssets[key]?.condition || null;

        document.querySelectorAll('input[name="condition"]').forEach(radio => {
            radio.checked = radio.value === selectedCondition;
        });
        document.getElementById('condition_options').classList.remove('hidden');
        conditionForm.classList.remove('hidden');
        selectedAssetName.innerText = name;
        selectedAssetQty.innerText = `Harus kembali: ${qty} Unit`;
        document.getElementById('selected_condition_value')?.remove();
        if (selectedCondition) {
            const info = document.createElement('p');
            info.id = 'selected_condition_value';
            info.className = 'text-sm font-semibold text-slate-600';
            info.textContent = `Kondisi terpilih: ${selectedCondition}`;
            document.querySelector('#condition_form .space-y-4').prepend(info);
        }
        conditionForm.scrollIntoView({ behavior: 'smooth' });
    }

    document.querySelectorAll('input[name="condition"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (!currentAssetKey) return;
            selectedCondition = this.value;
            selectedAssets[currentAssetKey] = {
                loan_id: currentLoanId,
                asset_id: currentAssetId,
                condition: selectedCondition
            };
            updateSelectedCount();

            const currentLabel = document.getElementById(`asset_label_${currentAssetKey}`);
            if (currentLabel) {
                currentLabel.textContent = selectedCondition;
            }
            const existing = document.getElementById('selected_condition_value');
            if (existing) {
                existing.textContent = `Kondisi terpilih: ${selectedCondition}`;
            } else {
                const info = document.createElement('p');
                info.id = 'selected_condition_value';
                info.className = 'text-sm font-semibold text-slate-600';
                info.textContent = `Kondisi terpilih: ${selectedCondition}`;
                document.querySelector('#condition_form .space-y-4').prepend(info);
            }
            document.getElementById('condition_options').classList.add('hidden');
        });
    });

    function updateSelectedCount() {
        const count = Object.keys(selectedAssets).length;
        selectedCountLabel.textContent = count ? `Sudah memilih kondisi untuk ${count} item` : '';
    }

    cancelSelectionBtn.addEventListener('click', function() {
        if (!currentAssetKey) {
            return;
        }

        delete selectedAssets[currentAssetKey];
        updateSelectedCount();

        const currentLabel = document.getElementById(`asset_label_${currentAssetKey}`);
        if (currentLabel) {
            currentLabel.textContent = 'Pilih';
        }

        selectedCondition = null;
        document.querySelectorAll('input[name="condition"]').forEach(radio => radio.checked = false);
        document.getElementById('selected_condition_value')?.remove();
        document.getElementById('condition_options').classList.remove('hidden');
    });

    document.getElementById('confirmBtn').addEventListener('click', async function() {
        const returns = Object.values(selectedAssets);

        if (!returns.length) {
            Swal.fire('Gagal', 'Pilih minimal satu barang untuk dikembalikan!', 'error');
            return;
        }

        this.disabled = true;
        this.innerText = 'MEMPROSES...';

        try {
            const response = await fetch("{{ route('pengembalian.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ returns })
            });

            const resData = await response.json();

            if (response.ok) {
                Swal.fire('Berhasil', resData.message, 'success').then(() => location.reload());
            } else {
                throw new Error(resData.message || 'Gagal memproses');
            }
        } catch (err) {
            Swal.fire('Error', err.message, 'error');
            this.disabled = false;
            this.innerText = 'Konfirmasi Pengembalian';
        }
    });
</script>
@endsection