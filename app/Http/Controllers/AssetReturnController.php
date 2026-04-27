<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Asset;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetReturnController extends Controller
{
    public function index()
    {
        /**
         * Mengambil user yang memiliki pinjaman aktif.
         * Karena tidak ada LoanDetail, kita memanggil relasi 'assets' langsung dari 'loans'.
         */
        $users = User::whereHas('loans', function($q) {
            $q->whereIn('status', ['dipinjam', 'terlambat']);
        })->with(['loans.assets'])->get();

        return view('pengembalian.index', compact('users'));
    }

    public function store(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                /**
                 * 1. Cari data Peminjaman (Loan) dan Aset yang dipilih.
                 * Kita menggunakan loan_id dan asset_id karena data disimpan di tabel pivot.
                 */
                $loan = Loan::findOrFail($request->loan_id);
                $asset = Asset::findOrFail($request->asset_id);

                /**
                 * 2. Update stok fisik di tabel assets.
                 * Barang kembali = Input Baik + Input Rusak.
                 */
                $qtyKembali = (int)$request->baik + (int)$request->rusak;
                $asset->increment('stock', $qtyKembali);

                /**
                 * 3. Update kondisi global aset jika ada yang dilaporkan rusak.
                 */
                if ((int)$request->rusak > 0) {
                    $asset->update(['condition' => 'rusak']);
                }

                /**
                 * 4. Logika Pengurangan Item di Pivot atau Update Status Transaksi.
                 * Karena kamu ingin mengembalikan barang per item:
                 * Kita lepas (detach) asset ini dari loan tersebut karena sudah dikembalikan.
                 */
                $loan->assets()->detach($asset->id);

                /**
                 * 5. Cek apakah masih ada aset lain yang belum dikembalikan dalam transaksi ini.
                 * Jika sudah kosong, ubah status peminjaman menjadi 'dikembalikan'.
                 */
                if ($loan->assets()->count() == 0) {
                    $loan->update(['status' => 'dikembalikan']);
                }
            });

            return response()->json(['message' => 'Barang berhasil dikembalikan ke stok!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }
}