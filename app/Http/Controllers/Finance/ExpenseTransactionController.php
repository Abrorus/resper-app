<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\ExpenseTransaction;
use App\Models\TransactionLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ExpenseTransactionController extends Controller
{
    private const PER_PAGE = 10;

    /** Read: daftar Transaksi Pengeluaran dengan pencarian dan filter kategori. */
    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('cari', ''));
        $kategori = (string) $request->query('kategori', '');

        $query = ExpenseTransaction::query()
            ->with('user')
            ->when($cari !== '', fn ($q) => $q->where(function ($q) use ($cari) {
                $q->where('description', 'like', "%{$cari}%")->orWhere('category', 'like', "%{$cari}%");
            }))
            ->when(in_array($kategori, ExpenseTransaction::CATEGORIES, true), fn ($q) => $q->where('category', $kategori));

        return view('keuangan.expenses.index', [
            'expenses' => (clone $query)->latest('transaction_date')->latest('id')->paginate(self::PER_PAGE)->withQueryString(),
            'total' => (clone $query)->sum('amount'),
            'jumlah' => (clone $query)->count(),
            'categories' => ExpenseTransaction::CATEGORIES,
            'cari' => $cari,
            'kategori' => $kategori,
        ]);
    }

    /** Create (form): halaman Form Tambah Pengeluaran. */
    public function create(): View
    {
        return view('keuangan.expenses.create', [
            'expense' => new ExpenseTransaction(['transaction_date' => now()->toDateString()]),
            'categories' => ExpenseTransaction::CATEGORIES,
        ]);
    }

    /** Create (simpan): Keuangan-2, Validasi Input Pengeluaran lalu Simpan Transaksi Pengeluaran. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules(), $this->messages());

        ExpenseTransaction::create($data + ['user_id' => $request->user()?->id]);

        return redirect()->route('finance.expenses.index')
            ->with('success', 'Transaksi pengeluaran berhasil disimpan dan laporan keuangan sudah diperbarui.');
    }

    /** Update (form): halaman Form Koreksi Transaksi Pengeluaran. */
    public function edit(ExpenseTransaction $expense): View
    {
        return view('keuangan.expenses.edit', [
            'expense' => $expense,
            'categories' => ExpenseTransaction::CATEGORIES,
        ]);
    }

    /** Update (simpan): Keuangan-4, koreksi transaksi dan catat ke TransactionLog. */
    public function update(Request $request, ExpenseTransaction $expense): RedirectResponse
    {
        $data = $request->validate($this->rules(), $this->messages());

        $lama = $this->snapshot($expense);
        $baru = ['category' => $data['category'], 'amount' => (int) $data['amount'],
            'description' => $data['description'] ?? null, 'transaction_date' => $data['transaction_date']];

        if ($lama == $baru) {
            return back()->withInput()->with('info', 'Tidak ada perubahan. Ubah minimal satu data sebelum menyimpan koreksi.');
        }

        DB::transaction(function () use ($expense, $lama, $baru, $request) {
            $expense->update($baru);
            TransactionLog::create([
                'transaction_type' => 'Pengeluaran',
                'transaction_id' => $expense->id,
                'action' => 'ubah',
                'label' => $expense->label(),
                'old_values' => $lama,
                'new_values' => $baru,
                'user_id' => $request->user()?->id,
            ]);
        });

        return redirect()->route('finance.expenses.index')
            ->with('success', 'Koreksi transaksi pengeluaran berhasil disimpan.');
    }

    /** Delete: hapus transaksi pengeluaran, tetap dicatat di TransactionLog. */
    public function destroy(Request $request, ExpenseTransaction $expense): RedirectResponse
    {
        DB::transaction(function () use ($expense, $request) {
            TransactionLog::create([
                'transaction_type' => 'Pengeluaran',
                'transaction_id' => $expense->id,
                'action' => 'hapus',
                'label' => $expense->label(),
                'old_values' => $this->snapshot($expense),
                'new_values' => null,
                'user_id' => $request->user()?->id,
            ]);
            $expense->delete();
        });

        return redirect()->route('finance.expenses.index', $request->only('cari', 'kategori', 'page'))
            ->with('success', 'Transaksi pengeluaran berhasil dihapus.');
    }

    private function snapshot(ExpenseTransaction $expense): array
    {
        return [
            'category' => $expense->category,
            'amount' => (int) $expense->amount,
            'description' => $expense->description,
            'transaction_date' => $expense->transaction_date?->toDateString(),
        ];
    }

    private function rules(): array
    {
        return [
            'category' => ['required', Rule::in(ExpenseTransaction::CATEGORIES)],
            'amount' => ['required', 'integer', 'min:1', 'max:1000000000'],
            'description' => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    private function messages(): array
    {
        return [
            'category.required' => 'Kategori pengeluaran belum dipilih.',
            'category.in' => 'Kategori pengeluaran tidak valid.',
            'amount.required' => 'Nominal pengeluaran tidak boleh kosong.',
            'amount.integer' => 'Nominal pengeluaran harus berupa angka bulat.',
            'amount.min' => 'Nominal pengeluaran harus lebih dari nol.',
            'amount.max' => 'Nominal pengeluaran terlalu besar.',
            'description.max' => 'Keterangan maksimal 255 karakter.',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'transaction_date.date' => 'Tanggal transaksi tidak valid.',
            'transaction_date.before_or_equal' => 'Tanggal transaksi tidak boleh melewati hari ini.',
        ];
    }
}
