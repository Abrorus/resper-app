<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesTransaction extends Model
{
    /** Metode pembayaran yang tersedia di kasir. */
    public const PAYMENT_METHODS = ['Tunai', 'QRIS', 'Kartu debit'];

    protected $fillable = ['order_id', 'payment_method', 'amount', 'transaction_date', 'user_id'];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'transaction_date' => 'date',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function label(): string
    {
        return 'Order #' . ($this->order?->order_number ?? $this->order_id);
    }
}
