<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel TransactionLog (Keuangan-4: Mengoreksi Kesalahan Input Transaksi).
     * Menyimpan data lama, data baru, dan waktu koreksi.
     */
    public function up(): void
    {
        Schema::create('transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_type');              // Penjualan atau Pengeluaran
            $table->unsignedBigInteger('transaction_id');
            $table->string('action');                        // ubah atau hapus
            $table->string('label');                         // contoh: Order #ORD-001 atau Belanja bahan baku
            $table->json('old_values');
            $table->json('new_values')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['transaction_type', 'transaction_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_logs');
    }
};
