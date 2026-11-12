<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SalesTransaction;
use App\Models\TransactionLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SalesTransactionController extends Controller
{
    private const PER_PAGE = 10;

    /** Read: daftar Order yang belum tercatat dan riwayat Transaksi Penjualan. */
    public function index(): View
    {
        return view('keuangan.sales.index', [
            'pendingOrders' => $this->orderBelumTercatat()->with('items')->latest()->get(),
            'sales' => SalesTransaction::with(['order', 'user'])->latest('transaction_date')->latest('id')->paginate(self::PER_PAGE),
            'totalHariIni' => SalesTransaction::whereDate('transaction_date', today())->sum('amount'),
            'methods' => SalesTransaction::PAYMENT_METHODS,
        ]);
    }

    /** Create: Keuangan-1, Validasi Order lalu Buat Transaksi Penjualan. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('sales', [
            'order_id' => ['required', 'integer'],
            'payment_method' => ['required', Rule::in(SalesTransaction::PAYMENT_METHODS)],
            'amount' => ['required', 'integer', 'min:1'],
        ], [
            'order_id.required' => 'Order belum dipilih.',
            'payment_method.required' => 'Metode pembayaran belum dipilih. Lengkapi metode pembayaran terlebih dahulu.',
            'payment_method.in' => 'Metode pembayaran tidak valid.',
            'amount.required' => 'Nominal pembayaran tidak boleh kosong.',
            'amount.integer' => 'Nominal pembayaran harus berupa angka bulat.',
            'amount.min' => 'Nominal pembayaran harus lebih dari nol.',
        ]);

        // Alternate a: Order tidak ditemukan atau sudah tercatat.
        $order = $this->orderBelumTercatat()->find($data['order_id']);
        if (! $order) {
            return redirect()->route('finance.sales.index')
                ->with('error', 'Order tidak valid: Order tidak ditemukan, dibatalkan, atau sudah tercatat.');
        }

        // Alternate c: nominal tidak sesuai total Order.
        if ((int) $data['amount'] !== (int) $order->total) {
            return back()->withInput()->withErrors([
                'amount' => 'Nominal tidak sesuai total Order (Rp ' . number_format($order->total, 0, ',', '.') . '). Periksa kembali rincian Order.',
            ], 'sales');
        }

        $sale = SalesTransaction::create([
            'order_id' => $order->id,
            'payment_method' => $data['payment_method'],
            'amount' => $order->total,
            'transaction_date' => today()->toDateString(),
            'user_id' => $request->user()?->id,
        ]);

        return redirect()->route('finance.sales.show', $sale)
            ->with('success', 'Transaksi penjualan berhasil dicatat dan laporan keuangan sudah diperbarui.');
    }

    /** Read: halaman Struk Transaksi Penjualan. */
    public function show(SalesTransaction $sale): View
    {
        return view('keuangan.sales.show', ['sale' => $sale->load(['order.items', 'user'])]);
    }

    /** Update (form): Form Koreksi Transaksi Penjualan. */
    public function edit(SalesTransaction $sale): View
    {
        return view('keuangan.sales.edit', [
            'sale' => $sale->load('order'),
            'methods' => SalesTransaction::PAYMENT_METHODS,
        ]);
    }

    /** Update (simpan): Keuangan-4, koreksi transaksi dan catat ke TransactionLog. */
    public function update(Request $request, SalesTransaction $sale): RedirectResponse
    {
        $data = $request->validate([
            'payment_method' => ['required', Rule::in(SalesTransaction::PAYMENT_METHODS)],
            'amount' => ['required', 'integer', 'min:1', 'max:1000000000'],
            'transaction_date' => ['required', 'date', 'before_or_equal:today'],
        ], [
            'payment_method.required' => 'Metode pembayaran belum dipilih.',
            'amount.required' => 'Nominal tidak boleh kosong.',
            'amount.integer' => 'Nominal harus berupa angka bulat.',
            'amount.min' => 'Data koreksi tidak valid. Nominal harus lebih dari nol.',
            'transaction_date.before_or_equal' => 'Tanggal transaksi tidak boleh melewati hari ini.',
        ]);

        $lama = $this->snapshot($sale);
        $baru = ['payment_method' => $data['payment_method'], 'amount' => (int) $data['amount'], 'transaction_date' => $data['transaction_date']];

        if ($lama == $baru) {
            return back()->withInput()->with('info', 'Tidak ada perubahan. Ubah minimal satu data sebelum menyimpan koreksi.');
        }

        DB::transaction(function () use ($sale, $lama, $baru, $request) {
            $sale->update($baru);
            TransactionLog::create([
                'transaction_type' => 'Penjualan',
                'transaction_id' => $sale->id,
                'action' => 'ubah',
                'label' => $sale->label(),
                'old_values' => $lama,
                'new_values' => $baru,
                'user_id' => $request->user()?->id,
            ]);
        });

        return redirect()->route('finance.sales.index')->with('success', 'Koreksi transaksi penjualan berhasil disimpan.');
    }

    /** Delete: batalkan pencatatan, Order kembali muncul di daftar belum tercatat. */
    public function destroy(Request $request, SalesTransaction $sale): RedirectResponse
    {
        DB::transaction(function () use ($sale, $request) {
            TransactionLog::create([
                'transaction_type' => 'Penjualan',
                'transaction_id' => $sale->id,
                'action' => 'hapus',
                'label' => $sale->label(),
                'old_values' => $this->snapshot($sale),
                'new_values' => null,
                'user_id' => $request->user()?->id,
            ]);
            $sale->delete();
        });

        return redirect()->route('finance.sales.index')
            ->with('success', 'Transaksi penjualan dihapus. Order kembali ke daftar belum tercatat.');
    }

    /** Order yang boleh dicatat: belum dibatalkan dan belum punya SalesTransaction. */
    private function orderBelumTercatat()
    {
        return Order::query()
            ->where('status', '!=', 'cancelled')
            ->whereNotIn('id', SalesTransaction::query()->select('order_id'));
    }

    private function snapshot(SalesTransaction $sale): array
    {
        return [
            'payment_method' => $sale->payment_method,
            'amount' => (int) $sale->amount,
            'transaction_date' => $sale->transaction_date?->toDateString(),
        ];
    }
}
