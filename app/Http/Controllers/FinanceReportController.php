<?php

namespace App\Http\Controllers;

use App\Models\ExpenseTransaction;
use App\Models\SalesTransaction;
use App\Models\TransactionLog;
use Illuminate\View\View;

class FinanceReportController extends Controller
{
    /**
     * Menampilkan halaman Laporan Keuangan (use case Keuangan-3).
     * Data dibaca langsung dari tabel sales_transactions dan expense_transactions.
     */
    public function index(): View
    {
        $hariIni = today();
        $batasAwal = $hariIni->copy()->subYear()->toDateString();

        // Transaksi yang pernah dikoreksi diberi penanda "Dikoreksi".
        $dikoreksi = TransactionLog::query()
            ->where('action', 'ubah')
            ->get(['transaction_type', 'transaction_id'])
            ->mapWithKeys(fn ($log) => [$log->transaction_type . $log->transaction_id => true]);

        $penjualan = SalesTransaction::with('order')
            ->whereDate('transaction_date', '>=', $batasAwal)
            ->get()
            ->map(fn (SalesTransaction $t) => [
                'id' => 'J' . $t->id,
                'tanggal' => $t->transaction_date->toDateString(),
                'jam' => $t->created_at?->format('H.i') ?? '00.00',
                'jenis' => 'Penjualan',
                'ket' => $t->label(),
                'sub' => $t->payment_method,
                'nominal' => (int) $t->amount,
                'dikoreksi' => isset($dikoreksi['Penjualan' . $t->id]),
                'url' => route('finance.sales.edit', $t),
            ]);

        $pengeluaran = ExpenseTransaction::query()
            ->whereDate('transaction_date', '>=', $batasAwal)
            ->get()
            ->map(fn (ExpenseTransaction $t) => [
                'id' => 'K' . $t->id,
                'tanggal' => $t->transaction_date->toDateString(),
                'jam' => $t->created_at?->format('H.i') ?? '00.00',
                'jenis' => 'Pengeluaran',
                'ket' => $t->label(),
                'sub' => $t->category,
                'nominal' => (int) $t->amount,
                'dikoreksi' => isset($dikoreksi['Pengeluaran' . $t->id]),
                'url' => route('finance.expenses.edit', $t),
            ]);

        $transaksi = $penjualan->concat($pengeluaran)
            ->sortByDesc(fn ($t) => $t['tanggal'] . $t['jam'])
            ->values();

        $tanggal = $transaksi->pluck('tanggal');

        return view('finance-report', [
            'hariIni' => $hariIni->toDateString(),
            'transaksi' => $transaksi,
            'periodeData' => $transaksi->isEmpty()
                ? null
                : date('d/m/Y', strtotime($tanggal->min())) . ' sampai ' . date('d/m/Y', strtotime($tanggal->max())),
            'logs' => TransactionLog::with('user')->latest()->take(5)->get(),
            'jumlahLog' => TransactionLog::count(),
        ]);
    }
}
