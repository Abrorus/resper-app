<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionLog extends Model
{
    protected $fillable = ['transaction_type', 'transaction_id', 'action', 'label', 'old_values', 'new_values', 'user_id'];

    private const FIELDS = [
        'category' => 'Kategori',
        'amount' => 'Nominal',
        'description' => 'Keterangan',
        'transaction_date' => 'Tanggal',
        'payment_method' => 'Metode pembayaran',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ringkasan perubahan dalam kalimat, misalnya "Nominal Rp 50.000 menjadi Rp 55.000".
     *
     * @return array<int, string>
     */
    public function changes(): array
    {
        if ($this->action === 'hapus') {
            return ['Transaksi dihapus dengan nominal ' . self::value('amount', $this->old_values['amount'] ?? 0)];
        }

        $hasil = [];
        foreach (self::FIELDS as $field => $nama) {
            $lama = $this->old_values[$field] ?? null;
            $baru = $this->new_values[$field] ?? null;
            if (array_key_exists($field, $this->new_values ?? []) && $lama != $baru) {
                $hasil[] = $nama . ' ' . self::value($field, $lama) . ' menjadi ' . self::value($field, $baru);
            }
        }

        return $hasil;
    }

    private static function value(string $field, mixed $nilai): string
    {
        if ($nilai === null || $nilai === '') {
            return '(kosong)';
        }
        if ($field === 'amount') {
            return 'Rp ' . number_format((int) $nilai, 0, ',', '.');
        }
        if ($field === 'transaction_date') {
            return date('d/m/Y', strtotime((string) $nilai));
        }

        return '"' . $nilai . '"';
    }
}
