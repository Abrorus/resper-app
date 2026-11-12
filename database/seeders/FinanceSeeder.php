<?php

namespace Database\Seeders;

use App\Models\ExpenseTransaction;
use App\Models\Order;
use App\Models\SalesTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Data contoh package Keuangan untuk 30 hari terakhir.
 * Jalankan dengan: php artisan db:seed --class=FinanceSeeder
 */
class FinanceSeeder extends Seeder
{
    private const MENU = [
        ['Nasi Goreng Spesial', 35000],
        ['Mie Ayam Bakso', 28000],
        ['Ayam Bakar Madu', 42000],
        ['Sate Ayam', 38000],
        ['Es Teh Manis', 8000],
        ['Jus Alpukat', 18000],
    ];

    public function run(): void
    {
        if (SalesTransaction::exists() || ExpenseTransaction::exists()) {
            $this->command?->warn('Data Keuangan sudah ada, FinanceSeeder dilewati.');

            return;
        }

        mt_srand(2026);
        $kasir = User::query()->orderBy('id')->value('id');
        $hariIni = now()->startOfDay();

        DB::transaction(function () use ($kasir, $hariIni) {
            $nomor = 1;
            for ($i = 29; $i >= 0; $i--) {
                $tanggal = $hariIni->copy()->subDays($i);

                // Penjualan: Order yang sudah dibayar dan tercatat sebagai SalesTransaction.
                $jumlahOrder = mt_rand(3, 5);
                for ($k = 0; $k < $jumlahOrder; $k++) {
                    $waktu = $tanggal->copy()->setTime(mt_rand(11, 21), mt_rand(0, 59));
                    $order = $this->buatOrder($nomor++, $waktu);

                    // Tiga Order terakhir hari ini sengaja belum dicatat, untuk demo Keuangan-1.
                    if ($i === 0 && $k >= $jumlahOrder - 3) {
                        continue;
                    }

                    $sale = new SalesTransaction([
                        'order_id' => $order->id,
                        'payment_method' => SalesTransaction::PAYMENT_METHODS[mt_rand(0, 2)],
                        'amount' => $order->total,
                        'transaction_date' => $tanggal->toDateString(),
                        'user_id' => $kasir,
                    ]);
                    $sale->created_at = $sale->updated_at = $waktu->copy()->addMinutes(mt_rand(20, 60));
                    $sale->save();
                }

                // Pengeluaran rutin restoran.
                $pengeluaran = [];
                if ($i % 2 === 0) {
                    $pengeluaran[] = ['Bahan baku', 'Belanja bahan baku pasar', mt_rand(120, 220) * 5000, 7];
                }
                if ($tanggal->isSaturday()) {
                    $pengeluaran[] = ['Gaji', 'Gaji staf mingguan', 2500000, 17];
                }
                if ($tanggal->isMonday()) {
                    $pengeluaran[] = ['Utilitas', 'Listrik dan gas', mt_rand(90, 120) * 5000, 9];
                }
                if ($i % 10 === 3) {
                    $pengeluaran[] = ['Perawatan', 'Servis peralatan dapur', mt_rand(30, 80) * 5000, 10];
                }

                foreach ($pengeluaran as [$kategori, $keterangan, $nominal, $jam]) {
                    $expense = new ExpenseTransaction([
                        'category' => $kategori,
                        'amount' => $nominal,
                        'description' => $keterangan,
                        'transaction_date' => $tanggal->toDateString(),
                        'user_id' => $kasir,
                    ]);
                    $expense->created_at = $expense->updated_at = $tanggal->copy()->setTime($jam, mt_rand(0, 59));
                    $expense->save();
                }
            }
        });
    }

    private function buatOrder(int $nomor, $waktu): Order
    {
        $order = new Order([
            'order_number' => 'ORD-KEU-' . str_pad((string) $nomor, 4, '0', STR_PAD_LEFT),
            'customer_name' => 'Pelanggan ' . $nomor,
            'guest_count' => mt_rand(1, 6),
            'table_number' => (string) mt_rand(1, 20),
            'type' => 'offline',
            'status' => 'processed',
        ]);
        $order->created_at = $order->updated_at = $waktu;
        $order->save();

        $total = 0;
        foreach (array_rand(self::MENU, mt_rand(2, 4)) as $idx) {
            [$nama, $harga] = self::MENU[$idx];
            $qty = mt_rand(1, 4);
            $order->items()->create(['menu_name' => $nama, 'price' => $harga, 'quantity' => $qty]);
            $total += $harga * $qty;
        }

        $order->total = $total;
        $order->timestamps = false;
        $order->save();

        return $order;
    }
}
