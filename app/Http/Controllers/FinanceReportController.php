<?php

namespace App\Http\Controllers;

use DateTimeImmutable;
use Illuminate\View\View;

class FinanceReportController extends Controller
{
    /**
     * Menampilkan halaman Laporan Keuangan (use case Keuangan-3).
     */
    public function index(): View
    {
        $hariIni = '2026-11-02';
        $transaksi = $this->dataContoh($hariIni);
        $awalData = (new DateTimeImmutable($hariIni))->modify('-60 days');

        return view('finance-report', [
            'hariIni' => $hariIni,
            'transaksi' => $transaksi,
            'periodeData' => $awalData->format('d/m/Y') . ' sampai ' . (new DateTimeImmutable($hariIni))->format('d/m/Y'),
            'kategori' => ['Bahan baku', 'Gaji', 'Utilitas', 'Perawatan'],
            'metodeBayar' => ['Tunai', 'QRIS', 'Kartu debit'],
            'subKeuangan' => [
                ['nama' => 'Transaksi Penjualan', 'aksi' => 'penjualan'],
                ['nama' => 'Transaksi Pengeluaran', 'aksi' => 'pengeluaran'],
                ['nama' => 'Laporan Keuangan', 'aksi' => 'laporan'],
                ['nama' => 'Koreksi Transaksi', 'aksi' => 'koreksi'],
            ],
        ]);
    }

    /**
     * Data contoh 61 hari terakhir. Nantinya diganti dengan query Eloquent
     * ke SalesTransaction, ExpenseTransaction, dan FinancialReport.
     */
    private function dataContoh(string $hariIni): array
    {
        mt_srand(2026);
        $akhir = new DateTimeImmutable($hariIni);
        $metodeBayar = ['Tunai', 'QRIS', 'Kartu debit'];
        $transaksi = [];
        $noOrder = 1000;
        $noKeluar = 0;

        for ($i = 60; $i >= 0; $i--) {
            $tgl = $akhir->modify("-{$i} days");
            $iso = $tgl->format('Y-m-d');
            $hari = (int) $tgl->format('N'); // 1 = Senin, 7 = Minggu

            $jumlahOrder = mt_rand(2, 4) + ($hari >= 5 ? 1 : 0);
            for ($k = 0; $k < $jumlahOrder; $k++) {
                $noOrder++;
                $transaksi[] = [
                    'id' => 'J' . $noOrder,
                    'tanggal' => $iso,
                    'jam' => sprintf('%02d.%02d', mt_rand(11, 21), mt_rand(0, 59)),
                    'jenis' => 'Penjualan',
                    'ket' => 'Order #' . $noOrder,
                    'sub' => $metodeBayar[mt_rand(0, 2)],
                    'nominal' => mt_rand(30, 280) * 5000,
                ];
            }

            $pengeluaran = [];
            if ($i % 2 === 0) {
                $pengeluaran[] = ['Belanja bahan baku', 'Bahan baku', mt_rand(200, 360) * 5000, '07'];
            }
            if ($hari === 6) {
                $pengeluaran[] = ['Gaji staf mingguan', 'Gaji', 2500000, '17'];
            }
            if ($hari === 1) {
                $pengeluaran[] = ['Listrik dan gas', 'Utilitas', mt_rand(90, 120) * 5000, '09'];
            }
            if ($i % 10 === 3) {
                $pengeluaran[] = ['Perawatan peralatan', 'Perawatan', mt_rand(30, 80) * 5000, '10'];
            }

            foreach ($pengeluaran as [$ket, $kat, $nominal, $jam]) {
                $noKeluar++;
                $transaksi[] = [
                    'id' => 'K' . $noKeluar,
                    'tanggal' => $iso,
                    'jam' => $jam . '.' . sprintf('%02d', mt_rand(0, 59)),
                    'jenis' => 'Pengeluaran',
                    'ket' => $ket,
                    'sub' => $kat,
                    'nominal' => $nominal,
                ];
            }
        }

        usort($transaksi, fn ($a, $b) => strcmp($b['tanggal'] . $b['jam'], $a['tanggal'] . $a['jam']));

        return $transaksi;
    }
}