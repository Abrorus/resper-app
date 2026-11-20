<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseTransaction extends Model
{
    /** Kategori pengeluaran yang boleh dipilih Kasir. */
    public const CATEGORIES = ['Bahan baku', 'Gaji', 'Utilitas', 'Perawatan', 'Lainnya'];

    protected $fillable = ['category', 'amount', 'description', 'transaction_date', 'user_id'];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'transaction_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Nama singkat transaksi untuk tabel dan log koreksi. */
    public function label(): string
    {
        return $this->description ?: 'Pengeluaran ' . strtolower($this->category);
    }
}
