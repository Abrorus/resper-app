<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel ExpenseTransaction (Keuangan-2: Mencatat Transaksi Pengeluaran).
     */
    public function up(): void
    {
        Schema::create('expense_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('category');                     // Bahan baku, Gaji, Utilitas, Perawatan, Lainnya
            $table->unsignedBigInteger('amount');           // nominal dalam rupiah, selalu lebih dari nol
            $table->string('description')->nullable();
            $table->date('transaction_date');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // kasir yang mencatat
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_transactions');
    }
};
