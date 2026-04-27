<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Aset | SIMAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #F8FAFC; color: #272B30; }
        .card-custom { background: white; border-radius: 1.5rem; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); }
        .input-style { width: 100%; height: 3.5rem; padding-left: 1.25rem; background: white; border: 1px solid #E2E8F0; border-radius: 0.75rem; outline: none; transition: all 0.3s; font-weight: 600; }
        .input-style:focus { border-color: #D21E1E; ring: 2px; ring-color: #D21E1E/20; }
    </style>
</head>
<body class="min-h-screen p-6 flex justify-center items-start bg-[#F8FAFC]">

    <div class="w-full max-w-2xl card-custom p-8 space-y-8 mt-10">
        <div class="text-center">
            <h1 class="text-2xl font-extrabold text-[#D21E1E] uppercase tracking-wider">PENGEMBALIAN ASET</h1>
            <p class="text-slate-400 text-[10px] font-bold uppercase mt-1 tracking-[0.2em]">Pilih Peminjam & Validasi Kondisi</p>
        </div>

        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest ml-1">Cari Nama Peminjam</label>
            <select id="user_select" class="input-style appearance-none cursor-pointer">
                <option value="" disabled selected>-- Pilih Nama Peminjam --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" data-loans="{{ json_encode($user->loans) }}">
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div id="loan_list_container" class="hidden space-y-4">
            <label class="text-[10px] font-black uppercase text-slate-500 tracking-widest ml-1">Daftar Barang yang Masih Dipinjam</label>
            <div id="loan_items" class="grid grid-cols-1 gap-3">
                </div>
        </div>

        <div id="condition_form" class="hidden pt-6 border-t border-slate-100 space-y-6">
            <div class="p-4 bg-slate-50 rounded-xl border-l-4 border-[#D21E1E]">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Barang yang dipilih:</p>
                <h3 id="selected_asset_name" class="text-lg font-extrabold text-slate-800"></h3>
                <p id="selected_asset_qty" class="text-xs font-bold text-rose-500"></p>
            </div>

            <div class="grid grid-cols-1 gap-3">
                @foreach(['baik' => 'emerald', 'rusak' => 'amber', 'hilang' => 'rose'] as $status => $color)
                <div class="flex items-center justify-between p-4 bg-white border border-slate-100 rounded-xl shadow-sm">
                    <span class="text-[11px] font-bold text-{{ $color }}-600 uppercase">{{ $status }}</span>
                    <input type="number" id="input_{{ $status }}" value="0" min="0" class="w-20 h-10 text-center bg-slate-50 border-none rounded-lg font-bold text-slate-700">
                </div>
                @endforeach
            </div>

            <button type="button" id="confirmBtn" class="w-full h-14 bg-[#D21E1E] text-white rounded-xl font-bold uppercase tracking-widest text-xs hover:bg-[#B11818] shadow-lg transition-all">
                KONFIRMASI PENGEMBALIAN BARANG INI
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let currentLoanId = null;
        let currentAssetId = null;
        let maxQty = 0;

        document.getElementById('user_select').addEventListener('change', function() {
            const loans = JSON.parse(this.options[this.selectedIndex].getAttribute('data-loans'));
            const container = document.getElementById('loan_list_container');
            const itemsDiv = document.getElementById('loan_items');
            
            itemsDiv.innerHTML = '';
            container.classList.remove('hidden');
            document.getElementById('condition_form').classList.add('hidden');

            loans.forEach(loan => {
                // Pastikan hanya menampilkan yang statusnya belum dikembalikan
                if(loan.status !== 'dikembalikan') {
                    loan.assets.forEach(asset => {
                        const itemCard = `
                            <div onclick="selectItem(${loan.id}, ${asset.id}, '${asset.name}', ${asset.pivot.quantity})" 
                                 class="p-4 border border-slate-100 rounded-xl flex justify-between items-center cursor-pointer hover:bg-slate-50 transition-all">
                                <div>
                                    <p class="text-sm font-bold text-slate-800">${asset.name}</p>
                                    <p class="text-[10px] text-slate-400 font-medium tracking-wide">ID Pinjam: #${loan.id} | Qty: ${asset.pivot.quantity} Unit</p>
                                </div>
                                <span class="text-[10px] font-bold text-[#D21E1E] bg-rose-50 px-4 py-1.5 rounded-full uppercase tracking-tighter">Pilih</span>
                            </div>
                        `;
                        itemsDiv.innerHTML += itemCard;
                    });
                }
            });
        });

        function selectItem(loanId, assetId, name, qty) {
            currentLoanId = loanId;
            currentAssetId = assetId;
            maxQty = qty;

            document.getElementById('condition_form').classList.remove('hidden');
            document.getElementById('selected_asset_name').innerText = name;
            document.getElementById('selected_asset_qty').innerText = `Harus kembali: ${qty} Unit`;
            
            // Reset input ke 0 tiap ganti barang
            document.getElementById('input_baik').value = 0;
            document.getElementById('input_rusak').value = 0;
            document.getElementById('input_hilang').value = 0;

            document.getElementById('condition_form').scrollIntoView({ behavior: 'smooth' });
        }

        document.getElementById('confirmBtn').addEventListener('click', async function() {
            const b = parseInt(document.getElementById('input_baik').value || 0);
            const r = parseInt(document.getElementById('input_rusak').value || 0);
            const h = parseInt(document.getElementById('input_hilang').value || 0);

            if ((b + r + h) !== maxQty) {
                Swal.fire('Gagal', `Total input (${b+r+h}) harus sama dengan jumlah dipinjam (${maxQty})!`, 'error');
                return;
            }

            this.disabled = true;
            this.innerText = "MEMPROSES...";

            try {
                const response = await fetch("{{ route('pengembalian.store') }}", {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ 
                        loan_id: currentLoanId,
                        asset_id: currentAssetId,
                        baik: b, rusak: r, hilang: h
                    })
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
                this.innerText = "KONFIRMASI PENGEMBALIAN BARANG INI";
            }
        });
    </script>
</body>
</html>