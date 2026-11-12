<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\TransactionLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionLogController extends Controller
{
    /** Read: riwayat koreksi transaksi (TransactionLog) untuk Manajer. */
    public function index(Request $request): View
    {
        $jenis = (string) $request->query('jenis', '');

        return view('keuangan.corrections.index', [
            'logs' => TransactionLog::with('user')
                ->when(in_array($jenis, ['Penjualan', 'Pengeluaran'], true), fn ($q) => $q->where('transaction_type', $jenis))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'jenis' => $jenis,
        ]);
    }
}
